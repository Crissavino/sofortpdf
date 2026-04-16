<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Read+write to the shared `subscriptions` table on the avocode DB.
 *
 * Schema is brand-agnostic: scope queries by website_id (set from the VAD
 * router → config('services.bo.website_id')) so we only see this brand's
 * subscriptions.
 */
class Subscription extends Model
{
    protected $table = 'subscriptions';

    protected $fillable = [
        'customer_id',
        'website_id',
        'bo_website_id',
        'company_id',
        'trial_invoice_id',
        'first_subscription_invoice_id',
        'bo_product_id',
        'campaign_id',
        'payment_provider_id',
        'trial_version',
        'is_trial_active',
        'trial_started_at',
        'trial_ends_at',
        'cancelled_during_trial',
        'plan_type',
        'subscription_started_at',
        'subscription_ends_at',
        'is_subscription_active',
        'cancelled_at',
        'cancel_reason',
        'refunded',
        'refund_date',
        'total_paid',
        'last_payment_at',
        'next_payment_at',
        'payment_count',
    ];

    protected $casts = [
        'trial_started_at'        => 'datetime',
        'trial_ends_at'           => 'datetime',
        'subscription_started_at' => 'datetime',
        'subscription_ends_at'    => 'datetime',
        'cancelled_at'            => 'datetime',
        'refund_date'             => 'datetime',
        'last_payment_at'         => 'datetime',
        'next_payment_at'         => 'datetime',
        'is_trial_active'         => 'boolean',
        'is_subscription_active'  => 'boolean',
        'cancelled_during_trial'  => 'boolean',
        'refunded'                => 'boolean',
        'total_paid'              => 'decimal:2',
    ];

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class, 'customer_id');
    }

    /**
     * True if the customer currently has access (trial or active).
     */
    public function isActive(): bool
    {
        return $this->is_trial_active || $this->is_subscription_active;
    }

    /**
     * Status string for UI display (mirrors Stripe-style status).
     */
    public function getStatusAttribute(): string
    {
        if ($this->cancelled_at && !$this->is_subscription_active && !$this->is_trial_active) {
            return 'canceled';
        }
        if ($this->is_trial_active) {
            return 'trialing';
        }
        if ($this->is_subscription_active) {
            return 'active';
        }
        return 'inactive';
    }

    /**
     * Closest "current period end" we can derive from the shared schema —
     * used by views that previously read $sub->current_period_end.
     */
    public function getCurrentPeriodEndAttribute()
    {
        return $this->subscription_ends_at ?? $this->trial_ends_at;
    }
}
