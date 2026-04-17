<?php

namespace App\Http\Middleware;

use App\Classes\GetIpInformation;
use App\Models\BoPaymentRoute;
use App\Models\Customer;
use App\Services\Payment\VadRouter;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\View;

/**
 * Resolves which VAD (and therefore which company) is responsible for the
 * current visitor. Stores the result in the session and shares the
 * matching company profile with all blade views as `$company`.
 *
 * Tier 1: previously routed IPs are reused (bo_payment_routes lookup).
 * Tier 2: returning customer with a prior payment recovers their VAD.
 * Tier 3: fresh visitor → country-segmented weighted routing via VadRouter.
 */
class ResolveVad
{
    private VadRouter $vadRouter;

    public function __construct(VadRouter $vadRouter)
    {
        $this->vadRouter = $vadRouter;
    }

    public function handle(Request $request, Closure $next)
    {
        // Skip API + admin routes — the router only matters for user-facing
        // pages where the company profile is rendered.
        if ($request->is('api/*') || $request->is('admin*')) {
            return $next($request);
        }

        $forceProvider = $request->query('provider');

        $storedVad    = session('vad.used_vad');
        $needsResolve = !$storedVad
            || $forceProvider
            || empty($storedVad['bo_vad_id'])
            || !$this->vadBelongsToThisWebsite($storedVad);

        if ($needsResolve) {
            $this->resolveVad($request, $forceProvider);
        }

        $this->shareCompanyData();

        return $next($request);
    }

    private function resolveVad(Request $request, ?string $forceProvider): void
    {
        $ip = $request->ip();

        if ($forceProvider) {
            $vad = $this->vadRouter->selectForcedVad($forceProvider, $ip);
            $this->storeVadInSession($vad);
            return;
        }

        // Tier 1 + 2: returning visitor lookup
        $existing = $this->findExistingVadByIp($ip);
        if ($existing) {
            Log::info('ResolveVad: Found existing VAD by IP', [
                'ip'         => $ip,
                'company_id' => $existing['company_id'],
                'source'     => $existing['source'],
            ]);
            $this->storeVadInSession($existing);
            return;
        }

        // Tier 3: fresh visitor → country-routed
        $countryCode = $this->detectCountry($request);
        $vad         = $this->vadRouter->selectVad($countryCode, $ip);
        $this->storeVadInSession($vad);
    }

    /**
     * Look up an existing VAD assignment for this IP. Two tiers:
     *   1. Most recent bo_payment_routes row scoped to this website's
     *      bo_websites.
     *   2. Most recent payment of a customer matching this IP.
     */
    private function findExistingVadByIp(string $ip): ?array
    {
        $websiteId = config('services.bo.website_id');

        $boWebsiteIds = collect();
        if ($websiteId) {
            try {
                $boWebsiteIds = DB::table('bo_websites')
                    ->where('website_id', $websiteId)
                    ->pluck('id');
            } catch (\Throwable $e) {
                Log::debug('ResolveVad: bo_websites lookup failed', ['error' => $e->getMessage()]);
            }
        }

        // Tier 1
        try {
            $query = BoPaymentRoute::where('ip', $ip)
                ->whereNotNull('bo_vad_id')
                ->with('vad')
                ->latest('id');

            if ($boWebsiteIds->isNotEmpty()) {
                $query->whereIn('bo_website_id', $boWebsiteIds);
            }

            $paymentRoute = $query->first();

            if ($paymentRoute && $paymentRoute->vad) {
                $vad = $paymentRoute->vad;
                return [
                    'gateway'          => $vad->payment_gateway_name,
                    'vad_name'         => $vad->name,
                    'bo_vad_id'        => $vad->id,
                    'company_id'       => $vad->company_id,
                    'segment'          => $paymentRoute->segment,
                    'currency'         => $paymentRoute->currency,
                    'currency_id'      => null,
                    'payment_route_id' => $paymentRoute->id,
                    'vad_product_id'   => $paymentRoute->bo_vad_product_id,
                    'account_id'       => null,
                    'bo_website_id'    => $paymentRoute->bo_website_id,
                    'source'           => 'payment_route',
                ];
            }
        } catch (\Throwable $e) {
            Log::debug('ResolveVad: tier-1 lookup failed', ['error' => $e->getMessage()]);
        }

        // Tier 2
        try {
            $customerQuery = Customer::where('ip', $ip);

            if ($websiteId) {
                $customerQuery->where('website_id', $websiteId);
            }

            $customerQuery->whereHas('payments', function ($q) use ($boWebsiteIds) {
                $q->whereNotNull('bo_vad_id');
                if ($boWebsiteIds->isNotEmpty()) {
                    $q->whereIn('bo_website_id', $boWebsiteIds);
                }
            });

            $customer = $customerQuery->first();

            if ($customer) {
                $paymentQuery = $customer->payments()
                    ->whereNotNull('bo_vad_id')
                    ->with('vad')
                    ->latest('id');

                if ($boWebsiteIds->isNotEmpty()) {
                    $paymentQuery->whereIn('bo_website_id', $boWebsiteIds);
                }

                $payment = $paymentQuery->first();

                $vadRelation = $payment ? $payment->getRelation('vad') : null;
                if ($vadRelation) {
                    $vad = $vadRelation;
                    return [
                        'gateway'          => $vad->payment_gateway_name,
                        'vad_name'         => $vad->name,
                        'bo_vad_id'        => $vad->id,
                        'company_id'       => $vad->company_id,
                        'segment'          => null,
                        'currency'         => null,
                        'currency_id'      => null,
                        'payment_route_id' => null,
                        'vad_product_id'   => null,
                        'account_id'       => null,
                        'bo_website_id'    => $payment->bo_website_id,
                        'source'           => 'customer_payment',
                    ];
                }
            }
        } catch (\Throwable $e) {
            Log::debug('ResolveVad: tier-2 lookup failed', ['error' => $e->getMessage()]);
        }

        return null;
    }

    private function storeVadInSession(array $vad): void
    {
        session([
            'vad.used_vad'        => $vad,
            'bo_vad_id'           => $vad['bo_vad_id'] ?? null,
            'bo_payment_route_id' => $vad['payment_route_id'] ?? null,
            'vad.company_id'      => $vad['company_id'] ?? null,
            'vad.currency'        => $vad['currency'] ?? null,
            'vad.currency_id'     => $vad['currency_id'] ?? null,
            'vad.segment'         => $vad['segment'] ?? null,
        ]);
    }

    private function detectCountry(Request $request): string
    {
        if ($code = session('country_code')) {
            return $code;
        }

        $ip = $request->ip();

        if (in_array($ip, ['127.0.0.1', '::1'], true)
            || strpos($ip, '192.168.') === 0
            || strpos($ip, '10.') === 0
            || strpos($ip, '172.') === 0
        ) {
            // Local / private — fall back to DE since this is a German site.
            session(['country_code' => 'DE']);
            return 'DE';
        }

        try {
            $countryCode = GetIpInformation::get($ip, 'countryCode');

            if ($countryCode && strlen($countryCode) === 2) {
                $code = strtoupper($countryCode);
                session(['country_code' => $code]);
                return $code;
            }
        } catch (\Throwable $e) {
            Log::debug('ResolveVad: GetIpInformation failed', ['error' => $e->getMessage()]);
        }

        // Last resort: derive from locale
        $locale   = app()->getLocale();
        $fallback = $locale === 'en' ? 'GB' : 'DE';
        session(['country_code' => $fallback]);

        return $fallback;
    }

    /**
     * Guards the session against stale data from another brand sharing the
     * same IP (e.g., dev jumping between conversie-pdf and sofortpdf).
     */
    private function vadBelongsToThisWebsite(array $storedVad): bool
    {
        $websiteId = config('services.bo.website_id');

        if (!$websiteId) {
            return true;
        }

        $boWebsiteId = $storedVad['bo_website_id'] ?? null;

        if (!$boWebsiteId) {
            return false;
        }

        try {
            return DB::table('bo_websites')
                ->where('id', $boWebsiteId)
                ->where('website_id', $websiteId)
                ->exists();
        } catch (\Throwable $e) {
            Log::debug('ResolveVad: vadBelongsToThisWebsite check failed', ['error' => $e->getMessage()]);
            return true; // fail open
        }
    }

    private function shareCompanyData(): void
    {
        $defaultId = (int) config('company.default_company_id', 1);
        $companyId = (int) session('vad.company_id', $defaultId);

        $profile = config("company.profiles.{$companyId}")
            ?? config("company.profiles.{$defaultId}");

        View::share('company', $profile);
        View::share('companyEmail', config('company.email'));
        View::share('companyWebsite', config('company.website'));

        // Pricing — resolved once per session from bo_products via the VAD
        // route, cached so subsequent requests skip the DB queries.
        $pricing = session('vad.pricing');

        if (!$pricing) {
            try {
                $stripeService = app(\App\Services\Payment\StripeService::class);
                $data = $stripeService->resolvePaymentData();

                $pricing = [
                    'trial'        => $data['trial_price'] ?: 0.69,
                    'subscription' => $data['subscription_price'] ?: 39.90,
                    'currency'     => $data['currency'] ?: 'EUR',
                    'symbol'       => $this->currencySymbol($data['currency'] ?: 'EUR'),
                ];
                session(['vad.pricing' => $pricing]);
            } catch (\Throwable $e) {
                Log::debug('ResolveVad: pricing resolution failed', ['error' => $e->getMessage()]);
                $pricing = [
                    'trial'        => 0.69,
                    'subscription' => 39.90,
                    'currency'     => 'EUR',
                    'symbol'       => '€',
                ];
            }
        }

        View::share('pricing', $pricing);
    }

    private function currencySymbol(string $currency): string
    {
        $map = [
            'EUR' => '€', 'USD' => '$', 'GBP' => '£', 'RON' => 'lei',
            'CHF' => 'CHF', 'HUF' => 'Ft', 'CZK' => 'Kč', 'PLN' => 'zł',
        ];
        return $map[strtoupper($currency)] ?? $currency;
    }
}
