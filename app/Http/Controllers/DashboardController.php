<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Stripe\BillingPortal\Session as BillingPortalSession;
use Stripe\Stripe;
use Stripe\Subscription as StripeSubscription;

class DashboardController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Dashboard-Übersicht
     */
    public function index()
    {
        $user = Auth::user();

        $subscription = $user->subscriptions()
            ->where('stripe_price_id', 'like', '%sofortpdf_%')
            ->latest()
            ->first();

        $recentConversions = $user->conversionLogs()
            ->sofortpdf()
            ->latest()
            ->take(5)
            ->get();

        return view('dashboard.index', [
            'user' => $user,
            'subscription' => $subscription,
            'recentConversions' => $recentConversions,
        ]);
    }

    /**
     * Download-Verlauf
     */
    public function downloads()
    {
        $user = Auth::user();

        $conversions = $user->conversionLogs()
            ->sofortpdf()
            ->latest()
            ->paginate(50);

        return view('dashboard.downloads', [
            'user' => $user,
            'conversions' => $conversions,
        ]);
    }

    /**
     * Abonnement-Verwaltung
     */
    public function billing()
    {
        $user = Auth::user();

        $subscription = $user->subscriptions()
            ->where('stripe_price_id', 'like', '%sofortpdf_%')
            ->latest()
            ->first();

        return view('dashboard.billing', [
            'user' => $user,
            'subscription' => $subscription,
        ]);
    }

    /**
     * Weiterleitung zum Stripe Billing Portal
     */
    public function billingPortal()
    {
        $user = Auth::user();

        Stripe::setApiKey(config('services.stripe.secret'));

        $session = BillingPortalSession::create([
            'customer' => $user->stripe_customer_id,
            'return_url' => route('dashboard.billing'),
            'locale' => 'de',
        ]);

        return redirect($session->url);
    }

    /**
     * Abonnement kündigen
     */
    public function cancelSubscription()
    {
        $user = Auth::user();

        $subscription = $user->subscriptions()
            ->where('stripe_price_id', 'like', '%sofortpdf_%')
            ->whereIn('status', ['active', 'trialing'])
            ->latest()
            ->first();

        if (!$subscription) {
            return redirect()->route('dashboard.billing')->with('error', 'Kein aktives Abonnement gefunden.');
        }

        Stripe::setApiKey(config('services.stripe.secret'));

        StripeSubscription::update($subscription->stripe_subscription_id, [
            'cancel_at_period_end' => true,
        ]);

        $subscription->update(['status' => 'canceled']);

        return redirect()->route('dashboard.billing')->with('success', 'Ihr Abonnement wurde gekündigt. Sie behalten Ihren Zugang bis zum Ende des aktuellen Abrechnungszeitraums.');
    }

    /**
     * Benutzerprofil
     */
    public function profile()
    {
        $user = Auth::user();

        return view('dashboard.profile', [
            'user' => $user,
        ]);
    }

    /**
     * Profil aktualisieren
     */
    public function updateProfile(Request $request)
    {
        $user = Auth::user();

        $rules = [
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email,' . $user->id,
        ];

        $messages = [
            'name.required' => 'Bitte geben Sie Ihren Namen ein.',
            'email.required' => 'Bitte geben Sie Ihre E-Mail-Adresse ein.',
            'email.email' => 'Bitte geben Sie eine gueltige E-Mail-Adresse ein.',
            'email.unique' => 'Diese E-Mail-Adresse wird bereits verwendet.',
            'current_password.required_with' => 'Bitte geben Sie Ihr aktuelles Passwort ein.',
            'password.min' => 'Das neue Passwort muss mindestens 8 Zeichen lang sein.',
            'password.confirmed' => 'Die Passwortbestaetigung stimmt nicht ueberein.',
        ];

        if ($request->filled('password')) {
            $rules['current_password'] = 'required_with:password';
            $rules['password'] = 'required|string|min:8|confirmed';
        }

        $request->validate($rules, $messages);

        $user->name = $request->input('name');
        $user->email = $request->input('email');

        if ($request->filled('password')) {
            if (!Hash::check($request->input('current_password'), $user->password)) {
                return back()->withErrors(['current_password' => 'Das aktuelle Passwort ist nicht korrekt.']);
            }
            $user->password = Hash::make($request->input('password'));
        }

        $user->save();

        return redirect()->back()->with('success', 'Ihre Änderungen wurden gespeichert.');
    }
}
