<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\PaywallBypass;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * Live entitlement check for the client-side paywall gate.
 *
 * The gate used to read a boolean that Blade baked into the tool page
 * (`window.sofortpdfPaywall.userHasSubscription`). That value is correct
 * only at render time: the tool pages are public and cacheable, so after
 * a visitor pays and comes back to the tab — browser cache, bfcache, back
 * button — the page still claims they owe us money. Customer 72329 was
 * shown the payment modal three times that way on 2026-10-07 while holding
 * an active trial, and ended up opening four accounts and paying four
 * trials for one entitlement.
 *
 * This endpoint answers the same question at click time instead, so the
 * answer cannot be older than the click. It must stay reachable for
 * guests (no paywall middleware) — "not subscribed" is a valid answer.
 */
class EntitlementController extends Controller
{
    public function show(Request $request): JsonResponse
    {
        $user = $request->user();

        $subscribed = PaywallBypass::applies($request)
            || (bool) ($user && $user->hasSofortpdfSubscription());

        return response()
            ->json(['subscribed' => $subscribed])
            // Never let a CDN or the browser reuse one visitor's answer:
            // this flips the moment they pay, which is the whole point.
            ->header('Cache-Control', 'no-store, no-cache, must-revalidate, private')
            ->header('Pragma', 'no-cache');
    }
}
