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
        return $this->forward($request, false, true);
    }

    /**
     * JACKCODE Stripe account webhook.
     */
    public function handleJack(Request $request)
    {
        return $this->forward($request, true, false);
    }

    private function forward(Request $request, bool $isJack, bool $isAvocode)
    {
        $boBaseUri = config('services.bo.base_uri');
        $siteId    = config('services.bo.website_id');

        if (!$boBaseUri || !$siteId) {
            Log::error('Stripe Webhook: BO_BASE_URI or WEBSITE_ID not configured');
            return response()->json(['received' => true]);
        }

        try {
            Log::channel('activity')->info('webhook_received', [
                'jack' => $isJack,
                'ip'   => $request->ip(),
            ]);

            $response = Http::timeout(15)->post("{$boBaseUri}/api/payments/stripe/webhooks", [
                'stripeSignature' => $request->header('Stripe-Signature'),
                'requestData'     => $request->getContent(),
                'siteId'          => $siteId,
                'isJack'          => $isJack ? 'true' : 'false',
                'isAvocode'       => $isAvocode ? 'true' : 'false',
            ]);

            if (!$response->successful()) {
                Log::warning('Stripe Webhook: BO returned non-success', [
                    'status' => $response->status(),
                    'body'   => $response->body(),
                ]);
            }
        } catch (\Throwable $e) {
            Log::error('Stripe Webhook: BO forward failed', [
                'error' => $e->getMessage(),
            ]);
        }

        // Always return 200 to Stripe — retries are the BO's problem, not
        // Stripe's. A persistent BO outage will show up in our logs.
        return response()->json(['received' => true]);
    }
}
