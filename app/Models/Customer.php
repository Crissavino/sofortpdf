<?php

namespace App\Models;

use App\Mail\ResetPasswordMail;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Mail;

/**
 * Sofortpdf authentication runs on the shared `customers` table — same as
 * conversie-pdf / contract-kit / convierte-pdf — so a single account can
 * span multiple brands.
 */
class Customer extends Authenticatable
{
    use Notifiable;

    protected $table = 'customers';

    const CREATED_AT = 'create_time';
    const UPDATED_AT = null;

    protected $fillable = [
        'first_name',
        'last_name',
        'email',
        'password',
        'language',
        'country',
        'ip',
        'website_id',
        'remember_token',
        'last_time_connected',
        'came_from_ads',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    /* ============== Accessors ============== */

    /**
     * Single-name accessor for back-compat with views/mails that used
     * $user->name. Returns "First Last" trimmed.
     */
    public function getNameAttribute(): string
    {
        $name = trim(($this->first_name ?? '') . ' ' . ($this->last_name ?? ''));
        return $name !== '' ? $name : (string) ($this->email ?? '');
    }

    /* ============== Relations ============== */

    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class, 'customer_id');
    }

    public function subscriptions(): HasMany
    {
        return $this->hasMany(Subscription::class, 'customer_id');
    }

    public function boStripeCustomer(): HasOne
    {
        return $this->hasOne(BoStripeCustomer::class, 'customer_id')
            ->where('website_id', config('services.bo.website_id'));
    }

    /* ============== Helpers ============== */

    /**
     * True if this customer currently has an active or trialing subscription
     * scoped to the sofortpdf website.
     *
     * Checks bo_stripe_customers first (source of truth from Stripe via BO).
     * If Stripe says canceled, the subscription is dead — regardless of
     * what the local subscriptions table says (which may be stale because
     * webhooks are processed by the BO, not sofortpdf).
     */
    public function hasSofortpdfSubscription(): bool
    {
        // Same logic as conversie-pdf: payment_status_id = 2 means subscribed/active.
        // This covers trial (status set to 2 at pay-trial) and active subscriptions.
        // Status 3 = terminated/cancelled → no access.
        return $this->payments()
            ->where('payment_status_id', 2)
            ->exists();
    }

    /**
     * Subscription status for UI display — reads from both sources.
     */
    public function getSubscriptionStatus(): string
    {
        $payment = $this->payments()->orderBy('id', 'desc')->first();
        if (!$payment) return 'none';

        // payment_status_id: 2=subscribed, 3=terminated, 4=pending, others=inactive
        switch ($payment->payment_status_id) {
            case 2: return 'active';
            case 3: return 'canceled';
            case 4: return 'pending';
            default: return 'inactive';
        }
    }

    /**
     * Use the branded, locale-aware reset email instead of Laravel's default.
     */
    public function sendPasswordResetNotification($token): void
    {
        $locale = app()->getLocale();
        $slug   = config("locales.auth_slugs.{$locale}.password_reset", 'passwort-reset');
        $resetUrl = url("/{$locale}/{$slug}/{$token}?email=" . urlencode($this->getEmailForPasswordReset()));

        Mail::to($this->email)->send(new ResetPasswordMail($this, $resetUrl));
    }
}
