<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class BoVad extends Model
{
    protected $table = 'bo_vads';
    public $timestamps = false;

    public function rules(): HasMany
    {
        return $this->hasMany(BoVadRule::class, 'bo_vad_id');
    }

    public function products(): HasMany
    {
        return $this->hasMany(BoVadProduct::class, 'bo_vad_id');
    }
}
