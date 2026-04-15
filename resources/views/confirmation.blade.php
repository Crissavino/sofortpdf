@extends('layouts.app')

@php
    $loc = app()->getLocale();
    $dashboardSlug = config("locales.auth_slugs.{$loc}.login");
@endphp

@section('content')
    <section class="relative overflow-hidden bg-gradient-to-b from-emerald-50/40 via-white to-white">
        <div class="absolute inset-0 pointer-events-none overflow-hidden" aria-hidden="true">
            <div class="hero-orb absolute w-[30rem] h-[30rem] rounded-full top-[-6rem] right-[-8rem]" style="background: radial-gradient(circle, rgba(16,185,129,0.18) 0%, transparent 70%); animation-duration: 12s;"></div>
            <div class="hero-orb absolute w-[24rem] h-[24rem] rounded-full bottom-[-4rem] left-[-6rem]" style="background: radial-gradient(circle, rgba(59,108,245,0.12) 0%, transparent 70%); animation-duration: 14s; animation-delay: -3s;"></div>
        </div>

        <div class="relative max-w-2xl mx-auto px-4 sm:px-6 lg:px-8 py-20 sm:py-28">
            <div id="confirmation-card" class="bg-white rounded-3xl border border-slate-100 shadow-xl shadow-slate-200/40 p-8 sm:p-12 text-center"
                 data-state="{{ $jobState }}"
                 @if($statusUrl) data-status-url="{{ $statusUrl }}" @endif>

                {{-- ══════════ READY ══════════ --}}
                @if($jobState === 'ready' && $tokenValid && $downloadUrl)
                    <div class="confirmation-icon confirmation-check w-20 h-20 rounded-full bg-emerald-50 flex items-center justify-center mx-auto mb-6">
                        <svg class="w-10 h-10 text-emerald-500" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24" stroke-linecap="round" stroke-linejoin="round">
                            <polyline points="20 6 9 17 4 12"></polyline>
                        </svg>
                    </div>
                    <h1 class="font-display font-extrabold text-3xl sm:text-4xl text-slate-900 tracking-tight">{{ __('confirmation.heading') }}</h1>
                    <p class="mt-4 text-slate-500 text-base sm:text-lg leading-relaxed">{{ $paymentSuccess ? __('confirmation.subheading_paid') : __('confirmation.subheading') }}</p>

                    @if ($filename)
                        <div class="mt-8 inline-flex items-center gap-3 max-w-full bg-slate-50 border border-slate-100 rounded-2xl px-5 py-4">
                            <div class="w-10 h-10 rounded-xl bg-brand-50 flex items-center justify-center flex-shrink-0">
                                <svg class="w-5 h-5 text-brand-600" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24" stroke-linecap="round" stroke-linejoin="round"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/></svg>
                            </div>
                            <div class="text-left min-w-0">
                                <p class="text-xs text-slate-400 uppercase tracking-wider">{{ __('confirmation.filename_label') }}</p>
                                <p class="text-sm font-medium text-slate-800 truncate" title="{{ $filename }}">{{ $filename }}</p>
                            </div>
                        </div>
                    @endif

                    <p class="mt-6 text-xs text-slate-400">{{ __('confirmation.auto_download_note') }}</p>

                    <div class="mt-8">
                        <a id="confirmation-download" href="{{ $downloadUrl }}"
                           class="btn-press inline-flex items-center justify-center gap-2.5 bg-emerald-500 hover:bg-emerald-600 text-white font-display font-bold px-10 py-4 rounded-2xl shadow-lg shadow-emerald-500/25 hover:shadow-emerald-500/40 text-base"
                           style="transition: transform 160ms cubic-bezier(0.23,1,0.32,1), background-color 200ms ease-out, box-shadow 200ms ease-out;">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/></svg>
                            <span>{{ __('confirmation.download_button') }}</span>
                        </a>
                    </div>

                {{-- ══════════ PROCESSING (polled) ══════════ --}}
                @elseif ($jobState === 'processing')
                    <div class="confirmation-icon w-20 h-20 rounded-full bg-brand-50 flex items-center justify-center mx-auto mb-6 relative">
                        <svg class="w-10 h-10 text-brand-500 animate-spin-slow" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M21 12a9 9 0 11-6.219-8.56"/>
                        </svg>
                    </div>
                    <h1 class="font-display font-extrabold text-3xl sm:text-4xl text-slate-900 tracking-tight">{{ __('confirmation.working_heading') }}</h1>
                    <p class="mt-4 text-slate-500 text-base sm:text-lg leading-relaxed">{{ __('confirmation.working_message') }}</p>

                    @if ($filename)
                        <div class="mt-8 inline-flex items-center gap-3 max-w-full bg-slate-50 border border-slate-100 rounded-2xl px-5 py-4">
                            <div class="w-10 h-10 rounded-xl bg-brand-50 flex items-center justify-center flex-shrink-0">
                                <svg class="w-5 h-5 text-brand-600" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24" stroke-linecap="round" stroke-linejoin="round"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/></svg>
                            </div>
                            <div class="text-left min-w-0">
                                <p class="text-xs text-slate-400 uppercase tracking-wider">{{ __('confirmation.filename_label') }}</p>
                                <p class="text-sm font-medium text-slate-800 truncate" title="{{ $filename }}">{{ $filename }}</p>
                            </div>
                        </div>
                    @endif

                    <p id="confirmation-elapsed" class="mt-6 text-xs text-slate-400">{{ __('confirmation.working_elapsed', ['seconds' => 0]) }}</p>

                    <div class="mt-6 proc-progress max-w-xs mx-auto"></div>

                    <div class="mt-8 bg-slate-50 border border-slate-100 rounded-2xl p-5 text-left text-sm text-slate-600 leading-relaxed">
                        <div class="flex items-start gap-3">
                            <svg class="w-5 h-5 text-brand-500 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" stroke-linecap="round" stroke-linejoin="round"><path d="M12 9v2m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            <p>{{ __('confirmation.working_dashboard_hint') }}</p>
                        </div>
                    </div>

                {{-- ══════════ FAILED ══════════ --}}
                @elseif ($jobState === 'failed')
                    <div class="confirmation-icon w-20 h-20 rounded-full bg-red-50 flex items-center justify-center mx-auto mb-6">
                        <svg class="w-10 h-10 text-red-500" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24" stroke-linecap="round" stroke-linejoin="round"><path d="M12 9v4m0 4h.01"/><path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"/></svg>
                    </div>
                    <h1 class="font-display font-extrabold text-3xl sm:text-4xl text-slate-900 tracking-tight">{{ __('confirmation.failed_heading') }}</h1>
                    <p class="mt-4 text-slate-500 text-base sm:text-lg leading-relaxed">{{ $jobErrorMessage ?: __('confirmation.failed_message_fallback') }}</p>

                    <div class="mt-8">
                        <a href="{{ route('home') }}#tools"
                           class="inline-flex items-center justify-center gap-2 px-6 py-3 rounded-xl border border-slate-200 bg-white text-sm font-medium text-slate-700 hover:bg-slate-50 transition-colors">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" stroke-linecap="round" stroke-linejoin="round"><path d="M3 12a9 9 0 0118 0M3 12a9 9 0 0018 0M3 12h18"/></svg>
                            {{ __('confirmation.retry_button') }}
                        </a>
                    </div>

                {{-- ══════════ UNKNOWN / EXPIRED ══════════ --}}
                @else
                    <div class="confirmation-icon w-20 h-20 rounded-full bg-amber-50 flex items-center justify-center mx-auto mb-6">
                        <svg class="w-10 h-10 text-amber-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><path d="M12 8v4m0 4h.01"/></svg>
                    </div>
                    <h1 class="font-display font-extrabold text-3xl sm:text-4xl text-slate-900 tracking-tight">{{ __('confirmation.no_token_heading') }}</h1>
                    <p class="mt-4 text-slate-500 text-base sm:text-lg leading-relaxed">{{ __('confirmation.no_token_message') }}</p>
                @endif

                {{-- Secondary CTAs (always visible) --}}
                <div class="mt-10 flex flex-col sm:flex-row items-center justify-center gap-3">
                    @auth
                        <a href="{{ route('dashboard.index') }}" class="inline-flex items-center justify-center gap-2 px-5 py-3 rounded-xl border border-slate-200 bg-white text-sm font-medium text-slate-700 hover:bg-slate-50 transition-colors">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" stroke-linecap="round" stroke-linejoin="round"><path d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
                            {{ __('confirmation.dashboard_button') }}
                        </a>
                    @else
                        <a href="/{{ $loc }}/{{ $dashboardSlug }}" class="inline-flex items-center justify-center gap-2 px-5 py-3 rounded-xl border border-slate-200 bg-white text-sm font-medium text-slate-700 hover:bg-slate-50 transition-colors">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="7" r="4"/><path d="M5.5 21a6.5 6.5 0 0 1 13 0"/></svg>
                            {{ __('confirmation.dashboard_button') }}
                        </a>
                    @endauth

                    <a href="{{ route('home') }}" class="inline-flex items-center justify-center gap-2 px-5 py-3 rounded-xl text-sm font-medium text-slate-500 hover:text-brand-600 transition-colors">
                        {{ __('confirmation.home_button') }}
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12h14M12 5l7 7-7 7"/></svg>
                    </a>
                </div>
            </div>
        </div>
    </section>
@endsection

@push('head')
<style>
    .confirmation-check {
        animation: confirmation-pop 0.5s cubic-bezier(0.34, 1.56, 0.64, 1) both;
    }
    @keyframes confirmation-pop {
        0%   { opacity: 0; transform: scale(0.4); }
        100% { opacity: 1; transform: scale(1); }
    }
    .animate-spin-slow { animation: spin-slow 1.6s linear infinite; }
    @keyframes spin-slow { to { transform: rotate(360deg); } }

    /* Indeterminate progress bar (mirrors the tool-page processing state) */
    .proc-progress {
        position: relative; height: 3px;
        background: rgba(148, 163, 184, 0.15);
        border-radius: 2px; overflow: hidden;
    }
    .proc-progress::after {
        content: ''; position: absolute;
        top: 0; left: -40%; width: 40%; height: 100%;
        background: linear-gradient(90deg, transparent, #3b82f6, #8b5cf6, transparent);
        animation: proc-progress-slide 1.4s cubic-bezier(0.23, 1, 0.32, 1) infinite;
    }
    @keyframes proc-progress-slide {
        0%   { left: -40%; }
        100% { left: 100%; }
    }
    @media (prefers-reduced-motion: reduce) {
        .confirmation-check { animation: none !important; opacity: 1 !important; }
        .animate-spin-slow { animation: none !important; }
        .proc-progress::after { animation: none !important; }
    }
</style>
@endpush

@push('scripts')
<script>
(function() {
    var card = document.getElementById('confirmation-card');
    if (!card) return;

    var state = card.getAttribute('data-state');
    var statusUrl = card.getAttribute('data-status-url');

    // Auto-download on ready
    if (state === 'ready') {
        var anchor = document.getElementById('confirmation-download');
        if (anchor && anchor.href) {
            var a = document.createElement('a');
            a.href = anchor.href;
            a.style.display = 'none';
            document.body.appendChild(a);
            setTimeout(function() {
                a.click();
                setTimeout(function() { document.body.removeChild(a); }, 800);
            }, 350);
        }
        return;
    }

    // Polling for processing jobs
    if (state === 'processing' && statusUrl) {
        var elapsedEl = document.getElementById('confirmation-elapsed');
        var labelTpl = @json(__('confirmation.working_elapsed', ['seconds' => ':SECONDS']));
        var startedAt = Date.now();
        var tickSec = setInterval(function() {
            var elapsed = Math.floor((Date.now() - startedAt) / 1000);
            if (elapsedEl) elapsedEl.textContent = labelTpl.replace(':SECONDS', String(elapsed));
        }, 1000);

        var attempts = 0;
        function poll() {
            fetch(statusUrl, { headers: { 'Accept': 'application/json' } })
                .then(function(r) { return r.ok ? r.json() : null; })
                .then(function(data) {
                    if (!data) return;
                    if (data.status === 'completed' || data.status === 'failed') {
                        clearInterval(tickSec);
                        // Full reload so Blade re-renders the matching state
                        // with server-side data (URLs, token validation, etc.).
                        window.location.reload();
                        return;
                    }
                    scheduleNext();
                })
                .catch(function() { scheduleNext(); });
        }
        function scheduleNext() {
            attempts++;
            // Exponential-ish backoff: 2s, 2s, 3s, 3s, 4s, 5s, then cap at 6s.
            var delay = attempts < 4 ? 2000 : (attempts < 8 ? 3000 : (attempts < 14 ? 5000 : 6000));
            setTimeout(poll, delay);
        }
        setTimeout(poll, 1500); // first poll a bit faster
    }
})();
</script>
@endpush
