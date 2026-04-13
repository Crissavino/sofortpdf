@extends('layouts.app')

@section('title', __('checkout.success_title'))

@section('content')
<div class="container mx-auto max-w-2xl px-4 py-16">
    <div class="bg-white rounded-lg shadow-lg p-8 text-center">
        <div class="mb-6">
            <svg class="mx-auto h-16 w-16 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
            </svg>
        </div>

        <h1 class="text-3xl font-bold text-gray-900 mb-4">
            {{ __('checkout.success_heading') }}
        </h1>

        <p class="text-lg text-gray-600 mb-6">
            {{ __('checkout.success_trial_info', ['days' => config('services.stripe.trial_days')]) }}
        </p>

        <p class="text-gray-500 mb-8">
            {{ __('checkout.success_renewal_info', ['price' => app()->getLocale() === 'en'
                ? number_format(config('services.stripe.subscription_price'), 2, '.', ',')
                : number_format(config('services.stripe.subscription_price'), 2, ',', '.')]) }}
        </p>

        <a href="{{ route('home') }}"
           class="inline-block bg-blue-600 hover:bg-blue-700 text-white font-semibold py-3 px-8 rounded-lg transition duration-150 ease-in-out">
            {{ __('checkout.success_cta') }}
        </a>
    </div>
</div>

{{-- Google Ads Conversion Tracking --}}
{{-- Uncomment and replace YOUR_CONVERSION_ID and YOUR_CONVERSION_LABEL with actual values --}}
{{--
<script async src="https://www.googletagmanager.com/gtag/js?id=AW-YOUR_CONVERSION_ID"></script>
<script>
    window.dataLayer = window.dataLayer || [];
    function gtag(){dataLayer.push(arguments);}
    gtag('js', new Date());
    gtag('config', 'AW-YOUR_CONVERSION_ID');
    gtag('event', 'conversion', {
        'send_to': 'AW-YOUR_CONVERSION_ID/YOUR_CONVERSION_LABEL',
        'transaction_id': '{{ $sessionId ?? '' }}'
    });
</script>
--}}
@endsection
