<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use Notifiable;

    protected $table = 'users';

    protected $fillable = [
        'name',
        'email',
        'password',
        'stripe_customer_id',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function subscriptions()
    {
        return $this->hasMany(Subscription::class, 'customer_id');
    }

    public function conversionLogs()
    {
        return $this->hasMany(ConversionLog::class, 'customer_id');
    }

    public function downloads()
    {
        return $this->hasMany(Download::class, 'customer_id');
    }

    public function hasSofortpdfSubscription(): bool
    {
        return $this->subscriptions()
            ->where('stripe_price_id', 'like', '%sofortpdf_%')
            ->whereIn('status', ['trialing', 'active'])
            ->exists();
    }
}
