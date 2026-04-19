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
    /**
     * Resolve locale from session/referer for API routes (they don't pass
     * through the locale middleware).
     */
    private function setLocale(Request $request): void
    {
        $locale = session('locale', 'de');
        if (!$locale || !in_array($locale, ['de', 'en'])) {
            $referer = $request->headers->get('referer', '');
            $locale = str_contains($referer, '/en/') ? 'en' : 'de';
        }
        app()->setLocale($locale);
    }

    public function createCustomer(Request $request): JsonResponse
    {
        $this->setLocale($request);

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
                'came_from_ads'       => session('cameFromAds') ? 1 : 0,
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

        // Save Google Ads details (same as conversie-pdf / contract-kit)
        $this->saveGoogleAdsDetails(session('utm_params', []), $customer->id);

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
        $this->setLocale($request);

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
        $this->setLocale($request);

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

        // Send emails after payment (same as conversie-pdf: registration + order)
        $customer = \App\Models\Customer::find($customerId);
        if ($customer) {
            $emailService = app(\App\Services\EmailService::class);
            $locale = app()->getLocale();
            $trialPrice = session('vad.pricing.trial', 0.69);
            $symbol = session('vad.pricing.symbol', '€');
            $amount = number_format($trialPrice, 2, ',', '') . ' ' . $symbol;
            $orderNumber = $payment ? $payment->order_number : '';

            $emailService->sendWelcome($customer, '', $locale);
            $emailService->sendOrderConfirmation($customer, $amount, $orderNumber, $locale);
        }

        return response()->json([
            'success' => true,
        ]);
    }

    /* ── Helpers ── */

    /**
     * Save Google Ads attribution data (same as conversie-pdf / contract-kit).
     * Stores gclid + UTM params linked to the customer_id so we can trace
     * which campaign/keyword brought each paying customer.
     */
    private function saveGoogleAdsDetails(array $utmParams, int $customerId): void
    {
        if (empty($utmParams)) {
            return;
        }

        if (\App\Models\GoogleAdsDetail::where('customer_id', $customerId)->exists()) {
            return;
        }

        try {
            \App\Models\GoogleAdsDetail::create([
                'customer_id'  => $customerId,
                'gclid'        => $utmParams['gclid'] ?? session('gclid', ''),
                'utm_source'   => $utmParams['utm_source'] ?? '',
                'utm_medium'   => $utmParams['utm_medium'] ?? '',
                'utm_campaign' => $utmParams['utm_campaign'] ?? '',
                'utm_term'     => $utmParams['utm_term'] ?? '',
            ]);
        } catch (\Throwable $e) {
            Log::warning('saveGoogleAdsDetails failed', ['error' => $e->getMessage()]);
        }
    }

    private function getVadSessionData(): array
    {
        $vad = session('vad.used_vad', []);
        return [
            'bo_website_id' => (int) ($vad['bo_website_id'] ?? 0),
            'bo_vad_id'     => (int) ($vad['bo_vad_id'] ?? session('bo_vad_id', 0)),
            'bo_product_id' => null, // resolved by BO from the stripe product
        ];
    }

    /**
     * Generate a unique order number — same format as conversie-pdf:
     * unix_timestamp-ABC (3 random uppercase letters).
     */
    private function generateOrderNumber(): string
    {
        do {
            $chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
            $rand  = '';
            for ($i = 0; $i < 3; $i++) {
                $rand .= $chars[random_int(0, 25)];
            }
            $orderNumber = time() . '-' . $rand;
        } while (Payment::where('order_number', $orderNumber)->exists());

        return $orderNumber;
    }

    /**
     * Save payment when trial charge succeeds — mirrors conversie-pdf's
     * savePaymentWhenStartTheProcess(). All fields populated from the
     * VAD route + StripeService so the BO has complete data.
     */
    private function savePaymentSuccess(int $customerId): int
    {
        $vad = $this->getVadSessionData();

        $vadProduct  = null;
        $boProduct   = null;
        $currencyId  = null;
        $boProductId = null;

        try {
            $vadProduct  = $this->stripeService->getVadProduct();
            $boProduct   = $this->stripeService->getBoProduct();
            $currencyId  = $vadProduct ? $vadProduct->currency_id : null;
            $boProductId = $vadProduct ? $vadProduct->bo_product_id : null;
        } catch (\Throwable $e) {}

        // Check if customer already has a payment (reuse like conversie-pdf)
        $payment = Payment::where('customer_id', $customerId)->first();
        $isNew   = !$payment;

        try {
            // product_id FK → `products` table (NOT bo_products).
            // Resolve from bo_stripe_products.product_id which maps correctly.
            $productId = null;
            try {
                $trialProduct = $this->stripeService->getTrialProduct();
                $productId = $trialProduct ? $trialProduct->product_id : null;
            } catch (\Throwable $e) {}

            $data = [
                'customer_id'         => $customerId,
                'payment_status_id'   => 4, // in progress (en_cours)
                'product_id'          => $productId,
                'currency_id'         => $currencyId ?? 2,
                'bo_website_id'       => $vad['bo_website_id'],
                'bo_vad_id'           => $vad['bo_vad_id'],
                'bo_product_id'       => $boProductId,
                'subscription_amount' => $boProduct ? $boProduct->subscription_price : null,
                'rebill_amount'       => $boProduct ? $boProduct->periodical_price : null,
                'vad'                 => session('vad.used_vad.vad_name'),
                'first_vad'           => session('vad.used_vad.vad_name'),
                'order_number'        => $isNew ? $this->generateOrderNumber() : ($payment->order_number ?: $this->generateOrderNumber()),
                'is_test'             => !app()->isProduction(),
            ];

            if ($isNew) {
                $payment = Payment::create($data);
            } else {
                $payment->update($data);
            }

            Log::info('Payment saved', [
                'id'            => $payment->id,
                'customer_id'   => $customerId,
                'order_number'  => $payment->order_number,
                'bo_website_id' => $vad['bo_website_id'],
                'vad'           => $data['vad'],
            ]);
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
            $vadProduct    = $this->stripeService->getVadProduct();
            $boProduct     = $this->stripeService->getBoProduct();
            $trialProduct  = $this->stripeService->getTrialProduct();
            $currencyId    = $vadProduct ? $vadProduct->currency_id : null;
            $boProductId   = $vadProduct ? $vadProduct->bo_product_id : null;

            Payment::create([
                'customer_id'         => $customerId,
                'payment_status_id'   => 3, // failed
                'product_id'          => $trialProduct ? $trialProduct->product_id : null,
                'currency_id'         => $currencyId ?? 2,
                'bo_website_id'       => $vad['bo_website_id'],
                'bo_vad_id'           => $vad['bo_vad_id'],
                'bo_product_id'       => $boProductId,
                'subscription_amount' => $boProduct ? $boProduct->subscription_price : null,
                'rebill_amount'       => $boProduct ? $boProduct->periodical_price : null,
                'vad'                 => session('vad.used_vad.vad_name'),
                'first_vad'           => session('vad.used_vad.vad_name'),
                'order_number'        => $this->generateOrderNumber(),
                'error_return'        => Str::limit($error, 250),
                'is_test'             => !app()->isProduction(),
            ]);
        } catch (\Throwable $e) {
            Log::error('savePaymentFailed failed', ['error' => $e->getMessage()]);
        }
    }
}
