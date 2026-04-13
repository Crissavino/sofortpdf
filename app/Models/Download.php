<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Download extends Model
{
    protected $table = 'downloads';

    protected $fillable = [
        'customer_id',
        'token',
        'file_path',
        'original_filename',
        'expires_at',
    ];

    protected $casts = [
        'expires_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'customer_id');
    }

    public function isExpired(): bool
    {
        return $this->expires_at->isPast();
    }

    public static function createToken(int $customerId, string $filePath, string $originalFilename): self
    {
        return self::create([
            'customer_id' => $customerId,
            'token' => Str::random(64),
            'file_path' => $filePath,
            'original_filename' => $originalFilename,
            'expires_at' => now()->addHours((int) env('DOWNLOAD_LINK_TTL_HOURS', 24)),
        ]);
    }
}
