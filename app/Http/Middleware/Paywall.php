<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

/**
 * Paywall: gates a request behind both authentication and an active
 * sofortpdf subscription, except when config('sofortpdf.payment_bypass')
 * is enabled. Used in place of the ['auth', 'sofortpdf.subscribed']
 * middleware stack so we can flip the entire site to free-use mode
 * without touching every route group.
 */
class Paywall
{
    public function handle(Request $request, Closure $next)
    {
        if (config('sofortpdf.payment_bypass', false)) {
            return $next($request);
        }

        $user = $request->user();

        if (! $user) {
            // Mirror the previous CheckSofortpdfSubscription redirect target
            // so behavior is identical when bypass is off.
            $returnUrl = $request->fullUrl();

            // For JSON/AJAX callers, return 401 instead of an HTML redirect.
            if ($request->expectsJson()) {
                return response()->json(['message' => 'Authentication required.'], 401);
            }

            return redirect()->route('checkout.start', ['return_to' => $returnUrl]);
        }

        if (! $user->hasSofortpdfSubscription()) {
            $returnUrl = $request->fullUrl();

            if ($request->expectsJson()) {
                return response()->json(['message' => 'Subscription required.'], 402);
            }

            return redirect()->route('checkout.start', ['return_to' => $returnUrl]);
        }

        return $next($request);
    }
}
