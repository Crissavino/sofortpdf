@extends('layouts.app')

@section('title', $pageTitle)

@section('content')
@php
    $loc = app()->getLocale();
    $pick = function ($value) use ($loc) {
        if (is_array($value)) {
            return $value[$loc] ?? ($value['en'] ?? array_values($value)[0] ?? '');
        }
        return $value;
    };

    $companyName    = $pick($company['name'] ?? 'sofortpdf.com');
    $companyAddress = $pick($company['address'] ?? '');
    $companyCountry = $pick($company['country'] ?? '');
    $jurisdiction   = $pick($company['jurisdiction'] ?? '');
    $governingLaw   = $pick($company['governing_law'] ?? '');

    // Pricing tokens for Section 4 — match the format the payment modal
    // already shows ("0,69 €" in non-EN locales, "0.69 €" in EN). Falls
    // back to the marketing defaults when the VAD pricing chain hasn't
    // resolved (e.g., direct hit on /terms without a session).
    $trialNum         = (float) ($pricing['trial']           ?? 0.69);
    $trialMarketing   = (float) ($pricing['trial_marketing'] ?? 2.00);
    $subscriptionNum  = (float) ($pricing['subscription']    ?? 39.90);
    $currencySym      = $pricing['symbol'] ?? '€';
    $trialDays        = (int) config('services.stripe.trial_days', 2);
    $fmt              = function (float $v) use ($loc, $currencySym) {
        $useDot = $loc === 'en';
        return number_format($v, 2, $useDot ? '.' : ',', $useDot ? ',' : '.') . ' ' . $currencySym;
    };

    $tokens = [
        'company'             => $companyName,
        'address'             => $companyAddress ?: 'sofortpdf.com',
        'email'               => $companyEmail,
        'country'             => $companyCountry,
        'jurisdiction'        => $jurisdiction,
        'governing_law'       => $governingLaw,
        'website'             => 'sofortpdf.com',
        'trial_days'          => $trialDays,
        'trial_price'         => $fmt($trialNum),
        'trial_marketing'     => $fmt($trialMarketing),
        'subscription_price' => $fmt($subscriptionNum),
    ];
@endphp
<div class="max-w-3xl mx-auto px-4 py-12">
    <h1 class="text-3xl font-bold mb-8">{{ __('legal.agb_heading') }}</h1>

    <h2 class="text-xl font-semibold mt-8 mb-4">{{ __('legal.agb_section_1_title') }}</h2>
    <p class="mb-4 leading-relaxed">{{ __('legal.agb_section_1_p1', $tokens) }}</p>
    <p class="mb-4 leading-relaxed">{{ __('legal.agb_section_1_p2') }}</p>

    <h2 class="text-xl font-semibold mt-8 mb-4">{{ __('legal.agb_section_2_title') }}</h2>
    <p class="mb-4 leading-relaxed">{{ __('legal.agb_section_2_p1') }}</p>

    <h2 class="text-xl font-semibold mt-8 mb-4">{{ __('legal.agb_section_3_title') }}</h2>
    <p class="mb-4 leading-relaxed">{{ __('legal.agb_section_3_p1') }}</p>
    <p class="mb-4 leading-relaxed">{{ __('legal.agb_section_3_p2') }}</p>

    <h2 class="text-xl font-semibold mt-8 mb-4">{{ __('legal.agb_section_4_title') }}</h2>
    <p class="mb-4 leading-relaxed">{{ __('legal.agb_section_4_p1') }}</p>
    <p class="mb-4 leading-relaxed">{{ __('legal.agb_section_4_p2') }}</p>
    <p class="mb-4 leading-relaxed">{{ __('legal.agb_section_4_p3', $tokens) }}</p>
    <p class="mb-4 leading-relaxed">{{ __('legal.agb_section_4_p4') }}</p>

    <h2 class="text-xl font-semibold mt-8 mb-4">{{ __('legal.agb_section_5_title') }}</h2>
    <p class="mb-4 leading-relaxed">{{ __('legal.agb_section_5_p1') }}</p>
    <p class="mb-4 leading-relaxed">{{ __('legal.agb_section_5_p2', $tokens) }}</p>
    <p class="mb-4 leading-relaxed">{{ __('legal.agb_section_5_p3') }}</p>

    <h3 class="text-lg font-medium mt-6 mb-3">{{ __('legal.agb_section_5_consequences_title') }}</h3>
    <p class="mb-4 leading-relaxed">{{ __('legal.agb_section_5_consequences_p1') }}</p>

    <h2 class="text-xl font-semibold mt-8 mb-4">{{ __('legal.agb_section_5b_title') }}</h2>
    <p class="mb-4 leading-relaxed">{{ __('legal.agb_section_5b_p1') }}</p>
    <p class="mb-4 leading-relaxed">{{ __('legal.agb_section_5b_p2', $tokens) }}</p>
    <p class="mb-4 leading-relaxed">{{ __('legal.agb_section_5b_p3') }}</p>

    <h2 class="text-xl font-semibold mt-8 mb-4">{{ __('legal.agb_section_6_title') }}</h2>
    <p class="mb-4 leading-relaxed">{{ __('legal.agb_section_6_p1') }}</p>

    <h2 class="text-xl font-semibold mt-8 mb-4">{{ __('legal.agb_section_7_title') }}</h2>
    <p class="mb-4 leading-relaxed">{{ __('legal.agb_section_7_p1') }}</p>

    <h2 class="text-xl font-semibold mt-8 mb-4">{{ __('legal.agb_section_8_title') }}</h2>
    <p class="mb-4 leading-relaxed">{{ __('legal.agb_section_8_p1') }}</p>
    <p class="mb-4 leading-relaxed">{{ __('legal.agb_section_8_p2') }}</p>

    <h2 class="text-xl font-semibold mt-8 mb-4">{{ __('legal.agb_section_9_title') }}</h2>
    <p class="mb-4 leading-relaxed">{!! __('legal.agb_section_9_p1_html', ['url' => route('datenschutz')]) !!}</p>

    <h2 class="text-xl font-semibold mt-8 mb-4">{{ __('legal.agb_section_10_title') }}</h2>
    <p class="mb-4 leading-relaxed">{{ __('legal.agb_section_10_p1', $tokens) }}</p>
    <p class="mb-4 leading-relaxed">{{ __('legal.agb_section_10_p2') }}</p>
</div>
@endsection
