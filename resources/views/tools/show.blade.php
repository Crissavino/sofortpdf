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

    /* ── Tool page: entrance stagger ── */
    .tool-stagger {
        opacity: 0;
        transform: translateY(14px) scale(0.98);
        animation: tool-stagger-in 0.5s var(--ease-out-expo) forwards;
        animation-delay: calc(var(--stagger, 0) * 80ms + 80ms);
    }
    @keyframes tool-stagger-in {
        to { opacity: 1; transform: translateY(0) scale(1); }
    }

    /* ── Upload zone idle pulse (halo) ── */
    .upload-zone-pulse {
        position: relative;
    }
    .upload-zone-pulse::before {
        content: '';
        position: absolute;
        inset: -2px;
        border-radius: inherit;
        box-shadow: 0 0 0 0 var(--pulse-color, rgba(59, 108, 245, 0.35));
        animation: upload-pulse 1.6s var(--ease-out-expo) infinite;
        pointer-events: none;
    }
    @keyframes upload-pulse {
        /* The visible expansion occupies 90% of the cycle so the wave
           looks as slow as before (~1.44s) while the interval between
           pulses drops from 2.6s → 1.6s. */
        0%   { box-shadow: 0 0 0 0    var(--pulse-color, rgba(59, 108, 245, 0.35)); }
        90%  { box-shadow: 0 0 0 14px rgba(59, 108, 245, 0); }
        100% { box-shadow: 0 0 0 0    rgba(59, 108, 245, 0); }
    }
    /* Pause pulse on hover, drag, or when a file is already loaded */
    .upload-zone-pulse:hover::before,
    .upload-zone-pulse.drag-active::before,
    .upload-zone-pulse.is-hidden::before {
        animation-play-state: paused;
        box-shadow: none;
    }

    /* ── Upload zone icon: gentle float ── */
    .icon-circle {
        animation: icon-float 2.6s ease-in-out infinite;
    }
    @keyframes icon-float {
        0%, 100% { transform: translateY(0); }
        50%      { transform: translateY(-4px); }
    }
    /* When drag-active, icon scale-up (already set elsewhere) wins via !important */
    .upload-zone.drag-active .icon-circle {
        animation: none;
        transform: scale(1.08);
    }

    /* ── Processing: indeterminate progress bar ── */
    .proc-progress {
        position: relative;
        height: 3px;
        background: rgba(148, 163, 184, 0.15);
        border-radius: 2px;
        overflow: hidden;
    }
    .proc-progress::after {
        content: '';
        position: absolute;
        top: 0;
        left: -40%;
        width: 40%;
        height: 100%;
        background: linear-gradient(90deg, transparent, #3b82f6, #8b5cf6, transparent);
        animation: proc-progress-slide 1.4s var(--ease-out-expo) infinite;
    }
    @keyframes proc-progress-slide {
        0%   { left: -40%; }
        100% { left: 100%; }
    }

    /* ── Merge tool: thumbnail grid ── */
    .merge-grid {
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: 0.75rem;
    }
    @media (min-width: 640px) {
        .merge-grid { grid-template-columns: repeat(3, minmax(0, 1fr)); gap: 1rem; }
    }
    @media (min-width: 768px) {
        .merge-grid { grid-template-columns: repeat(4, minmax(0, 1fr)); }
    }
    .merge-card {
        position: relative;
        background: #fff;
        border: 1px solid rgb(226 232 240);
        border-radius: 14px;
        padding: 12px 12px 10px;
        cursor: grab;
        user-select: none;
        transition: border-color 180ms var(--ease-out-expo),
                    box-shadow   180ms var(--ease-out-expo),
                    transform    180ms var(--ease-out-expo);
    }
    .merge-card:hover {
        border-color: rgb(191 219 254);
        box-shadow: 0 6px 18px -6px rgba(59, 108, 245, 0.18);
    }
    .merge-card:active,
    .merge-card.sortable-chosen { cursor: grabbing; }
    .merge-card.sortable-ghost {
        opacity: 0.35;
        background: #eef4ff;
    }
    .merge-thumb {
        position: relative;
        aspect-ratio: 3 / 4;
        background: #f8fafc;
        border-radius: 10px;
        overflow: hidden;
        display: flex;
        align-items: center;
        justify-content: center;
    }
    .merge-thumb img,
    .merge-thumb canvas {
        max-width: 100%;
        max-height: 100%;
        object-fit: contain;
    }
    .merge-thumb-placeholder {
        display: flex;
        flex-direction: column;
        align-items: center;
        gap: 6px;
        color: #94a3b8;
    }
    .merge-thumb-ext {
        font-family: inherit;
        font-weight: 800;
        font-size: 11px;
        letter-spacing: 0.08em;
        padding: 2px 8px;
        border-radius: 999px;
        background: #e2e8f0;
        color: #475569;
        text-transform: uppercase;
    }
    .merge-thumb-ext.ext-doc,  .merge-thumb-ext.ext-docx { background:#dbeafe; color:#1d4ed8; }
    .merge-thumb-ext.ext-xls,  .merge-thumb-ext.ext-xlsx { background:#d1fae5; color:#047857; }
    .merge-thumb-ext.ext-ppt,  .merge-thumb-ext.ext-pptx { background:#ffedd5; color:#c2410c; }
    .merge-thumb-ext.ext-jpg,  .merge-thumb-ext.ext-jpeg,
    .merge-thumb-ext.ext-png                             { background:#ede9fe; color:#6d28d9; }
    .merge-thumb-ext.ext-pdf                             { background:#fee2e2; color:#b91c1c; }
    .merge-thumb-spinner {
        width: 28px; height: 28px;
        border: 2px solid #cbd5e1;
        border-top-color: #3b6cf5;
        border-radius: 50%;
        animation: spin 0.8s linear infinite;
    }
    @keyframes spin { to { transform: rotate(360deg); } }
    .merge-order-badge {
        position: absolute;
        top: 8px;
        left: 8px;
        background: rgba(15, 23, 42, 0.85);
        color: white;
        font-size: 11px;
        font-weight: 700;
        border-radius: 999px;
        padding: 2px 8px;
        z-index: 2;
        backdrop-filter: blur(4px);
    }
    .merge-remove {
        position: absolute;
        top: 8px;
        right: 8px;
        width: 24px;
        height: 24px;
        border-radius: 50%;
        background: rgba(15, 23, 42, 0.7);
        color: white;
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        z-index: 2;
        transition: background 160ms var(--ease-out-expo);
    }
    .merge-remove:hover { background: rgb(220, 38, 38); }
    .merge-filename {
        margin-top: 10px;
        font-size: 12px;
        color: #334155;
        font-weight: 500;
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
    }
    .merge-filesize {
        font-size: 11px;
        color: #94a3b8;
    }

    /* ── Reduced motion ── */
    @media (prefers-reduced-motion: reduce) {
        *, *::before, *::after {
            animation-duration: 0.01ms !important;
            animation-iteration-count: 1 !important;
            transition-duration: 0.01ms !important;
        }
        .state-enter, .file-card, .check-icon-enter,
        .tool-stagger {
            opacity: 1 !important;
            transform: none !important;
            filter: none !important;
        }
        .processing-card::before,
        .upload-zone-pulse::before,
        .icon-circle,
        .merge-thumb-spinner {
            animation: none !important;
        }
    }
</style>

@if ($tool === 'merge')
    {{-- Merge tool needs PDF.js for PDF thumbnail rendering and SortableJS
         for drag-drop reordering. Both loaded via CDN to avoid a build step. --}}
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.11.174/pdf.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.2/Sortable.min.js"></script>
    <script>
        // cdnjs's pdf.js exposes two globals: window['pdfjs-dist/build/pdf']
        // (CommonJS name) and window.pdfjsLib. Either may be present or
        // partially initialized — guard everything.
        try {
            var __pdfLib = window['pdfjs-dist/build/pdf'] || window.pdfjsLib;
            if (__pdfLib && __pdfLib.GlobalWorkerOptions) {
                __pdfLib.GlobalWorkerOptions.workerSrc =
                    'https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.11.174/pdf.worker.min.js';
            }
        } catch (e) { /* ignore */ }
    </script>
@endif
@endpush

@section('content')
    {{-- ═══════ SECTION 1: HERO + UPLOAD ═══════ --}}
    <section class="relative overflow-hidden">
        <div class="absolute inset-0 bg-gradient-to-b {{ $c['gradient-from'] }} to-white pointer-events-none"></div>

        <div class="relative max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 pt-14 pb-20">
            {{-- Headlines --}}
            <div class="text-center mb-10">
                <h1 class="tool-stagger font-display font-extrabold text-4xl sm:text-5xl text-slate-900 tracking-tight leading-tight" style="--stagger: 0">
                    {{ $h1 }}
                </h1>
                <h2 class="tool-stagger mt-4 text-lg sm:text-xl text-slate-500 max-w-xl mx-auto leading-relaxed" style="--stagger: 1">
                    {{ $h2 }}
                </h2>
            </div>

            {{-- Upload Zone --}}
            <div id="upload-zone"
                 class="upload-zone upload-zone-pulse tool-stagger relative {{ $c['bg'] }} rounded-3xl border-2 border-dashed {{ $c['border'] }} min-h-[260px] flex flex-col items-center justify-center p-8 sm:p-12 text-center cursor-pointer"
                 style="--stagger: 2"
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

                {{-- Tool-specific params (text / number / radio / select inputs
                     driven by config('tools.{tool}.params')). Rendered only if
                     the tool declares any. --}}
                @if (! empty($toolConfig['params'] ?? []))
                    <div id="tool-params" class="mt-6 space-y-4">
                        @foreach ($toolConfig['params'] as $param)
                            @php
                                $inputId = 'tool-param-' . $param['key'];
                                $label = $param['label_key'] ?? null ? __($param['label_key']) : ($param['label'] ?? $param['key']);
                                $placeholder = $param['placeholder_key'] ?? null ? __($param['placeholder_key']) : ($param['placeholder'] ?? '');
                                $hint = $param['hint_key'] ?? null ? __($param['hint_key']) : ($param['hint'] ?? '');
                                $required = ! empty($param['required']);
                                $default = $param['default'] ?? '';
                            @endphp
                            <div class="bg-white rounded-xl border border-slate-100 p-4">
                                <label for="{{ $inputId }}" class="block text-sm font-medium text-slate-700 mb-1.5">
                                    {{ $label }}@if($required)<span class="text-red-500 ml-0.5">{{ __('tool.param_required_suffix') }}</span>@endif
                                </label>
                                @switch($param['type'] ?? 'text')
                                    @case('textarea')
                                        <textarea id="{{ $inputId }}"
                                                  data-tool-param="{{ $param['key'] }}"
                                                  @if(!empty($param['maxlength'])) maxlength="{{ $param['maxlength'] }}" @endif
                                                  @if($required) required @endif
                                                  placeholder="{{ $placeholder }}"
                                                  rows="3"
                                                  class="w-full px-4 py-2.5 border border-slate-200 rounded-lg text-sm text-slate-900 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-brand-600/20 focus:border-brand-600 transition-colors">{{ $default }}</textarea>
                                        @break
                                    @case('select')
                                        <select id="{{ $inputId }}"
                                                data-tool-param="{{ $param['key'] }}"
                                                @if($required) required @endif
                                                class="w-full px-4 py-2.5 border border-slate-200 rounded-lg text-sm text-slate-900 bg-white focus:outline-none focus:ring-2 focus:ring-brand-600/20 focus:border-brand-600 transition-colors">
                                            @foreach($param['options'] ?? [] as $opt)
                                                @php
                                                    $optLabel = isset($opt['label_key']) ? __($opt['label_key']) : ($opt['label'] ?? $opt['value']);
                                                @endphp
                                                <option value="{{ $opt['value'] }}" @if((string)($default ?? '') === (string)$opt['value']) selected @endif>{{ $optLabel }}</option>
                                            @endforeach
                                        </select>
                                        @break
                                    @case('radio-pills')
                                        <div class="flex flex-wrap gap-2">
                                            @foreach($param['options'] ?? [] as $opt)
                                                @php
                                                    $optId = $inputId . '-' . $opt['value'];
                                                    $optLabel = isset($opt['label_key']) ? __($opt['label_key']) : ($opt['label'] ?? $opt['value']);
                                                @endphp
                                                <label for="{{ $optId }}"
                                                       class="radio-pill inline-flex items-center gap-2 px-4 py-2.5 rounded-lg border border-slate-200 text-sm text-slate-700 cursor-pointer hover:border-brand-300 hover:bg-brand-50 transition-colors">
                                                    <input type="radio"
                                                           id="{{ $optId }}"
                                                           name="{{ $inputId }}"
                                                           value="{{ $opt['value'] }}"
                                                           data-tool-param="{{ $param['key'] }}"
                                                           @if($required && $loop->first) required @endif
                                                           @if((string)($default ?? '') === (string)$opt['value']) checked @endif
                                                           class="w-4 h-4 text-brand-600 border-slate-300 focus:ring-brand-600/20">
                                                    <span>{{ $optLabel }}</span>
                                                </label>
                                            @endforeach
                                        </div>
                                        @break
                                    @default
                                        <input type="{{ $param['type'] ?? 'text' }}"
                                               id="{{ $inputId }}"
                                               data-tool-param="{{ $param['key'] }}"
                                               @if(!empty($param['maxlength'])) maxlength="{{ $param['maxlength'] }}" @endif
                                               @if(!empty($param['autocomplete'])) autocomplete="{{ $param['autocomplete'] }}" @endif
                                               @if($required) required @endif
                                               placeholder="{{ $placeholder }}"
                                               value="{{ $default }}"
                                               class="w-full px-4 py-2.5 border border-slate-200 rounded-lg text-sm text-slate-900 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-brand-600/20 focus:border-brand-600 transition-colors">
                                @endswitch
                                @if($hint)
                                    <p class="mt-1.5 text-xs text-slate-400">{{ $hint }}</p>
                                @endif
                            </div>
                        @endforeach
                    </div>
                @endif

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
                        <div class="proc-progress mt-6 max-w-xs mx-auto"></div>
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
            <div class="tool-stagger mt-10" style="--stagger: 3">
                @include('partials.trust-signals')
            </div>
        </div>
    </section>

    {{-- ═══════ SECTION 6a: HOW IT WORKS ═══════ --}}
    <section class="bg-white py-16 sm:py-20">
        <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">
            <h3 class="observe-animate font-display font-extrabold text-2xl sm:text-3xl text-slate-900 text-center mb-12">
                {{ __('tool.how_heading') }}
            </h3>
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-8 sm:gap-6 relative">
                {{-- Step 1 --}}
                <div class="observe-animate relative text-center" data-delay="0">
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
                <div class="observe-animate relative text-center" data-delay="120">
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
                <div class="observe-animate text-center" data-delay="240">
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
            <h3 class="observe-animate font-display font-extrabold text-2xl sm:text-3xl text-slate-900 text-center mb-10">
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
                    <details class="observe-animate group faq-item bg-white rounded-2xl border border-slate-100 overflow-hidden" data-delay="{{ $loop->index * 60 }}">
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
            <h3 class="observe-animate font-display font-extrabold text-2xl sm:text-3xl text-slate-900 text-center mb-10">
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
                       class="observe-animate group bg-white rounded-2xl border border-slate-100 p-5 {{ $relColor['border'] }} transition-all duration-200"
                       data-delay="{{ $loop->index * 70 }}"
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

@php
    $jsMessages = [
        'onlyOneFile'      => __('tool.js_only_one_file'),
        'maxFiles'         => __('tool.js_max_files'),
        'fileTooLarge'     => __('tool.js_file_too_large'),
        'addAnotherFile'   => __('tool.js_add_another'),
        'processing'       => __('tool.processing'),
        'uploadFailed'     => __('tool.js_upload_failed'),
        'conversionFailed' => __('tool.js_conversion_failed'),
        'genericError'     => __('tool.error_generic'),
        'paramRequired'    => __('tool.param_required_error'),
    ];
@endphp
@push('scripts')
<script>
(function() {
    const __t = @json($jsMessages);
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

    // Merge tool renders a draggable thumbnail grid instead of the flat list.
    const mergeMode = toolKey === 'merge';
    let mergeSortable = null;
    const previewCache = new Map(); // fileKey -> dataURL/objectURL (so we don't re-render on each reorder)

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
            if (mergeSortable) { mergeSortable.destroy(); mergeSortable = null; }
            previewCache.clear();
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

        if (mergeMode) {
            renderMergeGrid();
        } else {
            renderLinearList();
        }

        // Add more files button (shared)
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
    }

    /* ── Linear list renderer (all tools except merge) ── */
    function renderLinearList() {
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

        // Stagger file cards in
        var cards = fileList.querySelectorAll('.file-card');
        cards.forEach(function(card, i) {
            setTimeout(function() {
                card.classList.add('visible');
            }, 50 * i + 50);
        });
    }

    /* ── Merge grid renderer ── */
    function renderMergeGrid() {
        // Rebuild from scratch every time. Previews are cached per file key
        // so reorders/additions don't re-render existing PDFs.
        fileList.classList.add('merge-grid');
        fileList.innerHTML = '';

        selectedFiles.forEach(function(f, idx) {
            const fileKey = fileIdentity(f);
            const card = document.createElement('div');
            card.className = 'merge-card';
            card.setAttribute('data-index', idx);
            card.setAttribute('data-key', fileKey);

            const ext = (f.name.split('.').pop() || '').toLowerCase();

            card.innerHTML =
                '<div class="merge-order-badge">' + (idx + 1) + '</div>' +
                '<button type="button" class="merge-remove" aria-label="Remove" onmousedown="event.stopPropagation()" onclick="event.stopPropagation(); removeFile(' + idx + ')">' +
                    '<svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><path d="M18 6L6 18"/><path d="M6 6l12 12"/></svg>' +
                '</button>' +
                '<div class="merge-thumb" data-thumb>' +
                    '<div class="merge-thumb-placeholder"><div class="merge-thumb-spinner"></div></div>' +
                '</div>' +
                '<p class="merge-filename" title="' + escapeHtml(f.name) + '">' + escapeHtml(f.name) + '</p>' +
                '<p class="merge-filesize">' + formatSize(f.size) + '</p>';

            fileList.appendChild(card);

            const thumbEl = card.querySelector('[data-thumb]');
            if (previewCache.has(fileKey)) {
                thumbEl.innerHTML = previewCache.get(fileKey);
            } else {
                buildThumbnail(f, ext).then(function(html) {
                    previewCache.set(fileKey, html);
                    // Only replace if the card is still attached to the DOM
                    if (thumbEl.isConnected) thumbEl.innerHTML = html;
                });
            }
        });

        // (Re)initialize SortableJS
        if (mergeSortable) { mergeSortable.destroy(); }
        if (typeof Sortable !== 'undefined') {
            mergeSortable = Sortable.create(fileList, {
                animation: 180,
                easing: 'cubic-bezier(0.23, 1, 0.32, 1)',
                ghostClass: 'sortable-ghost',
                chosenClass: 'sortable-chosen',
                onEnd: updateMergeOrderBadges,
            });
        }

        updateMergeOrderBadges();
    }

    function updateMergeOrderBadges() {
        if (!fileList) return;
        fileList.querySelectorAll('.merge-card').forEach(function(card, i) {
            const badge = card.querySelector('.merge-order-badge');
            if (badge) badge.textContent = (i + 1);
        });
    }

    // A stable-ish key for a File. Two File objects with identical
    // (name, size, lastModified) share a preview.
    function fileIdentity(f) {
        return f.name + '|' + f.size + '|' + (f.lastModified || 0);
    }

    function escapeHtml(s) {
        return String(s).replace(/[&<>"']/g, function(c) {
            return ({ '&':'&amp;', '<':'&lt;', '>':'&gt;', '"':'&quot;', "'":'&#39;' })[c];
        });
    }

    async function buildThumbnail(file, ext) {
        if (ext === 'pdf') {
            try {
                return await renderPdfThumbnail(file);
            } catch (e) {
                console.error('PDF thumbnail failed', e);
                return placeholderThumbnail('pdf');
            }
        }
        if (['jpg', 'jpeg', 'png', 'gif', 'webp', 'bmp'].includes(ext)) {
            return '<img src="' + URL.createObjectURL(file) + '" alt="">';
        }
        return placeholderThumbnail(ext);
    }

    function placeholderThumbnail(ext) {
        return '<div class="merge-thumb-placeholder">' +
                '<svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/></svg>' +
                '<span class="merge-thumb-ext ext-' + ext + '">' + (ext || 'file') + '</span>' +
            '</div>';
    }

    async function renderPdfThumbnail(file) {
        const pdfjs = window['pdfjs-dist/build/pdf'] || window.pdfjsLib;
        if (!pdfjs || !pdfjs.getDocument) throw new Error('pdfjs not loaded');

        const arrayBuffer = await file.arrayBuffer();
        const pdf = await pdfjs.getDocument({ data: arrayBuffer }).promise;
        const page = await pdf.getPage(1);

        // Target a reasonable thumbnail height (~240px at 2x device pixel ratio)
        const unscaled = page.getViewport({ scale: 1 });
        const targetHeight = 240;
        const scale = targetHeight / unscaled.height;
        const viewport = page.getViewport({ scale: scale * (window.devicePixelRatio || 1) });

        const canvas = document.createElement('canvas');
        canvas.width = viewport.width;
        canvas.height = viewport.height;
        canvas.style.width = '100%';
        canvas.style.height = 'auto';
        const ctx = canvas.getContext('2d');
        await page.render({ canvasContext: ctx, viewport }).promise;
        return canvas.outerHTML;
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

        // Paywall gate — open the payment modal instead of redirecting,
        // so the already-selected files + drag order stay intact. We
        // resolve the current sort order here (same logic as below, but
        // run before the upload so the modal sees the final array).
        if (window.sofortpdfPaywall && window.sofortpdfPaywall.needsPayment()
            && window.SofortpdfPaymentModal) {
            var modalFiles = selectedFiles.slice();
            if (mergeMode) {
                var cards = fileList.querySelectorAll('.merge-card');
                if (cards.length === selectedFiles.length) {
                    modalFiles = Array.from(cards).map(function(card) {
                        return selectedFiles[parseInt(card.getAttribute('data-index'), 10)];
                    }).filter(Boolean);
                }
            }
            window.SofortpdfPaymentModal.open({
                files: modalFiles,
                onSuccess: function() {
                    // Retry the conversion. The flag `__sofortpdfTrialJustPaid`
                    // is read below when the confirmation_url is built.
                    processBtn.click();
                },
            });
            // Unlock the button so the user can close the modal and try again.
            processBtn.disabled = false;
            btnText.textContent = '{{ $actionLabel }}';
            btnSpinner.classList.add('hidden');
            btnArrow.classList.remove('hidden');
            return;
        }

        processBtn.disabled = true;
        btnText.textContent = __t.processing;
        btnSpinner.classList.remove('hidden');
        btnArrow.classList.add('hidden');

        // For merge: respect the user's drag-drop order (DOM order) before upload.
        var filesInSubmitOrder = selectedFiles;
        if (mergeMode) {
            var cards = fileList.querySelectorAll('.merge-card');
            if (cards.length === selectedFiles.length) {
                filesInSubmitOrder = Array.from(cards).map(function(card) {
                    return selectedFiles[parseInt(card.getAttribute('data-index'), 10)];
                }).filter(Boolean);
            }
        }

        // Collect tool-specific params (rendered from config.tools.<key>.params)
        var toolParams = {};
        var missingRequired = false;
        document.querySelectorAll('[data-tool-param]').forEach(function(input) {
            // For radio groups, only the checked option contributes.
            if (input.type === 'radio' && !input.checked) return;
            var k = input.getAttribute('data-tool-param');
            var v = (input.value || '').trim();
            if (input.required && !v) {
                missingRequired = true;
                input.classList.add('border-red-400', 'ring-2', 'ring-red-100');
            } else {
                input.classList.remove('border-red-400', 'ring-2', 'ring-red-100');
            }
            if (v !== '') toolParams[k] = v;
        });
        if (missingRequired) {
            showError(__t.paramRequired);
            processBtn.disabled = false;
            btnText.textContent = '{{ $actionLabel }}';
            btnSpinner.classList.add('hidden');
            btnArrow.classList.remove('hidden');
            return;
        }

        var formData = new FormData();
        filesInSubmitOrder.forEach(function(f) { formData.append('files[]', f); });
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
                body: JSON.stringify(Object.assign({
                    tool: toolKey,
                    file_ids: uploadData.file_ids,
                }, toolParams)),
            });

            if (!convertRes.ok) {
                var errConv = await convertRes.json();
                throw new Error(errConv.message || __t.conversionFailed);
            }

            var result = await convertRes.json();

            // Redirect to the confirmation page. The page will trigger
            // the download itself and offer a re-download button + a link
            // to the user's account.
            if (result.confirmation_url) {
                var confUrl = result.confirmation_url;
                // First-payment marker (base64 of "paymentSuccess"). Appended
                // only when the user just completed the trial payment in
                // the payment modal.
                if (window.__sofortpdfTrialJustPaid) {
                    confUrl += (confUrl.indexOf('?') >= 0 ? '&' : '?') + 'cGF5bWVudFN1Y2Nlc3M=';
                    try { delete window.__sofortpdfTrialJustPaid; } catch (e) { window.__sofortpdfTrialJustPaid = false; }
                }
                window.location.href = confUrl;
                return;
            }

            // Fallback: if no confirmation_url was returned (older API or
            // future tool), use the inline download state we already have.
            processingState.classList.add('hidden');
            downloadState.classList.remove('hidden');
            document.getElementById('download-link').href = result.download_url;

            refreshIcons();

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
