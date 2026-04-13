@extends('layouts.app')

@section('title', $pageTitle)

@section('content')
<div class="max-w-3xl mx-auto px-4 py-12">
    <h1 class="text-3xl font-bold mb-8">{{ __('legal.agb_heading') }}</h1>

    <h2 class="text-xl font-semibold mt-8 mb-4">{{ __('legal.agb_section_1_title') }}</h2>
    <p class="mb-4 leading-relaxed">{{ __('legal.agb_section_1_p1') }}</p>
    <p class="mb-4 leading-relaxed">{{ __('legal.agb_section_1_p2') }}</p>

    <h2 class="text-xl font-semibold mt-8 mb-4">{{ __('legal.agb_section_2_title') }}</h2>
    <p class="mb-4 leading-relaxed">{{ __('legal.agb_section_2_p1') }}</p>

    <h2 class="text-xl font-semibold mt-8 mb-4">{{ __('legal.agb_section_3_title') }}</h2>
    <p class="mb-4 leading-relaxed">{{ __('legal.agb_section_3_p1') }}</p>
    <p class="mb-4 leading-relaxed">{{ __('legal.agb_section_3_p2') }}</p>

    <h2 class="text-xl font-semibold mt-8 mb-4">{{ __('legal.agb_section_4_title') }}</h2>
    <p class="mb-4 leading-relaxed">{{ __('legal.agb_section_4_p1') }}</p>
    <p class="mb-4 leading-relaxed">{{ __('legal.agb_section_4_p2') }}</p>

    <h2 class="text-xl font-semibold mt-8 mb-4">{{ __('legal.agb_section_5_title') }}</h2>
    <p class="mb-4 leading-relaxed">{{ __('legal.agb_section_5_p1') }}</p>
    <p class="mb-4 leading-relaxed">{{ __('legal.agb_section_5_p2') }}</p>
    <p class="mb-4 leading-relaxed">{{ __('legal.agb_section_5_p3') }}</p>

    <h3 class="text-lg font-medium mt-6 mb-3">{{ __('legal.agb_section_5_consequences_title') }}</h3>
    <p class="mb-4 leading-relaxed">{{ __('legal.agb_section_5_consequences_p1') }}</p>

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
    <p class="mb-4 leading-relaxed">{{ __('legal.agb_section_10_p1') }}</p>
    <p class="mb-4 leading-relaxed">{{ __('legal.agb_section_10_p2') }}</p>
</div>
@endsection
