<?php

namespace App\Services\Payment;

use App\Models\BoPaymentRoute;
use App\Models\BoProduct;
use App\Models\BoStripeAccount;
use App\Models\BoStripeProduct;
use App\Models\BoVadProduct;

/**
 * Resolves Stripe accounts, products and pricing from the VAD route stored
 * in the session. Same pattern as contract-kit's StripeService + conversie-pdf's
 * StripeAux / PaymentGatewayPreloaderComponent.
 *
 * Chain:
 *   session('bo_payment_route_id')
 *     → BoPaymentRoute → vadProduct  (bo_vad_products)
 *       → trial_product_id           → BoStripeProduct (trial price ID)
 *       → subscription_product_id    → BoStripeProduct (rebill price ID)
 *       → account_id                 → BoStripeAccount (API keys)
 *       → bo_product_id              → BoProduct       (display prices)
 */
class StripeService
{
    /**
     * The Stripe account for this website (test or live depending on env).
     */
    public function getStripeAccount(): ?BoStripeAccount
    {
        $websiteId = config('services.bo.website_id');
        $isTest    = app()->environment('local', 'testing');

        return BoStripeAccount::where('website_id', $websiteId)
            ->where('is_test', $isTest ? 1 : 0)
            ->first();
    }

    /**
     * Trial Stripe product (price ID, product ID) for the resolved VAD.
     */
    public function getTrialProduct(): ?BoStripeProduct
    {
        $fromVad = $this->getVadStripeProduct('trial_product_id');
        if ($fromVad) {
            return $fromVad;
        }

        // Fallback: pick any active trial product for the account.
        $account = $this->getStripeAccount();
        if (!$account) {
            return null;
        }

        return BoStripeProduct::where('bo_stripe_account_id', $account->id)
            ->where('is_active', 1)
            ->where('premium', 0)
            ->first();
    }

    /**
     * Premium / subscription Stripe product (rebill price ID).
     */
    public function getPremiumProduct(): ?BoStripeProduct
    {
        $fromVad = $this->getVadStripeProduct('subscription_product_id');
        if ($fromVad) {
            return $fromVad;
        }

        $account = $this->getStripeAccount();
        if (!$account) {
            return null;
        }

        return BoStripeProduct::where('bo_stripe_account_id', $account->id)
            ->where('is_active', 1)
            ->where('premium', 1)
            ->first();
    }

    /**
     * BoProduct row (display prices: subscription_price = trial,
     * periodical_price = rebill/subscription).
     */
    public function getBoProduct(): ?BoProduct
    {
        $vadProduct = $this->getVadProduct();
        if (!$vadProduct || !$vadProduct->bo_product_id) {
            return null;
        }

        return BoProduct::find($vadProduct->bo_product_id);
    }

    /**
     * The BoVadProduct resolved from the current payment route.
     */
    public function getVadProduct(): ?BoVadProduct
    {
        $paymentRouteId = session('bo_payment_route_id');

        if ($paymentRouteId) {
            $route = BoPaymentRoute::find($paymentRouteId);
            if ($route && $route->bo_vad_product_id) {
                return BoVadProduct::find($route->bo_vad_product_id);
            }
        }

        // Fallback: vad_product_id stored directly in the session array
        $vadProductId = session('vad.used_vad.vad_product_id');
        if ($vadProductId) {
            return BoVadProduct::find($vadProductId);
        }

        return null;
    }

    /**
     * All-in-one resolution for views / checkout: returns an associative
     * array with everything needed to render the payment UI and create
     * the subscription.
     *
     * @return array{
     *   stripe_public_key: string|null,
     *   trial_price_id: string|null,
     *   subscription_price_id: string|null,
     *   trial_price: float,
     *   subscription_price: float,
     *   currency: string,
     *   bo_product_id: int|null,
     *   bo_stripe_account_id: int|null,
     * }
     */
    public function resolvePaymentData(): array
    {
        $account      = $this->getStripeAccount();
        $trialProduct = $this->getTrialProduct();
        $premProduct  = $this->getPremiumProduct();
        $boProduct    = $this->getBoProduct();

        return [
            'stripe_public_key'      => $account->public_api_key ?? null,
            'stripe_secret_key'      => $account->secret_api_key ?? null,
            'stripe_webhook_secret'  => $account->webhook_secret ?? null,
            'trial_price_id'         => $trialProduct->id_stripe_price ?? null,
            'subscription_price_id'  => $premProduct->id_stripe_price ?? null,
            'trial_price'            => (float) ($boProduct->subscription_price ?? 0),
            'subscription_price'     => (float) ($boProduct->periodical_price ?? 0),
            'currency'               => session('vad.currency', 'EUR'),
            'bo_product_id'          => $boProduct->id ?? null,
            'bo_stripe_account_id'   => $account->id ?? null,
        ];
    }

    /**
     * Resolve a BoStripeProduct through the active VAD route.
     * $field is 'trial_product_id' or 'subscription_product_id' on bo_vad_products.
     */
    private function getVadStripeProduct(string $field): ?BoStripeProduct
    {
        $vadProduct = $this->getVadProduct();
        $stripeProductId = $vadProduct ? $vadProduct->{$field} : null;

        if (!$stripeProductId) {
            return null;
        }

        return BoStripeProduct::where('id', $stripeProductId)
            ->where('is_active', 1)
            ->first();
    }
}
