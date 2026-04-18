<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    {{-- Google Tag Manager --}}
    <script>(function(w,d,s,l,i){w[l]=w[l]||[];w[l].push({'gtm.start':
    new Date().getTime(),event:'gtm.js'});var f=d.getElementsByTagName(s)[0],
    j=d.createElement(s),dl=l!='dataLayer'?'&l='+l:'';j.async=true;j.src=
    'https://www.googletagmanager.com/gtm.js?id='+i+dl;f.parentNode.insertBefore(j,f);
    })(window,document,'script','dataLayer','GTM-NLFWQZGT');</script>

    @include('partials.seo')

    {{-- Fonts: DM Sans (body) + Cabinet Grotesk via Fontshare --}}
    <link rel="preconnect" href="https://api.fontshare.com">
    <link href="https://api.fontshare.com/v2/css?f[]=cabinet-grotesk@700,800&f[]=dm-sans@400,500,700&display=swap" rel="stylesheet">

    {{-- Lucide Icons --}}
    <script src="https://unpkg.com/lucide@latest/dist/umd/lucide.min.js"></script>

    {{-- Tailwind via CDN --}}
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        brand: {
                            50:  '#eef4ff',
                            100: '#dbe6fe',
                            200: '#bfd3fe',
                            300: '#93b4fd',
                            400: '#6090fa',
                            500: '#3b6cf5',
                            600: '#254bea',
                            700: '#1d3ad7',
                            800: '#1e31ae',
                            900: '#1e2f89',
                            950: '#171e54',
                        },
                        surface: {
                            50:  '#f8fafc',
                            100: '#f1f5f9',
                            200: '#e2e8f0',
                            300: '#cbd5e1',
                        },
                    },
                    fontFamily: {
                        display: ['"Cabinet Grotesk"', 'system-ui', 'sans-serif'],
                        body:    ['"DM Sans"', 'system-ui', 'sans-serif'],
                    },
                },
            },
        }
    </script>

    <style>
        * { font-family: 'DM Sans', system-ui, sans-serif; }
        .font-display { font-family: 'Cabinet Grotesk', system-ui, sans-serif; }

        /* Upload zone pulse */
        @keyframes dropzone-pulse {
            0%, 100% { border-color: rgb(59 108 245 / 0.3); }
            50% { border-color: rgb(59 108 245 / 0.7); }
        }
        .dropzone-active { animation: dropzone-pulse 1.5s ease-in-out infinite; }

        /* Smooth fade-in for tool cards */
        @keyframes fade-up {
            from { opacity: 0; transform: translateY(20px); }
            to   { opacity: 1; transform: translateY(0); }
        }
        .animate-fade-up {
            animation: fade-up 0.6s cubic-bezier(0.16, 1, 0.3, 1) forwards;
            opacity: 0;
        }

        /* Fade in only */
        @keyframes fade-in {
            from { opacity: 0; }
            to   { opacity: 1; }
        }
        .animate-fade-in {
            animation: fade-in 0.5s ease-out forwards;
            opacity: 0;
        }

        /* Scale up entrance */
        @keyframes scale-up {
            from { opacity: 0; transform: scale(0.95); }
            to   { opacity: 1; transform: scale(1); }
        }
        .animate-scale-up {
            animation: scale-up 0.4s cubic-bezier(0.16, 1, 0.3, 1) forwards;
            opacity: 0;
        }

        /* Processing spinner */
        @keyframes spin-slow { to { transform: rotate(360deg); } }
        .animate-spin-slow { animation: spin-slow 1.2s linear infinite; }

        /* Gradient text */
        .text-gradient {
            background: linear-gradient(135deg, #3b6cf5 0%, #1d3ad7 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        /* Floating background shapes */
        @keyframes float-slow {
            0%, 100% { transform: translateY(0) rotate(0deg); }
            50% { transform: translateY(-20px) rotate(3deg); }
        }
        .animate-float { animation: float-slow 8s ease-in-out infinite; }
        .animate-float-delay { animation: float-slow 10s ease-in-out 2s infinite; }

        /* Shimmer for loading */
        @keyframes shimmer {
            0% { background-position: -200% 0; }
            100% { background-position: 200% 0; }
        }
        .animate-shimmer {
            background: linear-gradient(90deg, transparent 25%, rgba(255,255,255,0.4) 50%, transparent 75%);
            background-size: 200% 100%;
            animation: shimmer 1.5s ease-in-out infinite;
        }

        /* Gradient border animation */
        @keyframes gradient-rotate {
            0% { --angle: 0deg; }
            100% { --angle: 360deg; }
        }

        /* Card hover lift */
        .card-hover {
            transition: transform 0.25s cubic-bezier(0.16, 1, 0.3, 1), box-shadow 0.25s ease-out;
        }
        .card-hover:hover {
            transform: translateY(-4px);
            box-shadow: 0 12px 40px -8px rgba(37, 75, 234, 0.15);
        }

        /* Respect reduced motion */
        @media (prefers-reduced-motion: reduce) {
            .animate-fade-up,
            .animate-fade-in,
            .animate-scale-up,
            .animate-float,
            .animate-float-delay { animation: none; opacity: 1; transform: none; }
            .card-hover:hover { transform: none; }
        }
    </style>

    <!-- Google Ads Conversion Tracking — completar AW-XXXXXXXXX al configurar cuenta
    <script async src="https://www.googletagmanager.com/gtag/js?id={{ config('services.google_ads.tag_id', 'AW-XXXXXXXXX') }}"></script>
    <script>
      window.dataLayer = window.dataLayer || [];
      function gtag(){dataLayer.push(arguments);}
      gtag('js', new Date());
      gtag('config', '{{ config('services.google_ads.tag_id', 'AW-XXXXXXXXX') }}');
    </script>
    -->

    @include('partials.animations')

    @stack('head')
</head>
<body class="bg-white text-slate-800 antialiased min-h-screen flex flex-col">
    {{-- Google Tag Manager (noscript) --}}
    <noscript><iframe src="https://www.googletagmanager.com/ns.html?id=GTM-NLFWQZGT" height="0" width="0" style="display:none;visibility:hidden"></iframe></noscript>

    {{-- Navbar --}}
    <nav class="sticky top-0 z-50 bg-white/80 backdrop-blur-lg border-b border-slate-100">
        <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between h-16">
                {{-- Logo --}}
                <a href="{{ route('home') }}" class="flex items-center gap-2 group">
                    <div class="w-8 h-8 rounded-lg bg-brand-600 flex items-center justify-center shadow-sm shadow-brand-600/25 group-hover:shadow-brand-600/40 transition-shadow">
                        <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                        </svg>
                    </div>
                    <span class="font-display font-bold text-lg text-slate-900 logo-shimmer">sofort<span class="text-brand-600">pdf</span></span>
                </a>

                {{-- Desktop nav --}}
                @php $loc = app()->getLocale(); @endphp
                <div class="hidden md:flex items-center gap-1">
                    <div class="relative group">
                        <button class="nav-link flex items-center gap-1 px-3 py-2 text-sm font-medium text-slate-600 hover:text-brand-600 rounded-lg hover:bg-brand-50 transition-colors">
                            {{ __('layout.nav_all_tools') }}
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                        </button>
                        <div class="absolute top-full left-1/2 -translate-x-1/2 mt-1 w-[28rem] sm:w-[34rem] lg:w-[40rem] bg-white rounded-xl shadow-xl shadow-slate-200/50 border border-slate-100 opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all duration-200 p-2 z-50">
                            <div class="grid grid-cols-2 lg:grid-cols-3 gap-x-1 gap-y-0.5">
                                @foreach(config('tools') as $key => $tool)
                                    @if($key !== 'aliases' && ($tool['enabled'] ?? false))
                                        <a href="{{ \App\Services\LocaleHelper::toolUrl($key) }}" class="flex items-center gap-3 px-3 py-2.5 text-sm text-slate-600 hover:text-brand-700 hover:bg-brand-50 rounded-lg transition-colors">
                                            @include('partials.tool-icon', ['icon' => $tool['icon'], 'size' => 'w-5 h-5'])
                                            <span class="truncate">{{ \App\Services\LocaleHelper::toolTitle($key) }}</span>
                                        </a>
                                    @endif
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Auth + Language --}}
                <div class="flex items-center gap-2">
                    {{-- Language selector --}}
                    <div class="relative group">
                        <button class="flex items-center gap-1.5 px-2.5 py-1.5 text-sm font-medium text-slate-500 hover:text-slate-700 rounded-lg hover:bg-slate-50 transition-colors">
                            <span class="text-base leading-none">{{ $loc === 'de' ? '🇩🇪' : '🇬🇧' }}</span>
                            <span class="hidden sm:inline">{{ strtoupper($loc) }}</span>
                            <i data-lucide="chevron-down" class="w-3 h-3 opacity-50"></i>
                        </button>
                        <div class="absolute top-full right-0 mt-1 w-40 bg-white rounded-xl shadow-xl shadow-slate-200/50 border border-slate-100 opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all duration-200 py-1 z-50">
                            <a href="{{ \App\Services\LocaleHelper::switchLocaleUrl('de') }}" class="flex items-center gap-2.5 px-3 py-2 text-sm {{ $loc === 'de' ? 'text-brand-600 bg-brand-50 font-medium' : 'text-slate-600 hover:bg-slate-50' }} transition-colors">
                                <span class="text-base leading-none">🇩🇪</span> Deutsch
                            </a>
                            <a href="{{ \App\Services\LocaleHelper::switchLocaleUrl('en') }}" class="flex items-center gap-2.5 px-3 py-2 text-sm {{ $loc === 'en' ? 'text-brand-600 bg-brand-50 font-medium' : 'text-slate-600 hover:bg-slate-50' }} transition-colors">
                                <span class="text-base leading-none">🇬🇧</span> English
                            </a>
                        </div>
                    </div>

                    @auth
                        <a href="{{ route('dashboard.index') }}" class="text-sm font-medium text-slate-600 hover:text-brand-600 px-3 py-2 rounded-lg hover:bg-brand-50 transition-colors">{{ __('layout.nav_dashboard') }}</a>
                        <form method="POST" action="/{{ $loc }}/{{ config("locales.auth_slugs.{$loc}.logout") }}" class="inline">
                            @csrf
                            <button type="submit" class="text-sm font-medium text-slate-400 hover:text-slate-600 px-3 py-2 transition-colors">{{ __('layout.nav_logout') }}</button>
                        </form>
                    @else
                        <a href="/{{ $loc }}/{{ config("locales.auth_slugs.{$loc}.login") }}" class="text-sm font-medium text-white bg-brand-600 hover:bg-brand-700 px-4 py-2 rounded-lg shadow-sm shadow-brand-600/25 hover:shadow-brand-600/40 transition-all">{{ __('layout.nav_login') }}</a>
                    @endauth

                    {{-- Mobile menu --}}
                    <button onclick="document.getElementById('mobile-menu').classList.toggle('hidden')" class="md:hidden p-2 text-slate-500 hover:text-slate-700">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/></svg>
                    </button>
                </div>
            </div>
        </div>

        {{-- Mobile menu --}}
        <div id="mobile-menu" class="hidden md:hidden border-t border-slate-100 bg-white pb-4">
            <div class="px-4 pt-2 space-y-1">
                @foreach(config('tools') as $key => $tool)
                    @if($key !== 'aliases' && ($tool['enabled'] ?? false))
                        <a href="{{ \App\Services\LocaleHelper::toolUrl($key) }}" class="block px-3 py-2 text-sm text-slate-600 hover:text-brand-600 rounded-lg hover:bg-brand-50">{{ \App\Services\LocaleHelper::toolTitle($key) }}</a>
                    @endif
                @endforeach
            </div>
        </div>
    </nav>

    {{-- Flash messages --}}
    @if(session('success'))
        <div class="max-w-6xl mx-auto px-4 mt-4 flash-message">
            <div class="bg-emerald-50 text-emerald-700 px-4 py-3 rounded-lg text-sm border border-emerald-100">{{ session('success') }}</div>
        </div>
    @endif
    @if(session('error'))
        <div class="max-w-6xl mx-auto px-4 mt-4 flash-message">
            <div class="bg-red-50 text-red-700 px-4 py-3 rounded-lg text-sm border border-red-100">{{ session('error') }}</div>
        </div>
    @endif

    {{-- Main content --}}
    <main class="flex-1">
        @yield('content')
    </main>

    {{-- Footer --}}
    <footer class="bg-slate-950 text-slate-400 mt-auto">
        <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
            <div class="grid grid-cols-2 md:grid-cols-4 gap-8">
                {{-- Brand --}}
                <div class="col-span-2 md:col-span-1">
                    <div class="flex items-center gap-2 mb-4">
                        <div class="w-7 h-7 rounded-md bg-brand-600 flex items-center justify-center">
                            <svg class="w-3.5 h-3.5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                            </svg>
                        </div>
                        <span class="font-display font-bold text-white">sofort<span class="text-brand-400">pdf</span></span>
                    </div>
                    <p class="text-sm text-slate-500 leading-relaxed">{{ __('layout.footer_brand_tagline') }}</p>
                </div>

                @php $fLoc = app()->getLocale(); @endphp
                {{-- Tools --}}
                <div>
                    <h4 class="font-display font-bold text-slate-200 text-sm mb-3">{{ __('layout.nav_tools') }}</h4>
                    <ul class="space-y-2 text-sm">
                        <li><a href="{{ \App\Services\LocaleHelper::toolUrl('merge') }}" class="hover:text-white transition-colors">{{ \App\Services\LocaleHelper::toolTitle('merge') }}</a></li>
                        <li><a href="{{ \App\Services\LocaleHelper::toolUrl('compress') }}" class="hover:text-white transition-colors">{{ \App\Services\LocaleHelper::toolTitle('compress') }}</a></li>
                        <li><a href="{{ \App\Services\LocaleHelper::toolUrl('pdf-to-word') }}" class="hover:text-white transition-colors">{{ \App\Services\LocaleHelper::toolTitle('pdf-to-word') }}</a></li>
                        <li><a href="{{ \App\Services\LocaleHelper::toolUrl('split') }}" class="hover:text-white transition-colors">{{ \App\Services\LocaleHelper::toolTitle('split') }}</a></li>
                    </ul>
                </div>

                {{-- Convert --}}
                <div>
                    <h4 class="font-display font-bold text-slate-200 text-sm mb-3">{{ __('layout.nav_convert') }}</h4>
                    <ul class="space-y-2 text-sm">
                        <li><a href="{{ \App\Services\LocaleHelper::toolUrl('jpg-to-pdf') }}" class="hover:text-white transition-colors">{{ \App\Services\LocaleHelper::toolTitle('jpg-to-pdf') }}</a></li>
                        <li><a href="{{ \App\Services\LocaleHelper::toolUrl('word-to-pdf') }}" class="hover:text-white transition-colors">{{ \App\Services\LocaleHelper::toolTitle('word-to-pdf') }}</a></li>
                        <li><a href="{{ \App\Services\LocaleHelper::toolUrl('pdf-to-jpg') }}" class="hover:text-white transition-colors">{{ \App\Services\LocaleHelper::toolTitle('pdf-to-jpg') }}</a></li>
                        <li><a href="{{ \App\Services\LocaleHelper::toolUrl('sign') }}" class="hover:text-white transition-colors">{{ \App\Services\LocaleHelper::toolTitle('sign') }}</a></li>
                    </ul>
                </div>

                {{-- Legal --}}
                <div>
                    <h4 class="font-display font-bold text-slate-200 text-sm mb-3">{{ __('layout.nav_legal') }}</h4>
                    <ul class="space-y-2 text-sm">
                        <li><a href="/{{ $fLoc }}/{{ config("locales.legal_slugs.{$fLoc}.imprint") }}" class="hover:text-white transition-colors">{{ __('layout.footer_imprint') }}</a></li>
                        <li><a href="/{{ $fLoc }}/{{ config("locales.legal_slugs.{$fLoc}.privacy") }}" class="hover:text-white transition-colors">{{ __('layout.footer_privacy') }}</a></li>
                        <li><a href="/{{ $fLoc }}/{{ config("locales.legal_slugs.{$fLoc}.terms") }}" class="hover:text-white transition-colors">{{ __('layout.footer_terms') }}</a></li>
                        <li><a href="/{{ $fLoc }}/{{ config("locales.contact_slugs.{$fLoc}") }}" class="hover:text-white transition-colors">{{ __('contact_ui.footer_link') }}</a></li>
                    </ul>
                </div>
            </div>

            <div class="border-t border-slate-800 mt-10 pt-6 flex flex-col sm:flex-row justify-between items-center gap-4">
                <p class="text-xs text-slate-500">{{ __('layout.footer_copyright', ['year' => date('Y')]) }}</p>
                <div class="flex items-center gap-4 text-xs text-slate-600">
                    <span class="flex items-center gap-1">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                        {{ __('layout.footer_ssl') }}
                    </span>
                    <span class="flex items-center gap-1">
                        🇪🇺 {{ __('layout.footer_eu_servers') }}
                    </span>
                </div>
            </div>
        </div>
    </footer>

    {{-- Payment modal (rendered globally, opened on demand by tool JS) --}}
    @include('partials.payment-modal')

    {{-- Loading overlay — conversie-pdf style with progress bar + steps --}}
    <div id="sofortpdf-loading-overlay" style="display:none; position:fixed; inset:0; z-index:9998; background:rgba(15,23,42,0.5); backdrop-filter:blur(4px); align-items:center; justify-content:center;">
        <div style="background:#fff; border-radius:16px; padding:32px 36px; text-align:center; box-shadow:0 24px 60px -12px rgba(15,23,42,0.35); width:100%; max-width:400px; margin:0 16px;">
            <p style="font-size:13px; color:#64748b; margin:0 0 14px;">{{ __('tool.please_wait') }}</p>
            <p id="spm-loading-pct" style="font-family:'Cabinet Grotesk',system-ui,sans-serif; font-weight:800; font-size:28px; color:#0f172a; margin:0 0 10px;">0 %</p>
            <div style="width:100%; height:8px; background:#e2e8f0; border-radius:4px; overflow:hidden; margin:0 0 20px;">
                <div id="spm-loading-bar" style="width:0%; height:100%; background:linear-gradient(90deg,#3b6cf5,#059669); border-radius:4px; transition:width 0.3s ease-out;"></div>
            </div>
            <div id="spm-loading-steps" style="text-align:left; display:flex; flex-direction:column; gap:8px;">
                <div class="spm-load-step" data-step="1" style="display:flex; align-items:center; gap:8px; font-size:13px; color:#94a3b8;">
                    <span class="spm-load-icon" style="width:18px; height:18px; border-radius:50%; border:1.5px solid #cbd5e1; display:inline-flex; align-items:center; justify-content:center; flex-shrink:0;"></span>
                    <span data-label>{{ __('tool.loading_step_1') }}</span>
                </div>
                <div class="spm-load-step" data-step="2" style="display:flex; align-items:center; gap:8px; font-size:13px; color:#94a3b8;">
                    <span class="spm-load-icon" style="width:18px; height:18px; border-radius:50%; border:1.5px solid #cbd5e1; display:inline-flex; align-items:center; justify-content:center; flex-shrink:0;"></span>
                    <span data-label>{{ __('tool.loading_step_2') }}</span>
                </div>
                <div class="spm-load-step" data-step="3" style="display:flex; align-items:center; gap:8px; font-size:13px; color:#94a3b8;">
                    <span class="spm-load-icon" style="width:18px; height:18px; border-radius:50%; border:1.5px solid #cbd5e1; display:inline-flex; align-items:center; justify-content:center; flex-shrink:0;"></span>
                    <span data-label>{{ __('tool.loading_step_3') }}</span>
                </div>
            </div>
        </div>
    </div>

    {{-- Global paywall flags for client-side gating --}}
    <script>
        window.sofortpdfPaywall = {
            bypassed: {{ \App\Services\PaywallBypass::applies(request()) ? 'true' : 'false' }},
            userHasSubscription: {{ (auth()->user() && auth()->user()->hasSofortpdfSubscription()) ? 'true' : 'false' }},
            needsPayment: function() {
                return !this.bypassed && !this.userHasSubscription;
            }
        };

        // Global helper: show loading overlay → open payment modal.
        // Only shows the loading animation once per page — subsequent clicks
        // go straight to the payment modal.
        window.__loadingShown = false;

        window.__sofortpdfShowLoadingThenPay = function(files, onSuccess, stepLabel) {
            if (window.__loadingShown) {
                // Skip loading, go straight to payment
                if (window.SofortpdfPaymentModal) {
                    window.SofortpdfPaymentModal.open({ files: files, onSuccess: onSuccess });
                }
                return;
            }

            var overlay = document.getElementById('sofortpdf-loading-overlay');
            var bar     = document.getElementById('spm-loading-bar');
            var pctEl   = document.getElementById('spm-loading-pct');
            var steps   = document.querySelectorAll('#spm-loading-steps .spm-load-step');
            var checkSvg = '<svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="#059669" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg>';

            // Update step 2 label if tool-specific text was passed
            if (stepLabel && steps[1]) {
                steps[1].querySelector('[data-label]').textContent = stepLabel;
            }

            // Reset
            if (bar) bar.style.width = '0%';
            if (pctEl) pctEl.textContent = '0 %';
            steps.forEach(function(s) {
                s.style.color = '#94a3b8';
                var icon = s.querySelector('.spm-load-icon');
                if (icon) { icon.innerHTML = ''; icon.style.background = 'transparent'; icon.style.borderColor = '#cbd5e1'; }
            });

            if (overlay) overlay.style.display = 'flex';

            function activateStep(idx) {
                if (!steps[idx]) return;
                steps[idx].style.color = '#0f172a';
                steps[idx].style.fontWeight = '600';
            }
            function completeStep(idx) {
                if (!steps[idx]) return;
                steps[idx].style.color = '#059669';
                var icon = steps[idx].querySelector('.spm-load-icon');
                if (icon) { icon.innerHTML = checkSvg; icon.style.borderColor = '#059669'; icon.style.background = '#f0fdf4'; }
            }

            // Animated sequence: 0→30% step1, 30→70% step2, 70→85% step3 → open modal
            var pct = 0;
            function setPct(val) { pct = val; if (bar) bar.style.width = val+'%'; if (pctEl) pctEl.textContent = val+' %'; }

            activateStep(0);
            var t1 = setInterval(function() { if (pct < 30) setPct(pct + 2); }, 50);

            setTimeout(function() { clearInterval(t1); setPct(30); completeStep(0); activateStep(1); }, 800);

            var t2;
            setTimeout(function() { t2 = setInterval(function() { if (pct < 70) setPct(pct + 2); }, 50); }, 900);

            setTimeout(function() { clearInterval(t2); setPct(70); completeStep(1); activateStep(2); }, 2000);

            var t3;
            setTimeout(function() { t3 = setInterval(function() { if (pct < 85) setPct(pct + 1); }, 50); }, 2100);

            setTimeout(function() {
                clearInterval(t3);
                setPct(85);
                if (overlay) overlay.style.display = 'none';
                window.__loadingShown = true;
                if (window.SofortpdfPaymentModal) {
                    window.SofortpdfPaymentModal.open({ files: files, onSuccess: onSuccess });
                }
            }, 3000);
        };
    </script>

    @stack('scripts')
    <script>lucide.createIcons();</script>
</body>
</html>
