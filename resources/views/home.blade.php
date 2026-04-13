@extends('layouts.app')

@php use App\Services\LocaleHelper; @endphp

@section('content')

    {{-- ============================== HERO ============================== --}}
    <section class="relative overflow-hidden bg-gradient-to-b from-brand-50/60 via-white to-white">

        {{-- Noise texture overlay --}}
        <div class="absolute inset-0 pointer-events-none opacity-[0.03]" aria-hidden="true" style="background-image: url('data:image/svg+xml,%3Csvg viewBox=%220 0 256 256%22 xmlns=%22http://www.w3.org/2000/svg%22%3E%3Cfilter id=%22noise%22%3E%3CfeTurbulence type=%22fractalNoise%22 baseFrequency=%220.9%22 numOctaves=%224%22 stitchTiles=%22stitch%22/%3E%3C/filter%3E%3Crect width=%22100%25%22 height=%22100%25%22 filter=%22url(%23noise)%22/%3E%3C/svg%3E'); background-repeat: repeat; background-size: 256px 256px;"></div>

        {{-- Floating gradient orbs --}}
        <div class="absolute inset-0 pointer-events-none overflow-hidden" aria-hidden="true">
            <div class="hero-orb absolute w-[28rem] h-[28rem] rounded-full top-[-6rem] left-[-8rem]" style="background: radial-gradient(circle, rgba(191,211,254,0.30) 0%, transparent 70%); animation-duration: 10s;"></div>
            <div class="hero-orb absolute w-[36rem] h-[36rem] rounded-full top-8 right-[-10rem]" style="background: radial-gradient(circle, rgba(96,144,250,0.20) 0%, transparent 70%); animation-duration: 12s; animation-delay: -3s;"></div>
            <div class="hero-orb absolute w-[22rem] h-[22rem] rounded-full bottom-4 left-[30%]" style="background: radial-gradient(circle, rgba(147,180,253,0.25) 0%, transparent 70%); animation-duration: 8s; animation-delay: -5s;"></div>
        </div>

        <div class="relative max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 pt-20 sm:pt-28 lg:pt-36 pb-16 sm:pb-20 lg:pb-28 text-center">

            {{-- Pill badge --}}
            <div class="hero-stagger inline-flex items-center gap-2 bg-white/80 backdrop-blur-sm border border-brand-100/80 rounded-full px-4 py-1.5 mb-8 shadow-sm" style="--stagger: 0">
                <i data-lucide="sparkles" class="w-3.5 h-3.5 text-brand-500"></i>
                <span class="text-sm font-medium text-slate-600">{{ __('home.hero_badge') }}</span>
            </div>

            {{-- Headline --}}
            <h1 class="hero-stagger font-display font-extrabold text-5xl sm:text-6xl lg:text-7xl text-slate-900 tracking-tight leading-[1.08] max-w-4xl mx-auto" style="--stagger: 1">
                {!! __('home.hero_title_line1') !!}
                <br class="hidden sm:block">
                <span class="text-gradient">{!! __('home.hero_title_highlight') !!}</span>
            </h1>

            {{-- Subtitle --}}
            <p class="hero-stagger mt-6 text-lg sm:text-xl text-slate-500 max-w-2xl mx-auto leading-relaxed" style="--stagger: 2">
                {!! __('home.hero_subtitle') !!}
            </p>

            {{-- CTA Buttons --}}
            <div class="hero-stagger mt-10 flex flex-col sm:flex-row items-center justify-center gap-4" style="--stagger: 3">
                <a href="{{ route('checkout.start') }}"
                   class="btn-press inline-flex items-center gap-2.5 bg-brand-600 text-white font-display font-bold px-8 py-4 rounded-2xl shadow-lg shadow-brand-600/25 transition-all duration-200 text-base"
                   style="transition-timing-function: cubic-bezier(0.23, 1, 0.32, 1);">
                    {{ __('home.hero_cta_primary') }}
                    <i data-lucide="arrow-right" class="w-5 h-5"></i>
                </a>
                <a href="#tools"
                   class="btn-press inline-flex items-center gap-2 text-slate-600 font-medium px-6 py-4 rounded-2xl transition-all duration-200 text-base"
                   style="transition-timing-function: cubic-bezier(0.23, 1, 0.32, 1);">
                    {{ __('home.hero_cta_secondary') }}
                    <i data-lucide="chevron-down" class="w-4 h-4"></i>
                </a>
            </div>

            {{-- Social proof bar --}}
            <div class="hero-stagger mt-14 sm:mt-16 flex flex-wrap items-center justify-center gap-x-6 gap-y-4" style="--stagger: 4">
                <div class="flex items-center gap-2 text-sm text-slate-500">
                    <i data-lucide="users" class="w-4 h-4 text-brand-500"></i>
                    <span class="font-medium text-slate-700">10.000+</span> {{ __('home.social_users') }}
                </div>
                <span class="hidden sm:inline-block w-1 h-1 rounded-full bg-slate-300" aria-hidden="true"></span>
                <div class="flex items-center gap-2 text-sm text-slate-500">
                    <i data-lucide="lock" class="w-4 h-4 text-emerald-500"></i>
                    <span class="font-medium text-slate-700">256-Bit</span> SSL
                </div>
                <span class="hidden sm:inline-block w-1 h-1 rounded-full bg-slate-300" aria-hidden="true"></span>
                <div class="flex items-center gap-2 text-sm text-slate-500">
                    <i data-lucide="shield-check" class="w-4 h-4 text-blue-500"></i>
                    {{ __('home.social_gdpr') }}
                </div>
                <span class="hidden sm:inline-block w-1 h-1 rounded-full bg-slate-300" aria-hidden="true"></span>
                <div class="flex items-center gap-2 text-sm text-slate-500">
                    <i data-lucide="globe" class="w-4 h-4 text-violet-500"></i>
                    {{ __('home.social_eu_servers') }}
                </div>
            </div>
        </div>
    </section>

    {{-- ============================== TOOL GRID ============================== --}}
    <section id="tools" class="scroll-mt-20 max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-20 sm:py-24">

        <div class="text-center mb-14">
            <div class="flex items-center justify-center gap-4 mb-4">
                <div class="h-px w-12 bg-gradient-to-r from-transparent to-slate-200" aria-hidden="true"></div>
                <h2 class="font-display font-extrabold text-3xl sm:text-4xl text-slate-900 tracking-tight">{{ __('home.tools_heading') }}</h2>
                <div class="h-px w-12 bg-gradient-to-l from-transparent to-slate-200" aria-hidden="true"></div>
            </div>
            <p class="text-lg text-slate-500 max-w-xl mx-auto">{{ __('home.tools_subheading') }}</p>
        </div>

        @php
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

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-5">
            @foreach($tools as $tool)
                @php $tc = $toolColorMap[$tool['icon']] ?? $defaultToolColor; @endphp
                <a href="{{ LocaleHelper::toolUrl($tool['key']) }}"
                   class="tool-card group relative bg-white rounded-2xl border border-slate-100 p-6 {{ $tc['border'] }} transition-all duration-200"
                   style="--card-delay: {{ $loop->index * 60 }}ms; transition-timing-function: cubic-bezier(0.23, 1, 0.32, 1);">

                    <div class="flex items-start justify-between">
                        {{-- Icon container --}}
                        <div class="w-12 h-12 rounded-2xl {{ $tc['bg'] }} flex items-center justify-center tool-card-icon transition-transform duration-200" style="transition-timing-function: cubic-bezier(0.23, 1, 0.32, 1);">
                            @include('partials.tool-icon', ['icon' => $tool['icon'], 'size' => 'w-5 h-5'])
                        </div>

                        {{-- Arrow (appears on hover) --}}
                        <div class="tool-card-arrow opacity-0 translate-x-1 transition-all duration-200" style="transition-timing-function: cubic-bezier(0.23, 1, 0.32, 1);">
                            <i data-lucide="arrow-right" class="w-4 h-4 text-slate-300"></i>
                        </div>
                    </div>

                    {{-- Text --}}
                    <h3 class="font-display font-bold text-[15px] text-slate-900 mt-4 transition-colors duration-200">{{ $tool['name'] }}</h3>
                    <p class="text-xs text-slate-400 mt-1.5 leading-relaxed line-clamp-2">{{ $tool['description'] }}</p>
                </a>
            @endforeach
        </div>
    </section>

    {{-- ============================== HOW IT WORKS ============================== --}}
    <section class="bg-slate-50/50 border-y border-slate-100">
        <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 py-20 sm:py-24">

            <div class="text-center mb-16">
                <h2 class="font-display font-extrabold text-3xl sm:text-4xl text-slate-900 tracking-tight">{{ __('home.how_heading') }}</h2>
                <p class="mt-4 text-lg text-slate-500">{{ __('home.how_subheading') }}</p>
            </div>

            <div class="relative grid grid-cols-1 md:grid-cols-3 gap-12 md:gap-8">

                {{-- Dashed connecting line (desktop only) --}}
                <div class="hidden md:block absolute top-10 left-[calc(16.666%+2rem)] right-[calc(16.666%+2rem)] h-px border-t-2 border-dashed border-brand-200" aria-hidden="true"></div>

                @php
                    $howSteps = [
                        ['num' => '1', 'icon' => 'upload-cloud', 'title' => __('home.how_step1_title'), 'desc' => __('home.how_step1_desc')],
                        ['num' => '2', 'icon' => 'zap',          'title' => __('home.how_step2_title'), 'desc' => __('home.how_step2_desc')],
                        ['num' => '3', 'icon' => 'download',     'title' => __('home.how_step3_title'), 'desc' => __('home.how_step3_desc')],
                    ];
                @endphp

                @foreach($howSteps as $step)
                    <div class="observe-animate relative text-center" data-delay="{{ $loop->index * 120 }}">
                        {{-- Step number circle --}}
                        <div class="relative z-10 w-20 h-20 rounded-full bg-gradient-to-br from-brand-500 to-brand-700 text-white font-display font-extrabold text-2xl flex items-center justify-center mx-auto mb-5 shadow-lg shadow-brand-500/20 ring-4 ring-white">
                            {{ $step['num'] }}
                        </div>

                        {{-- Icon --}}
                        <div class="flex justify-center mb-4">
                            <i data-lucide="{{ $step['icon'] }}" class="w-6 h-6 text-brand-500"></i>
                        </div>

                        <h3 class="font-display font-bold text-lg text-slate-900 mb-2">{{ $step['title'] }}</h3>
                        <p class="text-sm text-slate-500 leading-relaxed max-w-xs mx-auto">{!! $step['desc'] !!}</p>
                    </div>
                @endforeach
            </div>
        </div>
    </section>

    {{-- ============================== TESTIMONIALS ============================== --}}
    <section class="bg-white py-20 sm:py-24">
        <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">

            <div class="text-center mb-14">
                <h2 class="font-display font-extrabold text-3xl sm:text-4xl text-slate-900 tracking-tight">{{ __('home.testimonials_heading') }}</h2>
                <p class="mt-4 text-lg text-slate-500 max-w-xl mx-auto">{{ __('home.testimonials_subheading') }}</p>
            </div>

            @php
                $testimonials = [];
                for ($i = 1; $i <= 6; $i++) {
                    $testimonials[] = [
                        'quote' => __("home.testimonial_{$i}_quote"),
                        'name'  => __("home.testimonial_{$i}_name"),
                        'role'  => __("home.testimonial_{$i}_role"),
                        'tool'  => __("home.testimonial_{$i}_tool"),
                    ];
                }
            @endphp

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                @foreach($testimonials as $testimonial)
                    <div class="observe-animate bg-slate-50 rounded-2xl p-6 border border-slate-100" data-delay="{{ $loop->index * 80 }}">
                        {{-- Stars --}}
                        <div class="flex items-center gap-0.5 mb-4">
                            @for($i = 0; $i < 5; $i++)
                                <i data-lucide="star" class="w-4 h-4 text-amber-400 fill-amber-400"></i>
                            @endfor
                        </div>

                        {{-- Quote --}}
                        <p class="text-slate-600 text-sm leading-relaxed italic mb-5">&ldquo;{!! $testimonial['quote'] !!}&rdquo;</p>

                        {{-- Author --}}
                        <div>
                            <p class="font-display font-bold text-slate-900 text-sm">{{ $testimonial['name'] }}</p>
                            <p class="text-slate-400 text-xs">{{ $testimonial['role'] }} &middot; {!! $testimonial['tool'] !!}</p>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </section>

    {{-- ============================== TRUST SECTION ============================== --}}
    <section class="bg-slate-950">
        <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-20 sm:py-24">

            <div class="text-center mb-14">
                <h2 class="font-display font-extrabold text-3xl sm:text-4xl text-white tracking-tight">{{ __('home.trust_heading') }}</h2>
                <p class="mt-4 text-lg text-slate-400 max-w-xl mx-auto">{{ __('home.trust_subheading') }}</p>
            </div>

            @php
                $trustFeatures = [
                    ['icon' => 'lock',         'title' => __('home.trust_ssl_title'),      'desc' => __('home.trust_ssl_desc')],
                    ['icon' => 'shield-check', 'title' => __('home.trust_gdpr_title'),     'desc' => __('home.trust_gdpr_desc')],
                    ['icon' => 'timer',        'title' => __('home.trust_deletion_title'), 'desc' => __('home.trust_deletion_desc')],
                    ['icon' => 'globe',        'title' => __('home.trust_servers_title'),  'desc' => __('home.trust_servers_desc')],
                ];
            @endphp

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
                @foreach($trustFeatures as $feature)
                    <div class="observe-animate trust-card bg-slate-900/50 backdrop-blur rounded-2xl border border-slate-800 p-6 text-center sm:text-left" data-delay="{{ $loop->index * 70 }}">
                        <div class="w-12 h-12 rounded-2xl bg-brand-400/10 flex items-center justify-center mx-auto sm:mx-0 mb-4">
                            <i data-lucide="{{ $feature['icon'] }}" class="w-6 h-6 text-brand-400"></i>
                        </div>
                        <h3 class="font-display font-bold text-white text-base mb-2">{!! $feature['title'] !!}</h3>
                        <p class="text-sm text-slate-400 leading-relaxed">{!! $feature['desc'] !!}</p>
                    </div>
                @endforeach
            </div>
        </div>
    </section>

    {{-- ============================== FAQ ============================== --}}
    <section class="bg-slate-50/50 border-y border-slate-100 py-20 sm:py-24">
        <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">

            <div class="text-center mb-12">
                <h2 class="font-display font-extrabold text-3xl sm:text-4xl text-slate-900 tracking-tight">{{ __('home.faq_heading') }}</h2>
                <p class="mt-4 text-lg text-slate-500">{{ __('home.faq_subheading') }}</p>
            </div>

            @php
                $homeFaqs = [];
                for ($i = 1; $i <= 6; $i++) {
                    $homeFaqs[] = [
                        'q' => __("home.faq_{$i}_q"),
                        'a' => __("home.faq_{$i}_a"),
                    ];
                }
            @endphp

            <div class="space-y-3">
                @foreach($homeFaqs as $faq)
                    <details class="observe-animate group bg-white rounded-2xl border border-slate-100 overflow-hidden" data-delay="{{ $loop->index * 60 }}">
                        <summary class="flex items-center justify-between cursor-pointer px-6 py-5 font-display font-bold text-slate-800 select-none list-none" style="list-style:none">
                            <span>{!! $faq['q'] !!}</span>
                            <i data-lucide="chevron-down" class="w-5 h-5 text-slate-400 flex-shrink-0 ml-4 transition-transform duration-200" style="transition-timing-function:cubic-bezier(0.23,1,0.32,1)"></i>
                        </summary>
                        <div class="px-6 pb-5 text-sm text-slate-500 leading-relaxed">
                            {!! $faq['a'] !!}
                        </div>
                    </details>
                @endforeach
            </div>
        </div>
    </section>

    {{-- ============================== CTA SECTION ============================== --}}
    <section class="relative overflow-hidden bg-gradient-to-br from-brand-600 to-brand-700">

        {{-- Decorative blurred orbs --}}
        <div class="absolute inset-0 pointer-events-none" aria-hidden="true">
            <div class="absolute -top-32 -right-32 w-[30rem] h-[30rem] rounded-full bg-white/[0.07] blur-3xl"></div>
            <div class="absolute -bottom-32 -left-32 w-[26rem] h-[26rem] rounded-full bg-white/[0.05] blur-3xl"></div>
        </div>

        <div class="relative max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-20 sm:py-24 text-center">
            <h2 class="font-display font-extrabold text-3xl sm:text-4xl lg:text-5xl text-white tracking-tight leading-tight">
                {{ __('home.cta_title') }}
            </h2>
            <p class="mt-5 text-lg sm:text-xl text-brand-100 max-w-lg mx-auto leading-relaxed">
                {!! __('home.cta_subtitle') !!}
            </p>

            {{-- Glassmorphic price card --}}
            <div class="mt-10 inline-flex flex-col items-center bg-white/10 backdrop-blur-md rounded-3xl px-10 py-8 ring-1 ring-white/20">
                <span class="text-brand-100 text-sm font-medium uppercase tracking-wider mb-2">{{ __('home.cta_try_now') }}</span>
                <div class="flex items-baseline gap-1">
                    <span class="font-display font-extrabold text-5xl sm:text-6xl text-white">1,50</span>
                    <span class="font-display font-bold text-2xl text-brand-200">&euro;</span>
                </div>
                <span class="text-brand-200 text-sm mt-1">{{ __('home.cta_trial_days', ['days' => config('services.stripe.trial_days', 2)]) }}</span>
            </div>

            <div class="mt-10">
                <a href="{{ route('checkout.start') }}"
                   class="btn-press inline-flex items-center gap-2.5 bg-white text-brand-700 font-display font-bold px-10 py-4 rounded-2xl shadow-xl shadow-brand-900/20 transition-all duration-200 text-lg"
                   style="transition-timing-function: cubic-bezier(0.23, 1, 0.32, 1);">
                    {{ __('home.cta_unlock') }}
                    <i data-lucide="arrow-right" class="w-5 h-5"></i>
                </a>
            </div>
            <p class="mt-5 text-sm text-brand-200">{{ __('home.cta_no_auto_renewal') }}</p>
        </div>
    </section>

@endsection

@push('head')
{{-- FAQ Schema.org structured data --}}
<script type="application/ld+json">
@php
    $faqSchemaItems = [];
    for ($i = 1; $i <= 6; $i++) {
        $faqSchemaItems[] = [
            'q' => __("home.faq_{$i}_q"),
            'a' => __("home.faq_{$i}_a"),
        ];
    }
@endphp
{!! json_encode([
    '@context' => 'https://schema.org',
    '@type' => 'FAQPage',
    'mainEntity' => array_map(fn($f) => [
        '@type' => 'Question',
        'name' => $f['q'],
        'acceptedAnswer' => [
            '@type' => 'Answer',
            'text' => $f['a'],
        ],
    ], $faqSchemaItems),
], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT) !!}
</script>
<style>
    /* ========= HERO ORBS ========= */
    @keyframes orb-float {
        0%, 100% { transform: translateY(0) scale(1); }
        50%      { transform: translateY(-24px) scale(1.04); }
    }
    .hero-orb {
        animation: orb-float 10s ease-in-out infinite;
        filter: blur(80px);
    }

    /* ========= HERO STAGGER ENTRANCE ========= */
    .hero-stagger {
        opacity: 0;
        transform: translateY(16px) scale(0.96);
        animation: hero-enter 0.5s cubic-bezier(0.23, 1, 0.32, 1) forwards;
        animation-delay: calc(var(--stagger, 0) * 70ms + 100ms);
    }
    @keyframes hero-enter {
        to { opacity: 1; transform: translateY(0) scale(1); }
    }

    /* ========= TOOL CARDS ========= */
    .tool-card {
        opacity: 0;
        transform: translateY(12px) scale(0.96);
        animation: tool-enter 0.45s cubic-bezier(0.23, 1, 0.32, 1) forwards;
        animation-delay: var(--card-delay, 0ms);
    }
    @keyframes tool-enter {
        to { opacity: 1; transform: translateY(0) scale(1); }
    }

    /* Hover states gated behind fine pointer */
    @media (hover: hover) and (pointer: fine) {
        .tool-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 16px 48px -12px rgba(0,0,0,0.10), 0 4px 16px -4px rgba(0,0,0,0.06);
        }
        .tool-card:hover .tool-card-icon {
            transform: scale(1.05);
        }
        .tool-card:hover .tool-card-arrow {
            opacity: 1;
            transform: translateX(0);
        }
        .tool-card:hover h3 {
            color: rgb(29 58 215); /* brand-700 */
        }
        .btn-press:hover {
            box-shadow: 0 20px 48px -12px rgba(0,0,0,0.18);
            transform: translateY(-1px);
        }
    }

    /* Active / press states (all pointers) */
    .btn-press:active {
        transform: scale(0.97) !important;
        transition-duration: 100ms;
    }
    .tool-card:active {
        transform: scale(0.98) !important;
        transition-duration: 100ms;
    }

    /* ========= SCROLL-TRIGGERED ANIMATIONS ========= */
    .observe-animate {
        opacity: 0;
        transform: translateY(16px) scale(0.96);
        transition: opacity 0.45s cubic-bezier(0.23, 1, 0.32, 1),
                    transform 0.45s cubic-bezier(0.23, 1, 0.32, 1);
    }
    .observe-animate.is-visible {
        opacity: 1;
        transform: translateY(0) scale(1);
    }

    /* Trust card hover */
    @media (hover: hover) and (pointer: fine) {
        .trust-card {
            transition: border-color 0.2s ease, transform 0.2s cubic-bezier(0.23, 1, 0.32, 1);
        }
        .trust-card:hover {
            border-color: rgba(96,144,250,0.3);
            transform: translateY(-2px);
        }
    }

    /* ========= FAQ ACCORDION ========= */
    details summary::-webkit-details-marker { display: none; }
    details summary::marker { display: none; }
    details[open] summary i[data-lucide="chevron-down"] {
        transform: rotate(180deg);
    }

    /* ========= REDUCED MOTION ========= */
    @media (prefers-reduced-motion: reduce) {
        .hero-orb {
            animation: none !important;
        }
        .hero-stagger {
            animation: none !important;
            opacity: 1 !important;
            transform: none !important;
        }
        .tool-card {
            animation: none !important;
            opacity: 1 !important;
            transform: none !important;
        }
        .observe-animate {
            opacity: 1 !important;
            transform: none !important;
            transition: none !important;
        }
        *,
        *::before,
        *::after {
            transition-duration: 0.01ms !important;
            animation-duration: 0.01ms !important;
        }
    }
</style>
@endpush

@push('scripts')
<script>
    // IntersectionObserver for scroll-triggered animations
    (function() {
        var els = document.querySelectorAll('.observe-animate');
        if (!els.length) return;

        // Respect reduced motion
        if (window.matchMedia('(prefers-reduced-motion: reduce)').matches) {
            els.forEach(function(el) { el.classList.add('is-visible'); });
            return;
        }

        var observer = new IntersectionObserver(function(entries) {
            entries.forEach(function(entry) {
                if (entry.isIntersecting) {
                    var delay = parseInt(entry.target.getAttribute('data-delay') || '0', 10);
                    setTimeout(function() {
                        entry.target.classList.add('is-visible');
                    }, delay);
                    observer.unobserve(entry.target);
                }
            });
        }, { threshold: 0.15, rootMargin: '0px 0px -40px 0px' });

        els.forEach(function(el) { observer.observe(el); });
    })();
</script>
@endpush
