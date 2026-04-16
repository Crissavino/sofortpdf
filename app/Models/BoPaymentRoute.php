<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BoPaymentRoute extends Model
{
    protected $table = 'bo_payment_routes';

    protected $fillable = [
        'bo_vad_rule_id',
        'bo_vad_id',
        'bo_vad_product_id',
        'bo_website_id',
        'bo_product_id',
        'payment_id',
        'segment',
        'currency',
        'ip',
        'utm_params',
    ];

    protected $casts = [
        'utm_params' => 'array',
    ];

    public function vad(): BelongsTo
    {
        return $this->belongsTo(BoVad::class, 'bo_vad_id');
    }

    public function vadRule(): BelongsTo
    {
        return $this->belongsTo(BoVadRule::class, 'bo_vad_rule_id');
    }

    public function vadProduct(): BelongsTo
    {
        return $this->belongsTo(BoVadProduct::class, 'bo_vad_product_id');
    }
}
