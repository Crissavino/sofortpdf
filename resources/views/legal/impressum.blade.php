@extends('layouts.app')

@section('title', $pageTitle)

@section('content')
@php
    // Render the company postal address as multiline HTML: name, address, postcode + city, country
    $hasCompany = !empty($company['name']);
    $addressLines = array_filter([
        $company['name'] ?? '',
        $company['address'] ?? '',
        trim(($company['postcode'] ?? '') . ' ' . ($company['city'] ?? '')),
        $company['country'] ?? '',
    ]);
@endphp

<div class="max-w-3xl mx-auto px-4 py-12">
    <h1 class="text-3xl font-bold mb-8">{{ __('legal.impressum_heading') }}</h1>

    @if(!$hasCompany)
        <div class="mb-8 p-4 bg-amber-50 border border-amber-200 rounded-lg text-amber-800 text-sm">
            {{ __('legal.impressum_missing_notice', ['email' => $company['email'] ?? 'info@sofortpdf.com']) }}
        </div>
    @endif

    <h2 class="text-xl font-semibold mt-8 mb-4">{{ __('legal.impressum_tmg_title') }}</h2>
    <p class="mb-4 leading-relaxed">{!! implode('<br>', array_map('e', $addressLines)) !!}</p>

    @if(!empty($company['representative']))
        <h2 class="text-xl font-semibold mt-8 mb-4">{{ __('legal.impressum_represented_title') }}</h2>
        <p class="mb-4 leading-relaxed">{{ $company['representative'] }}</p>
    @endif

    <h2 class="text-xl font-semibold mt-8 mb-4">{{ __('legal.impressum_contact_title') }}</h2>
    <p class="mb-4 leading-relaxed">
        @if(!empty($company['phone']))
            {{ __('legal.impressum_contact_phone_label') }} {{ $company['phone'] }}<br>
        @endif
        @if(!empty($company['email']))
            {{ __('legal.impressum_contact_email_label') }} <a href="mailto:{{ $company['email'] }}" class="text-blue-600 hover:underline">{{ $company['email'] }}</a>
        @endif
    </p>

    @if(!empty($company['register_entry']) || !empty($company['register_court']))
        <h2 class="text-xl font-semibold mt-8 mb-4">{{ __('legal.impressum_register_title') }}</h2>
        <p class="mb-4 leading-relaxed">
            @if(!empty($company['register_court']))
                {{ __('legal.impressum_register_court_label') }} {{ $company['register_court'] }}<br>
            @endif
            @if(!empty($company['register_entry']))
                {{ __('legal.impressum_register_entry_label') }} {{ $company['register_entry'] }}
            @endif
        </p>
    @endif

    @if(!empty($company['num_reg_com']))
        <h2 class="text-xl font-semibold mt-8 mb-4">{{ __('legal.impressum_registration_title') }}</h2>
        <p class="mb-4 leading-relaxed">{{ $company['num_reg_com'] }}</p>
    @endif

    @if(!empty($company['vat_number']))
        <h2 class="text-xl font-semibold mt-8 mb-4">{{ __('legal.impressum_vat_title') }}</h2>
        <p class="mb-4 leading-relaxed">{{ __('legal.impressum_vat_intro') }}<br>{{ $company['vat_number'] }}</p>
    @endif

    @if($hasCompany && !empty($company['representative']))
        <h2 class="text-xl font-semibold mt-8 mb-4">{{ __('legal.impressum_responsible_title') }}</h2>
        <p class="mb-4 leading-relaxed">
            {{ $company['representative'] }}<br>
            {!! implode('<br>', array_map('e', array_slice($addressLines, 1))) !!}
        </p>
    @endif

    <h2 class="text-xl font-semibold mt-8 mb-4">{{ __('legal.impressum_dispute_title') }}</h2>
    <p class="mb-4 leading-relaxed">{!! __('legal.impressum_dispute_p1_html') !!}</p>
    <p class="mb-4 leading-relaxed">{{ __('legal.impressum_dispute_p2') }}</p>

    <h2 class="text-xl font-semibold mt-8 mb-4">{{ __('legal.impressum_liability_content_title') }}</h2>
    <p class="mb-4 leading-relaxed">{{ __('legal.impressum_liability_content_p1') }}</p>

    <h2 class="text-xl font-semibold mt-8 mb-4">{{ __('legal.impressum_liability_links_title') }}</h2>
    <p class="mb-4 leading-relaxed">{{ __('legal.impressum_liability_links_p1') }}</p>

    <h2 class="text-xl font-semibold mt-8 mb-4">{{ __('legal.impressum_copyright_title') }}</h2>
    <p class="mb-4 leading-relaxed">{{ __('legal.impressum_copyright_p1') }}</p>
</div>
@endsection
