<?php

namespace App\Http\Controllers;

use App\Models\BoStripeCustomer;
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
     * Read-only demo view of the dashboard, rendered with stub data so we
     * can showcase the UI on the gated /dashboard/demo route without DB
     * dependencies.
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
                $this->trial_ends_at      = now()->addDays(2);
                $this->current_period_end = now()->addDays(2)->addMonth();
            }
        };

        $demoConversions = collect([
            (object) ['created_at' => now()->subMinutes(12), 'tool_slug' => 'sofortpdf_pdf-to-word', 'original_filename' => 'annual-report-2025.pdf', 'status' => 'completed', 'result_filename' => 'demo-result.docx'],
            (object) ['created_at' => now()->subHours(2),    'tool_slug' => 'sofortpdf_merge',       'original_filename' => 'invoices-q3.pdf',         'status' => 'completed', 'result_filename' => 'demo-result.pdf'],
            (object) ['created_at' => now()->subDay(),       'tool_slug' => 'sofortpdf_compress',    'original_filename' => 'presentation-slides.pdf', 'status' => 'completed', 'result_filename' => 'demo-result.pdf'],
            (object) ['created_at' => now()->subDays(2),     'tool_slug' => 'sofortpdf_jpg-to-pdf',  'original_filename' => 'scanned-pages.jpg',       'status' => 'failed',    'result_filename' => null],
            (object) ['created_at' => now()->subDays(4),     'tool_slug' => 'sofortpdf_rotate',      'original_filename' => 'receipt.pdf',             'status' => 'completed', 'result_filename' => 'demo-result.pdf'],
        ]);

        $quickTools = collect(ToolConfig::allEnabled(app()->getLocale()))
            ->take(6)->values()->all();

        return view('dashboard.index', [
            'user'              => $demoUser,
            'subscription'      => $demoSubscription,
            'recentConversions' => $demoConversions,
            'stats'             => ['this_month' => 12, 'total' => 87, 'top_tool' => 'pdf-to-word'],
            'quickTools'        => $quickTools,
        ]);
    }

    public function index()
    {
        $customer = Auth::user();

        $subscription = $this->currentSubscription($customer);

        $quickTools = collect(ToolConfig::allEnabled(app()->getLocale()))
            ->take(6)->values()->all();

        // Conversion telemetry isn't persisted to the shared DB yet, so
        // we render the empty state for stats + recent activity. UI hides
        // sections cleanly when the values are zero / empty.
        return view('dashboard.index', [
            'user'              => $customer,
            'subscription'      => $subscription,
            'recentConversions' => collect(),
            'stats'             => ['this_month' => 0, 'total' => 0, 'top_tool' => null],
            'quickTools'        => $quickTools,
        ]);
    }

    public function downloads()
    {
        $customer = Auth::user();

        return view('dashboard.downloads', [
            'user'        => $customer,
            'conversions' => collect()->paginate(50),
        ]);
    }

    public function billing()
    {
        $customer     = Auth::user();
        $subscription = $this->currentSubscription($customer);

        return view('dashboard.billing', [
            'user'         => $customer,
            'subscription' => $subscription,
        ]);
    }

    public function billingPortal()
    {
        $customer = Auth::user();

        $boStripe = BoStripeCustomer::where('customer_id', $customer->id)
            ->where('website_id', config('services.bo.website_id'))
            ->first();

        if (!$boStripe || !$boStripe->id_stripe_customer) {
            return redirect()->route('dashboard.billing')->with('error', __('dashboard.flash_no_active_subscription'));
        }

        Stripe::setApiKey(config('services.stripe.secret'));

        $session = BillingPortalSession::create([
            'customer'   => $boStripe->id_stripe_customer,
            'return_url' => route('dashboard.billing'),
            'locale'     => app()->getLocale(),
        ]);

        return redirect($session->url);
    }

    public function cancelSubscription()
    {
        $customer = Auth::user();

        $boStripe = BoStripeCustomer::where('customer_id', $customer->id)
            ->where('website_id', config('services.bo.website_id'))
            ->first();

        if (!$boStripe || !$boStripe->id_stripe_subscription) {
            return redirect()->route('dashboard.billing')->with('error', __('dashboard.flash_no_active_subscription'));
        }

        Stripe::setApiKey(config('services.stripe.secret'));
        StripeSubscription::update($boStripe->id_stripe_subscription, [
            'cancel_at_period_end' => true,
        ]);

        // Local row is updated by the customer.subscription.updated webhook;
        // optimistically reflect the cancel intent in the UI now.
        $boStripe->update(['stripe_subscription_status' => 'canceled']);

        return redirect()->route('dashboard.billing')->with('success', __('dashboard.flash_subscription_canceled'));
    }

    public function profile()
    {
        return view('dashboard.profile', [
            'user' => Auth::user(),
        ]);
    }

    public function updateProfile(Request $request)
    {
        $customer = Auth::user();

        $rules = [
            'name'  => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:customers,email,' . $customer->id,
        ];

        $messages = [
            'name.required'                => __('dashboard.profile_val_name_required'),
            'email.required'               => __('dashboard.profile_val_email_required'),
            'email.email'                  => __('dashboard.profile_val_email_invalid'),
            'email.unique'                 => __('dashboard.profile_val_email_unique'),
            'current_password.required_with' => __('dashboard.profile_val_current_pw_required'),
            'password.min'                 => __('dashboard.profile_val_password_min'),
            'password.confirmed'           => __('dashboard.profile_val_password_confirmed'),
        ];

        if ($request->filled('password')) {
            $rules['current_password'] = 'required_with:password';
            $rules['password']         = 'required|string|min:8|confirmed';
        }

        $request->validate($rules, $messages);

        // Split the single "name" field into first/last for the shared schema.
        $parts                = explode(' ', trim($request->input('name')), 2);
        $customer->first_name = $parts[0];
        $customer->last_name  = $parts[1] ?? '';
        $customer->email      = $request->input('email');

        if ($request->filled('password')) {
            if (!Hash::check($request->input('current_password'), $customer->password)) {
                return back()->withErrors(['current_password' => __('dashboard.profile_val_current_pw_wrong')]);
            }
            $customer->password = Hash::make($request->input('password'));
        }

        $customer->save();

        return redirect()->back()->with('success', __('dashboard.flash_profile_saved'));
    }

    /**
     * Latest sofortpdf-scoped subscription for this customer (or null).
     */
    private function currentSubscription($customer)
    {
        if (!$customer || !($websiteId = config('services.bo.website_id'))) {
            return null;
        }

        return $customer->subscriptions()
            ->where('website_id', $websiteId)
            ->latest('id')
            ->first();
    }
}
