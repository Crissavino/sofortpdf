@extends('layouts.app')

@section('title', $pageTitle)

@section('content')
<div class="max-w-3xl mx-auto px-4 py-12">
    <h1 class="text-3xl font-bold mb-8">{{ __('legal.cookies_heading') }}</h1>

    <h2 class="text-xl font-semibold mt-8 mb-4">{{ __('legal.cookies_what_title') }}</h2>
    <p class="mb-4 leading-relaxed">{{ __('legal.cookies_what_p1') }}</p>

    <h2 class="text-xl font-semibold mt-8 mb-4">{{ __('legal.cookies_types_title') }}</h2>

    <h3 class="text-lg font-medium mt-6 mb-3">{{ __('legal.cookies_necessary_title') }}</h3>
    <p class="mb-4 leading-relaxed">{{ __('legal.cookies_necessary_p1') }}</p>

    <h3 class="text-lg font-medium mt-6 mb-3">{{ __('legal.cookies_analytics_title') }}</h3>
    <p class="mb-4 leading-relaxed">{{ __('legal.cookies_analytics_p1') }}</p>

    <h3 class="text-lg font-medium mt-6 mb-3">{{ __('legal.cookies_marketing_title') }}</h3>
    <p class="mb-4 leading-relaxed">{{ __('legal.cookies_marketing_p1') }}</p>

    <h2 class="text-xl font-semibold mt-8 mb-4">{{ __('legal.cookies_tools_title') }}</h2>

    <h3 class="text-lg font-medium mt-6 mb-3">Google Analytics (GA4)</h3>
    <p class="mb-4 leading-relaxed">{{ __('legal.cookies_ga4_p1') }}</p>

    <h3 class="text-lg font-medium mt-6 mb-3">Google Tag Manager (GTM)</h3>
    <p class="mb-4 leading-relaxed">{{ __('legal.cookies_gtm_p1') }}</p>

    <h3 class="text-lg font-medium mt-6 mb-3">Stripe</h3>
    <p class="mb-4 leading-relaxed">{{ __('legal.cookies_stripe_p1') }}</p>

    <h3 class="text-lg font-medium mt-6 mb-3">Cookiebot</h3>
    <p class="mb-4 leading-relaxed">{{ __('legal.cookies_cookiebot_p1') }}</p>

    <h2 class="text-xl font-semibold mt-8 mb-4">{{ __('legal.cookies_manage_title') }}</h2>
    <p class="mb-4 leading-relaxed">{!! __('legal.cookies_manage_p1') !!}</p>

    <h2 class="text-xl font-semibold mt-8 mb-4">{{ __('legal.cookies_changes_title') }}</h2>
    <p class="mb-4 leading-relaxed">{{ __('legal.cookies_changes_p1') }}</p>
</div>
@endsection
