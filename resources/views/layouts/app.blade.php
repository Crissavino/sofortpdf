<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    {{-- Cookiebot — MUST be before GTM so it blocks cookies until consent --}}
    <script id="Cookiebot" src="https://consent.cookiebot.com/uc.js"
        data-cbid="5845f4ac-62a8-462b-864e-3e41ffaa2b9b"
        data-blockingmode="auto"
        data-culture="{{ app()->getLocale() }}"
        type="text/javascript"></script>

    {{-- Microsoft Clarity --}}
    <script type="text/javascript">
    (function(c,l,a,r,i,t,y){
        c[a]=c[a]||function(){(c[a].q=c[a].q||[]).push(arguments)};
        t=l.createElement(r);t.async=1;t.src="https://www.clarity.ms/tag/"+i;
        y=l.getElementsByTagName(r)[0];y.parentNode.insertBefore(t,y);
    })(window, document, "clarity", "script", "wh83oub7h7");
    </script>

    {{-- Google Tag Manager --}}
    <script>(function(w,d,s,l,i){w[l]=w[l]||[];w[l].push({'gtm.start':
    new Date().getTime(),event:'gtm.js'});var f=d.getElementsByTagName(s)[0],
    j=d.createElement(s),dl=l!='dataLayer'?'&l='+l:'';j.async=true;j.src=
    'https://www.googletagmanager.com/gtm.js?id='+i+dl;f.parentNode.insertBefore(j,f);
    })(window,document,'script','dataLayer','GTM-NLFWQZGT');</script>

    @include('partials.seo')

    {{-- Fonts: DM Sans (body) + Cabinet Grotesk via Fontshare --}}
    <link rel="preconnect" href="https://api.fontshare.com" data-cookieconsent="ignore">
    <link href="https://api.fontshare.com/v2/css?f[]=cabinet-grotesk@700,800&f[]=dm-sans@400,500,700&display=swap" rel="stylesheet" data-cookieconsent="ignore">

    {{-- Lucide Icons — pinned version (was @latest), deferred to unblock render --}}
    <script data-cookieconsent="ignore" src="https://unpkg.com/lucide@0.263.0/dist/umd/lucide.min.js" defer></script>

    {{-- Compiled Tailwind CSS + custom animations.
         Built via `npx tailwindcss -i resources/css/app.css -o public/css/app.css --minify`.
         Cache-busted by file mtime. --}}
    @php
        $appCssPath = public_path('css/app.css');
        $appCssVersion = file_exists($appCssPath) ? filemtime($appCssPath) : '0';
    @endphp
    <link rel="stylesheet" href="{{ asset('css/app.css') }}?v={{ $appCssVersion }}" data-cookieconsent="ignore">

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
                    @php
                        $localeMeta = [
                            'de' => ['flag' => '🇩🇪', 'label' => 'Deutsch'],
                            'en' => ['flag' => '🇬🇧', 'label' => 'English'],
                            'hu' => ['flag' => '🇭🇺', 'label' => 'Magyar'],
                            'cs' => ['flag' => '🇨🇿', 'label' => 'Čeština'],
                            'pl' => ['flag' => '🇵🇱', 'label' => 'Polski'],
                        ];
                        $supportedLocales = config('locales.supported', ['en']);
                        $currentMeta = $localeMeta[$loc] ?? $localeMeta['en'];
                    @endphp
                    <div class="relative group">
                        <button class="flex items-center gap-1.5 px-2.5 py-1.5 text-sm font-medium text-slate-500 hover:text-slate-700 rounded-lg hover:bg-slate-50 transition-colors">
                            <span class="text-base leading-none">{{ $currentMeta['flag'] }}</span>
                            <span class="hidden sm:inline">{{ strtoupper($loc) }}</span>
                            <i data-lucide="chevron-down" class="w-3 h-3 opacity-50"></i>
                        </button>
                        <div class="absolute top-full right-0 mt-1 w-40 bg-white rounded-xl shadow-xl shadow-slate-200/50 border border-slate-100 opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all duration-200 py-1 z-50">
                            @foreach ($supportedLocales as $supLoc)
                                @php $meta = $localeMeta[$supLoc] ?? null; @endphp
                                @if ($meta)
                                    <a href="{{ \App\Services\LocaleHelper::switchLocaleUrl($supLoc) }}" class="flex items-center gap-2.5 px-3 py-2 text-sm {{ $loc === $supLoc ? 'text-brand-600 bg-brand-50 font-medium' : 'text-slate-600 hover:bg-slate-50' }} transition-colors">
                                        <span class="text-base leading-none">{{ $meta['flag'] }}</span> {{ $meta['label'] }}
                                    </a>
                                @endif
                            @endforeach
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
                        <li><a href="/{{ $fLoc }}/{{ config("locales.legal_slugs.{$fLoc}.cookies", 'cookie-policy') }}" class="hover:text-white transition-colors">{{ __('legal.cookies_heading') }}</a></li>
                        <li><a href="/{{ $fLoc }}/{{ config("locales.cancellation_slugs.{$fLoc}", 'cancel') }}" class="hover:text-white transition-colors">{{ __('layout.footer_cancel') }}</a></li>
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

    {{-- Fake conversion loading modal (paywall flow, opened by __sofortpdfShowLoadingThenPay) --}}
    @include('partials.loading-modal')

    {{-- Global paywall flags for client-side gating --}}
    <script>
        window.sofortpdfPaywall = {
            bypassed: {{ \App\Services\PaywallBypass::applies(request()) ? 'true' : 'false' }},
            userHasSubscription: {{ (auth()->user() && auth()->user()->hasSofortpdfSubscription()) ? 'true' : 'false' }},
            needsPayment: function() {
                return !this.bypassed && !this.userHasSubscription;
            }
        };

        // GTM: user properties + UTM attribution (on every page)
        window.dataLayer = window.dataLayer || [];
        window.dataLayer.push({
            user_logged_in: {{ auth()->check() ? 'true' : 'false' }},
            @if(auth()->check())
            user_id: '{{ auth()->user()->id }}',
            user_plan: '{{ auth()->user()->hasSofortpdfSubscription() ? "subscriber" : "trial" }}',
            @endif
            @if(session('utm_params'))
            utm_source: '{{ session("utm_params.utm_source", "") }}',
            utm_medium: '{{ session("utm_params.utm_medium", "") }}',
            utm_campaign: '{{ session("utm_params.utm_campaign", "") }}',
            utm_content: '{{ session("utm_params.utm_content", "") }}',
            utm_term: '{{ session("utm_params.utm_term", "") }}',
            @endif
        });

        // GTM: server-side flash events (login, signup, etc.)
        @if(session('gtm_event'))
        window.dataLayer.push({ event: '{{ session("gtm_event") }}' });
        @endif

        // Global helper: show fake conversion loading (~3.5s) then open
        // the payment modal. Mirrors the conversie-pdf pattern — gives
        // paywall users the perception that their document is being
        // processed before the payment ask, which lifted conversion in
        // A/B testing on conversie-pdf. Paid users skip this entirely
        // (their flow goes through the real processing-state UI in
        // tools/show.blade.php).
        window.__sofortpdfShowLoadingThenPay = function(files, onSuccess, stepLabel) {
            function openPayment() {
                if (window.SofortpdfPaymentModal) {
                    window.SofortpdfPaymentModal.open({ files: files, onSuccess: onSuccess });
                }
            }

            // Skip the fake loading animation if it already ran for the
            // current upload session — replaying it after the user
            // dismisses the payment modal feels deceptive and wastes
            // their time. Resets when handleFiles() fires a new upload.
            if (window.__sofortpdfLoadingShown) {
                openPayment();
                return;
            }

            if (window.SofortpdfLoadingModal) {
                window.SofortpdfLoadingModal.run({
                    files: files,
                    duration: 2500,
                    onDone: function() {
                        window.__sofortpdfLoadingShown = true;
                        window.SofortpdfLoadingModal.hide();
                        openPayment();
                    },
                });
            } else {
                // Defensive fallback: if the loading partial failed to
                // load, skip straight to the modal so the paywall still
                // works.
                openPayment();
            }
        };
    </script>

    @stack('scripts')
    {{-- Lucide is loaded with `defer` in <head>, so it executes after
         HTML parse but before DOMContentLoaded — wait for that event
         before calling createIcons() to avoid a ReferenceError. --}}
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            if (window.lucide) lucide.createIcons();
        });
    </script>
</body>
</html>
