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
            <div class="bg-white rounded-3xl border border-slate-100 shadow-xl shadow-slate-200/40 p-8 sm:p-12 text-center">

                {{-- Success check icon --}}
                <div class="confirmation-check w-20 h-20 rounded-full bg-emerald-50 flex items-center justify-center mx-auto mb-6">
                    <svg class="w-10 h-10 text-emerald-500" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24" stroke-linecap="round" stroke-linejoin="round">
                        <polyline points="20 6 9 17 4 12"></polyline>
                    </svg>
                </div>

                {{-- Heading --}}
                <h1 class="font-display font-extrabold text-3xl sm:text-4xl text-slate-900 tracking-tight">
                    {{ __('confirmation.heading') }}
                </h1>
                <p class="mt-4 text-slate-500 text-base sm:text-lg leading-relaxed">
                    {{ $paymentSuccess ? __('confirmation.subheading_paid') : __('confirmation.subheading') }}
                </p>

                @if ($tokenValid && $downloadUrl)
                    {{-- Filename pill --}}
                    @if ($filename)
                        <div class="mt-8 inline-flex items-center gap-3 max-w-full bg-slate-50 border border-slate-100 rounded-2xl px-5 py-4">
                            <div class="w-10 h-10 rounded-xl bg-brand-50 flex items-center justify-center flex-shrink-0">
                                <svg class="w-5 h-5 text-brand-600" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/>
                                    <polyline points="14 2 14 8 20 8"/>
                                </svg>
                            </div>
                            <div class="text-left min-w-0">
                                <p class="text-xs text-slate-400 uppercase tracking-wider">{{ __('confirmation.filename_label') }}</p>
                                <p class="text-sm font-medium text-slate-800 truncate" title="{{ $filename }}">{{ $filename }}</p>
                            </div>
                        </div>
                    @endif

                    <p class="mt-6 text-xs text-slate-400">{{ __('confirmation.auto_download_note') }}</p>

                    {{-- Primary CTA: Download --}}
                    <div class="mt-8">
                        <a id="confirmation-download" href="{{ $downloadUrl }}"
                           class="btn-press inline-flex items-center justify-center gap-2.5 bg-emerald-500 hover:bg-emerald-600 text-white font-display font-bold px-10 py-4 rounded-2xl shadow-lg shadow-emerald-500/25 hover:shadow-emerald-500/40 text-base"
                           style="transition: transform 160ms cubic-bezier(0.23,1,0.32,1), background-color 200ms ease-out, box-shadow 200ms ease-out;">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/>
                                <polyline points="7 10 12 15 17 10"/>
                                <line x1="12" y1="15" x2="12" y2="3"/>
                            </svg>
                            <span id="confirmation-download-text">{{ __('confirmation.download_button') }}</span>
                        </a>
                    </div>
                @else
                    <div class="mt-8 bg-amber-50 border border-amber-100 rounded-2xl p-6 text-left">
                        <p class="font-display font-bold text-amber-800 text-base">{{ __('confirmation.no_token_heading') }}</p>
                        <p class="mt-2 text-sm text-amber-700">{{ __('confirmation.no_token_message') }}</p>
                    </div>
                @endif

                {{-- Secondary CTAs --}}
                <div class="mt-10 flex flex-col sm:flex-row items-center justify-center gap-3">
                    @auth
                        <a href="{{ route('dashboard.index') }}" class="inline-flex items-center justify-center gap-2 px-5 py-3 rounded-xl border border-slate-200 bg-white text-sm font-medium text-slate-700 hover:bg-slate-50 transition-colors">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                            </svg>
                            {{ __('confirmation.dashboard_button') }}
                        </a>
                    @else
                        <a href="/{{ $loc }}/{{ $dashboardSlug }}" class="inline-flex items-center justify-center gap-2 px-5 py-3 rounded-xl border border-slate-200 bg-white text-sm font-medium text-slate-700 hover:bg-slate-50 transition-colors">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" stroke-linecap="round" stroke-linejoin="round">
                                <circle cx="12" cy="7" r="4"/>
                                <path d="M5.5 21a6.5 6.5 0 0 1 13 0"/>
                            </svg>
                            {{ __('confirmation.dashboard_button') }}
                        </a>
                    @endauth

                    <a href="{{ route('home') }}" class="inline-flex items-center justify-center gap-2 px-5 py-3 rounded-xl text-sm font-medium text-slate-500 hover:text-brand-600 transition-colors">
                        {{ __('confirmation.home_button') }}
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M5 12h14M12 5l7 7-7 7"/>
                        </svg>
                    </a>
                </div>
            </div>
        </div>
    </section>
@endsection

@push('head')
<style>
    .confirmation-check {
        animation: confirmation-pop 0.5s var(--ease-spring, cubic-bezier(0.34, 1.56, 0.64, 1)) both;
    }
    @keyframes confirmation-pop {
        0%   { opacity: 0; transform: scale(0.4); }
        100% { opacity: 1; transform: scale(1); }
    }
</style>
@endpush

@if ($tokenValid && $downloadUrl)
    @push('scripts')
    <script>
        (function() {
            // Trigger the download exactly once per page load. Use a hidden
            // anchor click so the browser shows its native download UI
            // instead of replacing the page contents.
            var downloadUrl = @json($downloadUrl);
            var filename = @json($filename);

            function triggerDownload() {
                var a = document.createElement('a');
                a.href = downloadUrl;
                if (filename) a.download = filename;
                a.style.display = 'none';
                document.body.appendChild(a);
                a.click();
                setTimeout(function() { document.body.removeChild(a); }, 1000);
            }

            // Small delay so the page renders before the browser dialog appears
            if (document.readyState === 'loading') {
                document.addEventListener('DOMContentLoaded', function() { setTimeout(triggerDownload, 350); });
            } else {
                setTimeout(triggerDownload, 350);
            }
        })();
    </script>
    @endpush
@endif
