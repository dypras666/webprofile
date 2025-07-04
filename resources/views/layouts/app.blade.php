<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    
    {{-- SEO Meta Tags --}}
    <title>@yield('title', $siteSettings['site_name'] ?? config('app.name', 'Laravel'))</title>
    <meta name="description" content="@yield('description', $siteSettings['seo_meta_description'] ?? 'Default description')">
    <meta name="keywords" content="@yield('keywords', $siteSettings['seo_meta_keywords'] ?? 'default, keywords')">
    <meta name="author" content="@yield('author', $siteSettings['site_author'] ?? 'Admin')">
    
    {{-- Open Graph Meta Tags --}}
    <meta property="og:title" content="@yield('og_title', $siteSettings['site_name'] ?? config('app.name'))">
    <meta property="og:description" content="@yield('og_description', $siteSettings['seo_meta_description'] ?? 'Default description')">
    <meta property="og:image" content="@yield('og_image', $siteSettings['seo_og_image'] ?? asset('images/default-og.jpg'))">
    <meta property="og:url" content="{{ url()->current() }}">
    <meta property="og:type" content="@yield('og_type', 'website')">
    <meta property="og:site_name" content="{{ $siteSettings['site_name'] ?? config('app.name') }}">
    
    {{-- Twitter Card Meta Tags --}}
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="@yield('twitter_title', $siteSettings['site_name'] ?? config('app.name'))">
    <meta name="twitter:description" content="@yield('twitter_description', $siteSettings['seo_meta_description'] ?? 'Default description')">
    <meta name="twitter:image" content="@yield('twitter_image', $siteSettings['seo_og_image'] ?? asset('images/default-og.jpg'))">
    
    {{-- Canonical URL --}}
    <link rel="canonical" href="@yield('canonical', url()->current())">
    
    {{-- Favicon --}}
    <link rel="icon" type="image/x-icon" href="{{ $siteSettings['favicon'] ? asset('storage/' . $siteSettings['favicon']) : asset('favicon.ico') }}">
    
    {{-- Fonts --}}
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700&display=swap" rel="stylesheet" />
    
    {{-- Styles --}}
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @stack('styles')
    
    {{-- Structured Data --}}
    @stack('structured_data')
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