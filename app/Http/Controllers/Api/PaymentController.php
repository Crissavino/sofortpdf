<?php

namespace App\Http\Controllers\Api;

use App\Classes\GetIpInformation;
use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Models\Payment;
use App\Services\Payment\PaymentGatewayFactory;
use App\Services\Payment\StripeService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

/**
 * Payment API — 3-step flow delegated to BO (same as contract-kit):
 *   1. POST /api/payment/create-customer
 *   2. POST /api/payment/pay-trial
 *   3. POST /api/payment/create-subscription
 *
 * Sofortpdf never talks to Stripe directly; the BO handles everything.
 */
class PaymentController extends Controller
{
    private PaymentGatewayFactory $gatewayFactory;
    private StripeService $stripeService;

    public function __construct(PaymentGatewayFactory $gatewayFactory, StripeService $stripeService)
    {
        $this->gatewayFactory = $gatewayFactory;
        $this->stripeService  = $stripeService;
    }

    /**
     * Step 1: Create/find customer locally + forward to BO to create
     * the Stripe customer. Returns customer_id for the next steps.
     */
    public function createCustomer(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'email'             => 'required|email',
            'full_name'         => 'required|string|max:255',
            'payment_method_id' => 'required|string',
        ]);

        $parts     = explode(' ', trim($validated['full_name']), 2);
        $firstName = $parts[0];
        $lastName  = $parts[1] ?? '';
        $email     = strtolower(trim($validated['email']));
        $websiteId = (int) config('services.bo.website_id');

        // Create/find customer in shared DB (scoped to this brand)
        $plainPassword = Str::random(16);

        $customer = Customer::updateOrCreate(
            ['email' => $email, 'website_id' => $websiteId],
            [
                'first_name'          => $firstName,
                'last_name'           => $lastName,
                'ip'                  => $request->ip(),
                'country'             => GetIpInformation::get($request->ip(), 'countryCode')
                    ?? strtolower(session('country_code', 'de')),
                'language'            => app()->getLocale(),
                'last_time_connected' => now(),
            ]
        );

        $isNewCustomer = empty($customer->password);
        if ($isNewCustomer) {
            $customer->update(['password' => Hash::make($plainPassword)]);
        }

        // Preserve VAD session data across the login (Auth::login regenerates
        // the session, which would wipe the vad.* keys set by ResolveVad).
        $vadData = [
            'vad.used_vad'        => session('vad.used_vad'),
            'vad.company_id'      => session('vad.company_id'),
            'vad.currency'        => session('vad.currency'),
            'vad.currency_id'     => session('vad.currency_id'),
            'vad.segment'         => session('vad.segment'),
            'vad.pricing'         => session('vad.pricing'),
            'bo_vad_id'           => session('bo_vad_id'),
            'bo_payment_route_id' => session('bo_payment_route_id'),
            'country_code'        => session('country_code'),
        ];

        Auth::login($customer);

        // Restore VAD + set customer id
        foreach ($vadData as $key => $value) {
            if ($value !== null) {
                session([$key => $value]);
            }
        }
        session(['idCustomer' => $customer->id]);

        // Forward to BO to create Stripe customer
        $gateway = $this->gatewayFactory->resolveFromSession();
        $result  = $gateway->createCustomer([
            'customer_id'       => $customer->id,
            'email'             => $email,
            'first_name'        => $firstName,
            'last_name'         => $lastName,
            'payment_method_id' => $validated['payment_method_id'],
            'ip'                => $request->ip(),
            'website_id'        => $websiteId,
            'bo_payment_route_id' => session('bo_payment_route_id'),
        ]);

        if (!($result['success'] ?? false)) {
            Log::error('PaymentController::createCustomer BO failed');
            return response()->json(['success' => false, 'message' => __('payment.err_generic')], 422);
        }

        // Store Stripe customer ID from BO response
        $stripeCustomerId = $result['customer']['bo_stripe_customer']['id_stripe_customer'] ?? null;
        session(['stripe_customer_id' => $stripeCustomerId]);

        return response()->json([
            'success'     => true,
            'customer_id' => $customer->id,
            'csrf_token'  => csrf_token(),
        ]);
    }

    /**
     * Step 2: Pay trial — forward to BO, which charges via Stripe.
     * Returns paymentIntent for 3DS handling on the frontend.
     */
    public function payTrial(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'payment_method_id' => 'required|string',
        ]);

        $gateway = $this->gatewayFactory->resolveFromSession();

        $result = $gateway->payTrial([
            'stripe_customer_id' => session('stripe_customer_id'),
            'payment_method_id'  => $validated['payment_method_id'],
        ]);

        Log::info('PaymentController::payTrial BO response', ['result' => $result]);

        $customerId = session('idCustomer');

        if (!($result['success'] ?? false)) {
            $this->savePaymentFailed($customerId, $result['message'] ?? 'Payment failed');
        } else {
            $paymentId = $this->savePaymentSuccess($customerId);
            session(['payment_id' => $paymentId]);
        }

        return response()->json($result);
    }

    /**
     * Step 3: Create subscription — called AFTER trial + 3DS succeeds.
     * Forward to BO, update payment status locally, return confirmation URL.
     */
    public function createSubscription(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'payment_method_id' => 'required|string',
        ]);

        $gateway = $this->gatewayFactory->resolveFromSession();

        $result = $gateway->createSubscription([
            'stripe_customer_id' => session('stripe_customer_id'),
            'payment_method_id'  => $validated['payment_method_id'],
        ]);

        Log::info('PaymentController::createSubscription BO response', ['result' => $result]);

        if (!($result['success'] ?? false)) {
            return response()->json(['success' => false, 'message' => __('payment.err_generic')], 422);
        }

        // Update payment with card details (like contract-kit)
        $customerId = session('idCustomer');
        $payment    = Payment::where('customer_id', $customerId)->latest('id')->first();

        if ($payment) {
            $paymentMethod = $result['paymentMethod'] ?? null;
            if ($paymentMethod) {
                $card        = $paymentMethod['card'] ?? [];
                $billingName = $paymentMethod['billing_details']['name'] ?? null;

                $payment->last_four_digit  = $card['last4'] ?? null;
                $payment->cardholders_name = $billingName;
                $payment->hash_card        = hash('sha1', str_repeat('0', 12) . ($card['last4'] ?? '0000'));
            }

            $payment->payment_status_id = 2; // subscribed
            $payment->payment_code      = (string) $payment->id;
            $payment->processed         = now();
            $payment->save();
        }

        // Create/update subscription row so hasSofortpdfSubscription() returns
        // true on subsequent requests (the paywall middleware checks this).
        $vad       = $this->getVadSessionData();
        $websiteId = (int) config('services.bo.website_id');

        try {
            \App\Models\Subscription::updateOrCreate(
                ['customer_id' => $customerId, 'website_id' => $websiteId],
                [
                    'bo_website_id'          => $vad['bo_website_id'],
                    'company_id'             => (int) session('vad.company_id', config('company.default_company_id', 1)),
                    'bo_product_id'          => $payment->bo_product_id ?? 0,
                    'payment_provider_id'    => 1, // Stripe
                    'plan_type'              => 'monthly',
                    'is_trial_active'        => true,
                    'trial_started_at'       => now(),
                    'trial_ends_at'          => now()->addDays((int) config('services.stripe.trial_days', 2)),
                    'is_subscription_active' => false,
                    'subscription_started_at'=> null,
                ]
            );
        } catch (\Throwable $e) {
            Log::warning('Subscription upsert failed', ['error' => $e->getMessage()]);
        }

        return response()->json([
            'success' => true,
        ]);
    }

    /* ── Helpers ── */

    private function getVadSessionData(): array
    {
        $vad = session('vad.used_vad', []);
        return [
            'bo_website_id' => (int) ($vad['bo_website_id'] ?? 0),
            'bo_vad_id'     => (int) ($vad['bo_vad_id'] ?? session('bo_vad_id', 0)),
            'bo_product_id' => null, // resolved by BO from the stripe product
        ];
    }

    private function savePaymentSuccess(int $customerId): int
    {
        $vad = $this->getVadSessionData();

        // Find bo_product_id from the VAD product chain
        $boProductId = null;
        try {
            $vadProduct = $this->stripeService->getVadProduct();
            $boProductId = $vadProduct ? $vadProduct->bo_product_id : null;
        } catch (\Throwable $e) {}

        // Resolve pricing from StripeService
        $boProduct = null;
        $currencyId = null;
        try {
            $boProduct = $this->stripeService->getBoProduct();
            $vadProduct = $this->stripeService->getVadProduct();
            $currencyId = $vadProduct ? $vadProduct->currency_id : null;
        } catch (\Throwable $e) {}

        try {
            $payment = Payment::create([
                'customer_id'         => $customerId,
                'payment_status_id'   => 4, // in progress
                'currency_id'         => $currencyId ?? 2, // EUR fallback
                'bo_website_id'       => $vad['bo_website_id'],
                'bo_vad_id'           => $vad['bo_vad_id'],
                'bo_product_id'       => $boProductId,
                'subscription_amount' => $boProduct ? $boProduct->subscription_price : null,
                'rebill_amount'       => $boProduct ? $boProduct->periodical_price : null,
                'is_test'             => !app()->isProduction(),
            ]);

            Log::info('Payment saved', ['id' => $payment->id, 'customer_id' => $customerId, 'bo_website_id' => $vad['bo_website_id']]);
            return $payment->id;
        } catch (\Throwable $e) {
            Log::error('savePaymentSuccess failed', ['error' => $e->getMessage(), 'customer_id' => $customerId]);
            return 0;
        }
    }

    private function savePaymentFailed(int $customerId, string $error): void
    {
        $vad = $this->getVadSessionData();

        try {
            Payment::create([
                'customer_id'       => $customerId,
                'payment_status_id' => 3, // failed
                'error_return'      => Str::limit($error, 250),
                'bo_website_id'     => $vad['bo_website_id'],
                'bo_vad_id'         => $vad['bo_vad_id'],
                'is_test'           => !app()->isProduction(),
            ]);
        } catch (\Throwable $e) {
            Log::error('savePaymentFailed failed', ['error' => $e->getMessage()]);
        }
    }
}
