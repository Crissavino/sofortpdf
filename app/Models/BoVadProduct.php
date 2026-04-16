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
}
