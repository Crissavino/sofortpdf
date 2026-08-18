<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\Payment;
use App\Services\Payment\PaymentGatewayFactory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class CancellationController extends Controller
{
    public function form()
    {
        return view('cancellation.form');
    }

    public function process(Request $request)
    {
        $validated = $request->validate([
            'email' => 'required|email',
        ]);

        $websiteId = config('services.bo.website_id');
        $customer = Customer::where('email', $validated['email'])
            ->where('website_id', $websiteId)
            ->first();

        if (!$customer) {
            Log::warning('Cancellation: customer not found', ['email' => $validated['email']]);
            return back()->withInput()->withErrors([
                'email' => __('cancellation.customer_not_found'),
            ]);
        }

        // Find a cancellable payment. Status 4 (pending/failed) is included on
        // purpose: those customers are stuck mid-trial and used to be unable to
        // cancel through any path at all.
        $payment = Payment::where('customer_id', $customer->id)
            ->whereIn('payment_status_id', [2, 4])
            ->latest('create_time')
            ->first();

        if (!$payment) {
            // Already cancelled — say so instead of "no subscription found".
            // A double-submitted form races the first request here: request #1
            // cancels and flips the payment to status 3, request #2 lands on
            // this branch. Telling the user they have no subscription reads as
            // "the cancellation failed" and sends them to support demanding a
            // refund, when in fact they are already cancelled.
            $alreadyCancelled = Payment::where('customer_id', $customer->id)
                ->where('payment_status_id', 3)
                ->exists();

            if ($alreadyCancelled) {
                return redirect()->route('home', ['locale' => app()->getLocale()])
                    ->with('success', __('cancellation.already_cancelled'));
            }

            Log::warning('Cancellation: no cancellable payment', ['customer_id' => $customer->id]);
            return back()->withInput()->withErrors([
                'email' => __('cancellation.no_active_subscription'),
            ]);
        }

        // Get Stripe account ID
        $boStripe = $customer->boStripeCustomer;
        $stripeAccountId = $boStripe ? $boStripe->bo_stripe_account_id : null;

        try {
            $gateway = app(PaymentGatewayFactory::class)->resolve('stripe');

            $result = $gateway->cancelSubscription([
                'customer'          => $customer,
                'payment_id'        => $payment->id,
                'stripe_account_id' => $stripeAccountId,
            ]);

            if (!($result['success'] ?? false)) {
                return back()->withInput()->withErrors([
                    'email' => __('cancellation.cancel_failed'),
                ]);
            }

            // Update payment status to terminated
            $payment->update(['payment_status_id' => 3]);

            // Update subscription
            $sub = $customer->subscriptions()
                ->where('website_id', $websiteId)
                ->latest('id')
                ->first();
            if ($sub) {
                $sub->update([
                    'is_trial_active'        => false,
                    'is_subscription_active' => false,
                    'cancelled_at'           => now(),
                ]);
            }

            // Logout if it's the same user
            if (Auth::id() === $customer->id) {
                Auth::logout();
                $request->session()->invalidate();
                $request->session()->regenerateToken();
            }

            $locale = app()->getLocale();
            return redirect()->route('home', ['locale' => $locale])
                ->with('success', __('cancellation.success'));

        } catch (\Throwable $e) {
            Log::error('CancellationController::process failed', [
                'error'       => $e->getMessage(),
                'customer_id' => $customer->id,
            ]);

            return back()->withInput()->withErrors([
                'email' => __('cancellation.cancel_failed'),
            ]);
        }
    }
}
