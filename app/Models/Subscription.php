<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Subscription extends Model
{
    protected $table = 'subscriptions';

    protected $fillable = [
        'customer_id',
        'stripe_subscription_id',
        'stripe_price_id',
        'status',
        'trial_ends_at',
        'current_period_start',
        'current_period_end',
    ];

    protected $casts = [
        'trial_ends_at' => 'datetime',
        'current_period_start' => 'datetime',
        'current_period_end' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'customer_id');
    }

    public function isActive(): bool
    {
        return in_array($this->status, ['trialing', 'active']);
    }

    public function isSofortpdf(): bool
    {
        return str_contains($this->stripe_price_id ?? '', 'sofortpdf_');
    }
}
