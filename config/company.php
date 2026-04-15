<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Company row for this brand
    |--------------------------------------------------------------------------
    |
    | Primary key in the shared `companies` table that corresponds to the
    | legal entity operating sofortpdf.com. Used by CompanyResolver to fetch
    | the row for legal pages (impressum, AGB, Datenschutz).
    |
    */
    'id' => env('COMPANY_ID'),

    /*
    |--------------------------------------------------------------------------
    | Fallback values
    |--------------------------------------------------------------------------
    |
    | Used when the DB is unreachable or the configured COMPANY_ID row is
    | missing, so legal pages never render with empty placeholders. Set each
    | value in .env — the site falls back to these verbatim until DB access
    | is restored.
    |
    */
    'fallback' => [
        'name'                => env('COMPANY_NAME', ''),
        'address'             => env('COMPANY_ADDRESS', ''),
        'city'                => env('COMPANY_CITY', ''),
        'country'             => env('COMPANY_COUNTRY', ''),
        'postcode'            => env('COMPANY_POSTCODE', ''),
        'phone'               => env('COMPANY_PHONE', ''),
        'vat_number'          => env('COMPANY_VAT_NUMBER', ''),
        'registration_number' => env('COMPANY_REGISTRATION_NUMBER', ''),
        'num_reg_com'         => env('COMPANY_NUM_REG_COM', ''),
        // Fields not on the DB row but still needed for German legal pages
        'representative'      => env('COMPANY_REPRESENTATIVE', ''),
        'email'               => env('COMPANY_EMAIL', env('CONTACT_EMAIL', 'info@sofortpdf.com')),
        'register_court'      => env('COMPANY_REGISTER_COURT', ''),
        'register_entry'      => env('COMPANY_REGISTER_ENTRY', ''),
    ],
];
