@extends('layouts.app')

@section('title', $pageTitle)

@section('content')
<div class="max-w-3xl mx-auto px-4 py-12">
    <h1 class="text-3xl font-bold mb-8">{{ __('legal.datenschutz_heading') }}</h1>

    <h2 class="text-xl font-semibold mt-8 mb-4">{{ __('legal.datenschutz_section_1_title') }}</h2>
    <p class="mb-4 leading-relaxed">{!! __('legal.datenschutz_section_1_html') !!}</p>

    <h2 class="text-xl font-semibold mt-8 mb-4">{{ __('legal.datenschutz_section_2_title') }}</h2>

    <h3 class="text-lg font-medium mt-6 mb-3">{{ __('legal.datenschutz_section_2a_title') }}</h3>
    <p class="mb-4 leading-relaxed">{{ __('legal.datenschutz_section_2a_p1') }}</p>
    <ul class="list-disc list-inside mb-4 leading-relaxed">
        <li>{{ __('legal.datenschutz_section_2a_li1') }}</li>
        <li>{{ __('legal.datenschutz_section_2a_li2') }}</li>
        <li>{{ __('legal.datenschutz_section_2a_li3') }}</li>
        <li>{{ __('legal.datenschutz_section_2a_li4') }}</li>
        <li>{{ __('legal.datenschutz_section_2a_li5') }}</li>
        <li>{{ __('legal.datenschutz_section_2a_li6') }}</li>
    </ul>
    <p class="mb-4 leading-relaxed">{{ __('legal.datenschutz_section_2a_p2') }}</p>

    <h3 class="text-lg font-medium mt-6 mb-3">{{ __('legal.datenschutz_section_2b_title') }}</h3>
    <p class="mb-4 leading-relaxed">{{ __('legal.datenschutz_section_2b_p1') }}</p>

    <h2 class="text-xl font-semibold mt-8 mb-4">{{ __('legal.datenschutz_section_3_title') }}</h2>
    <p class="mb-4 leading-relaxed">{{ __('legal.datenschutz_section_3_p1') }}</p>
    <p class="mb-4 leading-relaxed">{{ __('legal.datenschutz_section_3_p2') }}</p>

    <h2 class="text-xl font-semibold mt-8 mb-4">{{ __('legal.datenschutz_section_4_title') }}</h2>
    <p class="mb-4 leading-relaxed">{{ __('legal.datenschutz_section_4_p1') }}</p>
    <p class="mb-4 leading-relaxed">{!! __('legal.datenschutz_section_4_p2_html') !!}</p>

    <h2 class="text-xl font-semibold mt-8 mb-4">{{ __('legal.datenschutz_section_5_title') }}</h2>
    <p class="mb-4 leading-relaxed">{{ __('legal.datenschutz_section_5_p1') }}</p>

    <h2 class="text-xl font-semibold mt-8 mb-4">{{ __('legal.datenschutz_section_6_title') }}</h2>
    <p class="mb-4 leading-relaxed">{{ __('legal.datenschutz_section_6_p1') }}</p>

    <h2 class="text-xl font-semibold mt-8 mb-4">{{ __('legal.datenschutz_section_7_title') }}</h2>
    <p class="mb-4 leading-relaxed">{{ __('legal.datenschutz_section_7_p1') }}</p>
    <ul class="list-disc list-inside mb-4 leading-relaxed">
        <li>{{ __('legal.datenschutz_section_7_li1') }}</li>
        <li>{{ __('legal.datenschutz_section_7_li2') }}</li>
        <li>{{ __('legal.datenschutz_section_7_li3') }}</li>
        <li>{{ __('legal.datenschutz_section_7_li4') }}</li>
        <li>{{ __('legal.datenschutz_section_7_li5') }}</li>
        <li>{{ __('legal.datenschutz_section_7_li6') }}</li>
    </ul>
    <p class="mb-4 leading-relaxed">{{ __('legal.datenschutz_section_7_p2') }}</p>

    <h2 class="text-xl font-semibold mt-8 mb-4">{{ __('legal.datenschutz_section_8_title') }}</h2>
    <p class="mb-4 leading-relaxed">{{ __('legal.datenschutz_section_8_p1') }}</p>
</div>
@endsection
