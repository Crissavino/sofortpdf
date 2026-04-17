<?php

namespace App\Services\Payment;

use App\Contracts\PaymentGatewayInterface;
use App\Services\Payment\Gateways\StripeGateway;

class PaymentGatewayFactory
{
    public function resolve(string $gateway = 'stripe'): PaymentGatewayInterface
    {
        return app(StripeGateway::class);
    }

    public function resolveFromSession(): PaymentGatewayInterface
    {
        $vad = session('vad.used_vad', []);
        $gateway = $vad['gateway'] ?? 'stripe';

        return $this->resolve($gateway);
    }
}
