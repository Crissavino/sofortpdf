<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Read-only mirror of the shared `customers` table on the avocode DB.
 *
 * Sofortpdf's authentication today still lives on the local `users` table.
 * This model is used exclusively by `App\Http\Middleware\ResolveVad` to look
 * up returning visitors by IP and recover the VAD they previously used. The
 * full migration of authentication onto `customers` is a separate task.
 */
class Customer extends Model
{
    protected $table = 'customers';

    const CREATED_AT = 'create_time';
    const UPDATED_AT = null;

    protected $fillable = [
        'email',
        'first_name',
        'last_name',
        'language',
        'country',
        'ip',
        'password',
        'remember_token',
        'website_id',
        'last_time_connected',
        'came_from_ads',
    ];

    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class, 'customer_id');
    }
}
