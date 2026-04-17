<?php

namespace App\Services\Payment\Gateways;

use App\Contracts\PaymentGatewayInterface;
use App\Services\Payment\StripeService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

/**
 * Stripe payment gateway — delegates all Stripe API calls to the
 * centralized BO (avocode-bo.online), same as contract-kit / conversie-pdf.
 *
 * Sofortpdf never talks to Stripe directly; the BO handles customer
 * creation, trial charges, subscription setup, and webhooks.
 */
class StripeGateway implements PaymentGatewayInterface
{
    private string $baseUri;
    private StripeService $stripeService;

    public function __construct(StripeService $stripeService)
    {
        $this->baseUri = rtrim(config('services.bo.base_uri', ''), '/');
        $this->stripeService = $stripeService;
    }

    public function createCustomer(array $data): array
    {
        try {
            $account = $this->stripeService->getStripeAccount();

            $payload = [
                'customer' => json_encode([
                    'id'                => $data['customer_id'] ?? null,
                    'email'             => $data['email'],
                    'first_name'        => $data['first_name'],
                    'last_name'         => $data['last_name'],
                    'payment_method_id' => $data['payment_method_id'],
                    'ip'                => $data['ip'] ?? '',
                ]),
                'stripeAccountId' => $account ? $account->id : null,
                'siteId'          => config('services.bo.website_id'),
            ];

            Log::info('StripeGateway::createCustomer payload', $payload);

            $response = Http::timeout(15)->post("{$this->baseUri}/api/payments/stripe/create-customer", $payload);

            Log::info('StripeGateway::createCustomer response', [
                'status' => $response->status(),
                'body'   => $response->json(),
            ]);

            $json = $response->json();

            if ($response->successful() && ($json['success'] ?? false)) {
                return [
                    'success'  => true,
                    'customer' => $json['customer'] ?? null,
                ];
            }

            Log::error('StripeGateway::createCustomer failed', [
                'status' => $response->status(),
                'body'   => $response->body(),
            ]);

            return ['success' => false];
        } catch (\Throwable $e) {
            Log::error('StripeGateway::createCustomer exception', ['error' => $e->getMessage()]);
            return ['success' => false];
        }
    }

    public function payTrial(array $data): array
    {
        try {
            $account      = $this->stripeService->getStripeAccount();
            $trialProduct = $this->stripeService->getTrialProduct();

            $payload = [
                'stripeCustomerId' => $data['stripe_customer_id'],
                'paymentMethodId'  => $data['payment_method_id'],
                'trialProductId'   => $trialProduct ? $trialProduct->id : null,
                'stripeAccountId'  => $account ? $account->id : null,
                'siteId'           => config('services.bo.website_id'),
                'hasTrialDiscount' => $data['has_trial_discount'] ?? false,
                'test'             => !app()->isProduction(),
            ];

            Log::info('StripeGateway::payTrial payload', $payload);

            $response = Http::timeout(30)->post("{$this->baseUri}/api/payments/stripe/pay-trial", $payload);

            Log::info('StripeGateway::payTrial response', [
                'status' => $response->status(),
                'body'   => $response->json(),
            ]);

            $json = $response->json();

            if ($response->successful() && ($json['success'] ?? false)) {
                return $json;
            }

            Log::error('StripeGateway::payTrial failed', [
                'status' => $response->status(),
                'body'   => $response->body(),
            ]);

            return ['success' => false, 'message' => $json['message'] ?? 'Payment failed'];
        } catch (\Throwable $e) {
            Log::error('StripeGateway::payTrial exception', ['error' => $e->getMessage()]);
            return ['success' => false];
        }
    }

    public function createSubscription(array $data): array
    {
        try {
            $account        = $this->stripeService->getStripeAccount();
            $premiumProduct = $this->stripeService->getPremiumProduct();

            $boData = [
                'sender'           => request()->header('referer', ''),
                'web_agent'        => request()->userAgent(),
                'web_agent_accept' => request()->userAgent(),
                'description'      => 'sofortpdf.com subscription',
                'periodical_description' => 'sofortpdf.com periodical subscription',
                'ip'               => request()->ip(),
            ];

            $payload = [
                'stripeCustomerId' => $data['stripe_customer_id'],
                'paymentMethodId'  => $data['payment_method_id'],
                'stripeProductId'  => $premiumProduct ? $premiumProduct->id : null,
                'stripeAccountId'  => $account ? $account->id : null,
                'siteId'           => config('services.bo.website_id'),
                'bo_data'          => $boData,
            ];

            Log::info('StripeGateway::createSubscription payload', $payload);

            $response = Http::timeout(30)->post("{$this->baseUri}/api/payments/stripe/create-subscription", $payload);

            Log::info('StripeGateway::createSubscription response', [
                'status' => $response->status(),
                'body'   => $response->json(),
            ]);

            $json = $response->json();

            if ($response->successful() && ($json['success'] ?? false)) {
                return $json;
            }

            Log::error('StripeGateway::createSubscription failed', [
                'status' => $response->status(),
                'body'   => $response->body(),
            ]);

            return ['success' => false, 'message' => $json['message'] ?? 'Subscription failed'];
        } catch (\Throwable $e) {
            Log::error('StripeGateway::createSubscription exception', ['error' => $e->getMessage()]);
            return ['success' => false];
        }
    }

    public function handleWebhook(Request $request): array
    {
        try {
            $response = Http::timeout(15)->post("{$this->baseUri}/api/payments/stripe/webhooks", [
                'stripeSignature' => $request->header('stripe-signature'),
                'requestData'     => $request->getContent(),
                'siteId'          => config('services.bo.website_id'),
                'isJack'          => config('services.bo.is_jack', 'true'),
                'isAvocode'       => config('services.bo.is_avocode', 'false'),
            ]);

            return ['success' => $response->successful()];
        } catch (\Throwable $e) {
            Log::error('StripeGateway::handleWebhook exception', ['error' => $e->getMessage()]);
            return ['success' => false];
        }
    }
}
