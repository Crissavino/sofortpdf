<?php

namespace App\Contracts;

use Illuminate\Http\Request;

interface PaymentGatewayInterface
{
    public function createCustomer(array $data): array;
    public function payTrial(array $data): array;
    public function createSubscription(array $data): array;
    public function handleWebhook(Request $request): array;
}
