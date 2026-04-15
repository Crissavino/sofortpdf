<?php

namespace App\Models;

use App\Mail\ResetPasswordMail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Mail;

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

    /**
     * Use the custom branded, locale-aware reset password email instead of
     * Laravel's default notification.
     */
    public function sendPasswordResetNotification($token): void
    {
        $locale = app()->getLocale();
        $slug   = config("locales.auth_slugs.{$locale}.password_reset", 'passwort-reset');
        $resetUrl = url("/{$locale}/{$slug}/{$token}?email=" . urlencode($this->getEmailForPasswordReset()));

        Mail::to($this->email)->send(new ResetPasswordMail($this, $resetUrl));
    }
}
