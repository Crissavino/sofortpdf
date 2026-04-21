@php
    $pageTitle = $pageTitle ?? __('home.meta_title');
    $metaDescription = $metaDescription ?? __('home.meta_description');
    $slug = $slug ?? '';
    $canonical = $canonical ?? null;
    $baseUrl = 'https://sofortpdf.com';
    $locale = app()->getLocale();
    // Build locale-prefixed URL path
    $pathSuffix = $slug ? "/{$locale}/{$slug}" : "/{$locale}";
    $canonicalUrl = $canonical ? "{$baseUrl}/{$locale}/{$canonical}" : "{$baseUrl}{$pathSuffix}";
@endphp

<title>{{ $pageTitle }} | sofortpdf.com</title>
<meta name="description" content="{{ $metaDescription }}">
<link rel="canonical" href="{{ $canonicalUrl }}">

<meta property="og:type" content="website">
<meta property="og:title" content="{{ $pageTitle }} | sofortpdf.com">
<meta property="og:description" content="{{ $metaDescription }}">
<meta property="og:url" content="{{ $canonicalUrl }}">
<meta property="og:locale" content="{{ $locale === 'de' ? 'de_DE' : 'en_US' }}">

{{-- hreflang DE + EN + x-default --}}
@php
    $deSlug = $slug ? \App\Services\LocaleHelper::switchLocaleUrl('de') : '/de';
    $enSlug = $slug ? \App\Services\LocaleHelper::switchLocaleUrl('en') : '/en';
@endphp
<link rel="alternate" hreflang="de" href="{{ $baseUrl }}{{ $deSlug }}">
<link rel="alternate" hreflang="de-AT" href="{{ $baseUrl }}{{ $deSlug }}">
<link rel="alternate" hreflang="de-CH" href="{{ $baseUrl }}{{ $deSlug }}">
<link rel="alternate" hreflang="en" href="{{ $baseUrl }}{{ $enSlug }}">
<link rel="alternate" hreflang="x-default" href="{{ $baseUrl }}{{ $deSlug }}">

<link rel="icon" type="image/svg+xml" href="data:image/svg+xml,<svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 32 32'><rect width='32' height='32' rx='6' fill='%233b6cf5'/><text x='50%25' y='55%25' dominant-baseline='middle' text-anchor='middle' font-family='system-ui' font-weight='bold' font-size='16' fill='white'>S</text></svg>">

{{-- JSON-LD: Organization (all pages) --}}
<script type="application/ld+json">
{
    "@context": "https://schema.org",
    "@type": "Organization",
    "name": "sofortpdf.com",
    "url": "https://sofortpdf.com",
    "logo": "https://sofortpdf.com/images/logo.png",
    "description": "{{ $locale === 'de' ? 'Online-PDF-Tools — zusammenfügen, komprimieren, konvertieren und mehr.' : 'Online PDF tools — merge, compress, convert and more.' }}",
    "contactPoint": {
        "@type": "ContactPoint",
        "contactType": "customer support",
        "url": "https://sofortpdf.com/{{ $locale }}/{{ $locale === 'de' ? 'kontakt' : 'contact' }}"
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
    "inLanguage": ["de", "en"],
    "potentialAction": {
        "@type": "SearchAction",
        "target": "https://sofortpdf.com/de?q={search_term_string}",
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
            "name": "{{ $locale === 'de' ? 'Startseite' : 'Home' }}",
            "item": "{{ $baseUrl }}/{{ $locale }}"
        },
        {
            "@type": "ListItem",
            "position": 2,
            "name": "{{ $pageTitle }}",
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
    "name": "{{ $pageTitle }}",
    "description": "{{ $metaDescription }}",
    "url": "{{ $canonicalUrl }}",
    "applicationCategory": "UtilitiesApplication",
    "operatingSystem": "Web",
    "offers": {
        "@type": "Offer",
        "price": "0.69",
        "priceCurrency": "EUR",
        "description": "{{ $locale === 'de' ? '2-Tage-Testversion' : '2-day trial' }}"
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
