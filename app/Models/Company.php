<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Shared companies table (read-only from sofortpdf's perspective — lives in
 * the Avocode master DB). Used to power legal pages so company details stay
 * in sync across all brands.
 *
 * @property int    $id
 * @property string $name
 * @property string $address
 * @property string $city
 * @property string $country
 * @property string $postcode
 * @property string $phone
 * @property string $vat_number
 * @property string $registration_number
 * @property string $num_reg_com
 */
class Company extends Model
{
    protected $table = 'companies';
    public $timestamps = false;
    protected $guarded = [];
}
