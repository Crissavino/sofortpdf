@extends('layouts.app')

@php
    $colors = [
        'merge' => ['bg' => 'bg-blue-50', 'bg-light' => 'bg-blue-50/30', 'border' => 'border-blue-200', 'active' => 'border-blue-400', 'icon-bg' => 'bg-blue-100', 'icon' => 'text-blue-600', 'btn' => 'bg-blue-600 hover:bg-blue-700', 'ring' => 'ring-blue-200', 'gradient-from' => 'from-blue-50/40', 'dot' => 'bg-blue-500', 'step-bg' => 'bg-blue-50', 'step-icon' => 'text-blue-500', 'step-label' => 'text-blue-600'],
        'compress' => ['bg' => 'bg-amber-50', 'bg-light' => 'bg-amber-50/30', 'border' => 'border-amber-200', 'active' => 'border-amber-400', 'icon-bg' => 'bg-amber-100', 'icon' => 'text-amber-600', 'btn' => 'bg-amber-600 hover:bg-amber-700', 'ring' => 'ring-amber-200', 'gradient-from' => 'from-amber-50/40', 'dot' => 'bg-amber-500', 'step-bg' => 'bg-amber-50', 'step-icon' => 'text-amber-500', 'step-label' => 'text-amber-600'],
        'jpg-to-pdf' => ['bg' => 'bg-emerald-50', 'bg-light' => 'bg-emerald-50/30', 'border' => 'border-emerald-200', 'active' => 'border-emerald-400', 'icon-bg' => 'bg-emerald-100', 'icon' => 'text-emerald-600', 'btn' => 'bg-emerald-600 hover:bg-emerald-700', 'ring' => 'ring-emerald-200', 'gradient-from' => 'from-emerald-50/40', 'dot' => 'bg-emerald-500', 'step-bg' => 'bg-emerald-50', 'step-icon' => 'text-emerald-500', 'step-label' => 'text-emerald-600'],
        'pdf-to-word' => ['bg' => 'bg-indigo-50', 'bg-light' => 'bg-indigo-50/30', 'border' => 'border-indigo-200', 'active' => 'border-indigo-400', 'icon-bg' => 'bg-indigo-100', 'icon' => 'text-indigo-600', 'btn' => 'bg-indigo-600 hover:bg-indigo-700', 'ring' => 'ring-indigo-200', 'gradient-from' => 'from-indigo-50/40', 'dot' => 'bg-indigo-500', 'step-bg' => 'bg-indigo-50', 'step-icon' => 'text-indigo-500', 'step-label' => 'text-indigo-600'],
        'word-to-pdf' => ['bg' => 'bg-indigo-50', 'bg-light' => 'bg-indigo-50/30', 'border' => 'border-indigo-200', 'active' => 'border-indigo-400', 'icon-bg' => 'bg-indigo-100', 'icon' => 'text-indigo-600', 'btn' => 'bg-indigo-600 hover:bg-indigo-700', 'ring' => 'ring-indigo-200', 'gradient-from' => 'from-indigo-50/40', 'dot' => 'bg-indigo-500', 'step-bg' => 'bg-indigo-50', 'step-icon' => 'text-indigo-500', 'step-label' => 'text-indigo-600'],
        'pdf-to-jpg' => ['bg' => 'bg-emerald-50', 'bg-light' => 'bg-emerald-50/30', 'border' => 'border-emerald-200', 'active' => 'border-emerald-400', 'icon-bg' => 'bg-emerald-100', 'icon' => 'text-emerald-600', 'btn' => 'bg-emerald-600 hover:bg-emerald-700', 'ring' => 'ring-emerald-200', 'gradient-from' => 'from-emerald-50/40', 'dot' => 'bg-emerald-500', 'step-bg' => 'bg-emerald-50', 'step-icon' => 'text-emerald-500', 'step-label' => 'text-emerald-600'],
        'split' => ['bg' => 'bg-violet-50', 'bg-light' => 'bg-violet-50/30', 'border' => 'border-violet-200', 'active' => 'border-violet-400', 'icon-bg' => 'bg-violet-100', 'icon' => 'text-violet-600', 'btn' => 'bg-violet-600 hover:bg-violet-700', 'ring' => 'ring-violet-200', 'gradient-from' => 'from-violet-50/40', 'dot' => 'bg-violet-500', 'step-bg' => 'bg-violet-50', 'step-icon' => 'text-violet-500', 'step-label' => 'text-violet-600'],
        'edit' => ['bg' => 'bg-orange-50', 'bg-light' => 'bg-orange-50/30', 'border' => 'border-orange-200', 'active' => 'border-orange-400', 'icon-bg' => 'bg-orange-100', 'icon' => 'text-orange-600', 'btn' => 'bg-orange-600 hover:bg-orange-700', 'ring' => 'ring-orange-200', 'gradient-from' => 'from-orange-50/40', 'dot' => 'bg-orange-500', 'step-bg' => 'bg-orange-50', 'step-icon' => 'text-orange-500', 'step-label' => 'text-orange-600'],
        'sign' => ['bg' => 'bg-rose-50', 'bg-light' => 'bg-rose-50/30', 'border' => 'border-rose-200', 'active' => 'border-rose-400', 'icon-bg' => 'bg-rose-100', 'icon' => 'text-rose-600', 'btn' => 'bg-rose-600 hover:bg-rose-700', 'ring' => 'ring-rose-200', 'gradient-from' => 'from-rose-50/40', 'dot' => 'bg-rose-500', 'step-bg' => 'bg-rose-50', 'step-icon' => 'text-rose-500', 'step-label' => 'text-rose-600'],
        'pdf-to-excel' => ['bg' => 'bg-green-50', 'bg-light' => 'bg-green-50/30', 'border' => 'border-green-200', 'active' => 'border-green-400', 'icon-bg' => 'bg-green-100', 'icon' => 'text-green-600', 'btn' => 'bg-green-600 hover:bg-green-700', 'ring' => 'ring-green-200', 'gradient-from' => 'from-green-50/40', 'dot' => 'bg-green-500', 'step-bg' => 'bg-green-50', 'step-icon' => 'text-green-500', 'step-label' => 'text-green-600'],
        'excel-to-pdf' => ['bg' => 'bg-green-50', 'bg-light' => 'bg-green-50/30', 'border' => 'border-green-200', 'active' => 'border-green-400', 'icon-bg' => 'bg-green-100', 'icon' => 'text-green-600', 'btn' => 'bg-green-600 hover:bg-green-700', 'ring' => 'ring-green-200', 'gradient-from' => 'from-green-50/40', 'dot' => 'bg-green-500', 'step-bg' => 'bg-green-50', 'step-icon' => 'text-green-500', 'step-label' => 'text-green-600'],
    ];
    $c = $colors[$tool] ?? $colors['merge'];
@endphp

@push('head')
<style>
    /* ── Custom easing ── */
    :root {
        --ease-out-expo: cubic-bezier(0.23, 1, 0.32, 1);
        --ease-spring: cubic-bezier(0.34, 1.56, 0.64, 1);
    }

    /* ── Upload zone transitions ── */
    .upload-zone {
        transition: border-color 200ms var(--ease-out-expo),
                    background-color 200ms var(--ease-out-expo),
                    transform 200ms var(--ease-out-expo),
                    opacity 200ms var(--ease-out-expo),
                    box-shadow 200ms var(--ease-out-expo);
    }
    .upload-zone.drag-active {
        border-style: solid !important;
    }
    .upload-zone .icon-circle {
        transition: transform 200ms var(--ease-out-expo),
                    background-color 200ms var(--ease-out-expo);
    }
    .upload-zone.drag-active .icon-circle {
        transform: scale(1.08);
    }

    /* ── Hover gating (fine pointer only) ── */
    @media (hover: hover) and (pointer: fine) {
        .upload-zone:hover {
            box-shadow: 0 0 0 4px var(--zone-ring-color, rgba(59,130,246,0.1));
        }
        .btn-action:hover { transform: translateY(-1px); }
        .file-card:hover { box-shadow: 0 2px 8px rgba(0,0,0,0.06); }
        .faq-item:hover { background-color: rgb(248 250 252); }
    }

    /* ── Button active states ── */
    .btn-action:active,
    .btn-download:active,
    .btn-remove:active,
    .btn-add-more:active {
        transform: scale(0.97) !important;
        transition: transform 160ms var(--ease-out-expo);
    }
    .btn-remove:active {
        transform: scale(0.90) !important;
    }

    /* ── State transitions ── */
    .state-enter {
        opacity: 0;
        transform: scale(0.96);
        filter: blur(2px);
        transition: opacity 250ms var(--ease-out-expo),
                    transform 250ms var(--ease-out-expo),
                    filter 250ms var(--ease-out-expo);
    }
    .state-enter.visible {
        opacity: 1;
        transform: scale(1);
        filter: blur(0);
    }
    .state-exit {
        opacity: 0;
        transform: scale(0.98);
        filter: blur(2px);
        transition: opacity 200ms var(--ease-out-expo),
                    transform 200ms var(--ease-out-expo),
                    filter 200ms var(--ease-out-expo);
    }

    /* ── File card stagger ── */
    .file-card {
        opacity: 0;
        transform: translateY(8px) scale(0.98);
        transition: opacity 250ms var(--ease-out-expo),
                    transform 250ms var(--ease-out-expo),
                    box-shadow 200ms var(--ease-out-expo);
    }
    .file-card.visible {
        opacity: 1;
        transform: translateY(0) scale(1);
    }

    /* ── Processing: rotating conic gradient border ── */
    @property --angle {
        syntax: "<angle>";
        initial-value: 0deg;
        inherits: false;
    }
    .processing-card {
        position: relative;
        background: white;
        border-radius: 1.5rem;
        overflow: hidden;
    }
    .processing-card::before {
        content: '';
        position: absolute;
        inset: 0;
        border-radius: 1.5rem;
        padding: 2px;
        background: conic-gradient(from var(--angle), #3b82f6, #8b5cf6, #ec4899, #3b82f6);
        -webkit-mask: linear-gradient(#fff 0 0) content-box, linear-gradient(#fff 0 0);
        -webkit-mask-composite: xor;
        mask-composite: exclude;
        animation: rotateBorder 2s linear infinite;
    }
    @keyframes rotateBorder {
        to { --angle: 360deg; }
    }

    /* ── Processing dots ── */
    .proc-dot {
        width: 10px;
        height: 10px;
        border-radius: 50%;
        background-color: #94a3b8;
        animation: dotPulse 1.2s var(--ease-out-expo) infinite;
    }
    .proc-dot:nth-child(2) { animation-delay: 150ms; }
    .proc-dot:nth-child(3) { animation-delay: 300ms; }
    @keyframes dotPulse {
        0%, 100% { transform: scale(1); opacity: 0.4; }
        50% { transform: scale(1.4); opacity: 1; }
    }

    /* ── Processing text pulse ── */
    .proc-text {
        animation: textPulse 2s ease-in-out infinite;
    }
    @keyframes textPulse {
        0%, 100% { opacity: 1; }
        50% { opacity: 0.5; }
    }

    /* ── Download check entry ── */
    .check-icon-enter {
        opacity: 0;
        transform: scale(0.5);
        transition: opacity 400ms var(--ease-spring),
                    transform 400ms var(--ease-spring);
    }
    .check-icon-enter.visible {
        opacity: 1;
        transform: scale(1);
    }

    /* ── Error shake ── */
    .shake {
        animation: shake 400ms var(--ease-out-expo);
    }
    @keyframes shake {
        0% { transform: translateX(0); }
        15% { transform: translateX(-4px); }
        30% { transform: translateX(4px); }
        50% { transform: translateX(-2px); }
        70% { transform: translateX(2px); }
        100% { transform: translateX(0); }
    }

    /* ── FAQ ── */
    details summary::-webkit-details-marker { display: none; }
    details summary::marker { display: none; }
    .faq-item {
        transition: background-color 200ms var(--ease-out-expo);
    }
    .faq-chevron {
        transition: transform 200ms var(--ease-out-expo);
    }
    details[open] .faq-chevron {
        transform: rotate(180deg);
    }
    .faq-content {
        display: grid;
        grid-template-rows: 0fr;
        transition: grid-template-rows 250ms var(--ease-out-expo);
    }
    details[open] .faq-content {
        grid-template-rows: 1fr;
    }
    .faq-content > div {
        overflow: hidden;
    }

    /* ── How it works: dashed connector ── */
    .step-connector {
        display: none;
    }
    @media (min-width: 640px) {
        .step-connector {
            display: block;
            position: absolute;
            top: 28px;
            left: calc(50% + 36px);
            width: calc(100% - 72px);
            border-top: 2px dashed #e2e8f0;
        }
    }

    /* ── Reduced motion ── */
    @media (prefers-reduced-motion: reduce) {
        *, *::before, *::after {
            animation-duration: 0.01ms !important;
            animation-iteration-count: 1 !important;
            transition-duration: 0.01ms !important;
        }
        .state-enter, .file-card, .check-icon-enter {
            opacity: 1 !important;
            transform: none !important;
            filter: none !important;
        }
        .processing-card::before {
            animation: none;
        }
    }
</style>
@endpush

@section('content')
    {{-- ═══════ SECTION 1: HERO + UPLOAD ═══════ --}}
    <section class="relative overflow-hidden">
        <div class="absolute inset-0 bg-gradient-to-b {{ $c['gradient-from'] }} to-white pointer-events-none"></div>

        <div class="relative max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 pt-14 pb-20">
            {{-- Headlines --}}
            <div class="text-center mb-10">
                <h1 class="font-display font-extrabold text-4xl sm:text-5xl text-slate-900 tracking-tight leading-tight">
                    {{ $h1 }}
                </h1>
                <h2 class="mt-4 text-lg sm:text-xl text-slate-500 max-w-xl mx-auto leading-relaxed">
                    {{ $h2 }}
                </h2>
            </div>

            {{-- Upload Zone --}}
            <div id="upload-zone"
                 class="upload-zone relative {{ $c['bg'] }} rounded-3xl border-2 border-dashed {{ $c['border'] }} min-h-[260px] flex flex-col items-center justify-center p-8 sm:p-12 text-center cursor-pointer"
                 style="--zone-ring-color: var(--tw-ring-color)"
                 data-accept="{{ $accept }}"
                 data-multiple="{{ $multiple ? 'true' : 'false' }}"
                 data-max-files="{{ $maxFiles }}"
                 data-tool="{{ $tool }}"
                 data-max-size="{{ env('MAX_UPLOAD_SIZE_MB', 50) }}"
                 data-border-active="{{ $c['active'] }}"
                 data-bg-active="{{ $c['bg'] }}">

                {{-- Icon circle --}}
                <div class="icon-circle w-20 h-20 rounded-full {{ $c['icon-bg'] }} flex items-center justify-center mx-auto mb-5">
                    @include('partials.tool-icon', ['icon' => $toolConfig['icon'] ?? 'default', 'size' => 'w-8 h-8'])
                </div>

                <p class="font-display font-bold text-lg text-slate-700 mb-2">
                    {{ __('tool.drop_or_click') }}
                </p>
                <p class="text-sm text-slate-400">
                    {{ __('tool.formats_label') }} {{ str_replace('.', '', str_replace(',', ', ', $accept)) }} &middot; Max. {{ env('MAX_UPLOAD_SIZE_MB', 50) }} MB
                    @if($multiple) &middot; {{ __('tool.up_to_files', ['n' => $maxFiles]) }} @endif
                </p>

                <input type="file" id="file-input" class="hidden" accept="{{ $accept }}" {{ $multiple ? 'multiple' : '' }}>
            </div>

            {{-- ═══════ SECTION 2: FILE LIST ═══════ --}}
            <div id="file-list-wrapper" class="hidden">
                <div id="file-list" class="mt-6 space-y-3"></div>

                {{-- Add more files --}}
                <div id="add-more-area" class="hidden mt-3"></div>

                {{-- Action button --}}
                <div id="action-area" class="mt-8">
                    <button id="process-btn"
                            class="btn-action w-full inline-flex items-center justify-center gap-2.5 {{ $c['btn'] }} text-white font-display font-bold px-8 py-4 rounded-2xl shadow-lg transition-all duration-200 text-base disabled:opacity-50 disabled:cursor-not-allowed"
                            style="transition: transform 160ms cubic-bezier(0.23,1,0.32,1), background-color 200ms ease-out, box-shadow 200ms ease-out, opacity 200ms ease-out;">
                        <span id="btn-text">{{ $actionLabel }}</span>
                        <i data-lucide="arrow-right" id="btn-arrow" class="w-5 h-5 transition-transform duration-200"></i>
                        <span id="btn-spinner" class="hidden">
                            <svg class="animate-spin w-5 h-5" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                            </svg>
                        </span>
                    </button>
                </div>
            </div>

            {{-- ═══════ SECTION 3: PROCESSING STATE ═══════ --}}
            <div id="processing-state" class="hidden mt-8">
                <div class="processing-card shadow-lg">
                    <div class="relative bg-white rounded-3xl p-10 text-center" style="margin:2px; border-radius: calc(1.5rem - 2px);">
                        <div class="flex items-center justify-center gap-2.5 mb-6">
                            <div class="proc-dot"></div>
                            <div class="proc-dot"></div>
                            <div class="proc-dot"></div>
                        </div>
                        <p class="proc-text font-display font-bold text-xl text-slate-700">{{ __('tool.processing') }}</p>
                        <p class="text-sm text-slate-400 mt-2">{{ __('tool.please_wait') }}</p>
                    </div>
                </div>
            </div>

            {{-- ═══════ SECTION 4: DOWNLOAD STATE ═══════ --}}
            <div id="download-state" class="hidden mt-8">
                <div class="state-enter bg-white rounded-3xl overflow-hidden shadow-lg shadow-emerald-100/50" id="download-card">
                    <div class="h-[3px] bg-gradient-to-r from-emerald-400 via-emerald-500 to-teal-500 rounded-t-3xl"></div>
                    <div class="p-10 text-center">
                        <div class="check-icon-enter w-16 h-16 rounded-full bg-emerald-50 flex items-center justify-center mx-auto mb-5" id="check-icon">
                            <i data-lucide="check" class="w-8 h-8 text-emerald-500"></i>
                        </div>
                        <p class="font-display font-extrabold text-2xl text-slate-800 mb-2">{{ __('tool.done') }}</p>
                        <p class="text-slate-500 mb-6">{{ __('tool.ready_for_download') }}</p>
                        <a id="download-link" href="#"
                           class="btn-download inline-flex items-center justify-center gap-2.5 bg-emerald-500 hover:bg-emerald-600 text-white font-display font-bold px-10 py-4 rounded-2xl shadow-lg shadow-emerald-500/25 hover:shadow-emerald-500/40 text-base"
                           style="transition: transform 160ms cubic-bezier(0.23,1,0.32,1), background-color 200ms ease-out, box-shadow 200ms ease-out;">
                            <i data-lucide="download" class="w-5 h-5"></i>
                            {{ __('tool.download') }}
                        </a>
                        <div class="mt-5">
                            <button onclick="resetUpload()" class="text-sm text-slate-400 hover:text-slate-600 underline underline-offset-2"
                                    style="transition: color 200ms ease-out;">
                                {{ __('tool.process_another') }}
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            {{-- ═══════ SECTION 5: ERROR STATE ═══════ --}}
            <div id="error-state" class="hidden mt-6">
                <div id="error-card" class="bg-red-50 border border-red-100 rounded-2xl p-6 text-center">
                    <div class="w-12 h-12 rounded-full bg-red-100 flex items-center justify-center mx-auto mb-3">
                        <i data-lucide="alert-circle" class="w-6 h-6 text-red-500"></i>
                    </div>
                    <p class="text-sm text-red-600 font-medium" id="error-message">{{ __('tool.error_generic') }}</p>
                    <button onclick="resetUpload()" class="text-sm text-red-500 hover:text-red-700 underline underline-offset-2 mt-3"
                            style="transition: color 200ms ease-out;">
                        {{ __('tool.try_again') }}
                    </button>
                </div>
            </div>

            {{-- Trust signals --}}
            <div class="mt-10">
                @include('partials.trust-signals')
            </div>
        </div>
    </section>

    {{-- ═══════ SECTION 6a: HOW IT WORKS ═══════ --}}
    <section class="bg-white py-16 sm:py-20">
        <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">
            <h3 class="font-display font-extrabold text-2xl sm:text-3xl text-slate-900 text-center mb-12">
                {{ __('tool.how_heading') }}
            </h3>
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-8 sm:gap-6 relative">
                {{-- Step 1 --}}
                <div class="relative text-center">
                    <div class="w-14 h-14 rounded-2xl {{ $c['step-bg'] }} flex items-center justify-center mx-auto mb-4">
                        <i data-lucide="upload-cloud" class="w-7 h-7 {{ $c['step-icon'] }}"></i>
                    </div>
                    <div class="font-display font-bold text-xs {{ $c['step-label'] }} uppercase tracking-wider mb-2">{{ __('tool.step_label', ['n' => 1]) }}</div>
                    <p class="font-display font-bold text-slate-800 mb-1">{{ __('tool.step1_title') }}</p>
                    <p class="text-sm text-slate-400 leading-relaxed">
                        {{ __('tool.step1_desc') }}
                    </p>
                    <div class="step-connector" aria-hidden="true"></div>
                </div>
                {{-- Step 2 --}}
                <div class="relative text-center">
                    <div class="w-14 h-14 rounded-2xl {{ $c['step-bg'] }} flex items-center justify-center mx-auto mb-4">
                        <i data-lucide="zap" class="w-7 h-7 {{ $c['step-icon'] }}"></i>
                    </div>
                    <div class="font-display font-bold text-xs {{ $c['step-label'] }} uppercase tracking-wider mb-2">{{ __('tool.step_label', ['n' => 2]) }}</div>
                    <p class="font-display font-bold text-slate-800 mb-1">{{ __('tool.step2_title') }}</p>
                    <p class="text-sm text-slate-400 leading-relaxed">
                        {{ __('tool.step2_desc') }}
                    </p>
                    <div class="step-connector" aria-hidden="true"></div>
                </div>
                {{-- Step 3 --}}
                <div class="text-center">
                    <div class="w-14 h-14 rounded-2xl {{ $c['step-bg'] }} flex items-center justify-center mx-auto mb-4">
                        <i data-lucide="download" class="w-7 h-7 {{ $c['step-icon'] }}"></i>
                    </div>
                    <div class="font-display font-bold text-xs {{ $c['step-label'] }} uppercase tracking-wider mb-2">{{ __('tool.step_label', ['n' => 3]) }}</div>
                    <p class="font-display font-bold text-slate-800 mb-1">{{ __('tool.step3_title') }}</p>
                    <p class="text-sm text-slate-400 leading-relaxed">
                        {{ __('tool.step3_desc') }}
                    </p>
                </div>
            </div>
        </div>
    </section>

    {{-- ═══════ SECTION 6b: TRUST SIGNALS (below the fold) ═══════ --}}

    {{-- ═══════ SECTION 6c: FAQ ═══════ --}}
    <section class="bg-slate-50 py-16 sm:py-20">
        <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">
            <h3 class="font-display font-extrabold text-2xl sm:text-3xl text-slate-900 text-center mb-10">
                {{ __('tool.faq_heading') }}
            </h3>
            <div class="space-y-3">
                @php
                    $formatsList = str_replace('.', '', str_replace(',', ', ', $accept));
                    $maxSize = env('MAX_UPLOAD_SIZE_MB', 50);
                    $faqs = [
                        ['q' => __('tool.faq_secure_q'),  'a' => __('tool.faq_secure_a')],
                        ['q' => __('tool.faq_formats_q'), 'a' => __('tool.faq_formats_a', ['formats' => $formatsList])],
                        ['q' => __('tool.faq_size_q'),   'a' => __('tool.faq_size_a', ['size' => $maxSize])],
                        ['q' => __('tool.faq_mobile_q'), 'a' => __('tool.faq_mobile_a')],
                    ];
                @endphp
                @foreach($faqs as $faq)
                    <details class="group faq-item bg-white rounded-2xl border border-slate-100 overflow-hidden">
                        <summary class="flex items-center justify-between cursor-pointer px-6 py-5 font-display font-bold text-slate-800 select-none list-none">
                            <span>{!! $faq['q'] !!}</span>
                            <i data-lucide="chevron-down" class="faq-chevron w-5 h-5 text-slate-400 flex-shrink-0 ml-4"></i>
                        </summary>
                        <div class="faq-content">
                            <div>
                                <div class="px-6 pb-5 text-sm text-slate-500 leading-relaxed">
                                    {!! $faq['a'] !!}
                                </div>
                            </div>
                        </div>
                    </details>
                @endforeach
            </div>
        </div>
    </section>

    {{-- ═══════ SECTION 7: RELATED TOOLS ═══════ --}}
    <section class="bg-white py-16 sm:py-20">
        <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">
            <h3 class="font-display font-extrabold text-2xl sm:text-3xl text-slate-900 text-center mb-10">
                {{ __('tool.related_heading') }}
            </h3>

            @php
                $relatedMap = [
                    'merge' => ['split', 'compress', 'pdf-to-word', 'rotate'],
                    'compress' => ['merge', 'pdf-to-jpg', 'split', 'jpg-to-pdf'],
                    'jpg-to-pdf' => ['pdf-to-jpg', 'merge', 'compress', 'word-to-pdf'],
                    'pdf-to-word' => ['word-to-pdf', 'pdf-to-excel', 'merge', 'compress'],
                    'word-to-pdf' => ['pdf-to-word', 'excel-to-pdf', 'merge', 'compress'],
                    'pdf-to-jpg' => ['jpg-to-pdf', 'compress', 'split', 'merge'],
                    'split' => ['merge', 'compress', 'pdf-to-word', 'rotate'],
                    'edit' => ['merge', 'split', 'sign', 'compress'],
                    'sign' => ['edit', 'merge', 'compress', 'split'],
                    'pdf-to-excel' => ['excel-to-pdf', 'pdf-to-word', 'merge', 'compress'],
                    'excel-to-pdf' => ['pdf-to-excel', 'word-to-pdf', 'merge', 'compress'],
                    'rotate' => ['merge', 'split', 'compress', 'pdf-to-word'],
                    'protect' => ['sign', 'merge', 'compress', 'split'],
                ];

                $allTools = config('tools');
                $relatedKeys = $relatedMap[$tool] ?? [];

                // Filter to only enabled tools
                $relatedTools = collect($relatedKeys)
                    ->filter(fn($key) => isset($allTools[$key]) && ($allTools[$key]['enabled'] ?? false) && $key !== $tool)
                    ->take(4)
                    ->values();

                // Fallback: first 4 other enabled tools
                if ($relatedTools->count() < 4) {
                    $fallback = collect($allTools)
                        ->filter(fn($cfg, $key) => ($cfg['enabled'] ?? false) && $key !== $tool && !$relatedTools->contains($key))
                        ->keys()
                        ->take(4 - $relatedTools->count());
                    $relatedTools = $relatedTools->merge($fallback);
                }

                $toolColorMap = [
                    'merge'    => ['bg' => 'bg-blue-50',    'icon' => 'text-blue-500',    'border' => 'hover:border-blue-200'],
                    'compress' => ['bg' => 'bg-amber-50',   'icon' => 'text-amber-500',   'border' => 'hover:border-amber-200'],
                    'image'    => ['bg' => 'bg-emerald-50', 'icon' => 'text-emerald-500', 'border' => 'hover:border-emerald-200'],
                    'word'     => ['bg' => 'bg-indigo-50',  'icon' => 'text-indigo-500',  'border' => 'hover:border-indigo-200'],
                    'split'    => ['bg' => 'bg-violet-50',  'icon' => 'text-violet-500',  'border' => 'hover:border-violet-200'],
                    'edit'     => ['bg' => 'bg-orange-50',  'icon' => 'text-orange-500',  'border' => 'hover:border-orange-200'],
                    'sign'     => ['bg' => 'bg-rose-50',    'icon' => 'text-rose-500',    'border' => 'hover:border-rose-200'],
                    'excel'    => ['bg' => 'bg-green-50',   'icon' => 'text-green-600',   'border' => 'hover:border-green-200'],
                ];
                $defaultToolColor = ['bg' => 'bg-slate-50', 'icon' => 'text-slate-500', 'border' => 'hover:border-slate-200'];
            @endphp

            <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
                @foreach($relatedTools as $relKey)
                    @php
                        $relConfig = $allTools[$relKey];
                        $relColor = $toolColorMap[$relConfig['icon']] ?? $defaultToolColor;
                        $relTitle = \App\Services\LocaleHelper::toolTitle($relKey);
                        $relUrl = \App\Services\LocaleHelper::toolUrl($relKey);
                        $relLocale = app()->getLocale();
                        $relDesc = $relLocale === 'de'
                            ? $relConfig['description']
                            : config("tools_{$relLocale}.{$relKey}.description", $relConfig['description']);
                    @endphp
                    <a href="{{ $relUrl }}"
                       class="group bg-white rounded-2xl border border-slate-100 p-5 {{ $relColor['border'] }} transition-all duration-200"
                       style="transition-timing-function: cubic-bezier(0.23, 1, 0.32, 1);">
                        <div class="w-10 h-10 rounded-xl {{ $relColor['bg'] }} flex items-center justify-center mb-3">
                            @include('partials.tool-icon', ['icon' => $relConfig['icon'], 'size' => 'w-5 h-5'])
                        </div>
                        <h4 class="font-display font-bold text-sm text-slate-900 mb-1 group-hover:text-brand-600 transition-colors duration-200">{{ $relTitle }}</h4>
                        <p class="text-xs text-slate-400 leading-relaxed line-clamp-2">{{ $relDesc }}</p>
                    </a>
                @endforeach
            </div>
        </div>
    </section>
@endsection

@push('scripts')
<script>
(function() {
    const __t = @json([
        'onlyOneFile'      => __('tool.js_only_one_file'),
        'maxFiles'         => __('tool.js_max_files'),
        'fileTooLarge'     => __('tool.js_file_too_large'),
        'addAnotherFile'   => __('tool.js_add_another'),
        'processing'       => __('tool.processing'),
        'uploadFailed'     => __('tool.js_upload_failed'),
        'conversionFailed' => __('tool.js_conversion_failed'),
        'genericError'     => __('tool.error_generic'),
    ]);
    const zone = document.getElementById('upload-zone');
    const fileInput = document.getElementById('file-input');
    const fileListWrapper = document.getElementById('file-list-wrapper');
    const fileList = document.getElementById('file-list');
    const addMoreArea = document.getElementById('add-more-area');
    const actionArea = document.getElementById('action-area');
    const processBtn = document.getElementById('process-btn');
    const processingState = document.getElementById('processing-state');
    const downloadState = document.getElementById('download-state');
    const errorState = document.getElementById('error-state');
    const btnText = document.getElementById('btn-text');
    const btnSpinner = document.getElementById('btn-spinner');
    const btnArrow = document.getElementById('btn-arrow');

    const toolKey = zone.dataset.tool;
    const acceptTypes = zone.dataset.accept;
    const allowMultiple = zone.dataset.multiple === 'true';
    const maxFiles = parseInt(zone.dataset.maxFiles);
    const maxSizeMB = parseInt(zone.dataset.maxSize);
    const borderActiveClass = zone.dataset.borderActive;

    let selectedFiles = [];

    /* ── Helpers ── */
    function refreshIcons() {
        if (typeof lucide !== 'undefined') lucide.createIcons();
    }

    function formatSize(bytes) {
        if (bytes < 1024) return bytes + ' B';
        if (bytes < 1024 * 1024) return (bytes / 1024).toFixed(1) + ' KB';
        return (bytes / 1024 / 1024).toFixed(1) + ' MB';
    }

    /* ── Click to upload ── */
    zone.addEventListener('click', function() { fileInput.click(); });

    /* ── Drag & drop ── */
    let dragCounter = 0;

    zone.addEventListener('dragenter', function(e) {
        e.preventDefault();
        dragCounter++;
        zone.classList.add('drag-active', borderActiveClass);
    });

    zone.addEventListener('dragover', function(e) {
        e.preventDefault();
    });

    zone.addEventListener('dragleave', function(e) {
        e.preventDefault();
        dragCounter--;
        if (dragCounter <= 0) {
            dragCounter = 0;
            zone.classList.remove('drag-active', borderActiveClass);
        }
    });

    zone.addEventListener('drop', function(e) {
        e.preventDefault();
        dragCounter = 0;
        zone.classList.remove('drag-active', borderActiveClass);
        handleFiles(e.dataTransfer.files);
    });

    fileInput.addEventListener('change', function() { handleFiles(fileInput.files); });

    /* ── Handle files ── */
    function handleFiles(files) {
        var arr = Array.from(files);

        if (!allowMultiple && arr.length > 1) {
            showError(__t.onlyOneFile);
            return;
        }
        if (allowMultiple && (selectedFiles.length + arr.length) > maxFiles) {
            showError(__t.maxFiles.replace('{n}', maxFiles));
            return;
        }

        for (var i = 0; i < arr.length; i++) {
            if (arr[i].size > maxSizeMB * 1024 * 1024) {
                showError(__t.fileTooLarge.replace('{name}', arr[i].name).replace('{size}', maxSizeMB));
                return;
            }
        }

        if (allowMultiple) {
            selectedFiles = selectedFiles.concat(arr);
        } else {
            selectedFiles = arr;
        }

        renderFileList();
        errorState.classList.add('hidden');
    }

    /* ── Render file list with stagger ── */
    function renderFileList() {
        if (selectedFiles.length === 0) {
            fileListWrapper.classList.add('hidden');
            zone.classList.remove('hidden', 'state-exit');
            zone.style.opacity = '';
            zone.style.transform = '';
            return;
        }

        // Fade out upload zone
        zone.classList.add('state-exit');
        setTimeout(function() {
            zone.classList.add('hidden');
            zone.classList.remove('state-exit');
        }, 200);

        // Show file list
        fileListWrapper.classList.remove('hidden');

        fileList.innerHTML = selectedFiles.map(function(f, idx) {
            return '<div class="file-card flex items-center justify-between bg-white rounded-xl px-5 py-4 border border-slate-100 shadow-sm" data-index="' + idx + '">' +
                '<div class="flex items-center gap-3.5 min-w-0">' +
                    '<div class="w-10 h-10 rounded-xl {{ $c["icon-bg"] }} flex items-center justify-center flex-shrink-0">' +
                        '<i data-lucide="file-text" class="w-5 h-5 {{ $c["icon"] }}"></i>' +
                    '</div>' +
                    '<div class="min-w-0">' +
                        '<p class="text-sm font-medium text-slate-700 truncate">' + f.name + '</p>' +
                        '<p class="text-xs text-slate-400">' + formatSize(f.size) + '</p>' +
                    '</div>' +
                '</div>' +
                '<button onclick="removeFile(' + idx + ')" class="btn-remove w-8 h-8 rounded-lg flex items-center justify-center text-slate-300 hover:text-red-500 hover:bg-red-50 flex-shrink-0 ml-3" style="transition: color 200ms ease-out, background-color 200ms ease-out, transform 160ms cubic-bezier(0.23,1,0.32,1);">' +
                    '<i data-lucide="x" class="w-4 h-4"></i>' +
                '</button>' +
            '</div>';
        }).join('');

        // Add more files button
        if (allowMultiple && selectedFiles.length < maxFiles) {
            addMoreArea.classList.remove('hidden');
            addMoreArea.innerHTML =
                '<button onclick="document.getElementById(\'file-input\').click()" class="btn-add-more w-full flex items-center justify-center gap-2 bg-white rounded-xl px-5 py-4 border border-dashed border-slate-200 hover:border-slate-300 text-sm text-slate-400 hover:text-slate-600" style="transition: border-color 200ms ease-out, color 200ms ease-out, transform 160ms cubic-bezier(0.23,1,0.32,1);">' +
                    '<i data-lucide="plus" class="w-4 h-4"></i>' +
                    __t.addAnotherFile +
                '</button>';
        } else {
            addMoreArea.classList.add('hidden');
            addMoreArea.innerHTML = '';
        }

        refreshIcons();

        // Stagger file cards in
        var cards = fileList.querySelectorAll('.file-card');
        cards.forEach(function(card, i) {
            setTimeout(function() {
                card.classList.add('visible');
            }, 50 * i + 50);
        });
    }

    /* ── Remove file ── */
    window.removeFile = function(index) {
        var card = fileList.querySelector('[data-index="' + index + '"]');
        if (card) {
            card.style.opacity = '0';
            card.style.transform = 'scale(0.96)';
            setTimeout(function() {
                selectedFiles.splice(index, 1);
                renderFileList();
            }, 200);
        } else {
            selectedFiles.splice(index, 1);
            renderFileList();
        }
    };

    /* ── Reset upload ── */
    window.resetUpload = function() {
        selectedFiles = [];
        fileInput.value = '';
        fileListWrapper.classList.add('hidden');
        processingState.classList.add('hidden');
        downloadState.classList.add('hidden');
        errorState.classList.add('hidden');
        zone.classList.remove('hidden', 'state-exit');
        zone.style.opacity = '';
        zone.style.transform = '';
        processBtn.disabled = false;
        btnText.textContent = '{{ $actionLabel }}';
        btnSpinner.classList.add('hidden');
        btnArrow.classList.remove('hidden');
        refreshIcons();
    };

    /* ── Process button ── */
    processBtn.addEventListener('click', async function() {
        if (selectedFiles.length === 0) return;

        @guest
            window.location.href = '/checkout/start?return_to=' + encodeURIComponent(window.location.pathname);
            return;
        @endguest

        processBtn.disabled = true;
        btnText.textContent = __t.processing;
        btnSpinner.classList.remove('hidden');
        btnArrow.classList.add('hidden');

        var formData = new FormData();
        selectedFiles.forEach(function(f) { formData.append('files[]', f); });
        formData.append('tool', toolKey);

        try {
            var uploadRes = await fetch('/api/upload', {
                method: 'POST',
                headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content },
                body: formData,
            });

            if (uploadRes.status === 403) {
                window.location.href = '/checkout/start?return_to=' + encodeURIComponent(window.location.pathname);
                return;
            }

            if (!uploadRes.ok) {
                var errData = await uploadRes.json();
                throw new Error(errData.message || __t.uploadFailed);
            }

            var uploadData = await uploadRes.json();

            // Switch to processing state
            fileListWrapper.classList.add('hidden');
            processingState.classList.remove('hidden');

            var convertRes = await fetch('/api/convert', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                },
                body: JSON.stringify({
                    tool: toolKey,
                    file_ids: uploadData.file_ids,
                }),
            });

            if (!convertRes.ok) {
                var errConv = await convertRes.json();
                throw new Error(errConv.message || __t.conversionFailed);
            }

            var result = await convertRes.json();

            // Switch to download state
            processingState.classList.add('hidden');
            downloadState.classList.remove('hidden');
            document.getElementById('download-link').href = result.download_url;

            refreshIcons();

            // Animate download card entry
            var downloadCard = document.getElementById('download-card');
            var checkIcon = document.getElementById('check-icon');
            requestAnimationFrame(function() {
                downloadCard.classList.add('visible');
                setTimeout(function() {
                    checkIcon.classList.add('visible');
                }, 150);
            });

        } catch (err) {
            showError(err.message || __t.genericError);
        }
    });

    /* ── Show error ── */
    function showError(message) {
        processingState.classList.add('hidden');
        fileListWrapper.classList.remove('hidden');
        errorState.classList.remove('hidden');
        document.getElementById('error-message').textContent = message;

        // Shake animation
        var errorCard = document.getElementById('error-card');
        errorCard.classList.remove('shake');
        void errorCard.offsetWidth; // force reflow
        errorCard.classList.add('shake');

        processBtn.disabled = false;
        btnText.textContent = '{{ $actionLabel }}';
        btnSpinner.classList.add('hidden');
        btnArrow.classList.remove('hidden');
        refreshIcons();
    }
})();
</script>
@endpush
