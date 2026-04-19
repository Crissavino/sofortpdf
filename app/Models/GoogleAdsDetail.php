<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GoogleAdsDetail extends Model
{
    protected $table = 'google_ads_details';

    protected $fillable = [
        'customer_id',
        'gclid',
        'utm_source',
        'utm_medium',
        'utm_campaign',
        'utm_term',
        'converted',
        'converted_at',
    ];
}
