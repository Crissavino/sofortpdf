<?php

namespace App\Http\Controllers;

use App\Services\ToolConfig;
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
        // Skip auth only on the demo route; everything else requires login.
        $this->middleware('auth')->except('demo');
    }

    /**
     * Read-only demo view of the dashboard index, rendered with stub data.
     * Gated in routes/web.php behind the paywall middleware so only
     * allowlisted IPs (PAYMENT_BYPASS_IPS) can reach it. Lives here so
     * we can showcase the /dashboard redesign without DB access.
     */
    public function demo()
    {
        $demoUser = new class {
            public $name = 'Cristian Savino';
            public $email = 'demo@sofortpdf.com';
        };

        $demoSubscription = new class {
            public $status = 'trialing';
            public $trial_ends_at;
            public $current_period_end;
            public function __construct()
            {
                $this->trial_ends_at     = now()->addDays(2);
                $this->current_period_end = now()->addDays(2)->addMonth();
            }
        };

        $demoConversions = collect([
            (object) [
                'created_at' => now()->subMinutes(12),
                'tool_slug' => 'sofortpdf_pdf-to-word',
                'original_filename' => 'annual-report-2025.pdf',
                'status' => 'completed',
                'result_filename' => 'demo-result.docx',
            ],
            (object) [
                'created_at' => now()->subHours(2),
                'tool_slug' => 'sofortpdf_merge',
                'original_filename' => 'invoices-q3.pdf',
                'status' => 'completed',
                'result_filename' => 'demo-result.pdf',
            ],
            (object) [
                'created_at' => now()->subDay(),
                'tool_slug' => 'sofortpdf_compress',
                'original_filename' => 'presentation-slides.pdf',
                'status' => 'completed',
                'result_filename' => 'demo-result.pdf',
            ],
            (object) [
                'created_at' => now()->subDays(2),
                'tool_slug' => 'sofortpdf_jpg-to-pdf',
                'original_filename' => 'scanned-pages.jpg',
                'status' => 'failed',
                'result_filename' => null,
            ],
            (object) [
                'created_at' => now()->subDays(4),
                'tool_slug' => 'sofortpdf_rotate',
                'original_filename' => 'receipt.pdf',
                'status' => 'completed',
                'result_filename' => 'demo-result.pdf',
            ],
        ]);

        $quickTools = collect(ToolConfig::allEnabled(app()->getLocale()))
            ->take(6)
            ->values()
            ->all();

        return view('dashboard.index', [
            'user' => $demoUser,
            'subscription' => $demoSubscription,
            'recentConversions' => $demoConversions,
            'stats' => [
                'this_month' => 12,
                'total' => 87,
                'top_tool' => 'pdf-to-word',
            ],
            'quickTools' => $quickTools,
        ]);
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

        // Stats for the welcome row (best-effort — if the shared DB is
        // temporarily unreachable we degrade to zero rather than 500).
        $stats = [
            'this_month' => 0,
            'total' => 0,
            'top_tool' => null,
        ];
        try {
            $stats['this_month'] = $user->conversionLogs()->sofortpdf()
                ->where('created_at', '>=', now()->startOfMonth())
                ->count();
            $stats['total'] = $user->conversionLogs()->sofortpdf()->count();
            $topRow = $user->conversionLogs()->sofortpdf()
                ->selectRaw('tool_slug, COUNT(*) as n')
                ->groupBy('tool_slug')
                ->orderByDesc('n')
                ->first();
            if ($topRow) {
                $stats['top_tool'] = str_replace('sofortpdf_', '', $topRow->tool_slug);
            }
        } catch (\Throwable $e) {
            report($e);
        }

        // Quick-access grid: first 6 enabled tools in the current locale.
        $quickTools = collect(ToolConfig::allEnabled(app()->getLocale()))
            ->take(6)
            ->values()
            ->all();

        return view('dashboard.index', [
            'user' => $user,
            'subscription' => $subscription,
            'recentConversions' => $recentConversions,
            'stats' => $stats,
            'quickTools' => $quickTools,
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
            'locale' => app()->getLocale(),
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
            return redirect()->route('dashboard.billing')->with('error', __('dashboard.flash_no_active_subscription'));
        }

        Stripe::setApiKey(config('services.stripe.secret'));

        StripeSubscription::update($subscription->stripe_subscription_id, [
            'cancel_at_period_end' => true,
        ]);

        $subscription->update(['status' => 'canceled']);

        return redirect()->route('dashboard.billing')->with('success', __('dashboard.flash_subscription_canceled'));
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
            'name.required' => __('dashboard.profile_val_name_required'),
            'email.required' => __('dashboard.profile_val_email_required'),
            'email.email' => __('dashboard.profile_val_email_invalid'),
            'email.unique' => __('dashboard.profile_val_email_unique'),
            'current_password.required_with' => __('dashboard.profile_val_current_pw_required'),
            'password.min' => __('dashboard.profile_val_password_min'),
            'password.confirmed' => __('dashboard.profile_val_password_confirmed'),
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
                return back()->withErrors(['current_password' => __('dashboard.profile_val_current_pw_wrong')]);
            }
            $user->password = Hash::make($request->input('password'));
        }

        $user->save();

        return redirect()->back()->with('success', __('dashboard.flash_profile_saved'));
    }
}
