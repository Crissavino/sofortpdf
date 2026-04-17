<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BoVadProduct extends Model
{
    protected $table = 'bo_vad_products';

    public function vad(): BelongsTo
    {
        return $this->belongsTo(BoVad::class, 'bo_vad_id');
    }

    public function boProduct(): BelongsTo
    {
        return $this->belongsTo(BoProduct::class, 'bo_product_id');
    }

    public function trialStripeProduct(): BelongsTo
    {
        return $this->belongsTo(BoStripeProduct::class, 'trial_product_id');
    }

    public function subscriptionStripeProduct(): BelongsTo
    {
        return $this->belongsTo(BoStripeProduct::class, 'subscription_product_id');
    }

    public function stripeAccount(): BelongsTo
    {
        return $this->belongsTo(BoStripeAccount::class, 'account_id');
    }
}
