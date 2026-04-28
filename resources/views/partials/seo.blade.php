@php
    $pageTitle = $pageTitle ?? __('home.meta_title');
    $metaDescription = $metaDescription ?? __('home.meta_description');
    $slug = $slug ?? '';
    $canonical = $canonical ?? null;
    $baseUrl = 'https://sofortpdf.com';
    $locale = app()->getLocale();
    $supportedLocales = config('locales.supported', ['en']);
    $defaultLocale = config('locales.default', 'en');

    // Build locale-prefixed URL path
    $pathSuffix = $slug ? "/{$locale}/{$slug}" : "/{$locale}";
    $canonicalUrl = $canonical ? "{$baseUrl}/{$locale}/{$canonical}" : "{$baseUrl}{$pathSuffix}";

    // og:locale uses BCP47-ish underscore form per OG spec.
    $ogLocaleMap = [
        'de' => 'de_DE',
        'en' => 'en_US',
        'hu' => 'hu_HU',
        'cs' => 'cs_CZ',
    ];
    $ogLocale = $ogLocaleMap[$locale] ?? 'en_US';

    // Per-locale Organization description (kept short — used in JSON-LD).
    $orgDescriptionMap = [
        'de' => 'Online-PDF-Tools — zusammenfügen, komprimieren, konvertieren und mehr.',
        'en' => 'Online PDF tools — merge, compress, convert and more.',
        'hu' => 'Online PDF eszközök — egyesítés, tömörítés, konvertálás és több.',
        'cs' => 'Online PDF nástroje — slučování, komprese, konverze a další.',
    ];
    $orgDescription = $orgDescriptionMap[$locale] ?? $orgDescriptionMap['en'];

    $contactSlug = config("locales.contact_slugs.{$locale}", 'contact');
    $homeBreadcrumbName = __('layout.breadcrumb_home');
    $trialDescription = __('payment.trial_label_short');
@endphp

<title>{{ $pageTitle }} | sofortpdf.com</title>
<meta name="description" content="{{ $metaDescription }}">
<link rel="canonical" href="{{ $canonicalUrl }}">

<meta property="og:type" content="website">
<meta property="og:title" content="{{ $pageTitle }} | sofortpdf.com">
<meta property="og:description" content="{{ $metaDescription }}">
<meta property="og:url" content="{{ $canonicalUrl }}">
<meta property="og:locale" content="{{ $ogLocale }}">

{{-- hreflang for every supported locale + x-default (points at the
     configured default — currently 'en' since that's the global anchor) --}}
@php
    $hreflangUrls = [];
    foreach ($supportedLocales as $hl) {
        $hreflangUrls[$hl] = $slug
            ? \App\Services\LocaleHelper::switchLocaleUrl($hl)
            : "/{$hl}";
    }
    // Map locales to hreflang tags. DE also serves AT and CH so we emit
    // those as separate tags pointing at the same DE URL.
    $hreflangTags = [
        'de'    => 'de',
        'de-AT' => 'de',
        'de-CH' => 'de',
        'en'    => 'en',
        'hu'    => 'hu',
        'cs'    => 'cs',
    ];
@endphp
@foreach ($hreflangTags as $tag => $sourceLocale)
    @if (isset($hreflangUrls[$sourceLocale]))
<link rel="alternate" hreflang="{{ $tag }}" href="{{ $baseUrl }}{{ $hreflangUrls[$sourceLocale] }}">
    @endif
@endforeach
<link rel="alternate" hreflang="x-default" href="{{ $baseUrl }}{{ $hreflangUrls[$defaultLocale] ?? ('/' . $defaultLocale) }}">

<link rel="icon" type="image/svg+xml" href="data:image/svg+xml,<svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 32 32'><rect width='32' height='32' rx='6' fill='%233b6cf5'/><text x='50%25' y='55%25' dominant-baseline='middle' text-anchor='middle' font-family='system-ui' font-weight='bold' font-size='16' fill='white'>S</text></svg>">

{{-- JSON-LD: Organization (all pages) --}}
<script type="application/ld+json">
{
    "@context": "https://schema.org",
    "@type": "Organization",
    "name": "sofortpdf.com",
    "url": "https://sofortpdf.com",
    "logo": "https://sofortpdf.com/images/logo.png",
    "description": {!! json_encode($orgDescription, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) !!},
    "contactPoint": {
        "@type": "ContactPoint",
        "contactType": "customer support",
        "url": "https://sofortpdf.com/{{ $locale }}/{{ $contactSlug }}"
    }
}
</script>

{{-- JSON-LD: WebSite + SearchAction --}}
<script type="application/ld+json">
{
    "@context": "https://schema.org",
    "@type": "WebSite",
    "name": "sofortpdf.com",
    "url": "https://sofortpdf.com",
    "inLanguage": {!! json_encode($supportedLocales) !!},
    "potentialAction": {
        "@type": "SearchAction",
        "target": "https://sofortpdf.com/{{ $defaultLocale }}?q={search_term_string}",
        "query-input": "required name=search_term_string"
    }
}
</script>

{{-- JSON-LD: BreadcrumbList --}}
@if (!empty($slug))
<script type="application/ld+json">
{
    "@context": "https://schema.org",
    "@type": "BreadcrumbList",
    "itemListElement": [
        {
            "@type": "ListItem",
            "position": 1,
            "name": {!! json_encode($homeBreadcrumbName, JSON_UNESCAPED_UNICODE) !!},
            "item": "{{ $baseUrl }}/{{ $locale }}"
        },
        {
            "@type": "ListItem",
            "position": 2,
            "name": {!! json_encode($pageTitle, JSON_UNESCAPED_UNICODE) !!},
            "item": "{{ $canonicalUrl }}"
        }
    ]
}
</script>
@endif

{{-- JSON-LD: SoftwareApplication (tool pages only) --}}
@if (!empty($toolKey))
@php
    $toolCfg = config("tools.{$toolKey}", []);
@endphp
<script type="application/ld+json">
{
    "@context": "https://schema.org",
    "@type": "SoftwareApplication",
    "name": {!! json_encode($pageTitle, JSON_UNESCAPED_UNICODE) !!},
    "description": {!! json_encode($metaDescription, JSON_UNESCAPED_UNICODE) !!},
    "url": "{{ $canonicalUrl }}",
    "applicationCategory": "UtilitiesApplication",
    "operatingSystem": "Web",
    "offers": {
        "@type": "Offer",
        "price": "0.69",
        "priceCurrency": "EUR",
        "description": {!! json_encode($trialDescription, JSON_UNESCAPED_UNICODE) !!}
    },
    "aggregateRating": {
        "@type": "AggregateRating",
        "ratingValue": "4.7",
        "ratingCount": "1280",
        "bestRating": "5"
    }
}
</script>
@endif
