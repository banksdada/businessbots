@php
    // SEO meta partial — rendered in <head> of every page.
    // Defaults to the page title/description passed from the layout; OG and
    // Twitter tags derive from those so the same copy works for sharing.
    // Falls back to config/seo.php keyed by the current route name.
    $routeName = request()->route()?->getName();
    $seoDefaults = $routeName ? (config('seo.' . $routeName) ?? []) : [];
    $seo = array_merge($seoDefaults, $seo ?? []);
    $title = $seo['title'] ?? $title ?? config('app.name');
    $description = $seo['description'] ?? 'NowSorted — AI OS for small businesses. Automate WhatsApp replies, content, and lead follow-up.';
    $url = $seo['url'] ?? request()->fullUrl();
    $image = $seo['image'] ?? config('legal.domain') . '/images/og-default.png';
    $type = $seo['type'] ?? 'website';
@endphp

<meta name="description" content="{{ $description }}">
<link rel="canonical" href="{{ $url }}">

<!-- Open Graph -->
<meta property="og:type" content="{{ $type }}">
<meta property="og:title" content="{{ $title }}">
<meta property="og:description" content="{{ $description }}">
<meta property="og:url" content="{{ $url }}">
<meta property="og:image" content="{{ $image }}">
<meta property="og:site_name" content="{{ config('legal.brand') }}">
<meta property="og:locale" content="en_GB">

<!-- Twitter -->
<meta name="twitter:card" content="summary_large_image">
<meta name="twitter:title" content="{{ $title }}">
<meta name="twitter:description" content="{{ $description }}">
<meta name="twitter:image" content="{{ $image }}">