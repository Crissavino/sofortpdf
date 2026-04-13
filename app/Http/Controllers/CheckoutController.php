<?php

namespace App\Http\Controllers;

use App\Mail\WelcomeMail;
use App\Models\Subscription;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
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
        $user = $request->user();

        // Wenn eingeloggt und bereits Abo vorhanden → weiterleiten
        if ($user && $user->hasSofortpdfSubscription()) {
            return redirect($request->get('return_to', '/'));
        }

        $returnUrl = $request->get('return_to', route('home'));

        return view('checkout.payment', [
            'stripeKey' => config('services.stripe.key'),
            'trialPrice' => config('services.stripe.trial_price', 1.50),
            'trialDays' => config('services.stripe.trial_days', 2),
            'subscriptionPrice' => config('services.stripe.subscription_price', 39.99),
            'returnUrl' => $returnUrl,
            'pageTitle' => 'Zahlungsinformationen',
            'slug' => '',
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

        try {
            // Benutzer ermitteln oder erstellen
            $user = User::where('email', $email)->first();

            if ($user) {
                // Benutzer existiert — pruefen ob bereits aktives Abo
                if ($user->hasSofortpdfSubscription()) {
                    return response()->json([
                        'error' => 'Sie haben bereits ein aktives Abonnement. Bitte melden Sie sich an.',
                    ], 422);
                }
                // Existierender Benutzer ohne Abo — verwenden
            } else {
                // Neuen Benutzer erstellen
                $generatedPassword = self::generatePassword();
                $user = User::create([
                    'name' => $name,
                    'email' => $email,
                    'password' => Hash::make($generatedPassword),
                ]);
            }

            // Stripe-Kunde erstellen oder vorhandenen verwenden
            if (!$user->stripe_customer_id) {
                $customer = \Stripe\Customer::create([
                    'email' => $user->email,
                    'name' => $user->name,
                    'metadata' => ['user_id' => $user->id, 'product' => 'sofortpdf'],
                ]);
                $user->update(['stripe_customer_id' => $customer->id]);
            }

            // PaymentMethod an Kunde anhaengen
            \Stripe\PaymentMethod::retrieve($paymentMethodId)->attach([
                'customer' => $user->stripe_customer_id,
            ]);

            // Als Standard-Zahlungsmethode setzen
            \Stripe\Customer::update($user->stripe_customer_id, [
                'invoice_settings' => ['default_payment_method' => $paymentMethodId],
            ]);

            // Abonnement mit Testzeitraum erstellen
            $subscription = \Stripe\Subscription::create([
                'customer' => $user->stripe_customer_id,
                'items' => [['price' => config('services.stripe.subscription_price_id')]],
                'trial_period_days' => config('services.stripe.trial_days', 2),
                'default_payment_method' => $paymentMethodId,
                'metadata' => [
                    'user_id' => $user->id,
                    'product' => 'sofortpdf',
                ],
                'expand' => ['latest_invoice.payment_intent'],
            ]);

            // Lokale Subscription speichern
            Subscription::updateOrCreate(
                [
                    'customer_id' => $user->id,
                    'stripe_subscription_id' => $subscription->id,
                ],
                [
                    'stripe_price_id' => 'sofortpdf_' . config('services.stripe.subscription_price_id'),
                    'status' => $subscription->status,
                    'trial_ends_at' => $subscription->trial_end ? date('Y-m-d H:i:s', $subscription->trial_end) : null,
                    'current_period_start' => date('Y-m-d H:i:s', $subscription->current_period_start),
                    'current_period_end' => date('Y-m-d H:i:s', $subscription->current_period_end),
                ]
            );

            // Benutzer automatisch einloggen
            Auth::login($user);

            // Willkommens-E-Mail senden (nur fuer neue Benutzer mit generiertem Passwort)
            if ($generatedPassword) {
                Mail::to($user->email)->send(new WelcomeMail($user, $generatedPassword));
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

        $user = $request->user();

        if (!$user) {
            return response()->json([
                'error' => 'Sitzung abgelaufen. Bitte versuchen Sie es erneut.',
            ], 401);
        }

        try {
            $subscription = \Stripe\Subscription::retrieve($request->input('subscription_id'));

            // Lokale DB aktualisieren
            Subscription::where('stripe_subscription_id', $subscription->id)
                ->where('customer_id', $user->id)
                ->update(['status' => $subscription->status]);

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
