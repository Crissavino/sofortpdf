<?php

namespace App\Http\Controllers;

use App\Mail\PaymentFailedMail;
use App\Mail\SubscriptionActiveMail;
use App\Mail\SubscriptionCanceledMail;
use App\Mail\TrialStartedMail;
use App\Mail\WelcomeMail;
use App\Models\Subscription;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Stripe\Stripe;
use Stripe\Webhook;

/**
 * Verarbeitet Stripe-Webhook-Events fuer das sofortpdf-Produkt.
 *
 * WICHTIG: Diese Route muss von der CSRF-Verifizierung ausgenommen sein.
 */

class WebhookController extends Controller
{
    /**
     * POST /stripe/webhook
     *
     * Verarbeitet eingehende Stripe-Webhook-Events.
     */
    public function handle(Request $request)
    {
        $payload = $request->getContent();
        $sigHeader = $request->header('Stripe-Signature');
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
                    break;
            }
        } catch (\Exception $e) {
            Log::error('Stripe Webhook: Fehler bei Verarbeitung', [
                'type' => $event->type,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
        }

        return response('OK', 200);
    }

    /**
     * checkout.session.completed
     *
     * Erstellt oder aktualisiert das Abo und sendet Willkommens-E-Mails.
     */
    protected function handleCheckoutSessionCompleted($session)
    {
        if (!$this->isSofortpdfProduct($session->metadata)) {
            return;
        }

        $userId = $session->metadata->user_id ?? null;
        $user = $userId ? User::find($userId) : null;

        if (!$user) {
            Log::warning('Stripe Webhook: Benutzer nicht gefunden', ['user_id' => $userId]);
            return;
        }

        // Stripe-Abo-Details abrufen
        $stripeSubscription = \Stripe\Subscription::retrieve($session->subscription);

        $subscription = Subscription::updateOrCreate(
            ['stripe_subscription_id' => $stripeSubscription->id],
            [
                'customer_id' => $user->id,
                'stripe_price_id' => $stripeSubscription->items->data[0]->price->id ?? null,
                'status' => $stripeSubscription->status,
                'trial_ends_at' => $stripeSubscription->trial_end
                    ? Carbon::createFromTimestamp($stripeSubscription->trial_end)
                    : null,
                'current_period_start' => Carbon::createFromTimestamp($stripeSubscription->current_period_start),
                'current_period_end' => Carbon::createFromTimestamp($stripeSubscription->current_period_end),
            ]
        );

        // Willkommens-E-Mails senden. CheckoutController setzt beim
        // Inline-Flow (generiertes Passwort) bereits eine WelcomeMail ab und
        // markiert das im Cache, damit wir hier keinen Duplikat senden.
        $welcomeLockKey = 'welcome_sent:' . $user->id;
        if (!Cache::has($welcomeLockKey)) {
            Mail::to($user->email)->send(new WelcomeMail($user));
            Cache::put($welcomeLockKey, true, now()->addHours(24));
        }
        Mail::to($user->email)->send(new TrialStartedMail($user));

        Log::info('Stripe Webhook: Checkout abgeschlossen', [
            'user_id' => $user->id,
            'subscription_id' => $subscription->id,
        ]);
    }

    /**
     * customer.subscription.updated
     *
     * Aktualisiert den Abo-Status in der Datenbank.
     */
    protected function handleSubscriptionUpdated($stripeSubscription)
    {
        $subscription = Subscription::where('stripe_subscription_id', $stripeSubscription->id)->first();

        if (!$subscription || !$subscription->isSofortpdf()) {
            return;
        }

        $subscription->update([
            'status' => $stripeSubscription->status,
            'trial_ends_at' => $stripeSubscription->trial_end
                ? Carbon::createFromTimestamp($stripeSubscription->trial_end)
                : null,
            'current_period_start' => Carbon::createFromTimestamp($stripeSubscription->current_period_start),
            'current_period_end' => Carbon::createFromTimestamp($stripeSubscription->current_period_end),
        ]);

        Log::info('Stripe Webhook: Abo aktualisiert', [
            'subscription_id' => $subscription->id,
            'status' => $stripeSubscription->status,
        ]);
    }

    /**
     * customer.subscription.deleted
     *
     * Markiert das Abo als gekündigt und sendet eine Benachrichtigung.
     */
    protected function handleSubscriptionDeleted($stripeSubscription)
    {
        $subscription = Subscription::where('stripe_subscription_id', $stripeSubscription->id)->first();

        if (!$subscription || !$subscription->isSofortpdf()) {
            return;
        }

        $subscription->update([
            'status' => 'canceled',
        ]);

        $user = $subscription->user;

        if ($user) {
            Mail::to($user->email)->send(new SubscriptionCanceledMail($user));
        }

        Log::info('Stripe Webhook: Abo gekündigt', [
            'subscription_id' => $subscription->id,
        ]);
    }

    /**
     * invoice.payment_succeeded
     *
     * Aktualisiert die Abrechnungsperiode und sendet eine Bestätigung bei der ersten echten Zahlung.
     */
    protected function handleInvoicePaymentSucceeded($invoice)
    {
        $subscriptionId = $invoice->subscription;

        if (!$subscriptionId) {
            return;
        }

        $subscription = Subscription::where('stripe_subscription_id', $subscriptionId)->first();

        if (!$subscription || !$subscription->isSofortpdf()) {
            return;
        }

        // Periodenende aktualisieren
        if (isset($invoice->lines->data[0])) {
            $lineItem = $invoice->lines->data[0];
            $subscription->update([
                'current_period_end' => Carbon::createFromTimestamp($lineItem->period->end),
                'status' => 'active',
            ]);
        }

        // Erste echte Zahlung (nicht Trial): SubscriptionActiveMail senden
        // billing_reason = 'subscription_cycle' bedeutet wiederkehrende Zahlung nach Trial
        if ($invoice->billing_reason === 'subscription_cycle' || ($invoice->billing_reason === 'subscription_create' && $invoice->amount_paid > 0)) {
            $user = $subscription->user;

            if ($user) {
                Mail::to($user->email)->send(new SubscriptionActiveMail($user));
            }
        }

        Log::info('Stripe Webhook: Zahlung erfolgreich', [
            'subscription_id' => $subscription->id,
            'amount' => $invoice->amount_paid,
        ]);
    }

    /**
     * invoice.payment_failed
     *
     * Markiert das Abo als überfällig und benachrichtigt den Benutzer.
     */
    protected function handleInvoicePaymentFailed($invoice)
    {
        $subscriptionId = $invoice->subscription;

        if (!$subscriptionId) {
            return;
        }

        $subscription = Subscription::where('stripe_subscription_id', $subscriptionId)->first();

        if (!$subscription || !$subscription->isSofortpdf()) {
            return;
        }

        $subscription->update([
            'status' => 'past_due',
        ]);

        $user = $subscription->user;

        if ($user) {
            Mail::to($user->email)->send(new PaymentFailedMail($user));
        }

        Log::info('Stripe Webhook: Zahlung fehlgeschlagen', [
            'subscription_id' => $subscription->id,
        ]);
    }

    /**
     * Prüft ob das Event zum Produkt 'sofortpdf' gehört.
     */
    protected function isSofortpdfProduct($metadata): bool
    {
        return isset($metadata->product) && $metadata->product === 'sofortpdf';
    }
}
