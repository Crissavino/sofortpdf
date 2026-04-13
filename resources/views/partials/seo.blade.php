@php
    $pageTitle = $pageTitle ?? 'Online PDF-Tools — Schnell & Sicher';
    $metaDescription = $metaDescription ?? 'sofortpdf.com — Ihre Online-PDF-Tools. PDF zusammenfügen, komprimieren, umwandeln und mehr. Schnell, sicher und ohne Installation.';
    $slug = $slug ?? '';
    $canonical = $canonical ?? null;
    $baseUrl = 'https://sofortpdf.com';
@endphp

<title>{{ $pageTitle }} | sofortpdf.com</title>
<meta name="description" content="{{ $metaDescription }}">
@if($canonical)
    <link rel="canonical" href="{{ $baseUrl }}/{{ $canonical }}">
@elseif($slug)
    <link rel="canonical" href="{{ $baseUrl }}/{{ $slug }}">
@else
    <link rel="canonical" href="{{ $baseUrl }}">
@endif

<meta property="og:type" content="website">
<meta property="og:title" content="{{ $pageTitle }} | sofortpdf.com">
<meta property="og:description" content="{{ $metaDescription }}">
@if($slug)
    <meta property="og:url" content="{{ $baseUrl }}/{{ $slug }}">
@else
    <meta property="og:url" content="{{ $baseUrl }}">
@endif

{{-- hreflang DACH --}}
@php $hrefSlug = $slug ? "/{$slug}" : ''; @endphp
<link rel="alternate" hreflang="de" href="{{ $baseUrl }}{{ $hrefSlug }}">
<link rel="alternate" hreflang="de-AT" href="{{ $baseUrl }}{{ $hrefSlug }}">
<link rel="alternate" hreflang="de-CH" href="{{ $baseUrl }}{{ $hrefSlug }}">
<link rel="alternate" hreflang="x-default" href="{{ $baseUrl }}{{ $hrefSlug }}">

<link rel="icon" type="image/svg+xml" href="data:image/svg+xml,<svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 32 32'><rect width='32' height='32' rx='6' fill='%233b6cf5'/><text x='50%25' y='55%25' dominant-baseline='middle' text-anchor='middle' font-family='system-ui' font-weight='bold' font-size='16' fill='white'>S</text></svg>">
