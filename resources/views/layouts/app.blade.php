<!DOCTYPE html>
<html lang="en">
<head>
    <!-- Google Tag Manager -->
    <script>(function(w,d,s,l,i){w[l]=w[l]||[];w[l].push({'gtm.start':
    new Date().getTime(),event:'gtm.js'});var f=d.getElementsByTagName(s)[0],
    j=d.createElement(s),dl=l!='dataLayer'?'&l='+l:'';j.async=true;j.src=
    'https://www.googletagmanager.com/gtm.js?id='+i+dl;f.parentNode.insertBefore(j,f);
    })(window,document,'script','dataLayer','GTM-WMRRM4GQ');</script>
    <!-- End Google Tag Manager -->
    @php
        $canonicalPath = trim($__env->yieldContent('canonical'));
        $canonicalUrl = app(\App\Support\Seo\CanonicalUrl::class)->make($canonicalPath !== '' ? $canonicalPath : null);
    @endphp
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="author" content="Golden Ratio Hospitality Supplies Trading LLC">
    <title>@yield('title', 'GRHS | Golden Ratio Hospitality Supplies Trading LLC')</title>
    <meta name="description" content="@yield('description', 'Golden Ratio Hospitality Supplies Trading LLC — hospitality equipment and tableware in Dubai.')">
    <link rel="canonical" href="{{ $canonicalUrl }}">
    <meta property="og:title" content="@yield('title', 'GRHS | Golden Ratio Hospitality Supplies Trading LLC')">
    <meta property="og:description" content="@yield('description', 'Golden Ratio Hospitality Supplies Trading LLC — hospitality equipment and tableware in Dubai.')">
    <meta property="og:url" content="{{ $canonicalUrl }}">
    <meta property="og:site_name" content="Golden Ratio Hospitality Supplies">
    <meta property="og:image" content="@yield('og_image', asset('og-image-1200x628.jpg'))">
    @isset($organizationSchema)
        <script type="application/ld+json">{!! json_encode($organizationSchema, JSON_UNESCAPED_SLASHES | JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_INVALID_UTF8_SUBSTITUTE) !!}</script>
    @endisset
    @isset($websiteSchema)
        <script type="application/ld+json">{!! json_encode($websiteSchema, JSON_UNESCAPED_SLASHES | JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_INVALID_UTF8_SUBSTITUTE) !!}</script>
    @endisset
    @isset($breadcrumbSchema)
        <script type="application/ld+json">{!! json_encode($breadcrumbSchema, JSON_UNESCAPED_SLASHES | JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_INVALID_UTF8_SUBSTITUTE) !!}</script>
    @endisset
    <link rel="icon" href="{{ asset('favicon.ico') }}" sizes="any">
    <link rel="icon" href="{{ asset('favicon-48.png') }}" type="image/png" sizes="48x48">
    <link rel="icon" href="{{ asset('favicon-96.png') }}" type="image/png" sizes="96x96">
    <link rel="icon" href="{{ asset('favicon-192.png') }}" type="image/png" sizes="192x192">
    <link rel="icon" href="{{ asset('favicon-512.png') }}" type="image/png" sizes="512x512">
    <link rel="apple-touch-icon" href="{{ asset('apple-touch-icon.png') }}">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-grhs-paper text-grhs-ink antialiased">
    <!-- Google Tag Manager (noscript) -->
    <noscript><iframe src="https://www.googletagmanager.com/ns.html?id=GTM-WMRRM4GQ"
    height="0" width="0" style="display:none;visibility:hidden"></iframe></noscript>
    <!-- End Google Tag Manager (noscript) -->
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
