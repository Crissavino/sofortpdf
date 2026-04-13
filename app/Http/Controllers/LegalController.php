<?php

namespace App\Http\Controllers;

class LegalController extends Controller
{
    public function imprint()
    {
        return view('legal.impressum', [
            'pageTitle' => 'Impressum',
            'slug' => 'impressum',
        ]);
    }

    public function privacy()
    {
        return view('legal.datenschutz', [
            'pageTitle' => 'Datenschutzerklärung',
            'slug' => 'datenschutz',
        ]);
    }

    public function terms()
    {
        return view('legal.agb', [
            'pageTitle' => 'Allgemeine Geschäftsbedingungen',
            'slug' => 'agb',
        ]);
    }
}
