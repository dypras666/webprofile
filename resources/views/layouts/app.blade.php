<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    {{-- SEO Meta Tags --}}
    <title>@yield('title', $siteSettings['site_name'] ?? config('app.name', 'Laravel'))</title>
    <meta name="description"
        content="@yield('description', $siteSettings['seo_meta_description'] ?? 'Default description')">
    <meta name="keywords" content="@yield('keywords', $siteSettings['seo_meta_keywords'] ?? 'default, keywords')">
    <meta name="author" content="@yield('author', $siteSettings['site_author'] ?? 'Admin')">

    {{-- Open Graph Meta Tags --}}
    <meta property="og:title" content="@yield('og_title', $siteSettings['site_name'] ?? config('app.name'))">
    <meta property="og:description"
        content="@yield('og_description', $siteSettings['seo_meta_description'] ?? 'Default description')">
    <meta property="og:image"
        content="@yield('og_image', $siteSettings['seo_og_image'] ?? asset('images/default-og.jpg'))">
    <meta property="og:url" content="{{ url()->current() }}">
    <meta property="og:type" content="@yield('og_type', 'website')">
    <meta property="og:site_name" content="{{ $siteSettings['site_name'] ?? config('app.name') }}">

    {{-- Twitter Card Meta Tags --}}
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="@yield('twitter_title', $siteSettings['site_name'] ?? config('app.name'))">
    <meta name="twitter:description"
        content="@yield('twitter_description', $siteSettings['seo_meta_description'] ?? 'Default description')">
    <meta name="twitter:image"
        content="@yield('twitter_image', $siteSettings['seo_og_image'] ?? asset('images/default-og.jpg'))">

    {{-- Canonical URL --}}
    <link rel="canonical" href="@yield('canonical', url()->current())">

    {{-- Favicon --}}
    {{-- Favicon --}}
    @if(isset($siteSettings['favicon']) && $siteSettings['favicon'])
        @php
            $faviconPath = $siteSettings['favicon'];
            $faviconDir = dirname($faviconPath) . '/favicons';
            $storageUrl = asset('storage');
        @endphp
        <link rel="icon" type="image/png" sizes="16x16" href="{{ $storageUrl . '/' . $faviconDir . '/favicon-16x16.png' }}">
        <link rel="icon" type="image/png" sizes="32x32" href="{{ $storageUrl . '/' . $faviconDir . '/favicon-32x32.png' }}">
        <link rel="icon" type="image/png" sizes="48x48" href="{{ $storageUrl . '/' . $faviconDir . '/favicon-48x48.png' }}">
        <link rel="icon" type="image/png" sizes="96x96" href="{{ $storageUrl . '/' . $faviconDir . '/favicon-96x96.png' }}">
        <link rel="apple-touch-icon" sizes="180x180" href="{{ $storageUrl . '/' . $faviconDir . '/apple-touch-icon.png' }}">
        <link rel="manifest" href="{{ $storageUrl . '/' . $faviconDir . '/site.webmanifest' }}">
        <link rel="shortcut icon" href="{{ $storageUrl . '/' . $faviconDir . '/favicon.ico' }}">
    @else
        <link rel="icon" type="image/x-icon" href="{{ asset('favicon.ico') }}">
    @endif

    {{-- Fonts --}}
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700&display=swap" rel="stylesheet" />

    {{-- Styles --}}
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @stack('styles')

    {{-- Structured Data --}}
    @stack('structured_data')

    <style>
        [x-cloak] {
            display: none !important;
        }
    </style>

    {{-- Schema.org JSON-LD --}}
    <script type="application/ld+json">
    {
        "@context": "https://schema.org",
        "@type": "WebSite",
        "name": "{{ $siteSettings['site_name'] ?? config('app.name') }}",
        "url": "{{ url('/') }}",
        "description": "{{ $siteSettings['seo_meta_description'] ?? '' }}",
        "potentialAction": {
            "@type": "SearchAction",
            "target": "{{ url('/search') }}?q={search_term_string}",
            "query-input": "required name=search_term_string"
        }
    }
    </script>
    <script type="application/ld+json">
    {
        "@context": "https://schema.org",
        "@type": "Organization",
        "name": "{{ $siteSettings['site_name'] ?? config('app.name') }}",
        "url": "{{ url('/') }}",
        "logo": "{{ isset($siteSettings['logo']) ? asset('storage/' . $siteSettings['logo']) : '' }}",
        "contactPoint": {
            "@type": "ContactPoint",
            "telephone": "{{ $siteSettings['phone'] ?? '' }}",
            "contactType": "customer service"
        }
    }
    </script>
</head>

<body class="font-sans antialiased bg-gray-50">
    {{-- Header Component --}}
    @include('components.header')

    {{-- Main Navigation --}}
    @include('components.navigation')

    {{-- Main Content --}}
    <main>
        @yield('content')
    </main>

    {{-- Footer Component --}}
    @include('components.footer')

    {{-- Scripts --}}
    @stack('scripts')

    {{-- Analytics --}}
    @stack('analytics')
</body>

</html>