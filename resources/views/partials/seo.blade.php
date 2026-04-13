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
