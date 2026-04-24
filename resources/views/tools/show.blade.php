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

    /* ── Page picker (remove-pages / extract-pages) ── */
    .page-picker {
        margin-top: 1.5rem;
    }
    .page-picker-header {
        display: flex;
        align-items: flex-start;
        justify-content: space-between;
        flex-wrap: wrap;
        gap: 12px;
        margin-bottom: 14px;
    }
    .page-picker-heading {
        font-family: 'Cabinet Grotesk', system-ui, sans-serif;
        font-weight: 700;
        font-size: 15px;
        color: #0f172a;
    }
    .page-picker-sub {
        font-size: 12px;
        color: #64748b;
        margin-top: 2px;
    }
    .page-picker-actions {
        display: inline-flex;
        gap: 6px;
    }
    .page-picker-btn {
        display: inline-flex; align-items: center; gap: 4px;
        padding: 6px 10px;
        border: 1px solid rgb(226 232 240);
        border-radius: 8px;
        background: #fff;
        font-size: 11px; font-weight: 600; color: #475569;
        cursor: pointer;
        transition: all 160ms ease-out;
    }
    .page-picker-btn:hover { background: #f8fafc; border-color: #cbd5e1; }
    .page-picker-count {
        font-size: 12px;
        color: #64748b;
        font-weight: 500;
    }
    .page-picker-grid {
        display: grid;
        grid-template-columns: repeat(3, minmax(0, 1fr));
        gap: 10px;
    }
    @media (min-width: 640px) {
        .page-picker-grid { grid-template-columns: repeat(4, minmax(0, 1fr)); gap: 12px; }
    }
    @media (min-width: 768px) {
        .page-picker-grid { grid-template-columns: repeat(5, minmax(0, 1fr)); }
    }
    .page-picker-loading {
        padding: 30px;
        text-align: center;
        color: #94a3b8;
        font-size: 13px;
    }
    .page-picker-card {
        position: relative;
        aspect-ratio: 3 / 4;
        background: #fff;
        border: 2px solid rgb(226 232 240);
        border-radius: 10px;
        padding: 4px;
        cursor: pointer;
        user-select: none;
        transition: all 180ms var(--ease-out-expo);
        overflow: hidden;
    }
    .page-picker-card:hover {
        border-color: rgb(148 163 184);
        transform: translateY(-1px);
    }
    .page-picker-card .page-thumb {
        width: 100%; height: 100%;
        display: flex; align-items: center; justify-content: center;
        background: #f8fafc;
        border-radius: 6px;
        overflow: hidden;
    }
    .page-picker-card .page-thumb img {
        max-width: 100%; max-height: 100%;
        object-fit: contain;
    }
    .page-picker-card .page-thumb-loading {
        width: 22px; height: 22px;
        border: 2px solid #cbd5e1;
        border-top-color: #3b6cf5;
        border-radius: 50%;
        animation: spin 0.8s linear infinite;
    }
    .page-picker-card .page-number {
        position: absolute;
        bottom: 6px;
        left: 50%;
        transform: translateX(-50%);
        background: rgba(15, 23, 42, 0.85);
        color: #fff;
        font-size: 10px;
        font-weight: 700;
        padding: 2px 8px;
        border-radius: 999px;
        z-index: 3;
    }
    .page-picker-card .page-mark {
        position: absolute;
        top: 6px;
        right: 6px;
        width: 22px;
        height: 22px;
        border-radius: 50%;
        background: #fff;
        border: 2px solid #cbd5e1;
        display: flex; align-items: center; justify-content: center;
        z-index: 3;
        transition: all 180ms var(--ease-out-expo);
    }
    .page-picker-card .page-mark svg { opacity: 0; transition: opacity 140ms ease-out; }

    /* Remove mode — selected pages get a red X and crossed-out overlay */
    .page-picker[data-mode="remove"] .page-picker-card.is-selected {
        border-color: #ef4444;
        background: #fef2f2;
    }
    .page-picker[data-mode="remove"] .page-picker-card.is-selected .page-mark {
        background: #ef4444;
        border-color: #ef4444;
        color: #fff;
    }
    .page-picker[data-mode="remove"] .page-picker-card.is-selected .page-mark svg { opacity: 1; }
    .page-picker[data-mode="remove"] .page-picker-card.is-selected .page-thumb {
        opacity: 0.35;
    }
    .page-picker[data-mode="remove"] .page-picker-card.is-selected::after {
        content: '';
        position: absolute;
        inset: 4px;
        background: repeating-linear-gradient(
            -45deg,
            rgba(239, 68, 68, 0.08) 0px,
            rgba(239, 68, 68, 0.08) 4px,
            transparent 4px,
            transparent 8px
        );
        border-radius: 6px;
        pointer-events: none;
    }

    /* Extract mode — selected pages get a green check + brand highlight */
    .page-picker[data-mode="extract"] .page-picker-card.is-selected {
        border-color: #10b981;
        background: #ecfdf5;
        box-shadow: 0 0 0 3px rgba(16, 185, 129, 0.15);
    }
    .page-picker[data-mode="extract"] .page-picker-card.is-selected .page-mark {
        background: #10b981;
        border-color: #10b981;
        color: #fff;
    }
    .page-picker[data-mode="extract"] .page-picker-card.is-selected .page-mark svg { opacity: 1; }

    /* Rotate mode — thumbnail rotates with the chosen angle, badge shows
       the number of degrees. The rotation badge appears at top-right when
       any rotation has been applied (≠ 0). */
    .page-picker[data-mode="rotate"] .page-picker-card .page-mark { display: none; }
    .page-picker[data-mode="rotate"] .page-picker-card .page-rotate-badge {
        position: absolute;
        top: 6px;
        right: 6px;
        background: #fff;
        border: 1.5px solid #cbd5e1;
        color: #475569;
        font-size: 10px;
        font-weight: 700;
        padding: 2px 7px;
        border-radius: 999px;
        z-index: 3;
        opacity: 0;
        transition: opacity 160ms ease-out;
    }
    .page-picker[data-mode="rotate"] .page-picker-card[data-angle="90"] .page-rotate-badge,
    .page-picker[data-mode="rotate"] .page-picker-card[data-angle="180"] .page-rotate-badge,
    .page-picker[data-mode="rotate"] .page-picker-card[data-angle="270"] .page-rotate-badge {
        opacity: 1;
    }
    .page-picker[data-mode="rotate"] .page-picker-card[data-angle="90"] .page-rotate-badge,
    .page-picker[data-mode="rotate"] .page-picker-card[data-angle="180"] .page-rotate-badge,
    .page-picker[data-mode="rotate"] .page-picker-card[data-angle="270"] .page-rotate-badge {
        background: #3b6cf5;
        border-color: #3b6cf5;
        color: #fff;
    }
    .page-picker[data-mode="rotate"] .page-picker-card[data-angle="90"]  { border-color: #93b4fd; }
    .page-picker[data-mode="rotate"] .page-picker-card[data-angle="180"] { border-color: #3b6cf5; }
    .page-picker[data-mode="rotate"] .page-picker-card[data-angle="270"] { border-color: #1d3ad7; }
    .page-picker[data-mode="rotate"] .page-picker-card .page-thumb img {
        transition: transform 280ms cubic-bezier(0.23, 1, 0.32, 1);
    }
    .page-picker[data-mode="rotate"] .page-picker-card[data-angle="90"]  .page-thumb img { transform: rotate(90deg);  }
    .page-picker[data-mode="rotate"] .page-picker-card[data-angle="180"] .page-thumb img { transform: rotate(180deg); }
    .page-picker[data-mode="rotate"] .page-picker-card[data-angle="270"] .page-thumb img { transform: rotate(270deg); }

    /* Split mode — cards flow horizontally with narrow vertical cut
       markers between them. Wraps naturally on long PDFs so we don't
       end up with 100 rows on a single column. */
    .page-picker[data-mode="split"] .page-picker-grid {
        display: flex;
        flex-wrap: wrap;
        gap: 10px;
        align-items: stretch;
    }
    .page-picker[data-mode="split"] .page-picker-card {
        flex: 0 0 calc(33.333% - 10px);
        cursor: default;
    }
    @media (min-width: 640px) {
        .page-picker[data-mode="split"] .page-picker-card { flex-basis: calc(25% - 10px); }
    }
    @media (min-width: 768px) {
        .page-picker[data-mode="split"] .page-picker-card { flex-basis: calc(16.666% - 10px); }
    }
    .page-picker[data-mode="split"] .page-picker-card .page-mark { display: none; }

    .split-cut {
        flex: 0 0 28px;
        align-self: stretch;
        display: flex; flex-direction: column; align-items: center; justify-content: center;
        gap: 4px;
        padding: 6px 0;
        color: #94a3b8;
        cursor: pointer;
        user-select: none;
        position: relative;
        transition: color 180ms ease-out;
    }
    /* Vertical line that runs the full height of the card */
    .split-cut::before {
        content: '';
        position: absolute;
        top: 6px; bottom: 6px;
        left: 50%;
        width: 1px;
        background: #e2e8f0;
        transition: background-color 180ms ease-out, width 180ms ease-out;
    }
    .split-cut .split-cut-icon {
        position: relative;
        z-index: 2;
        display: inline-flex; align-items: center; justify-content: center;
        width: 24px; height: 24px;
        border-radius: 50%;
        background: #fff;
        border: 1.5px solid currentColor;
        transition: all 180ms ease-out;
    }
    .split-cut span:not(.split-cut-icon) {
        font-size: 9px; font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.06em;
        position: relative;
        z-index: 2;
        background: #fff;
        padding: 0 2px;
    }
    .split-cut:hover { color: #3b6cf5; }
    .split-cut:hover::before { background: #93b4fd; }
    .split-cut.is-active { color: #ef4444; }
    .split-cut.is-active::before {
        background: #ef4444;
        width: 2px;
    }
    .split-cut.is-active .split-cut-icon {
        background: #ef4444;
        color: #fff;
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

@if ($tool === 'merge' || ! empty($toolConfig['page_picker'] ?? null))
    {{-- PDF.js needed for: merge grid thumbnails + page-picker grids (remove/
         extract pages). SortableJS is merge-specific. --}}
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.11.174/pdf.min.js"></script>
    @if ($tool === 'merge')
        <script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.2/Sortable.min.js"></script>
    @endif
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

        <div class="relative max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 pt-14 pb-20">
            {{-- Headlines (full width, centered) --}}
            <div class="text-center mb-10">
                <h1 class="tool-stagger font-display font-extrabold text-4xl sm:text-5xl text-slate-900 tracking-tight leading-tight" style="--stagger: 0">
                    {{ $h1 }}
                </h1>
                <h2 class="tool-stagger mt-4 text-lg sm:text-xl text-slate-500 max-w-xl mx-auto leading-relaxed" style="--stagger: 1">
                    {{ $h2 }}
                </h2>
            </div>

            {{-- 2-column layout: benefits left + upload right (desktop)
                 Stacked on mobile: upload first, benefits below --}}
            <div id="hero-grid" class="grid grid-cols-1 lg:grid-cols-2 gap-8 lg:gap-12 items-start">

                {{-- LEFT: Benefits (hidden on mobile, shown below upload on small screens) --}}
                <div id="benefits-col" class="order-2 lg:order-1 tool-stagger" style="--stagger: 3">
                    <div class="space-y-6 lg:pt-4">
                        <div class="flex items-start gap-4">
                            <div class="flex-shrink-0 w-10 h-10 rounded-xl {{ $c['icon-bg'] }} flex items-center justify-center">
                                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="{{ $c['icon'] }}"><polygon points="13 2 3 14 12 14 11 22 21 10 12 10 13 2"/></svg>
                            </div>
                            <div>
                                <p class="font-display font-bold text-slate-800 text-sm">{{ __('tool.benefit_fast_title') }}</p>
                                <p class="text-sm text-slate-400 mt-0.5 leading-relaxed">{{ __('tool.benefit_fast_desc') }}</p>
                            </div>
                        </div>
                        <div class="flex items-start gap-4">
                            <div class="flex-shrink-0 w-10 h-10 rounded-xl {{ $c['icon-bg'] }} flex items-center justify-center">
                                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="{{ $c['icon'] }}"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>
                            </div>
                            <div>
                                <p class="font-display font-bold text-slate-800 text-sm">{{ __('tool.benefit_secure_title') }}</p>
                                <p class="text-sm text-slate-400 mt-0.5 leading-relaxed">{{ __('tool.benefit_secure_desc') }}</p>
                            </div>
                        </div>
                        <div class="flex items-start gap-4">
                            <div class="flex-shrink-0 w-10 h-10 rounded-xl {{ $c['icon-bg'] }} flex items-center justify-center">
                                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="{{ $c['icon'] }}"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
                            </div>
                            <div>
                                <p class="font-display font-bold text-slate-800 text-sm">{{ __('tool.benefit_quality_title') }}</p>
                                <p class="text-sm text-slate-400 mt-0.5 leading-relaxed">{{ __('tool.benefit_quality_desc') }}</p>
                            </div>
                        </div>
                        <div class="flex items-start gap-4">
                            <div class="flex-shrink-0 w-10 h-10 rounded-xl {{ $c['icon-bg'] }} flex items-center justify-center">
                                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="{{ $c['icon'] }}"><rect x="2" y="3" width="20" height="14" rx="2"/><line x1="8" y1="21" x2="16" y2="21"/><line x1="12" y1="17" x2="12" y2="21"/></svg>
                            </div>
                            <div>
                                <p class="font-display font-bold text-slate-800 text-sm">{{ __('tool.benefit_free_title') }}</p>
                                <p class="text-sm text-slate-400 mt-0.5 leading-relaxed">{{ __('tool.benefit_free_desc') }}</p>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- RIGHT: Upload Zone --}}
                <div class="order-1 lg:order-2">
                    <div id="upload-zone"
                         class="upload-zone upload-zone-pulse tool-stagger relative {{ $c['bg'] }} rounded-3xl border-2 border-dashed {{ $c['border'] }} min-h-[260px] flex flex-col items-center justify-center p-8 sm:p-12 text-center cursor-pointer"
                         style="--stagger: 2"
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
                        <p class="text-sm text-slate-400 mb-5">
                            {{ __('tool.formats_label') }} {{ str_replace('.', '', str_replace(',', ', ', $accept)) }} &middot; Max. {{ env('MAX_UPLOAD_SIZE_MB', 50) }} MB
                            @if($multiple) &middot; {{ __('tool.up_to_files', ['n' => $maxFiles]) }} @endif
                        </p>

                        {{-- CTA button --}}
                        <span class="inline-flex items-center gap-2 px-8 py-3.5 {{ $c['btn'] }} text-white font-display font-bold text-sm rounded-xl shadow-lg pointer-events-none"
                              style="box-shadow: 0 6px 16px -4px rgba(0,0,0,0.25);">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="17 8 12 3 7 8"/><line x1="12" y1="3" x2="12" y2="15"/></svg>
                            {{ $actionLabel }}
                        </span>

                        <input type="file" id="file-input" class="hidden" accept="{{ $accept }}" {{ $multiple ? 'multiple' : '' }}>
                    </div>

                    {{-- Trust badges under upload zone --}}
                    <div id="upload-trust-badges" class="tool-stagger flex flex-wrap items-center justify-center gap-x-5 gap-y-2 mt-4 text-xs text-slate-400" style="--stagger: 3">
                        <span class="inline-flex items-center gap-1.5">
                            <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="#059669" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polygon points="13 2 3 14 12 14 11 22 21 10 12 10 13 2"/></svg>
                            {{ __('tool.trust_fast') }}
                        </span>
                        <span class="inline-flex items-center gap-1.5">
                            <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="#059669" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>
                            {{ __('tool.trust_secure') }}
                        </span>
                        <span class="inline-flex items-center gap-1.5">
                            <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="#059669" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
                            {{ __('tool.trust_quality') }}
                        </span>
                        <span class="inline-flex items-center gap-1.5">
                            <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="#059669" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
                            {{ __('tool.trust_delete') }}
                        </span>
                    </div>
                </div>
            </div>

            {{-- ═══════ SECTION 2: FILE LIST ═══════ --}}
            <div id="file-list-wrapper" class="hidden">
                <div id="file-list" class="mt-6 space-y-3"></div>

                {{-- Add more files --}}
                <div id="add-more-area" class="hidden mt-3"></div>

                {{-- Visual page picker (remove-pages / extract-pages).
                     Hidden input `pages` is populated by JS so the existing
                     tool-param collection picks it up without changes. --}}
                @if (! empty($toolConfig['page_picker'] ?? null))
                    @php
                        $pickerMode = $toolConfig['page_picker'];
                        $pickerHeadings = [
                            'remove'  => __('tool.picker_remove_heading'),
                            'extract' => __('tool.picker_extract_heading'),
                            'rotate'  => __('tool.picker_rotate_heading'),
                            'split'   => __('tool.picker_split_heading'),
                        ];
                        $pickerHints = [
                            'remove'  => __('tool.picker_remove_hint'),
                            'extract' => __('tool.picker_extract_hint'),
                            'rotate'  => __('tool.picker_rotate_hint'),
                            'split'   => __('tool.picker_split_hint'),
                        ];
                        // The remove/extract picker writes to `pages`; rotate writes
                        // to `rotations` (JSON); split writes to `pages`.
                        $pickerOutputKey = $pickerMode === 'rotate' ? 'rotations' : 'pages';
                    @endphp
                    <div id="page-picker" class="page-picker" data-mode="{{ $pickerMode }}" data-accept="{{ $accept }}">
                        <div class="page-picker-header">
                            <div>
                                <h3 class="page-picker-heading">{{ $pickerHeadings[$pickerMode] ?? '' }}</h3>
                                <p class="page-picker-sub">{{ $pickerHints[$pickerMode] ?? '' }}</p>
                                <p id="page-picker-count" class="page-picker-count mt-2" hidden></p>
                            </div>
                            <div class="page-picker-actions">
                                @if (in_array($pickerMode, ['remove', 'extract'], true))
                                    <button type="button" class="page-picker-btn" data-picker-action="all">
                                        <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M9 11l3 3L22 4"/><path d="M21 12v7a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11"/></svg>
                                        {{ __('tool.picker_select_all') }}
                                    </button>
                                    <button type="button" class="page-picker-btn" data-picker-action="none">
                                        <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M18 6L6 18"/><path d="M6 6l12 12"/></svg>
                                        {{ __('tool.picker_select_none') }}
                                    </button>
                                @elseif ($pickerMode === 'rotate')
                                    <button type="button" class="page-picker-btn" data-picker-action="reset-rotations">
                                        <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="1 4 1 10 7 10"/><path d="M3.51 15a9 9 0 1 0 2.13-9.36L1 10"/></svg>
                                        {{ __('tool.picker_reset_rotations') }}
                                    </button>
                                @elseif ($pickerMode === 'split')
                                    <button type="button" class="page-picker-btn" data-picker-action="reset-splits">
                                        <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="1 4 1 10 7 10"/><path d="M3.51 15a9 9 0 1 0 2.13-9.36L1 10"/></svg>
                                        {{ __('tool.picker_reset_splits') }}
                                    </button>
                                @endif
                            </div>
                        </div>

                        <div id="page-picker-grid" class="page-picker-grid">
                            <div class="page-picker-loading">{{ __('tool.picker_loading') }}</div>
                        </div>

                        {{-- Hidden input that feeds the existing param collection.
                             Key is `pages` for remove/extract/split, `rotations`
                             for the rotate mode (JSON array). --}}
                        <input type="hidden" data-tool-param="{{ $pickerOutputKey }}" id="page-picker-value" value="">
                    </div>
                @endif

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

            {{-- Trust signals removed — benefits in left column cover these --}}
        </div>
    </section>

    {{-- ═══════ SOCIAL PROOF STATS ═══════ --}}
    <section class="bg-white border-b border-slate-100">
        <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
            <div class="grid grid-cols-3 gap-6 text-center">
                <div class="observe-animate" data-delay="0">
                    <p class="font-display font-extrabold text-3xl sm:text-4xl text-slate-900 tracking-tight">50K+</p>
                    <p class="text-sm text-slate-400 mt-1">{{ __('tool.stat_docs') }}</p>
                </div>
                <div class="observe-animate" data-delay="80">
                    <p class="font-display font-extrabold text-3xl sm:text-4xl text-slate-900 tracking-tight">12K+</p>
                    <p class="text-sm text-slate-400 mt-1">{{ __('tool.stat_users') }}</p>
                </div>
                <div class="observe-animate" data-delay="160">
                    <p class="font-display font-extrabold text-3xl sm:text-4xl text-slate-900 tracking-tight">99%</p>
                    <p class="text-sm text-slate-400 mt-1">{{ __('tool.stat_quality') }}</p>
                </div>
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
    // Built here to sidestep Blade's parser truncating @json with inline
    // multi-line arrays + nested __() calls (we've seen this before).
    $pickerLabels = [
        'loading'      => __('tool.picker_loading'),
        'pageLabel'    => __('tool.picker_page_label', ['n' => ':N']),
        'countRemove'  => __('tool.picker_selected_count_remove', ['n' => ':N']),
        'countExtract' => __('tool.picker_selected_count_extract', ['n' => ':N']),
        'countRotate'  => __('tool.picker_rotate_count', ['n' => ':N']),
        'countSplit'   => __('tool.picker_split_count', ['n' => ':N', 'groups' => ':GROUPS']),
        'needRemove'   => __('tool.picker_need_selection_remove'),
        'needExtract'  => __('tool.picker_need_selection_extract'),
        'needRotation' => __('tool.picker_need_rotation'),
        'needSplit'    => __('tool.picker_need_split'),
    ];
@endphp
@push('scripts')
<script>
(function() {
    // GTM: tool page view
    window.dataLayer = window.dataLayer || [];
    window.dataLayer.push({ event: 'tool_view', tool: '{{ $tool["key"] ?? "" }}' });

    const __t = @json($jsMessages);
    const zone = document.getElementById('upload-zone');
    const fileInput = document.getElementById('file-input');
    const fileListWrapper = document.getElementById('file-list-wrapper');
    const heroGrid = document.getElementById('hero-grid');
    const benefitsCol = document.getElementById('benefits-col');
    const uploadTrustBadges = document.getElementById('upload-trust-badges');
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

    // Page picker (remove-pages / extract-pages): grid of every PDF page.
    const pickerEl = document.getElementById('page-picker');
    const pickerMode = pickerEl ? pickerEl.getAttribute('data-mode') : null; // 'remove' | 'extract' | null
    const pickerValueEl = document.getElementById('page-picker-value');
    const pickerCountEl = document.getElementById('page-picker-count');
    const pickerGridEl = document.getElementById('page-picker-grid');
    let pickerTotalPages = 0;
    const pickerSelected = new Set();
    const __pickerLabels = @json($pickerLabels);

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

        // GTM: file uploaded
        window.dataLayer = window.dataLayer || [];
        window.dataLayer.push({
            event: 'file_upload',
            tool: '{{ $tool["key"] ?? "" }}',
            file_count: selectedFiles.length,
        });

        // Log to backend
        fetch('/api/log/event', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content },
            body: JSON.stringify({ event: 'file_uploaded', tool: toolKey })
        }).catch(function() {});

        // Tools with a page-picker render every page of the PDF once the
        // user uploads a file. Single-file tools only (config enforces it).
        if (pickerEl && selectedFiles.length > 0) {
            initPagePicker(selectedFiles[0]).catch(function() { /* ignore */ });
        }
    }

    /* ── Render file list with stagger ── */
    function renderFileList() {
        if (selectedFiles.length === 0) {
            fileListWrapper.classList.add('hidden');
            zone.classList.remove('hidden', 'state-exit');
            zone.style.opacity = '';
            zone.style.transform = '';
            // Restore 2-column layout with benefits
            if (benefitsCol) benefitsCol.classList.remove('hidden');
            if (uploadTrustBadges) uploadTrustBadges.classList.remove('hidden');
            if (heroGrid) { heroGrid.classList.remove('lg:grid-cols-1'); heroGrid.classList.add('lg:grid-cols-2'); }
            if (mergeSortable) { mergeSortable.destroy(); mergeSortable = null; }
            previewCache.clear();
            return;
        }

        // Hide benefits and switch to 1-column for file list
        if (benefitsCol) benefitsCol.classList.add('hidden');
        if (uploadTrustBadges) uploadTrustBadges.classList.add('hidden');
        if (heroGrid) { heroGrid.classList.remove('lg:grid-cols-2'); heroGrid.classList.add('lg:grid-cols-1'); }

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

    // Picker state for rotate + split modes (separate from the
    // remove/extract `pickerSelected` set).
    const pickerRotations = new Map();   // page (int) → angle (0/90/180/270)
    const pickerSplits = new Set();      // cut points BEFORE page N (so {3} → cut between 2 and 3 → groups 1-2 and 3-…)

    /* ── Page picker: render every PDF page as a clickable tile ── */
    async function initPagePicker(file) {
        if (!pickerEl || !file) return;
        pickerSelected.clear();
        pickerRotations.clear();
        pickerSplits.clear();
        pickerTotalPages = 0;
        updatePickerValue();

        pickerGridEl.innerHTML = '<div class="page-picker-loading">' + __pickerLabels.loading + '</div>';

        var pdfjs = window['pdfjs-dist/build/pdf'] || window.pdfjsLib;
        if (!pdfjs || !pdfjs.getDocument) {
            pickerGridEl.innerHTML = '<div class="page-picker-loading">PDF.js not loaded.</div>';
            return;
        }

        try {
            var ab = await file.arrayBuffer();
            var pdf = await pdfjs.getDocument({ data: ab }).promise;
            pickerTotalPages = pdf.numPages;

            renderPickerCards();

            // Render thumbnails sequentially so we don't hammer the worker.
            for (var p = 1; p <= pdf.numPages; p++) {
                var tile = pickerGridEl.querySelector('[data-page="' + p + '"] .page-thumb');
                if (!tile) continue;
                try {
                    var page = await pdf.getPage(p);
                    var vp = page.getViewport({ scale: 1 });
                    var targetHeight = 180;
                    var scale = targetHeight / vp.height;
                    var viewport = page.getViewport({ scale: scale * (window.devicePixelRatio || 1) });
                    var canvas = document.createElement('canvas');
                    canvas.width = viewport.width;
                    canvas.height = viewport.height;
                    await page.render({ canvasContext: canvas.getContext('2d'), viewport: viewport }).promise;
                    tile.innerHTML = '<img src="' + canvas.toDataURL('image/png') + '" alt="">';
                } catch (e) { /* leave spinner → user still sees page number */ }
            }

            updatePickerValue();
        } catch (e) {
            pickerGridEl.innerHTML = '<div class="page-picker-loading">Failed to load PDF pages.</div>';
        }
    }

    function renderPickerCards() {
        pickerGridEl.innerHTML = '';

        // Split mode interleaves cut points + group labels between cards.
        if (pickerMode === 'split') {
            renderSplitGroups();
            return;
        }

        for (var i = 1; i <= pickerTotalPages; i++) {
            pickerGridEl.appendChild(buildPageCard(i));
        }
    }

    function buildPageCard(i) {
        var card = document.createElement('div');
        card.className = 'page-picker-card';
        card.setAttribute('data-page', i);
        card.setAttribute('title', __pickerLabels.pageLabel.replace(':N', i));

        var markSvg = '';
        if (pickerMode === 'remove') {
            markSvg = '<svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><path d="M18 6L6 18"/><path d="M6 6l12 12"/></svg>';
        } else if (pickerMode === 'extract') {
            markSvg = '<svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg>';
        }

        card.innerHTML =
            '<div class="page-thumb"><div class="page-thumb-loading"></div></div>' +
            '<span class="page-number">' + i + '</span>' +
            (pickerMode === 'rotate'
                ? '<span class="page-rotate-badge"></span>'
                : '<span class="page-mark">' + markSvg + '</span>');

        if (pickerMode === 'rotate') {
            card.setAttribute('data-angle', String(pickerRotations.get(i) || 0));
            updateRotateBadge(card, pickerRotations.get(i) || 0);
            card.addEventListener('click', function() { rotatePickerPage(this); });
        } else if (pickerMode === 'remove' || pickerMode === 'extract') {
            if (pickerSelected.has(i)) card.classList.add('is-selected');
            card.addEventListener('click', function() { togglePickerPage(this); });
        }

        return card;
    }

    function updateRotateBadge(card, angle) {
        var badge = card.querySelector('.page-rotate-badge');
        if (!badge) return;
        badge.textContent = angle ? angle + '°' : '';
    }

    function rotatePickerPage(cardEl) {
        var n = parseInt(cardEl.getAttribute('data-page'), 10);
        var current = pickerRotations.get(n) || 0;
        var next = (current + 90) % 360;
        if (next === 0) {
            pickerRotations.delete(n);
        } else {
            pickerRotations.set(n, next);
        }
        cardEl.setAttribute('data-angle', String(next));
        updateRotateBadge(cardEl, next);
        updatePickerValue();
    }

    function togglePickerPage(cardEl) {
        var n = parseInt(cardEl.getAttribute('data-page'), 10);
        if (pickerSelected.has(n)) {
            pickerSelected.delete(n);
            cardEl.classList.remove('is-selected');
        } else {
            pickerSelected.add(n);
            cardEl.classList.add('is-selected');
        }
        updatePickerValue();
    }

    /* ── Split mode: interleave clickable cut markers between cards ── */
    function renderSplitGroups() {
        pickerGridEl.innerHTML = '';
        if (pickerTotalPages === 0) return;

        for (var i = 1; i <= pickerTotalPages; i++) {
            // Inject a cut marker BEFORE pages 2..N (cut "before page i"
            // splits between page i-1 and page i).
            if (i > 1) pickerGridEl.appendChild(buildCutMarker(i));
            pickerGridEl.appendChild(buildPageCard(i));
        }
    }

    function buildCutMarker(beforePage) {
        var cut = document.createElement('div');
        cut.className = 'split-cut';
        cut.setAttribute('data-cut-before', String(beforePage));
        if (pickerSplits.has(beforePage)) cut.classList.add('is-active');
        cut.innerHTML =
            '<span class="split-cut-icon">' +
                '<svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">' +
                    '<circle cx="6" cy="6" r="3"/><circle cx="6" cy="18" r="3"/>' +
                    '<line x1="20" y1="4" x2="8.12" y2="15.88"/><line x1="14.47" y1="14.48" x2="20" y2="20"/><line x1="8.12" y1="8.12" x2="12" y2="12"/>' +
                '</svg>' +
            '</span>' +
            '<span>cut</span>';
        cut.addEventListener('click', function() { toggleSplitCut(beforePage); });
        return cut;
    }

    function toggleSplitCut(beforePage) {
        if (pickerSplits.has(beforePage)) pickerSplits.delete(beforePage);
        else pickerSplits.add(beforePage);
        // Just toggle the matching cut marker — rebuilding the whole grid
        // here used to wipe the rendered <img> thumbnails because PDF.js
        // only renders pages once on initial load (no cache lookup on
        // re-render), leaving every card stuck on the loading spinner.
        var marker = pickerGridEl.querySelector('[data-cut-before="' + beforePage + '"]');
        if (marker) marker.classList.toggle('is-active', pickerSplits.has(beforePage));
        updatePickerValue();
    }

    // Build the list of page-range groups produced by the current cut points.
    function splitGroups() {
        if (pickerTotalPages === 0) return [];
        var cuts = Array.from(pickerSplits).sort(function(a,b) { return a - b; });
        var ranges = [];
        var start = 1;
        cuts.forEach(function(c) {
            if (c > start) {
                ranges.push(rangeStr(start, c - 1));
                start = c;
            }
        });
        ranges.push(rangeStr(start, pickerTotalPages));
        return ranges;
    }
    function rangeStr(a, b) { return a === b ? String(a) : a + '-' + b; }

    function updatePickerValue() {
        if (!pickerEl) return;

        if (pickerMode === 'rotate') {
            // Submit JSON: [{"page":1,"angle":90},...]
            var payload = [];
            pickerRotations.forEach(function(angle, page) {
                if (angle !== 0) payload.push({ page: page, angle: angle });
            });
            payload.sort(function(a, b) { return a.page - b.page; });
            if (pickerValueEl) pickerValueEl.value = payload.length ? JSON.stringify(payload) : '';
            if (pickerCountEl) {
                if (payload.length === 0) {
                    pickerCountEl.hidden = true;
                } else {
                    pickerCountEl.hidden = false;
                    pickerCountEl.textContent = __pickerLabels.countRotate.replace(':N', payload.length);
                }
            }
            return;
        }

        if (pickerMode === 'split') {
            var ranges = splitGroups();
            if (pickerValueEl) pickerValueEl.value = ranges.length > 1 ? ranges.join(',') : '';
            if (pickerCountEl) {
                if (ranges.length <= 1) {
                    pickerCountEl.hidden = true;
                } else {
                    pickerCountEl.hidden = false;
                    pickerCountEl.textContent = __pickerLabels.countSplit
                        .replace(':N', ranges.length)
                        .replace(':GROUPS', ranges.join(' · '));
                }
            }
            return;
        }

        // Default: remove / extract
        var arr = Array.from(pickerSelected).sort(function(a, b) { return a - b; });
        var groups = [], start = null, prev = null;
        arr.forEach(function(n) {
            if (start === null) { start = n; prev = n; }
            else if (n === prev + 1) { prev = n; }
            else { groups.push(start === prev ? String(start) : start + '-' + prev); start = n; prev = n; }
        });
        if (start !== null) groups.push(start === prev ? String(start) : start + '-' + prev);

        if (pickerValueEl) pickerValueEl.value = groups.join(',');
        if (pickerCountEl) {
            var n = arr.length;
            if (n === 0) {
                pickerCountEl.hidden = true;
            } else {
                pickerCountEl.hidden = false;
                var tpl = pickerMode === 'remove' ? __pickerLabels.countRemove : __pickerLabels.countExtract;
                pickerCountEl.textContent = tpl.replace(':N', n);
            }
        }
    }

    // Action buttons (varies per mode)
    if (pickerEl) {
        pickerEl.addEventListener('click', function(e) {
            var btn = e.target.closest('[data-picker-action]');
            if (!btn) return;
            var action = btn.getAttribute('data-picker-action');
            if (action === 'all') {
                pickerSelected.clear();
                for (var i = 1; i <= pickerTotalPages; i++) pickerSelected.add(i);
                pickerGridEl.querySelectorAll('.page-picker-card').forEach(function(c) { c.classList.add('is-selected'); });
            } else if (action === 'none') {
                pickerSelected.clear();
                pickerGridEl.querySelectorAll('.page-picker-card').forEach(function(c) { c.classList.remove('is-selected'); });
            } else if (action === 'reset-rotations') {
                pickerRotations.clear();
                pickerGridEl.querySelectorAll('.page-picker-card').forEach(function(c) {
                    c.setAttribute('data-angle', '0');
                    updateRotateBadge(c, 0);
                });
            } else if (action === 'reset-splits') {
                pickerSplits.clear();
                // Just clear all is-active classes; same reason as
                // toggleSplitCut — preserve the rendered thumbnails.
                pickerGridEl.querySelectorAll('.split-cut.is-active').forEach(function(c) {
                    c.classList.remove('is-active');
                });
            }
            updatePickerValue();
        });
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
                (window.sofortpdfPaywall && window.sofortpdfPaywall.needsPayment()
                    ? ''
                    : '<button onclick="removeFile(' + idx + ')" class="btn-remove w-8 h-8 rounded-lg flex items-center justify-center text-slate-300 hover:text-red-500 hover:bg-red-50 flex-shrink-0 ml-3" style="transition: color 200ms ease-out, background-color 200ms ease-out, transform 160ms cubic-bezier(0.23,1,0.32,1);">' +
                        '<i data-lucide="x" class="w-4 h-4"></i>' +
                    '</button>') +
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
        // Restore 2-column layout with benefits
        if (benefitsCol) benefitsCol.classList.remove('hidden');
        if (uploadTrustBadges) uploadTrustBadges.classList.remove('hidden');
        if (heroGrid) { heroGrid.classList.remove('lg:grid-cols-1'); heroGrid.classList.add('lg:grid-cols-2'); }
        processBtn.disabled = false;
        btnText.textContent = '{{ $actionLabel }}';
        btnSpinner.classList.add('hidden');
        btnArrow.classList.remove('hidden');
        refreshIcons();
    };

    /* ── Process button ── */
    processBtn.addEventListener('click', async function() {
        if (selectedFiles.length === 0) return;

        // GTM: conversion started
        window.dataLayer = window.dataLayer || [];
        window.dataLayer.push({ event: 'conversion_started', tool: '{{ $tool["key"] ?? "" }}' });

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

            window.__sofortpdfShowLoadingThenPay(modalFiles, function() {
                processBtn.click();
            }, '{{ __("tool.loading_converting", ["tool" => $h1]) }}');
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

        // Page picker: require a meaningful selection. Mode-specific error
        // messages since "please fill all required fields" doesn't make
        // sense when the input is visual.
        if (pickerEl) {
            var pickerVal = pickerValueEl ? pickerValueEl.value.trim() : '';
            if (pickerVal === '') {
                var needMsg;
                if (pickerMode === 'rotate') needMsg = __pickerLabels.needRotation;
                else if (pickerMode === 'split') needMsg = __pickerLabels.needSplit;
                else if (pickerMode === 'extract') needMsg = __pickerLabels.needExtract;
                else needMsg = __pickerLabels.needRemove;
                showError(needMsg);
                processBtn.disabled = false;
                btnText.textContent = '{{ $actionLabel }}';
                btnSpinner.classList.add('hidden');
                btnArrow.classList.remove('hidden');
                return;
            }
            // Hidden input is bound to data-tool-param={pages | rotations}
            // and was already collected above; nothing else to do here.
            missingRequired = false; // false-positive guard for the hidden input
        }

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

            // Switch to processing state. Keep the user looking at the
            // progress UI for a minimum of 3s even if the backend ACK
            // comes back sooner — conversie-pdf's team found that an
            // instant flicker feels "fake" and a short ramp reads as
            // solid progress.
            fileListWrapper.classList.add('hidden');
            processingState.classList.remove('hidden');
            var processingStartedAt = Date.now();
            var MIN_PROCESSING_MS = 3000;

            var convertReq = fetch('/api/convert', {
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

            var convertRes = await convertReq;
            if (!convertRes.ok) {
                var errConv = await convertRes.json();
                throw new Error(errConv.message || __t.conversionFailed);
            }
            var result = await convertRes.json();

            // Enforce the minimum processing-state time before handing off
            // to the confirmation page.
            var elapsed = Date.now() - processingStartedAt;
            if (elapsed < MIN_PROCESSING_MS) {
                await new Promise(function(r) { setTimeout(r, MIN_PROCESSING_MS - elapsed); });
            }

            // Redirect to the confirmation page. The conversion now runs
            // asynchronously on the conversion-service; the confirmation
            // page polls /api/convert/status until it flips to completed
            // or failed.
            if (result.confirmation_url) {
                var confUrl = result.confirmation_url;
                // Append payment success flag for GTM if user just paid
                if (window.__sofortpdfTrialJustPaid) {
                    confUrl += (confUrl.indexOf('?') >= 0 ? '&' : '?') + 'cGF5bWVudFN1Y2Nlc3M=';
                    window.__sofortpdfTrialJustPaid = false;
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
