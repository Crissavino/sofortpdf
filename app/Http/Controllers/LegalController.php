<?php

namespace App\Http\Controllers;

class LegalController extends Controller
{
    public function imprint()
    {
        return view('legal.impressum', [
            'pageTitle' => __('legal.impressum_heading'),
            'slug'      => 'impressum',
        ]);
    }

    public function privacy()
    {
        return view('legal.datenschutz', [
            'pageTitle' => __('legal.datenschutz_heading'),
            'slug'      => 'datenschutz',
        ]);
    }

    public function cookiePolicy()
    {
        return view('legal.cookie-policy', [
            'pageTitle' => __('legal.cookies_heading'),
            'slug'      => 'cookie-policy',
        ]);
    }

    public function terms()
    {
        return view('legal.agb', [
            'pageTitle' => __('legal.agb_heading'),
            'slug'      => 'agb',
        ]);
    }
}
