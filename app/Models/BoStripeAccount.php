<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BoStripeAccount extends Model
{
    protected $table = 'bo_stripe_accounts';
    public $timestamps = false;

    protected $hidden = [
        'secret_api_key',
        'webhook_secret',
    ];
}
