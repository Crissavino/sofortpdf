<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ConversionLog extends Model
{
    protected $table = 'conversion_logs';

    protected $fillable = [
        'customer_id',
        'tool_slug',
        'original_filename',
        'result_filename',
        'status',
        'error_message',
        'file_size',
        'processing_time_ms',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'customer_id');
    }

    public function scopeSofortpdf($query)
    {
        return $query->where('tool_slug', 'like', 'sofortpdf_%');
    }
}
