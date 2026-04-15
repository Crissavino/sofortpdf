<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Contact — admin inbox
    |--------------------------------------------------------------------------
    |
    | Where messages submitted through the contact form land. Falls back to
    | MAIL_FROM_ADDRESS so nothing breaks if CONTACT_EMAIL isn't set.
    |
    */
    'email' => env('CONTACT_EMAIL', env('MAIL_FROM_ADDRESS', 'info@sofortpdf.com')),
];
