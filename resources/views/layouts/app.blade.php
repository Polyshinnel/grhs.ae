<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="author" content="Golden Ratio Hospitality Solutions">
    <title>@yield('title', 'GRHS | Golden Ratio Hospitality Solutions')</title>
    <meta name="description" content="@yield('description', 'Golden Ratio Hospitality Solutions — hospitality equipment and tableware in Dubai.')">
    <link rel="canonical" href="@yield('canonical', url()->current())">
    <meta property="og:title" content="@yield('title', 'GRHS | Golden Ratio Hospitality Solutions')">
    <meta property="og:description" content="@yield('description', 'Golden Ratio Hospitality Solutions — hospitality equipment and tableware in Dubai.')">
    <meta property="og:url" content="@yield('canonical', url()->current())">
    @hasSection('og_image')
        <meta property="og:image" content="@yield('og_image')">
    @endif
    @isset($organizationSchema)
        <script type="application/ld+json">{!! json_encode($organizationSchema, JSON_UNESCAPED_SLASHES | JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_INVALID_UTF8_SUBSTITUTE) !!}</script>
    @endisset
    @isset($breadcrumbSchema)
        <script type="application/ld+json">{!! json_encode($breadcrumbSchema, JSON_UNESCAPED_SLASHES | JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_INVALID_UTF8_SUBSTITUTE) !!}</script>
    @endisset
    <link rel="icon" href="{{ asset('images/site/favicon.svg') }}" type="image/svg+xml">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-grhs-paper text-grhs-ink antialiased">
    <a class="sr-only focus:not-sr-only focus:fixed focus:left-4 focus:top-4 focus:z-[500] focus:bg-white focus:px-4 focus:py-3" href="#main-content">Skip to content</a>
    @php
        $headerBlack = trim($__env->yieldContent('header_black')) === 'true';
    @endphp
    @include('components.site.header', ['headerBlack' => $headerBlack])

    <main id="main-content">
        @yield('content')
    </main>

    @include('components.site.footer')
    @include('components.site.floating-contact')
</body>
</html>
