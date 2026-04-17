<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BoStripeProduct extends Model
{
    protected $table = 'bo_stripe_products';

    const CREATED_AT = 'created';
    const UPDATED_AT = 'modified';

    public function stripeAccount(): BelongsTo
    {
        return $this->belongsTo(BoStripeAccount::class, 'bo_stripe_account_id');
    }
}
