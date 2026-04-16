@extends('layouts.app')

@section('title', $pageTitle)

@section('content')
@php
    $loc = app()->getLocale();

    // Locale-aware getters with fallback to a string value if the field
    // isn't an array (e.g., simple "Dubai").
    $pick = function ($value) use ($loc) {
        if (is_array($value)) {
            return $value[$loc] ?? ($value['en'] ?? array_values($value)[0] ?? '');
        }
        return $value;
    };

    $companyName    = $pick($company['name'] ?? '');
    $companyAddress = $pick($company['address'] ?? '');
    $companyCountry = $pick($company['country'] ?? '');
    $taxLabel       = $pick($company['tax_label'] ?? '');
    $regLabel       = $pick($company['reg_label'] ?? '');

    $addressLines = array_filter([
        $companyName,
        $pick($company['street'] ?? $company['address'] ?? ''),
        trim(($company['postcode'] ?? '') . ' ' . ($pick($company['city'] ?? ''))),
        $companyCountry,
    ]);
@endphp

<div class="max-w-3xl mx-auto px-4 py-12">
    <h1 class="text-3xl font-bold mb-8">{{ __('legal.impressum_heading') }}</h1>

    <h2 class="text-xl font-semibold mt-8 mb-4">{{ __('legal.impressum_tmg_title') }}</h2>
    <p class="mb-4 leading-relaxed">{!! implode('<br>', array_map('e', $addressLines)) !!}</p>

    <h2 class="text-xl font-semibold mt-8 mb-4">{{ __('legal.impressum_contact_title') }}</h2>
    <p class="mb-4 leading-relaxed">
        {{ __('legal.impressum_contact_email_label') }}
        <a href="mailto:{{ $companyEmail }}" class="text-blue-600 hover:underline">{{ $companyEmail }}</a><br>
        {{ __('legal.impressum_contact_website_label') }}
        <a href="https://{{ $companyWebsite }}" class="text-blue-600 hover:underline">{{ $companyWebsite }}</a>
    </p>

    @if(!empty($company['tax_id']))
        <h2 class="text-xl font-semibold mt-8 mb-4">{{ $taxLabel ?: __('legal.impressum_vat_title') }}</h2>
        <p class="mb-4 leading-relaxed">{{ $company['tax_id'] }}</p>
    @endif

    @if(!empty($company['reg_no']) && ($company['reg_no'] ?? '') !== ($company['tax_id'] ?? ''))
        <h2 class="text-xl font-semibold mt-8 mb-4">{{ $regLabel ?: __('legal.impressum_registration_title') }}</h2>
        <p class="mb-4 leading-relaxed">{{ $company['reg_no'] }}</p>
    @endif

    <h2 class="text-xl font-semibold mt-8 mb-4">{{ __('legal.impressum_dispute_title') }}</h2>
    <p class="mb-4 leading-relaxed">{!! __('legal.impressum_dispute_p1_html') !!}</p>
    <p class="mb-4 leading-relaxed">{{ __('legal.impressum_dispute_p2') }}</p>

    <h2 class="text-xl font-semibold mt-8 mb-4">{{ __('legal.impressum_liability_content_title') }}</h2>
    <p class="mb-4 leading-relaxed">{{ __('legal.impressum_liability_content_p1') }}</p>

    <h2 class="text-xl font-semibold mt-8 mb-4">{{ __('legal.impressum_liability_links_title') }}</h2>
    <p class="mb-4 leading-relaxed">{{ __('legal.impressum_liability_links_p1') }}</p>

    <h2 class="text-xl font-semibold mt-8 mb-4">{{ __('legal.impressum_copyright_title') }}</h2>
    <p class="mb-4 leading-relaxed">{{ __('legal.impressum_copyright_p1', ['company' => $companyName]) }}</p>
</div>
@endsection
