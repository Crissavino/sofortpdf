<?php

namespace App\Http\Controllers;

class LegalController extends Controller
{
    public function imprint()
    {
        return view('legal.impressum', [
            'pageTitle' => __('legal.impressum_heading'),
            'slug'      => $this->localizedSlug('imprint', 'impressum'),
        ]);
    }

    public function privacy()
    {
        return view('legal.datenschutz', [
            'pageTitle' => __('legal.datenschutz_heading'),
            'slug'      => $this->localizedSlug('privacy', 'datenschutz'),
        ]);
    }

    public function cookiePolicy()
    {
        return view('legal.cookie-policy', [
            'pageTitle' => __('legal.cookies_heading'),
            'slug'      => $this->localizedSlug('cookies', 'cookie-policy'),
        ]);
    }

    public function terms()
    {
        return view('legal.agb', [
            'pageTitle' => __('legal.agb_heading'),
            'slug'      => $this->localizedSlug('terms', 'agb'),
        ]);
    }

    /**
     * The seo partial builds the canonical as /{locale}/{slug}, so the
     * slug must be the locale's own URL slug — not the German one.
     */
    private function localizedSlug(string $key, string $fallback): string
    {
        $locale = app()->getLocale();

        return config("locales.legal_slugs.{$locale}.{$key}", $fallback);
    }
}
