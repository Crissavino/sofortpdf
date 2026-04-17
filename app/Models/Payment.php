<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Read-only mirror of the shared `payments` table. Used by
 * `App\Http\Middleware\ResolveVad` for tier-2 IP lookup (find a returning
 * customer's last payment to recover the originally assigned VAD).
 */
class Payment extends Model
{
    protected $table = 'payments';

    const CREATED_AT = 'create_time';
    const UPDATED_AT = null;

    protected $fillable = [
        'customer_id',
        'payment_status_id',
        'product_id',
        'currency_id',
        'bo_website_id',
        'bo_vad_id',
        'bo_product_id',
        'payment_code',
        'error_return',
        'hash_card',
        'last_four_digit',
        'processed',
        'cardholders_name',
        'order_number',
        'subscription_amount',
        'rebill_amount',
        'current_period_start',
        'current_period_end',
        'stripe_status',
        'is_test',
        'is_payment_confirmed',
    ];

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class, 'customer_id');
    }

    public function vad(): BelongsTo
    {
        return $this->belongsTo(BoVad::class, 'bo_vad_id');
    }
}
