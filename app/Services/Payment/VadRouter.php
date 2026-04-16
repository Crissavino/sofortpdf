<?php

namespace App\Services\Payment;

use App\Models\BoPaymentRoute;
use App\Models\BoVad;
use App\Models\BoVadProduct;
use App\Models\BoVadRule;
use Illuminate\Support\Facades\Log;

/**
 * Country-segmented, weighted VAD router. Mirrors contract-kit's flow:
 *   country code → segment → bo_vad_rules (weighted random) → BoVad +
 *   matching BoVadProduct → audit row in bo_payment_routes.
 */
class VadRouter
{
    /** EU + EEA + UK + CH country codes (uppercase). */
    private const EU_COUNTRIES = [
        'AT', 'BE', 'BG', 'HR', 'CY', 'CZ', 'DK', 'EE', 'FI', 'FR',
        'DE', 'GR', 'IE', 'IT', 'LV', 'LT', 'LU', 'MT', 'NL', 'PL',
        'PT', 'SK', 'SI', 'ES', 'SE', 'CH', 'NO', 'IS', 'LI', 'GB',
    ];

    public function selectVad(string $countryCode, string $ip): array
    {
        $websiteId = config('services.bo.website_id');

        if (!$websiteId) {
            Log::warning('VadRouter: WEBSITE_ID not configured, using defaults');
            return $this->defaultResult($countryCode);
        }

        $segment = $this->countryToSegment($countryCode);

        $rules = BoVadRule::where('website_id', $websiteId)
            ->where('segment', $segment)
            ->where('active', 1)
            ->orderByDesc('priority')
            ->with('vad')
            ->get();

        if ($rules->isEmpty() && $segment !== 'EU') {
            Log::info("VadRouter: No rules for website={$websiteId} segment={$segment}, trying EU fallback");
            $rules = BoVadRule::where('website_id', $websiteId)
                ->where('segment', 'EU')
                ->where('active', 1)
                ->orderByDesc('priority')
                ->with('vad')
                ->get();
        }

        if ($rules->isEmpty()) {
            Log::info("VadRouter: No rules for website={$websiteId} segment={$segment} or EU, trying any segment");
            $rules = BoVadRule::where('website_id', $websiteId)
                ->where('active', 1)
                ->orderByDesc('priority')
                ->with('vad')
                ->get();
        }

        if ($rules->isEmpty()) {
            Log::warning("VadRouter: No rules found for website={$websiteId}");
            return $this->defaultResult($countryCode);
        }

        $selectedRule = $this->getWeightedRule($rules);

        if (!$selectedRule || !$selectedRule->vad) {
            return $this->defaultResult($countryCode);
        }

        $vad = $selectedRule->vad;

        $isTest     = app()->environment('local', 'testing');
        $vadProduct = BoVadProduct::where('bo_vad_id', $vad->id)
            ->where('currency_id', $selectedRule->currency_id)
            ->where('active', 1)
            ->where('is_test', $isTest ? 1 : 0)
            ->first();

        if (!$vadProduct) {
            $vadProduct = BoVadProduct::where('bo_vad_id', $vad->id)
                ->where('currency_id', $selectedRule->currency_id)
                ->where('active', 1)
                ->first();
        }

        $paymentRoute = $this->savePaymentRoute($selectedRule, $vad, $vadProduct, $ip);

        $result = [
            'gateway'          => $vad->payment_gateway_name,
            'vad_name'         => $vad->name,
            'bo_vad_id'        => $vad->id,
            'company_id'       => $vad->company_id,
            'segment'          => $segment,
            'currency'         => $selectedRule->currency,
            'currency_id'      => $selectedRule->currency_id,
            'payment_route_id' => $paymentRoute ? $paymentRoute->id : null,
            'vad_product_id'   => $vadProduct ? $vadProduct->id : null,
            'account_id'       => $vadProduct ? ($vadProduct->account_id ?? null) : null,
            'bo_website_id'    => $selectedRule->bo_website_id ?? $selectedRule->website_id,
        ];

        Log::info('VadRouter: Selected', $result);

        return $result;
    }

    /**
     * Force a specific provider (?provider=stripe|payu|tap|revolut). Used
     * for QA / staff testing.
     */
    public function selectForcedVad(string $provider, string $ip): array
    {
        $websiteId = config('services.bo.website_id');

        $rule = BoVadRule::where('website_id', $websiteId)
            ->where('active', 1)
            ->whereHas('vad', function ($q) use ($provider) {
                $q->where('payment_gateway_name', $provider)->where('deleted', 0);
            })
            ->with('vad')
            ->first();

        if (!$rule || !$rule->vad) {
            Log::warning("VadRouter: No VAD found for forced provider={$provider}");
            return $this->defaultResult('XX');
        }

        $vad = $rule->vad;

        $vadProduct = BoVadProduct::where('bo_vad_id', $vad->id)
            ->where('currency_id', $rule->currency_id)
            ->where('active', 1)
            ->first();

        $paymentRoute = $this->savePaymentRoute($rule, $vad, $vadProduct, $ip);

        return [
            'gateway'          => $vad->payment_gateway_name,
            'vad_name'         => $vad->name,
            'bo_vad_id'        => $vad->id,
            'company_id'       => $vad->company_id,
            'segment'          => $rule->segment,
            'currency'         => $rule->currency,
            'currency_id'      => $rule->currency_id,
            'payment_route_id' => $paymentRoute ? $paymentRoute->id : null,
            'vad_product_id'   => $vadProduct ? $vadProduct->id : null,
            'account_id'       => $vadProduct ? ($vadProduct->account_id ?? null) : null,
            'bo_website_id'    => $rule->bo_website_id ?? $rule->website_id,
        ];
    }

    public function countryToSegment(string $countryCode): string
    {
        $cc = strtoupper($countryCode);

        if ($cc === 'RO') return 'RO';
        if ($cc === 'HU') return 'HU';
        if (in_array($cc, self::EU_COUNTRIES, true)) return 'EU';

        return 'REST';
    }

    private function getWeightedRule($rules): ?BoVadRule
    {
        $totalWeight = $rules->sum('weight');

        if ($totalWeight <= 0) {
            return $rules->first();
        }

        $random = mt_rand(1, $totalWeight);
        $cumulative = 0;

        foreach ($rules as $rule) {
            $cumulative += $rule->weight;
            if ($random <= $cumulative) {
                return $rule;
            }
        }

        return $rules->last();
    }

    private function savePaymentRoute(BoVadRule $rule, BoVad $vad, ?BoVadProduct $product, string $ip): ?BoPaymentRoute
    {
        try {
            return BoPaymentRoute::create([
                'bo_vad_rule_id'    => $rule->id,
                'bo_vad_id'         => $vad->id,
                'bo_vad_product_id' => $product ? $product->id : null,
                'bo_product_id'     => $product ? ($product->bo_product_id ?? null) : null,
                'bo_website_id'     => $rule->bo_website_id ?? $rule->website_id,
                'segment'           => $rule->segment,
                'currency'          => $rule->currency,
                'ip'                => $ip,
                'utm_params'        => session('utm_params'),
            ]);
        } catch (\Exception $e) {
            Log::error('VadRouter: Failed to save payment route', ['error' => $e->getMessage()]);
            return null;
        }
    }

    /**
     * Returned when nothing in the DB resolves. EU users default to AVOCODE
     * (Romania, EU jurisdiction); everyone else defaults to JACKCODE (UAE).
     * Both are overridable via env (DEFAULT_COMPANY_ID_EU / _REST).
     */
    private function defaultResult(string $countryCode): array
    {
        $segment    = $this->countryToSegment($countryCode);
        $defaultCo  = $segment === 'REST'
            ? (int) env('DEFAULT_COMPANY_ID_REST', 3)
            : (int) env('DEFAULT_COMPANY_ID_EU', 1);

        return [
            'gateway'          => 'stripe',
            'vad_name'         => null,
            'bo_vad_id'        => null,
            'company_id'       => $defaultCo,
            'segment'          => $segment,
            'currency'         => 'EUR',
            'currency_id'      => 2,
            'payment_route_id' => null,
            'vad_product_id'   => null,
            'account_id'       => null,
            'bo_website_id'    => null,
        ];
    }
}
