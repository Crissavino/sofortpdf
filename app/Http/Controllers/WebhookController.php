<?php

namespace App\Http\Controllers;

use App\Mail\PaymentFailedMail;
use App\Mail\SubscriptionActiveMail;
use App\Mail\SubscriptionCanceledMail;
use App\Mail\TrialStartedMail;
use App\Mail\WelcomeMail;
use App\Models\BoStripeCustomer;
use App\Models\Customer;
use App\Models\Subscription;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Stripe\Stripe;
use Stripe\Webhook;

/**
 * Verarbeitet Stripe-Webhook-Events für sofortpdf.
 *
 * WICHTIG: CSRF-Verifizierung muss für diese Route deaktiviert sein.
 */
class WebhookController extends Controller
{
    public function handle(Request $request)
    {
        $payload       = $request->getContent();
        $sigHeader     = $request->header('Stripe-Signature');
        $webhookSecret = config('services.stripe.webhook_secret');

        try {
            $event = Webhook::constructEvent($payload, $sigHeader, $webhookSecret);
        } catch (\UnexpectedValueException $e) {
            Log::error('Stripe Webhook: Ungültiger Payload', ['error' => $e->getMessage()]);
            return response('Ungültiger Payload.', 400);
        } catch (\Stripe\Exception\SignatureVerificationException $e) {
            Log::error('Stripe Webhook: Ungültige Signatur', ['error' => $e->getMessage()]);
            return response('Ungültige Signatur.', 400);
        }

        Stripe::setApiKey(config('services.stripe.secret'));

        try {
            switch ($event->type) {
                case 'checkout.session.completed':
                    $this->handleCheckoutSessionCompleted($event->data->object);
                    break;
                case 'customer.subscription.updated':
                    $this->handleSubscriptionUpdated($event->data->object);
                    break;
                case 'customer.subscription.deleted':
                    $this->handleSubscriptionDeleted($event->data->object);
                    break;
                case 'invoice.payment_succeeded':
                    $this->handleInvoicePaymentSucceeded($event->data->object);
                    break;
                case 'invoice.payment_failed':
                    $this->handleInvoicePaymentFailed($event->data->object);
                    break;
                default:
                    Log::info('Stripe Webhook: Nicht behandeltes Event', ['type' => $event->type]);
            }
        } catch (\Throwable $e) {
            Log::error('Stripe Webhook: Fehler bei Verarbeitung', [
                'type'  => $event->type,
                'error' => $e->getMessage(),
            ]);
        }

        return response('OK', 200);
    }

    /* =========================================================================
     | Helpers
     |======================================================================== */

    /**
     * Resolve the Customer behind a Stripe customer ID, scoped to this brand.
     */
    private function resolveCustomerFromStripeId(?string $stripeCustomerId): ?Customer
    {
        if (!$stripeCustomerId) {
            return null;
        }

        $boStripe = BoStripeCustomer::where('id_stripe_customer', $stripeCustomerId)
            ->where('website_id', config('services.bo.website_id'))
            ->first();

        return $boStripe ? Customer::find($boStripe->customer_id) : null;
    }

    private function isSofortpdfMetadata($metadata): bool
    {
        return isset($metadata->product) && $metadata->product === 'sofortpdf';
    }

    /* =========================================================================
     | Event handlers
     |======================================================================== */

    protected function handleCheckoutSessionCompleted($session)
    {
        if (!$this->isSofortpdfMetadata($session->metadata ?? null)) {
            return;
        }

        $customer = $this->resolveCustomerFromStripeId($session->customer ?? null);
        if (!$customer) {
            Log::warning('Stripe Webhook: Customer nicht gefunden', ['stripe_customer' => $session->customer ?? null]);
            return;
        }

        $stripeSubscription = \Stripe\Subscription::retrieve($session->subscription);
        $this->upsertSubscription($customer, $stripeSubscription);

        // Welcome-Mail dedup gegen den CheckoutController-Pfad
        $welcomeLockKey = 'welcome_sent:' . $customer->id;
        if (!Cache::has($welcomeLockKey)) {
            Mail::to($customer->email)->send(new WelcomeMail($customer));
            Cache::put($welcomeLockKey, true, now()->addHours(24));
        }
        Mail::to($customer->email)->send(new TrialStartedMail($customer));

        Log::info('Stripe Webhook: Checkout abgeschlossen', [
            'customer_id'     => $customer->id,
            'subscription_id' => $stripeSubscription->id,
        ]);
    }

    protected function handleSubscriptionUpdated($stripeSubscription)
    {
        $customer = $this->resolveCustomerFromStripeId($stripeSubscription->customer ?? null);
        if (!$customer) {
            return;
        }

        $this->upsertSubscription($customer, $stripeSubscription);

        Log::info('Stripe Webhook: Abo aktualisiert', [
            'customer_id'     => $customer->id,
            'subscription_id' => $stripeSubscription->id,
            'status'          => $stripeSubscription->status,
        ]);
    }

    protected function handleSubscriptionDeleted($stripeSubscription)
    {
        $customer = $this->resolveCustomerFromStripeId($stripeSubscription->customer ?? null);
        if (!$customer) {
            return;
        }

        Subscription::where('customer_id', $customer->id)
            ->where('website_id', config('services.bo.website_id'))
            ->update([
                'is_trial_active'        => false,
                'is_subscription_active' => false,
                'cancelled_at'           => now(),
            ]);

        BoStripeCustomer::where('customer_id', $customer->id)
            ->where('id_stripe_subscription', $stripeSubscription->id)
            ->update([
                'stripe_subscription_status'      => 'canceled',
                'stripe_subscription_canceled_at' => now(),
            ]);

        Mail::to($customer->email)->send(new SubscriptionCanceledMail($customer));

        Log::info('Stripe Webhook: Abo gekündigt', [
            'customer_id'     => $customer->id,
            'subscription_id' => $stripeSubscription->id,
        ]);
    }

    protected function handleInvoicePaymentSucceeded($invoice)
    {
        if (!$invoice->subscription) {
            return;
        }

        $customer = $this->resolveCustomerFromStripeId($invoice->customer ?? null);
        if (!$customer) {
            return;
        }

        $stripeSubscription = \Stripe\Subscription::retrieve($invoice->subscription);
        $this->upsertSubscription($customer, $stripeSubscription);

        // Erste echte Zahlung (nicht Trial) → SubscriptionActiveMail
        $firstRealPayment = $invoice->billing_reason === 'subscription_cycle'
            || ($invoice->billing_reason === 'subscription_create' && $invoice->amount_paid > 0);

        if ($firstRealPayment) {
            Mail::to($customer->email)->send(new SubscriptionActiveMail($customer));
        }

        Log::info('Stripe Webhook: Zahlung erfolgreich', [
            'customer_id' => $customer->id,
            'amount'      => $invoice->amount_paid,
        ]);
    }

    protected function handleInvoicePaymentFailed($invoice)
    {
        if (!$invoice->subscription) {
            return;
        }

        $customer = $this->resolveCustomerFromStripeId($invoice->customer ?? null);
        if (!$customer) {
            return;
        }

        Subscription::where('customer_id', $customer->id)
            ->where('website_id', config('services.bo.website_id'))
            ->update([
                'is_subscription_active' => false,
                'cancel_reason'          => 'payment_failed',
            ]);

        Mail::to($customer->email)->send(new PaymentFailedMail($customer));

        Log::info('Stripe Webhook: Zahlung fehlgeschlagen', [
            'customer_id' => $customer->id,
        ]);
    }

    /**
     * Upsert sofortpdf subscription row from a Stripe \Subscription object.
     * Best-effort: write fails are logged but never bubble up so webhooks
     * keep responding 200 to Stripe.
     */
    private function upsertSubscription(Customer $customer, $stripeSubscription): void
    {
        $websiteId   = (int) config('services.bo.website_id');
        $companyId   = (int) config('company.default_company_id', 1);

        $boStripe = BoStripeCustomer::where('customer_id', $customer->id)
            ->where('website_id', $websiteId)
            ->first();

        $boWebsiteId = $boStripe && $boStripe->bo_stripe_account_id
            ? (int) $boStripe->bo_stripe_account_id
            : 0;

        // Stripe-IDs in bo_stripe_customers refreshen
        if ($boStripe) {
            $boStripe->update([
                'id_stripe_subscription'    => $stripeSubscription->id,
                'stripe_subscription_status'=> $stripeSubscription->status,
                'current_period_start'      => Carbon::createFromTimestamp($stripeSubscription->current_period_start),
                'current_period_end'        => Carbon::createFromTimestamp($stripeSubscription->current_period_end),
            ]);
        }

        try {
            Subscription::updateOrCreate(
                [
                    'customer_id' => $customer->id,
                    'website_id'  => $websiteId,
                ],
                [
                    'bo_website_id'         => $boWebsiteId,
                    'company_id'            => $companyId,
                    'bo_product_id'         => (int) env('SOFORTPDF_BO_PRODUCT_ID', 0),
                    'payment_provider_id'   => (int) env('STRIPE_PAYMENT_PROVIDER_ID', 1),
                    'plan_type'             => 'monthly',
                    'is_trial_active'       => $stripeSubscription->status === 'trialing',
                    'trial_started_at'      => $stripeSubscription->trial_start
                        ? Carbon::createFromTimestamp($stripeSubscription->trial_start)
                        : null,
                    'trial_ends_at'         => $stripeSubscription->trial_end
                        ? Carbon::createFromTimestamp($stripeSubscription->trial_end)
                        : null,
                    'is_subscription_active'=> $stripeSubscription->status === 'active',
                    'subscription_started_at'=> $stripeSubscription->status === 'active' ? now() : null,
                    'subscription_ends_at'  => Carbon::createFromTimestamp($stripeSubscription->current_period_end),
                ]
            );
        } catch (\Throwable $e) {
            Log::warning('Webhook: Subscription upsert failed (DB seed pending)', [
                'error'       => $e->getMessage(),
                'customer_id' => $customer->id,
            ]);
        }
    }
}
