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
        rel="stylesheet">

    {{-- Tailwind CSS & Plugins --}}
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: '#059669', // Force Emerald-600 for ComproHijau
                        secondary: '#064e3b', // emerald-900
                        accent: '#10b981', // emerald-500
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

<body class="font-sans antialiased text-gray-800 bg-gray-50 flex flex-col min-h-screen">

    {{-- Header (Foxiz Style) --}}
    <header class="fixed w-full top-0 z-50 transition-all duration-300">

        {{-- Top Bar (New: Date & Utility) --}}
        <div class="bg-black text-xs text-gray-400 py-1.5 border-b border-white/10 hidden md:block relative z-30">
            <div class="container mx-auto px-4 md:px-6 flex justify-between items-center">
                {{-- Date --}}
                <div class="flex items-center gap-4">
                    <span class="flex items-center gap-2">
                        <i class="far fa-clock text-primary"></i>
                        <span x-data
                            x-text="new Date().toLocaleDateString('id-ID', { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' })"></span>
                    </span>
                    @if(\App\Models\SiteSetting::getValue('contact_phone'))
                        <span class="hidden lg:flex items-center gap-2 border-l border-white/10 pl-4">
                            <i class="fas fa-phone-alt text-primary"></i>
                            <span>{{ \App\Models\SiteSetting::getValue('contact_phone') }}</span>
                        </span>
                    @endif
                </div>

                {{-- Admin/Login & Socials --}}
                <div class="flex items-center gap-4">
                    <div class="flex items-center gap-3 border-r border-white/10 pr-4 mr-1">
                        @if(\App\Models\SiteSetting::getValue('facebook_url'))
                            <a href="{{ \App\Models\SiteSetting::getValue('facebook_url') }}"
                                class="hover:text-white transition-colors"><i class="fab fa-facebook-f"></i></a>
                        @endif
                        @if(\App\Models\SiteSetting::getValue('instagram_url'))
                            <a href="{{ \App\Models\SiteSetting::getValue('instagram_url') }}"
                                class="hover:text-white transition-colors"><i class="fab fa-instagram"></i></a>
                        @endif
                        @if(\App\Models\SiteSetting::getValue('youtube_url'))
                            <a href="{{ \App\Models\SiteSetting::getValue('youtube_url') }}"
                                class="hover:text-white transition-colors"><i class="fab fa-youtube"></i></a>
                        @endif
                    </div>
                    @auth
                        <a href="{{ route('admin.dashboard') }}"
                            class="hover:text-white transition-colors font-medium">Dashboard</a>
                    @else
                        <a href="{{ route('login') }}" class="hover:text-white transition-colors">Login</a>
                    @endauth
                </div>
            </div>
        </div>

        {{-- Main Header (Dark/Secondary) --}}
        <div class="bg-secondary text-white shadow-md relative z-20">
            <div class="container mx-auto px-4 md:px-6">
                <div class="flex justify-between items-center h-16 md:h-20">

                    {{-- Logo --}}
                    <a href="{{ route('frontend.index') }}" class="flex items-center gap-3 shrink-0 mr-8">
                        @if(\App\Models\SiteSetting::getValue('logo'))
                            <img src="{{ Storage::url(\App\Models\SiteSetting::getValue('logo')) }}" alt="Logo"
                                class="w-auto bg-white/10 rounded p-1" style="height: {{ \App\Models\SiteSetting::getValue('logo_height', '50') }}px">
                        @endif
                        
                    </a>

                    {{-- Desktop Menu --}}
                    <nav class="hidden lg:flex items-center space-x-1 flex-grow justify-end mr-6">
                        @php
                            $menus = \App\Models\NavigationMenu::getMenuTree('top');
                        @endphp

                        @foreach($menus as $menu)
                            @if($menu->children->count() > 0)
                                <div class="relative group h-16 md:h-20 flex items-center" x-data="{ open: false }"
                                    @mouseenter="open = true" @mouseleave="open = false">
                                    <button
                                        class="flex items-center gap-1 font-bold text-sm uppercase tracking-wide px-3 hover:text-accent transition-colors">
                                        {{ $menu->title }}
                                        <svg class="w-3 h-3 transition-transform duration-200" :class="{ 'rotate-180': open }"
                                            fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M19 9l-7 7-7-7"></path>
                                        </svg>
                                    </button>
                                    <div x-show="open" x-cloak
                                        class="absolute top-full right-0 mt-0 w-56 bg-white rounded-b-xl shadow-xl py-2 text-gray-800 border-t-2 border-primary origin-top-right transition-all duration-200">
                                        @foreach($menu->children as $child)
                                            <a href="{{ $child->final_url }}" target="{{ $child->target }}"
                                                class="block px-4 py-2 text-sm hover:bg-gray-50 hover:text-primary">
                                                {{ $child->title }}
                                            </a>
                                        @endforeach
                                    </div>
                                </div>
                            @else
                                <a href="{{ $menu->final_url }}" target="{{ $menu->target }}"
                                    class="h-16 md:h-20 flex items-center font-bold text-sm uppercase tracking-wide px-3 {{ request()->url() == $menu->final_url ? 'text-accent border-b-2 border-accent' : 'hover:text-accent' }} transition-colors">
                                    {{ $menu->title }}
                                </a>
                            @endif
                        @endforeach
                    </nav>

                    {{-- Search Icon (Only) --}}
                    <div class="flex items-center shrink-0">
                        <div x-data="{ searchOpen: false }">
                            <button @click="searchOpen = !searchOpen" class="hover:text-accent transition-colors p-2">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                                </svg>
                            </button>
                            <!-- Search Modal -->
                            <div x-show="searchOpen" x-cloak
                                class="absolute top-full right-0 mt-2 w-80 bg-white rounded-xl shadow-xl p-3 text-gray-800 z-50">
                                <form action="{{ route('frontend.search') }}" method="GET" class="relative">
                                    <input type="text" name="q"
                                        class="w-full bg-gray-100 rounded-lg pl-4 pr-10 py-2 text-sm focus:outline-none focus:ring-1 focus:ring-primary"
                                        placeholder="Cari...">
                                    <button type="submit"
                                        class="absolute right-2 top-1/2 -translate-y-1/2 text-gray-500 hover:text-primary">
                                        <i class="fas fa-search"></i>
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Sub Bar (Primary/Green) - Ticker --}}
        <div class="bg-primary text-white text-sm relative z-10 shadow-sm border-t border-white/10">
            <div class="container mx-auto px-4 md:px-6">
                <div class="flex items-center h-10 overflow-hidden">
                    <div class="shrink-0 font-bold bg-black/10 px-3 h-full flex items-center mr-4">
                        <i class="fas fa-bolt mr-2 text-yellow-300"></i> TRENDING
                    </div>
                    <div class="flex-grow overflow-hidden relative group">
                        {{-- Marquee Effect --}}
                        <div class="whitespace-nowrap animate-marquee hover:pause flex items-center gap-8">
                            @php try {
                                    $trending = \App\Models\Post::published()->orderBy('views', 'desc')->take(5)->get();
                                } catch (\Exception $e) {
                                    $trending = collect([]);
                            } @endphp
                        @foreach($trending as $post)
                                <a href="{{ route('frontend.post', $post->slug) }}"
                                    class="inline-flex items-center hover:underline opacity-90 hover:opacity-100">
                                    <span class="mr-2 text-xs opacity-70">&bull;</span> {{ $post->title }}
                            </a>
                        @endforeach
                        {{-- Repeat for smooth loop --}}
                        @foreach($trending as $post)
                            <a href="{{ route('frontend.post', $post->slug) }}"
                                class="inline-flex items-center hover:underline opacity-90 hover:opacity-100">
                                <span class="mr-2 text-xs opacity-70">&bull;</span> {{ $post->title }}
                            </a>
                        @endforeach
                    </div>
                </div>
                    <div class="shrink-0 flex items-center gap-3 ml-4 pl-4 border-l border-white/10 h-full">
                        @if(\App\Models\SiteSetting::getValue('facebook_url'))
                            <a href="{{ \App\Models\SiteSetting::getValue('facebook_url') }}" class="hover:text-white/80"><i
                                    class="fab fa-facebook-f"></i></a>
                        @endif
                        @if(\App\Models\SiteSetting::getValue('instagram_url'))
                            <a href="{{ \App\Models\SiteSetting::getValue('instagram_url') }}"
                                class="hover:text-white/80"><i class="fab fa-instagram"></i></a>
                        @endif
                        @if(\App\Models\SiteSetting::getValue('youtube_url'))
                            <a href="{{ \App\Models\SiteSetting::getValue('youtube_url') }}" class="hover:text-white/80"><i
                                    class="fab fa-youtube"></i></a>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </header>

    {{-- Main Content --}}
    {{-- Adjusted padding for 3-tier fixed header (approx 150px) --}}
    <main class="flex-grow pt-[140px] md:pt-[160px]">
        @yield('content')
    </main>

    {{-- Footer --}}
    <footer class="bg-secondary text-white pt-16 pb-24 lg:pb-8">
        <div class="container mx-auto px-4 md:px-6">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-12 mb-12">
            <!-- Identity -->
            <div>
                    <div class="flex items-center gap-2 mb-6">
                        @if(\App\Models\SiteSetting::getValue('logo'))
                            <img src="{{ Storage::url(\App\Models\SiteSetting::getValue('logo')) }}" alt="Logo"
                                class="h-10 bg-white rounded p-1">
                        @endif
                        <span
                            class="text-xl font-bold font-heading">{{ \App\Models\SiteSetting::getValue('site_name') }}</span>
                    </div>
                    <p class="text-gray-300 mb-6 leading-relaxed text-sm">
                        {{ Str::limit(\App\Models\SiteSetting::getValue('site_description'), 150) }}
                    </p>
                </div>

                <!-- Quick Links (Footer) -->
                <div>
                    <h3
                    class="text-lg font-bold font-heading mb-6 text-white border-b border-white/20 pb-2 inline-block">
                        Tautan</h3>
                    <ul class="space-y-2">
                    @php
                        $footerMenus = \App\Models\NavigationMenu::getMenuTree('quicklink');
                    @endphp
                    @foreach($footerMenus as $menu)
                        <li>
                            <a href="{{ $menu->final_url }}" target="{{ $menu->target }}"
                                    class="text-gray-300 hover:text-white hover:translate-x-1 transition-all inline-block">
                                    {{ $menu->title }}
                                </a>
                            </li>
                    @endforeach
                    </ul>
                </div>

                <!-- Contact -->
                <div>
                    <h3
                        class="text-lg font-bold font-heading mb-6 text-white border-b border-white/20 pb-2 inline-block">
                        Kontak</h3>
                    <ul class="space-y-4 text-sm text-gray-300">
                        <li class="flex items-start gap-3">
                            <i class="fas fa-map-marker-alt mt-1 text-accent"></i>
                            <span>{{ \App\Models\SiteSetting::getValue('contact_address', '-') }}</span>
                        </li>
                        <li class="flex items-center gap-3">
                            <i class="fas fa-envelope text-accent"></i>
                            <span>{{ \App\Models\SiteSetting::getValue('contact_email', '-') }}</span>
                        </li>
                        <li class="flex items-center gap-3">
                            <i class="fas fa-phone text-accent"></i>
                            <span>{{ \App\Models\SiteSetting::getValue('contact_phone', '-') }}</span>
                        </li>
                    </ul>
                </div>

                <!-- Social -->
                <div>
                    <h3
                    class="text-lg font-bold font-heading mb-6 text-white border-b border-white/20 pb-2 inline-block">
                    Sosial Media</h3>
                <div class="flex space-x-3">
                        @if(\App\Models\SiteSetting::getValue('facebook_url'))
                            <a href="{{ \App\Models\SiteSetting::getValue('facebook_url') }}"
                            class="w-9 h-9 rounded-full bg-white/10 flex items-center justify-center hover:bg-primary transition-colors"><i
                                class="fab fa-facebook-f"></i></a>
                        @endif
                        @if(\App\Models\SiteSetting::getValue('instagram_url'))
                            <a href="{{ \App\Models\SiteSetting::getValue('instagram_url') }}"
                            class="w-9 h-9 rounded-full bg-white/10 flex items-center justify-center hover:bg-primary transition-colors"><i
                                class="fab fa-instagram"></i></a>
                        @endif
                        @if(\App\Models\SiteSetting::getValue('twitter_url'))
                            <a href="{{ \App\Models\SiteSetting::getValue('twitter_url') }}"
                            class="w-9 h-9 rounded-full bg-white/10 flex items-center justify-center hover:bg-primary transition-colors"><i
                                class="fab fa-twitter"></i></a>
                        @endif
                        @if(\App\Models\SiteSetting::getValue('youtube_url'))
                            <a href="{{ \App\Models\SiteSetting::getValue('youtube_url') }}"
                                class="w-9 h-9 rounded-full bg-white/10 flex items-center justify-center hover:bg-primary transition-colors"><i
                                    class="fab fa-youtube"></i></a>
                        @endif
                    </div>
                </div>
            </div>

            <div class="border-t border-white/10 pt-8 text-center text-gray-400 text-sm">
                <p>&copy; {{ date('Y') }} {{ \App\Models\SiteSetting::getValue('site_name') }}. All rights reserved.</p>
            </div>
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
                    class="flex flex-col items-center gap-1 p-2 rounded-lg {{ request()->routeIs('frontend.index') ? 'text-primary bg-green-50' : 'text-gray-500 hover:text-primary' }}">
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
                class="flex flex-col items-center gap-1 p-2 rounded-lg {{ request()->url() == $menu->final_url ? 'text-primary bg-green-50' : 'text-gray-500 hover:text-primary' }}">
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
    @stack('scripts')
</body>

</html>