<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Default company id
    |--------------------------------------------------------------------------
    |
    | Used by ResolveVad's shareCompanyData() when the session has no
    | resolved company yet (e.g., very first request before middleware
    | runs, or when the DB is unreachable). Matches bo_vads.company_id:
    |   1 = AVOCODE  (Romania)
    |   2 = KIWIKODE (Romania)
    |   3 = JACKCODE (UAE)
    |
    | Sofortpdf operates with AVOCODE (EU consumers) and JACKCODE (rest of
    | the world) — defaulting to AVOCODE since the brand targets DE/EU.
    |
    */
    'default_company_id' => env('DEFAULT_COMPANY_ID', 1),

    /*
    |--------------------------------------------------------------------------
    | Company profiles (keyed by company_id)
    |--------------------------------------------------------------------------
    |
    | Locale-aware fields are arrays: ['de' => '…', 'en' => '…']. Plain
    | strings are reused as-is across locales. Views resolve the right
    | string with $company['address'][app()->getLocale()] etc.
    |
    */
    'profiles' => [

        // company_id = 1 — AVOCODE S.R.L. (Romania, EU)
        1 => [
            'name'    => 'AVOCODE S.R.L.',
            'tax_id'  => 'RO47950333',
            'reg_no'  => 'J40/6621/2023',
            'address' => [
                'de' => 'Sektor 6, Aleea ARINII DORNEI, Nr. 14, Erdgeschoss, Block 16, Treppe D, App. 48, 060797 Bukarest, Rumänien',
                'en' => 'Sector 6, Aleea ARINII DORNEI, No. 14, Ground Floor, Block 16, Stairwell D, Apt. 48, 060797 Bucharest, Romania',
            ],
            'street' => [
                'de' => 'Aleea ARINII DORNEI, Nr. 14, Erdgeschoss, Block 16, Treppe D, App. 48',
                'en' => 'Aleea ARINII DORNEI, No. 14, Ground Floor, Block 16, Stairwell D, Apt. 48',
            ],
            'city'     => 'Bucharest',
            'postcode' => '060797',
            'country'  => [
                'de' => 'Rumänien',
                'en' => 'Romania',
            ],
            'jurisdiction' => [
                'de' => 'die zuständigen Gerichte in Bukarest, Rumänien',
                'en' => 'the competent courts in Bucharest, Romania',
            ],
            'tax_label' => [
                'de' => 'Umsatzsteuer-ID (CUI)',
                'en' => 'VAT ID (CUI)',
            ],
            'reg_label' => [
                'de' => 'Handelsregisternummer',
                'en' => 'Commercial Register Number',
            ],
            'governing_law' => [
                'de' => 'rumänisches Recht',
                'en' => 'Romanian law',
            ],
        ],

        // company_id = 3 — JACKCODE - FZCO (UAE)
        3 => [
            'name'    => 'JACKCODE - FZCO',
            'tax_id'  => 'DSO-FZCO-46187',
            'reg_no'  => 'DSO-FZCO-46187',
            'address' => [
                'de' => 'Building A1, Dubai Digital Park, Dubai Silicon Oasis, Dubai, Vereinigte Arabische Emirate',
                'en' => 'Building A1, Dubai Digital Park, Dubai Silicon Oasis, Dubai, United Arab Emirates',
            ],
            'street' => [
                'de' => 'Building A1, Dubai Digital Park, Dubai Silicon Oasis',
                'en' => 'Building A1, Dubai Digital Park, Dubai Silicon Oasis',
            ],
            'city'     => 'Dubai',
            'postcode' => '',
            'country'  => [
                'de' => 'Vereinigte Arabische Emirate',
                'en' => 'United Arab Emirates',
            ],
            'jurisdiction' => [
                'de' => 'die zuständigen Gerichte in Dubai, VAE',
                'en' => 'the competent courts in Dubai, UAE',
            ],
            'tax_label' => [
                'de' => 'Registrierungsnummer',
                'en' => 'Registration Number',
            ],
            'reg_label' => [
                'de' => 'Registrierungsnummer',
                'en' => 'Registration Number',
            ],
            'governing_law' => [
                'de' => 'das Recht der Vereinigten Arabischen Emirate',
                'en' => 'the laws of the United Arab Emirates',
            ],
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Shared contact info (independent of company profile)
    |--------------------------------------------------------------------------
    */
    'email'   => env('COMPANY_EMAIL', env('CONTACT_EMAIL', 'info@sofortpdf.com')),
    'website' => env('COMPANY_WEBSITE', 'sofortpdf.com'),
];
