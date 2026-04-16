<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Persists Stripe customer / subscription / payment-method IDs for a
 * (customer, website, stripe-account) triple. Lives on the shared avocode
 * DB, scoped per brand via website_id.
 */
class BoStripeCustomer extends Model
{
    protected $table = 'bo_stripe_customers';

    const CREATED_AT = 'created';
    const UPDATED_AT = 'modified';

    protected $fillable = [
        'bo_stripe_account_id',
        'customer_id',
        'id_stripe_customer',
        'email',
        'website_id',
        'id_stripe_invoice',
        'id_stripe_payment_intent',
        'id_stripe_payment_method',
        'stripe_payment_method_fingerprint',
        'bo_stripe_product_id',
        'id_stripe_subscription_schedule',
        'stripe_subscription_status',
        'stripe_subscription_start',
        'id_stripe_subscription',
        'stripe_subscription_canceled_at',
        'current_period_start',
        'current_period_end',
    ];

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class, 'customer_id');
    }
}
