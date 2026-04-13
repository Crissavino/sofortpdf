<?php

namespace App\Services;

use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\IpUtils;

/**
 * Single source of truth for "does this request bypass the paywall?".
 * Used by the Paywall middleware, ConversionController (to choose between
 * a downloads row and a cache token), and the tool blade templates
 * (to skip the @guest redirect).
 */
class PaywallBypass
{
    public static function applies(?Request $request = null): bool
    {
        if (config('sofortpdf.payment_bypass', false)) {
            return true;
        }

        $allowlist = (array) config('sofortpdf.payment_bypass_ips', []);
        if (empty($allowlist)) {
            return false;
        }

        $request = $request ?: request();
        if (! $request) {
            return false;
        }

        $ip = $request->ip();
        return $ip !== null && IpUtils::checkIp($ip, $allowlist);
    }
}
