<?php

namespace App\Http\Controllers;

use App\Services\CompanyResolver;

class LegalController extends Controller
{
    public function imprint()
    {
        return view('legal.impressum', [
            'pageTitle' => __('legal.impressum_heading'),
            'slug'      => 'impressum',
            'company'   => CompanyResolver::current(),
        ]);
    }

    public function privacy()
    {
        return view('legal.datenschutz', [
            'pageTitle' => __('legal.datenschutz_heading'),
            'slug'      => 'datenschutz',
            'company'   => CompanyResolver::current(),
        ]);
    }

    public function terms()
    {
        return view('legal.agb', [
            'pageTitle' => __('legal.agb_heading'),
            'slug'      => 'agb',
            'company'   => CompanyResolver::current(),
        ]);
    }
}
