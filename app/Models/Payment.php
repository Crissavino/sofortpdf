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

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class, 'customer_id');
    }

    public function vad(): BelongsTo
    {
        return $this->belongsTo(BoVad::class, 'bo_vad_id');
    }
}
