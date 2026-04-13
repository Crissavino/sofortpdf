@extends('layouts.app')

@section('title', $pageTitle)

@section('content')
<div class="max-w-3xl mx-auto px-4 py-12">
    <h1 class="text-3xl font-bold mb-8">{{ __('legal.impressum_heading') }}</h1>

    <h2 class="text-xl font-semibold mt-8 mb-4">{{ __('legal.impressum_tmg_title') }}</h2>
    <p class="mb-4 leading-relaxed">{!! __('legal.impressum_tmg_address_html') !!}</p>

    <h2 class="text-xl font-semibold mt-8 mb-4">{{ __('legal.impressum_represented_title') }}</h2>
    <p class="mb-4 leading-relaxed">{{ __('legal.impressum_represented_text') }}</p>

    <h2 class="text-xl font-semibold mt-8 mb-4">{{ __('legal.impressum_contact_title') }}</h2>
    <p class="mb-4 leading-relaxed">{!! __('legal.impressum_contact_html') !!}</p>

    <h2 class="text-xl font-semibold mt-8 mb-4">{{ __('legal.impressum_register_title') }}</h2>
    <p class="mb-4 leading-relaxed">{!! __('legal.impressum_register_html') !!}</p>

    <h2 class="text-xl font-semibold mt-8 mb-4">{{ __('legal.impressum_vat_title') }}</h2>
    <p class="mb-4 leading-relaxed">{!! __('legal.impressum_vat_html') !!}</p>

    <h2 class="text-xl font-semibold mt-8 mb-4">{{ __('legal.impressum_responsible_title') }}</h2>
    <p class="mb-4 leading-relaxed">{!! __('legal.impressum_responsible_html') !!}</p>

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
