<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="scroll-smooth">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    {{-- SEO Meta Tags --}}
    <title>@yield('title', \App\Models\SiteSetting::getValue('site_name', config('app.name')))</title>
    <meta name="description" content="@yield('description', \App\Models\SiteSetting::getValue('meta_description', ''))">
    <meta name="keywords" content="@yield('keywords', \App\Models\SiteSetting::getValue('meta_keywords', ''))">
    <meta name="author" content="@yield('author', 'Admin')">

    {{-- Open Graph Meta Tags --}}
    <meta property="og:title" content="@yield('og_title', \App\Models\SiteSetting::getValue('site_name'))">
    <meta property="og:description"
        content="@yield('og_description', \App\Models\SiteSetting::getValue('meta_description'))">
    <meta property="og:image"
        content="@yield('og_image', \App\Models\SiteSetting::getValue('og_image') ? Storage::url(\App\Models\SiteSetting::getValue('og_image')) : asset('images/default-og.jpg'))">
    <meta property="og:url" content="{{ url()->current() }}">
    <meta property="og:type" content="@yield('og_type', 'website')">
    <meta property="og:site_name" content="{{ \App\Models\SiteSetting::getValue('site_name') }}">

    {{-- Canonical URL --}}
    <link rel="canonical" href="@yield('canonical', url()->current())">

    {{-- Favicon --}}
    @if(\App\Models\SiteSetting::getValue('favicon'))
        <link rel="icon" href="{{ Storage::url(\App\Models\SiteSetting::getValue('favicon')) }}">
    @else
        <link rel="icon" href="{{ asset('favicon.ico') }}">
    @endif

    {{-- Fonts --}}
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Outfit:wght@400;500;600;700&display=swap"
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Outfit:wght@400;500;600;700&display=swap"
        rel="stylesheet">
    
    {{-- Swiper CSS --}}
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css" />

    {{-- Tailwind CSS & Plugins --}}
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: '#1e40af', // blue-800
                        secondary: '#1e3a8a', // blue-900
                        accent: '#3b82f6', // blue-500
                    },
                    fontFamily: {
                        sans: ['Inter', 'sans-serif'],
                        heading: ['Outfit', 'sans-serif'],
                    }
                }
            }
        }
    </script>

    {{-- Alpine.js --}}
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

    {{-- Custom Styles --}}
    <link href="{{ \App\Helpers\TemplateHelper::asset('css/style.css') }}" rel="stylesheet">

    <style>
        [x-cloak] {
            display: none !important;
        }

        body {
            padding-bottom: 70px;
        }

        /* Space for mobile nav */
        @media (min-width: 1024px) {
            body {
                padding-bottom: 0;
            }
        }
    </style>

    @stack('head')
</head>

<body class="font-sans antialiased text-gray-800 bg-gray-50 flex flex-col min-h-screen" x-data="{ searchOpen: false }">

    {{-- Header (University Style) --}}
    <header class="fixed w-full top-0 z-50 transition-all duration-300">

        {{-- Top Bar (Cyan/Light Blue) --}}
        <div class="bg-cyan-500 text-white text-xs py-2 border-b border-white/10 hidden md:block relative z-30">
            <div class="container mx-auto px-4 md:px-6 flex justify-between items-center">
                {{-- Socials --}}
                <div class="flex items-center gap-4">
                    @if(\App\Models\SiteSetting::getValue('facebook_url'))
                        <a href="{{ \App\Models\SiteSetting::getValue('facebook_url') }}"
                            class="hover:text-white/80 transition-colors"><i class="fab fa-facebook-f"></i></a>
                    @endif
                    @if(\App\Models\SiteSetting::getValue('twitter_url'))
                        <a href="{{ \App\Models\SiteSetting::getValue('twitter_url') }}"
                            class="hover:text-white/80 transition-colors"><i class="fab fa-twitter"></i></a>
                    @endif
                    @if(\App\Models\SiteSetting::getValue('youtube_url'))
                        <a href="{{ \App\Models\SiteSetting::getValue('youtube_url') }}"
                            class="hover:text-white/80 transition-colors"><i class="fab fa-youtube"></i></a>
                    @endif
                </div>

                {{-- Admin/Login --}}
                <div class="flex items-center gap-4 font-medium uppercase tracking-wider text-[10px]">
                    @auth
                        <a href="{{ route('admin.dashboard') }}" class="hover:text-white/80 transition-colors">Dashboard</a>
                    @else
                        <a href="{{ route('login') }}" class="hover:text-white/80 transition-colors">Login</a>
                    @endauth
                </div>
            </div>
        </div>

        {{-- Main Header (Deep Blue) --}}
        <div class="bg-[#1e3a8a] text-white shadow-xl relative z-20 border-b-4 border-cyan-500">
            <div class="container mx-auto px-4 md:px-6">
                <div class="flex justify-between items-center h-24">

                    {{-- Logo --}}
                    <a href="{{ route('frontend.index') }}" class="flex items-center gap-3 shrink-0 mr-12 group">
                        @if(\App\Models\SiteSetting::getValue('logo'))
                            <img src="{{ Storage::url(\App\Models\SiteSetting::getValue('logo')) }}" alt="Logo"
                                class="w-auto object-contain" style="height: {{ \App\Models\SiteSetting::getValue('logo_height', '50') }}px">
                        @else
                            <div class="flex flex-col">
                                <span class="text-2xl font-heading font-bold leading-none tracking-tight">University</span>
                                <span class="text-[10px] text-cyan-300 tracking-widest uppercase">edu wordpress theme</span>
                            </div>
                        @endif
                    </a>

                    {{-- Desktop Menu --}}
                    <nav class="hidden lg:flex items-center space-x-1 flex-grow justify-end">
                        @php
                            $menus = \App\Models\NavigationMenu::getMenuTree('top');
                        @endphp

                        @foreach($menus as $menu)
                            {{-- Menu Item Container --}}
                            <div class="relative group h-24 flex items-center px-4 hover:bg-white/5 transition-colors cursor-pointer border-t-4 border-transparent hover:border-cyan-500 box-border"
                                x-data="{ open: false }" @mouseenter="open = true" @mouseleave="open = false">

                                @if($menu->children->count() > 0)
                                    <div class="flex flex-col">
                                        {{-- Main Label --}}
                                        <button
                                            class="font-bold text-sm text-white group-hover:text-cyan-300 transition-colors text-left flex items-center gap-1">
                                            {{ $menu->title }}
                                            <i class="fas fa-chevron-down text-[8px] opacity-70 ml-1"></i>
                                        </button>
                                        {{-- Sub Label --}}
                                        <span
                                            class="text-[10px] text-gray-400 font-normal italic group-hover:text-white/80 transition-colors">
                                            Explore Section
                                        </span>

                                        {{-- Dropdown --}}
                                        <div x-show="open" x-cloak
                                            class="absolute top-full left-0 mt-0 w-64 bg-[#1e3a8a] text-white shadow-2xl py-3 border-t-2 border-cyan-500 z-50">
                                            @foreach($menu->children as $child)
                                                <a href="{{ $child->final_url }}" target="{{ $child->target }}"
                                                    class="block px-6 py-2.5 text-sm hover:bg-cyan-500/20 hover:text-cyan-300 transition-colors border-l-2 border-transparent hover:border-cyan-300">
                                                    {{ $child->title }}
                                                </a>
                                            @endforeach
                                        </div>
                                    </div>
                                @else
                                    <a href="{{ $menu->final_url }}" target="{{ $menu->target }}"
                                        class="flex flex-col w-full h-full justify-center">
                                        <span class="font-bold text-sm text-white group-hover:text-cyan-300 transition-colors">
                                            {{ $menu->title }}
                                        </span>
                                        <span
                                            class="text-[10px] text-gray-400 font-normal italic group-hover:text-white/80 transition-colors">
                                            Visit Page
                                        </span>
                                    </a>
                                @endif
                            </div>
                        @endforeach

                        {{-- Search Icon --}}
                        <div class="h-24 flex items-center px-4 hover:bg-white/5 border-t-4 border-transparent hover:border-cyan-500 transition-colors cursor-pointer relative group z-30">
                            <button @click.prevent="searchOpen = true" class="flex items-center justify-center w-full h-full text-white hover:text-cyan-300 relative z-50 focus:outline-none">
                                <i class="fas fa-search text-lg"></i>
                            </button>
                        </div>
                    </nav>

                </div>
            </div>
        </div>
    </header>

    {{-- Main Content --}}
    {{-- Adjusted padding for 3-tier fixed header (approx 150px) --}}
    <main class="flex-grow pt-[125px] md:pt-[135px]">
        @yield('content')
    </main>

    {{-- Footer (University Dark Theme) --}}
    <footer class="bg-[#111] text-gray-400 font-sans border-t border-gray-800">

        {{-- Top Section (Darker/Black) --}}
        <div class="bg-black py-16 border-b border-white/5">
            <div class="container mx-auto px-4 md:px-6">
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8">

                    {{-- Column 1: Law & Business --}}
                    {{-- Column 1: Navigasi (Bottom Menu) --}}
                    <div>
                        <h4 class="text-white font-bold uppercase tracking-wider mb-6 text-sm">Navigasi</h4>
                        <ul class="space-y-2 text-sm">
                            @php $bottomMenus = \App\Models\NavigationMenu::getMenuTree('bottom'); @endphp
                            @foreach($bottomMenus as $menu)
                                <li>
                                    <a href="{{ $menu->final_url }}" target="{{ $menu->target }}" class="hover:text-white transition-colors">
                                        {{ $menu->title }}
                                    </a>
                                </li>
                            @endforeach
                            @if($bottomMenus->isEmpty())
                                <li><a href="#" class="hover:text-white transition-colors text-gray-600 italic">Menu 'bottom' empty</a></li>
                            @endif
                        </ul>
                    </div>

                    {{-- Column 2: Engineering --}}
                    {{-- Column 2: Pengumuman --}}
                    <div>
                        <h4 class="text-white font-bold uppercase tracking-wider mb-6 text-sm">Pengumuman</h4>
                        <ul class="space-y-3 text-sm">
                            @php 
                                $announcements = \App\Models\Post::published()
                                    ->whereHas('category', function($q) {
                                        $q->where('slug', 'like', '%pengumuman%')
                                          ->orWhere('name', 'like', '%pengumuman%');
                                    })
                                    ->latest()
                                    ->take(5)
                                    ->get(); 
                            @endphp
                            @foreach($announcements as $post)
                                <li>
                                    <a href="{{ route('frontend.post', $post->slug) }}" class="hover:text-white transition-colors block line-clamp-2">
                                        {{ $post->title }}
                                    </a>
                                    <span class="text-[10px] text-gray-600 block mt-1">{{ $post->published_at ? $post->published_at->format('d M Y') : $post->created_at->format('d M Y') }}</span>
                                </li>
                            @endforeach
                            @if($announcements->isEmpty())
                                <li class="text-gray-600 italic">Belum ada pengumuman.</li>
                            @endif
                        </ul>
                    </div>

                    {{-- Column 3: Higher Education --}}
                    {{-- Column 3: Mitra Kami (Post Type: Partner) --}}
                    <div>
                        <h4 class="text-white font-bold uppercase tracking-wider mb-6 text-sm">Mitra Kami</h4>
                        <ul class="space-y-2 text-sm">
                            @php 
                                $partners = \App\Models\Post::where('type', 'partner')
                                    ->published()
                                    ->latest()
                                    ->take(6)
                                    ->get(); 
                            @endphp
                            @foreach($partners as $partner)
                                <li>
                                    <a href="{{ strip_tags($partner->content) }}" target="_blank" class="hover:text-white transition-colors border-b border-transparent hover:border-gray-600 inline-block pb-0.5">
                                        {{ $partner->title }}
                                    </a>
                                </li>
                            @endforeach
                            @if($partners->isEmpty())
                                <li class="text-gray-600 italic">Belum ada mitra.</li>
                            @endif
                        </ul>
                    </div>

                    {{-- Column 4: Footer 2 Menu --}}
                    <div>
                        <h4 class="text-white font-bold uppercase tracking-wider mb-6 text-base">
                            {{ \App\Models\SiteSetting::getValue('theme_university_footer_menu_title', 'Tautan') }}
                        </h4>
                        <ul class="space-y-2 text-sm">
                            @php $footer2Menus = \App\Models\NavigationMenu::getMenuTree('footer_2'); @endphp
                            @foreach($footer2Menus as $menu)
                                <li>
                                    <a href="{{ $menu->final_url }}" target="{{ $menu->target }}" class="hover:text-white transition-colors">
                                        {{ $menu->title }}
                                    </a>
                                </li>
                            @endforeach
                            @if($footer2Menus->isEmpty())
                                <li class="text-gray-600 italic">Menu 'footer_2' empty</li>
                            @endif
                        </ul>
                    </div>

                </div>
            </div>
        </div>

        {{-- Middle Section --}}
        <div class="bg-[#1a1a1a] py-16">
            <div class="container mx-auto px-4 md:px-6">
                <div class="grid grid-cols-1 lg:grid-cols-3 gap-12">

                    {{-- About --}}
                    <div>
                        <h4 class="text-white font-bold uppercase tracking-wider mb-6 text-base">{{ \App\Models\SiteSetting::getValue('site_name') }}</h4>
                        <p class="text-sm leading-relaxed mb-4">
                            {{ Str::limit(\App\Models\SiteSetting::getValue('site_description'), 150) }}
                        </p>
                       
                        </p>
                        
                        {{-- Contact Info Moved Here --}}
                        <div class="space-y-3 text-sm mt-6 border-t border-white/10 pt-6">
                            <p class="flex items-start gap-3">
                                <i class="fas fa-envelope mt-1 text-gray-500"></i>
                                <span class="text-gray-300">{{ \App\Models\SiteSetting::getValue('contact_email', 'email-us@example.com') }}</span>
                            </p>
                            <p class="flex items-start gap-3">
                                <i class="fas fa-phone mt-1 text-gray-500"></i>
                                <span class="text-gray-300">{{ \App\Models\SiteSetting::getValue('contact_phone', '0123 456 789') }}</span>
                            </p>
                            <p class="flex items-start gap-3">
                                <i class="fas fa-map-marker-alt mt-1 text-gray-500"></i>
                                <span class="text-gray-300">{{ \App\Models\SiteSetting::getValue('contact_address', '123 Main street, Los Angeles, CA, USA') }}</span>
                            </p>
                        </div>
                    </div>

                    {{-- Recent Posts --}}
                    <div>
                        <h4 class="text-white font-bold uppercase tracking-wider mb-6 text-base">Recent Posts</h4>
                        <ul class="space-y-3">
@php $footerPosts = \App\Models\Post::where('type', 'berita')->published()->latest()->take(3)->get(); @endphp
                            @foreach($footerPosts as $post)
                                <li>
                                    <a href="{{ route('frontend.post', $post->slug) }}"
                                        class="text-sm hover:text-white transition-colors block">
                                        {{ $post->title }}
                                    </a>
                                    <span class="text-xs text-gray-600">{{ $post->published_at ? $post->published_at->format('F d, Y') : $post->created_at->format('F d, Y') }}</span>
                                </li>
                            @endforeach
                        </ul>
                    </div>

                    {{-- Other Links --}}
                    <div>
                        <h4 class="text-white font-bold uppercase tracking-wider mb-6 text-base">Other Links</h4>
                        <ul class="space-y-2 text-sm">
                            @php $footerMenus = \App\Models\NavigationMenu::getMenuTree('quicklink'); @endphp
                            @foreach($footerMenus as $menu)
                                <li><a href="{{ $menu->final_url }}" target="{{ $menu->target }}"
                                        class="hover:text-white transition-colors">{{ $menu->title }}</a></li>
                            @endforeach
                            @if($footerMenus->isEmpty())
                                <li><a href="#" class="hover:text-white transition-colors">SIAKAD</a></li>
                                <li><a href="#" class="hover:text-white transition-colors">Pendaftaran</a></li>
                                <li><a href="#" class="hover:text-white transition-colors">Biro</a></li>
                                <li><a href="#" class="hover:text-white transition-colors">Hubungi Kami</a></li>
                            @endif
                        </ul>
                    </div>

                </div>
            </div>
        </div>

        {{-- Bottom Copyright --}}
        <div class="bg-[#1a1a1a] text-xs py-8 border-t border-white/5 relative">
            <div class="container mx-auto px-4 md:px-6 flex flex-col md:flex-row justify-between items-center gap-4">

                {{-- Copyright --}}
                <div class="text-gray-500">
                    &copy; {{ date('Y') }} {{ \App\Models\SiteSetting::getValue('site_name') }}. All rights reserved.
                </div>

                {{-- Social Icons --}}
                <div class="flex gap-2">
                    @if(\App\Models\SiteSetting::getValue('facebook_url'))
                        <a href="{{ \App\Models\SiteSetting::getValue('facebook_url') }}"
                            class="w-8 h-8 flex items-center justify-center border border-white/10 hover:border-white hover:text-white transition-colors"><i
                                class="fab fa-facebook-f"></i></a>
                    @endif
                    @if(\App\Models\SiteSetting::getValue('twitter_url'))
                        <a href="{{ \App\Models\SiteSetting::getValue('twitter_url') }}"
                            class="w-8 h-8 flex items-center justify-center border border-white/10 hover:border-white hover:text-white transition-colors"><i
                                class="fab fa-twitter"></i></a>
                    @endif
                    @if(\App\Models\SiteSetting::getValue('linkedin_url'))
                        <a href="{{ \App\Models\SiteSetting::getValue('linkedin_url') }}"
                            class="w-8 h-8 flex items-center justify-center border border-white/10 hover:border-white hover:text-white transition-colors"><i
                                class="fab fa-linkedin-in"></i></a>
                    @endif
                    
                </div>

            </div>

            {{-- TOP Button (Centered on Border) --}}
            <button x-data @click="window.scrollTo({top: 0, behavior: 'smooth'})"
                class="absolute left-1/2 -top-4 -translate-x-1/2 bg-[#1a1a1a] text-white text-[10px] font-bold uppercase tracking-widest px-4 py-2 border border-white/10 hover:bg-white hover:text-black transition-colors z-10">
                Top
            </button>
        </div>
    </footer>

    {{-- Mobile Bottom Floating Navbar --}}
    <nav
        class="lg:hidden fixed bottom-0 left-0 right-0 bg-white border-t border-gray-200 z-50 px-6 py-2 shadow-2xl safe-area-pb">
        <ul class="flex justify-between items-center">
            @php
                // Use Quicklink menus for the bottom bar as requested
                $mobileMenus = \App\Models\NavigationMenu::getMenuTree('quicklink');
                // Ensure we have at least 'Home' if empty, or limit if too many
                $mobileMenus = $mobileMenus->take(4); 
            @endphp

            <li>
                <a href="{{ route('frontend.index') }}"
                    class="flex flex-col items-center gap-1 p-2 rounded-lg {{ request()->routeIs('frontend.index') ? 'text-primary bg-blue-50' : 'text-gray-500 hover:text-primary' }}">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6">
                        </path>
                    </svg>
                    <span class="text-[10px] font-medium">Beranda</span>
                </a>
            </li>
            @foreach($mobileMenus as $menu)
                <li>
                    <a href="{{ $menu->final_url }}" target="{{ $menu->target }}"
                        class="flex flex-col items-center gap-1 p-2 rounded-lg {{ request()->url() == $menu->final_url ? 'text-primary bg-blue-50' : 'text-gray-500 hover:text-primary' }}">
                        {{-- Determine Icon based on title or random --}}
                        @if(stripos($menu->title, 'berita') !== false)
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z">
                                </path>
                            </svg>
                        @elseif(stripos($menu->title, 'kontak') !== false)
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z">
                                </path>
                            </svg>
                        @elseif(stripos($menu->title, 'download') !== false)
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path>
                            </svg>
                        @else
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        @endif
                        <span class="text-[10px] font-medium">{{ Str::limit($menu->title, 8) }}</span>
                    </a>
                </li>
            @endforeach

            <!-- Mobile Menu Toggle (More) -->
            <li x-data="{ sheetOpen: false }">
                <button @click="sheetOpen = true"
                    class="flex flex-col items-center gap-1 p-2 rounded-lg text-gray-500 hover:text-primary">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M4 6h16M4 12h16M4 18h16"></path>
                    </svg>
                    <span class="text-[10px] font-medium">Menu</span>
                </button>

                <!-- Full Screen Menu Sheet -->
                <div x-show="sheetOpen" style="display: none;" class="fixed inset-0 z-[60] bg-white">
                    <div class="flex flex-col h-full">
                        <div class="flex items-center justify-between p-4 border-b">
                            <span class="font-bold text-lg">Menu Utama</span>
                            <button @click="sheetOpen = false" class="p-2 bg-gray-100 rounded-full">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M6 18L18 6M6 6l12 12"></path>
                                </svg>
                            </button>
                        </div>
                        <div class="flex-grow overflow-y-auto p-4">
                            <ul class="space-y-4">
                                @php $allMenus = \App\Models\NavigationMenu::getMenuTree('top'); @endphp
                                @foreach($allMenus as $menu)
                                    <li>
                                        @if($menu->children->count() > 0)
                                            <div x-data="{ collapse: false }">
                                                <button @click="collapse = !collapse"
                                                    class="flex items-center justify-between w-full font-medium text-lg text-gray-800">
                                                    {{ $menu->title }}
                                                    <svg class="w-5 h-5 transition-transform" :class="{'rotate-180': collapse}"
                                                        fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                            d="M19 9l-7 7-7-7"></path>
                                                    </svg>
                                                </button>
                                                <ul x-show="collapse"
                                                    class="pl-4 mt-2 space-y-3 border-l-2 border-gray-100 ml-1">
                                                    @foreach($menu->children as $child)
                                                        <li><a href="{{ $child->final_url }}"
                                                                class="text-gray-600 block py-1">{{ $child->title }}</a></li>
                                                    @endforeach
                                                </ul>
                                            </div>
                                        @else
                                            <a href="{{ $menu->final_url }}"
                                                class="block font-medium text-lg text-gray-800">{{ $menu->title }}</a>
                                        @endif
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                </div>
            </li>
        </ul>
    </nav>
    <style>
        .safe-area-pb {
            padding-bottom: env(safe-area-inset-bottom, 20px);
        }
    </style>

    {{-- FontAwesome --}}
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <script src="{{ \App\Helpers\TemplateHelper::asset('js/script.js') }}"></script>
    
    {{-- Swiper JS --}}
    <script src="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js"></script>
    @stack('scripts')
    {{-- Search Modal --}}
    @stack('modals')

    {{-- Ads Popup --}}
    @if(isset($adsPopup) && $adsPopup)
        <div x-data="{ showAd: false }" 
             x-init="setTimeout(() => { 
                if (!sessionStorage.getItem('ad_popup_shown_{{ $adsPopup->id }}')) {
                    showAd = true; 
                    sessionStorage.setItem('ad_popup_shown_{{ $adsPopup->id }}', 'true');
                }
             }, 1000)" 
             x-show="showAd"
             class="fixed inset-0 z-[10000] flex items-center justify-center bg-black/80 backdrop-blur-sm p-4"
             x-cloak
             style="display: none;">
             
            <div class="relative max-w-lg w-full bg-white rounded-lg shadow-2xl overflow-hidden" @click.away="showAd = false">
                <button @click="showAd = false" class="absolute top-2 right-2 text-white bg-black/50 hover:bg-black rounded-full w-8 h-8 flex items-center justify-center transition-colors">
                    <i class="fas fa-times"></i>
                </button>
                
                <a href="{{ $adsPopup->excerpt ?? '#' }}" target="{{ $adsPopup->excerpt ? '_blank' : '_self' }}">
                    <img src="{{ Storage::url($adsPopup->featured_image) }}" alt="{{ $adsPopup->title }}" class="w-full h-auto">
                </a>
            </div>
        </div>
    @endif
    
    {{-- Footer Ad --}}
    @if(isset($adsFooter) && $adsFooter)
        <div x-data="{ 
                minimized: false,
                init() {
                    setTimeout(() => {
                        this.minimized = true;
                    }, 5000); // Auto hide after 5 seconds
                }
             }" 
             class="fixed bottom-0 left-0 right-0 z-50 transition-transform duration-500 ease-in-out"
             :class="minimized ? 'translate-y-full' : 'translate-y-0'">
             
             {{-- Ad Container --}}
             <div class="relative bg-white shadow-[0_-4px_6px_-1px_rgba(0,0,0,0.1)] p-2 text-center group">
                
                {{-- Toggle Button (Visible when minimized or hovered) --}}
                <button @click="minimized = !minimized" 
                        class="absolute -top-6 left-1/2 transform -translate-x-1/2 bg-white text-gray-700 px-4 py-1 rounded-t-lg shadow-[0_-4px_6px_-1px_rgba(0,0,0,0.1)] text-xs font-bold uppercase flex items-center gap-2 hover:bg-gray-50 transition-colors">
                    <span x-text="minimized ? 'Show Ad' : 'Hide Ad'"></span>
                    <i class="fas" :class="minimized ? 'fa-chevron-up' : 'fa-chevron-down'"></i>
                </button>

                <div class="container mx-auto relative">
                    <a href="{{ $adsFooter->excerpt ?? '#' }}" target="{{ $adsFooter->excerpt ? '_blank' : '_self' }}" class="block">
                        <img src="{{ Storage::url($adsFooter->featured_image) }}" alt="{{ $adsFooter->title }}" class="mx-auto h-20 md:h-24 object-contain">
                    </a>
                </div>
             </div>
        </div>
    @endif

    <div x-show="searchOpen" x-cloak 
         class="fixed inset-0 z-[100] flex items-center justify-center bg-black/90 backdrop-blur-sm transition-opacity duration-300"
         x-transition:enter="ease-out duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="ease-in duration-200"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0">
         
         <div class="container mx-auto px-4 relative" @click.away="searchOpen = false">
            <button @click="searchOpen = false" class="absolute -top-20 right-4 text-white hover:text-cyan-300 text-4xl focus:outline-none">
                <i class="fas fa-times"></i>
            </button>
            
            <div class="max-w-3xl mx-auto text-center">
                <h2 class="text-3xl md:text-5xl font-bold text-white mb-8 font-heading">Search</h2>
                <form action="{{ route('frontend.search') }}" method="GET" class="relative">
                    <input type="text" name="q" placeholder="What are you looking for?" 
                           class="w-full bg-transparent border-b-2 border-gray-700 text-white text-2xl md:text-3xl py-4 focus:outline-none focus:border-cyan-500 transition-colors placeholder-gray-600 font-light"
                           autofocus x-trap="searchOpen">
                    <button type="submit" class="absolute right-0 top-1/2 -translate-y-1/2 text-cyan-500 hover:text-white text-3xl transition-colors">
                        <i class="fas fa-search"></i>
                    </button>
                </form>
                <p class="text-gray-500 mt-6 text-sm">Type keywords and hit enter to search for courses, news, or pages.</p>
            </div>
         </div>
    </div>
</body>

</html>