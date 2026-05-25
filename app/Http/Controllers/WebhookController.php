<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

/**
 * Stripe-Webhook-Proxy.
 *
 * Sofortpdf does NOT process Stripe events locally — same as conversie-pdf
 * and contract-kit. The raw request (signature + body) is forwarded to the
 * centralized BO API which handles verification, customer resolution,
 * subscription updates, payment recording, and lifecycle emails.
 *
 * Two routes, one per Stripe account:
 *   POST /stripe/webhook       → AVOCODE account (isJack=false)
 *   POST /stripe/webhook-jack  → JACKCODE account (isJack=true)
 */
class WebhookController extends Controller
{
    /**
     * AVOCODE Stripe account webhook.
     */
    public function handle(Request $request)
    {
        return $this->forward($request, false, false);
    }

    /**
     * JACKCODE Stripe account webhook.
     */
    public function handleJack(Request $request)
    {
        return $this->forward($request, true, false);
    }

    private function forward(Request $request, bool $isJack, bool $isKiwi)
    {
        $boBaseUri = config('services.bo.base_uri');
        $siteId    = config('services.bo.website_id');

        // Config issue is not transient — Stripe retrying for 3 days
        // won't help. Acknowledge with 200 and log loudly so we notice.
        if (!$boBaseUri || !$siteId) {
            Log::error('Stripe Webhook: BO_BASE_URI or WEBSITE_ID not configured');
            return response()->json(['received' => true]);
        }

        Log::channel('activity')->info('webhook_received', [
            'jack' => $isJack,
            'ip'   => $request->ip(),
        ]);

        try {
            $response = Http::timeout(30)->post("{$boBaseUri}/api/payments/stripe/webhooks", [
                'stripeSignature' => $request->header('Stripe-Signature'),
                'requestData'     => $request->getContent(),
                'siteId'          => $siteId,
                'isJack'          => $isJack ? 'true' : 'false',
                'isKiwi'          => $isKiwi ? 'true' : 'false',
            ]);
        } catch (\Throwable $e) {
            // Transient network failure (timeout, DNS, connection refused).
            // Return 503 so Stripe retries with exponential backoff —
            // 1h, 2h, 4h, ..., up to 3 days. Without this, the BO never
            // sees the event and Stripe never tries again (seen on
            // 2026-05-21 12:14: a single cURL timeout silently dropped
            // one event).
            Log::error('Stripe Webhook: BO forward failed', [
                'error' => $e->getMessage(),
            ]);
            return response()->json(['error' => 'bo_unreachable'], 503);
        }

        if (!$response->successful()) {
            Log::warning('Stripe Webhook: BO returned non-success', [
                'status' => $response->status(),
                'body'   => $response->body(),
            ]);
            // BO replied but rejected the payload — also a transient
            // BO-side issue. Let Stripe retry rather than swallow.
            return response()->json(['error' => 'bo_rejected'], 503);
        }

        return response()->json(['received' => true]);
    }
}
