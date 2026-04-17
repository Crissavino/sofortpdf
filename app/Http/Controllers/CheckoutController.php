<?php

namespace App\Http\Controllers;

use App\Mail\WelcomeMail;
use App\Models\BoStripeCustomer;
use App\Models\Customer;
use App\Models\Subscription;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Stripe\Stripe;

class CheckoutController extends Controller
{
    public function __construct()
    {
        Stripe::setApiKey(config('services.stripe.secret'));
    }

    /**
     * Zufaelliges Passwort generieren (12 Zeichen, Buchstaben + Zahlen).
     */
    public static function generatePassword(int $length = 12): string
    {
        $chars = 'abcdefghjkmnpqrstuvwxyzABCDEFGHJKMNPQRSTUVWXYZ23456789';
        $password = '';
        for ($i = 0; $i < $length; $i++) {
            $password .= $chars[random_int(0, strlen($chars) - 1)];
        }
        return $password;
    }

    /**
     * GET /checkout/start?return_to=...
     * Zeigt das Zahlungsformular mit Stripe Elements an.
     * Kein Auth erforderlich — anonyme Benutzer koennen bezahlen.
     */
    public function start(Request $request)
    {
        $customer = $request->user();

        // Wenn eingeloggt und bereits Abo vorhanden → weiterleiten
        if ($customer && $customer->hasSofortpdfSubscription()) {
            return redirect($request->get('return_to', '/'));
        }

        $returnUrl = $request->get('return_to', route('home'));

        // Pricing comes from the VAD-resolved BoProduct — shared via $pricing
        // by the ResolveVad middleware. Pass them to the view explicitly too
        // so the checkout form can embed the right Stripe price IDs.
        $stripeService = app(\App\Services\Payment\StripeService::class);
        $paymentData   = $stripeService->resolvePaymentData();

        return view('checkout.payment', [
            'stripeKey'         => $paymentData['stripe_public_key'] ?: config('services.stripe.key'),
            'trialPrice'        => $paymentData['trial_price'] ?: ($pricing['trial'] ?? 0.69),
            'trialDays'         => (int) config('services.stripe.trial_days', 2),
            'subscriptionPrice' => $paymentData['subscription_price'] ?: ($pricing['subscription'] ?? 39.90),
            'returnUrl'         => $returnUrl,
            'pageTitle'         => 'Zahlungsinformationen',
            'slug'              => '',
        ]);
    }

    /**
     * POST /checkout/create-subscription
     * Erstellt ein Stripe-Abonnement mit PaymentMethod aus dem Frontend.
     * Erstellt ggf. ein neues Benutzerkonto (ohne separate Registrierung).
     */
    public function createSubscription(Request $request)
    {
        $request->validate([
            'payment_method_id' => 'required|string',
            'email' => 'required|email|max:255',
            'name' => 'required|string|max:255',
        ], [
            'email.required' => 'Bitte geben Sie Ihre E-Mail-Adresse ein.',
            'email.email' => 'Bitte geben Sie eine gueltige E-Mail-Adresse ein.',
            'name.required' => 'Bitte geben Sie Ihren Namen ein.',
        ]);

        $email = strtolower(trim($request->input('email')));
        $name = trim($request->input('name'));
        $paymentMethodId = $request->input('payment_method_id');
        $generatedPassword = null;

        $websiteId    = (int) config('services.bo.website_id');
        $companyId    = (int) (session('vad.company_id') ?? config('company.default_company_id'));
        $boWebsiteId  = (int) (session('vad.used_vad.bo_website_id') ?? 0);

        try {
            // Customer ermitteln oder erstellen — gescoped auf diese Marke.
            $customer = Customer::where('email', $email)
                ->when($websiteId, fn ($q) => $q->where('website_id', $websiteId))
                ->first();

            if ($customer) {
                if ($customer->hasSofortpdfSubscription()) {
                    return response()->json([
                        'error' => 'Sie haben bereits ein aktives Abonnement. Bitte melden Sie sich an.',
                    ], 422);
                }
            } else {
                $generatedPassword = self::generatePassword();
                $parts             = explode(' ', $name, 2);
                $customer          = Customer::create([
                    'first_name'         => $parts[0],
                    'last_name'          => $parts[1] ?? '',
                    'email'              => $email,
                    'password'           => Hash::make($generatedPassword),
                    'language'           => app()->getLocale(),
                    'country'            => session('country_code'),
                    'ip'                 => $request->ip(),
                    'website_id'         => $websiteId,
                    'last_time_connected'=> now(),
                ]);
            }

            // Stripe-Kunde erstellen oder vorhandenen wiederverwenden — wir
            // tracken die Stripe-IDs in der shared bo_stripe_customers-Tabelle
            // (scoped pro Marke + Stripe-Account).
            $boStripe = BoStripeCustomer::where('customer_id', $customer->id)
                ->where('website_id', $websiteId)
                ->first();

            if (!$boStripe || !$boStripe->id_stripe_customer) {
                $stripeCustomer = \Stripe\Customer::create([
                    'email'    => $customer->email,
                    'name'     => $customer->name,
                    'metadata' => [
                        'customer_id' => $customer->id,
                        'website_id'  => $websiteId,
                        'product'     => 'sofortpdf',
                    ],
                ]);

                $boStripe = BoStripeCustomer::updateOrCreate(
                    [
                        'customer_id' => $customer->id,
                        'website_id'  => $websiteId,
                    ],
                    [
                        'bo_stripe_account_id' => 0, // not yet wired to specific Stripe account
                        'id_stripe_customer'   => $stripeCustomer->id,
                        'email'                => $customer->email,
                    ]
                );
            }

            // PaymentMethod attachen + als default setzen
            \Stripe\PaymentMethod::retrieve($paymentMethodId)->attach([
                'customer' => $boStripe->id_stripe_customer,
            ]);
            \Stripe\Customer::update($boStripe->id_stripe_customer, [
                'invoice_settings' => ['default_payment_method' => $paymentMethodId],
            ]);

            // Abonnement mit Testzeitraum erstellen
            $stripeSubscription = \Stripe\Subscription::create([
                'customer'               => $boStripe->id_stripe_customer,
                'items'                  => [['price' => config('services.stripe.subscription_price_id')]],
                'trial_period_days'      => config('services.stripe.trial_days', 2),
                'default_payment_method' => $paymentMethodId,
                'metadata' => [
                    'customer_id' => $customer->id,
                    'website_id'  => $websiteId,
                    'product'     => 'sofortpdf',
                ],
                'expand' => ['latest_invoice.payment_intent'],
            ]);

            // Stripe-IDs aktualisieren
            $boStripe->update([
                'id_stripe_subscription'    => $stripeSubscription->id,
                'stripe_subscription_status'=> $stripeSubscription->status,
                'stripe_subscription_start' => $stripeSubscription->start_date
                    ? date('Y-m-d H:i:s', $stripeSubscription->start_date)
                    : null,
                'current_period_start'      => date('Y-m-d H:i:s', $stripeSubscription->current_period_start),
                'current_period_end'        => date('Y-m-d H:i:s', $stripeSubscription->current_period_end),
            ]);

            // Subscription-Eintrag im shared `subscriptions`-Schema. Best
            // effort — wenn bo_product_id / payment_provider_id noch nicht
            // konfiguriert sind, wird der Insert geloggt aber Checkout läuft.
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
                        'trial_started_at'      => now(),
                        'trial_ends_at'         => $stripeSubscription->trial_end
                            ? date('Y-m-d H:i:s', $stripeSubscription->trial_end)
                            : null,
                        'is_subscription_active'=> $stripeSubscription->status === 'active',
                        'subscription_started_at'=> $stripeSubscription->status === 'active' ? now() : null,
                        'subscription_ends_at'  => date('Y-m-d H:i:s', $stripeSubscription->current_period_end),
                    ]
                );
            } catch (\Throwable $e) {
                Log::warning('Subscription row insert failed (DB seed pending)', [
                    'error'       => $e->getMessage(),
                    'customer_id' => $customer->id,
                ]);
            }

            // Customer automatisch einloggen
            Auth::login($customer);

            // Willkommens-E-Mail senden (nur fuer neue Customers mit generiertem Passwort).
            // Cache-Lock verhindert Duplikate gegenüber dem Webhook-Pfad.
            if ($generatedPassword) {
                Mail::to($customer->email)->send(new WelcomeMail($customer, $generatedPassword));
                Cache::put('welcome_sent:' . $customer->id, true, now()->addHours(24));
            }

            // Pruefen ob 3DS erforderlich
            $latestInvoice = $subscription->latest_invoice;
            if ($latestInvoice && $latestInvoice->payment_intent) {
                $pi = $latestInvoice->payment_intent;

                if ($pi->status === 'requires_action') {
                    return response()->json([
                        'requires_action' => true,
                        'client_secret' => $pi->client_secret,
                        'subscription_id' => $subscription->id,
                    ]);
                }

                if ($pi->status === 'requires_payment_method') {
                    return response()->json([
                        'error' => 'Ihre Karte wurde abgelehnt. Bitte versuchen Sie eine andere Zahlungsmethode.',
                    ], 402);
                }
            }

            return response()->json([
                'success' => true,
                'subscription_id' => $subscription->id,
                'redirect_url' => route('checkout.success'),
            ]);

        } catch (\Stripe\Exception\CardException $e) {
            return response()->json([
                'error' => 'Ihre Karte wurde abgelehnt: ' . $e->getMessage(),
            ], 402);
        } catch (\Exception $e) {
            Log::error('Stripe subscription error: ' . $e->getMessage());
            return response()->json([
                'error' => 'Ein Fehler ist aufgetreten. Bitte versuchen Sie es erneut.',
            ], 500);
        }
    }

    /**
     * POST /checkout/confirm-payment
     * Bestaetigt die Zahlung nach 3D Secure.
     */
    public function confirmPayment(Request $request)
    {
        $request->validate([
            'subscription_id' => 'required|string',
        ]);

        $customer = $request->user();

        if (!$customer) {
            return response()->json([
                'error' => 'Sitzung abgelaufen. Bitte versuchen Sie es erneut.',
            ], 401);
        }

        try {
            $stripeSubscription = \Stripe\Subscription::retrieve($request->input('subscription_id'));

            // bo_stripe_customers: Stripe-Status aktualisieren
            BoStripeCustomer::where('customer_id', $customer->id)
                ->where('id_stripe_subscription', $stripeSubscription->id)
                ->update(['stripe_subscription_status' => $stripeSubscription->status]);

            // subscriptions: is_*_active je nach neuem Status
            Subscription::where('customer_id', $customer->id)
                ->where('website_id', config('services.bo.website_id'))
                ->update([
                    'is_trial_active'        => $stripeSubscription->status === 'trialing',
                    'is_subscription_active' => $stripeSubscription->status === 'active',
                ]);

            return response()->json([
                'success' => true,
                'redirect_url' => route('checkout.success'),
            ]);

        } catch (\Exception $e) {
            Log::error('Stripe confirm error: ' . $e->getMessage());
            return response()->json([
                'error' => 'Ein Fehler ist aufgetreten. Bitte versuchen Sie es erneut.',
            ], 500);
        }
    }

    /**
     * GET /checkout/success
     */
    public function success(Request $request)
    {
        return view('checkout.success', [
            'sessionId' => $request->get('session_id', ''),
            'pageTitle' => 'Zahlung erfolgreich',
            'slug' => '',
        ]);
    }
}
