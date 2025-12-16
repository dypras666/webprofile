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

    {{-- Twitter Card Meta Tags --}}
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="@yield('twitter_title', \App\Models\SiteSetting::getValue('site_name'))">
    <meta name="twitter:description"
        content="@yield('twitter_description', \App\Models\SiteSetting::getValue('meta_description'))">
    <meta name="twitter:image"
        content="@yield('twitter_image', \App\Models\SiteSetting::getValue('og_image') ? Storage::url(\App\Models\SiteSetting::getValue('og_image')) : asset('images/default-og.jpg'))">

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
        href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Playfair+Display:wght@400;500;600;700&display=swap"
        rel="stylesheet">

    {{-- Tailwind CSS & Plugins (CDN for comprehensive template) --}}
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: '{{ \App\Models\SiteSetting::getValue('theme_color', '#3b82f6') }}',
                        secondary: '#1e293b',
                    },
                    fontFamily: {
                        sans: ['Inter', 'sans-serif'],
                        serif: ['Playfair Display', 'serif'],
                    }
                }
            }
        }
    </script>

    {{-- Alpine.js --}}
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

    {{-- AOS Animation --}}
    <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">

    {{-- Custom Styles --}}
    <link href="{{ \App\Helpers\TemplateHelper::asset('css/style.css') }}" rel="stylesheet">

    <style>
        [x-cloak] {
            display: none !important;
        }

        .hero-pattern {
            background-color: #f3f4f6;
            background-image: url("data:image/svg+xml,%3Csvg width='60' height='60' viewBox='0 0 60 60' xmlns='http://www.w3.org/2000/svg'%3E%3Cg fill='none' fill-rule='evenodd'%3E%3Cg fill='%239C92AC' fill-opacity='0.05'%3E%3Cpath d='M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6 4V0H4v4H0v2h4v4h2V6h4V4H6z'/%3E%3C/g%3E%3C/g%3E%3C/svg%3E");
        }
    </style>

    {{-- Schema.org --}}
    <script type="application/ld+json">
    {
        "@context": "https://schema.org",
        "@type": "WebSite",
        "name": "{{ \App\Models\SiteSetting::getValue('site_name') }}",
        "url": "{{ url('/') }}",
        "description": "{{ \App\Models\SiteSetting::getValue('meta_description') }}"
    }
    </script>

    @stack('head')
</head>

<body class="font-sans antialiased text-gray-800 bg-gray-50 flex flex-col min-h-screen">

    {{-- Header --}}
    <header x-data="{ mobileMenuOpen: false, scrolled: false, searchOpen: false }"
        @scroll.window="scrolled = (window.pageYOffset > 20)"
        :class="{ 'bg-white/95 backdrop-blur shadow-sm': scrolled, 'bg-transparent': !scrolled }"
        class="fixed w-full top-0 z-50 transition-all duration-300">
        <nav class="container mx-auto px-4 md:px-6 py-3 md:py-4">
            <div class="flex justify-between items-center">
                <!-- Logo -->
                <a href="{{ route('frontend.index') }}" class="flex items-center gap-2 group">
                    @if(\App\Models\SiteSetting::getValue('logo'))
                        <img src="{{ Storage::url(\App\Models\SiteSetting::getValue('logo')) }}" alt="Logo"
                            class="w-auto transition-transform group-hover:scale-105"
                            style="height: {{ \App\Models\SiteSetting::getValue('logo_height', '50') }}px">
                    @endif
                    <div class="flex flex-col">
                        <span
                            class="text-lg md:text-xl font-bold text-gray-900 leading-tight group-hover:text-primary transition-colors">
                            {{ \App\Models\SiteSetting::getValue('site_name', 'Web Profile') }}
                        </span>
                    </div>
                </a>

                <!-- Desktop Menu -->
                <div class="hidden lg:flex items-center space-x-1">
                    @php
                        $menus = \App\Models\NavigationMenu::getMenuTree('top');
                    @endphp

                    @foreach($menus as $menu)
                        @if($menu->children->count() > 0)
                            <div class="relative group" x-data="{ open: false }" @mouseenter="open = true"
                                @mouseleave="open = false">
                                <button
                                    class="flex items-center gap-1 px-4 py-2 rounded-full font-medium text-gray-600 hover:text-primary hover:bg-gray-100 transition-all">
                                    {{ $menu->title }}
                                    <svg class="w-4 h-4 transition-transform duration-200" :class="{ 'rotate-180': open }"
                                        fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M19 9l-7 7-7-7"></path>
                                    </svg>
                                </button>
                                <div x-show="open" x-transition:enter="transition ease-out duration-200"
                                    x-transition:enter-start="opacity-0 translate-y-1"
                                    x-transition:enter-end="opacity-100 translate-y-0"
                                    x-transition:leave="transition ease-in duration-150"
                                    x-transition:leave-start="opacity-100 translate-y-0"
                                    x-transition:leave-end="opacity-0 translate-y-1"
                                    class="absolute left-0 mt-0 w-56 bg-white rounded-xl shadow-lg ring-1 ring-black ring-opacity-5 p-2 z-50"
                                    style="display: none;">
                                    @foreach($menu->children as $child)
                                        <a href="{{ $child->final_url }}" target="{{ $child->target }}"
                                            class="block px-4 py-2.5 text-sm text-gray-700 hover:bg-gray-50 hover:text-primary rounded-lg">
                                            {{ $child->title }}
                                        </a>
                                    @endforeach
                                </div>
                            </div>
                        @else
                            <a href="{{ $menu->final_url }}" target="{{ $menu->target }}"
                                class="px-4 py-2 rounded-full font-medium {{ request()->url() == $menu->final_url ? 'bg-primary/10 text-primary' : 'text-gray-600 hover:text-primary hover:bg-gray-100' }} transition-all">
                                {{ $menu->title }}
                            </a>
                        @endif
                    @endforeach

                    <div class="ml-4 pl-4 border-l border-gray-200 flex items-center gap-3">
                        <button @click="searchOpen = !searchOpen"
                            class="p-2 text-gray-500 hover:text-primary transition-colors" title="Search">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                            </svg>
                        </button>
                    </div>
                </div>

                <!-- Mobile Menu Button -->
                <div class="lg:hidden flex items-center">
                    <button @click="mobileMenuOpen = !mobileMenuOpen"
                        class="text-gray-600 hover:text-primary focus:outline-none p-2">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path x-show="!mobileMenuOpen" stroke-linecap="round" stroke-linejoin="round"
                                stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
                            <path x-show="mobileMenuOpen" x-cloak stroke-linecap="round" stroke-linejoin="round"
                                stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>
            </div>
        </nav>

        <!-- Mobile Menu -->
        <div x-show="mobileMenuOpen" x-transition:enter="transition ease-out duration-200"
            x-transition:enter-start="opacity-0 -translate-y-2" x-transition:enter-end="opacity-100 translate-y-0"
            x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100 translate-y-0"
            x-transition:leave-end="opacity-0 -translate-y-2"
            class="absolute top-full left-0 w-full bg-white shadow-lg border-t border-gray-100 lg:hidden"
            style="display: none;">
            <div class="flex flex-col p-4 space-y-2">
                @foreach($menus as $menu)
                    @if($menu->children->count() > 0)
                        <div x-data="{ subOpen: false }" class="space-y-1">
                            <button @click="subOpen = !subOpen"
                                class="flex items-center justify-between w-full px-4 py-3 rounded-lg font-medium hover:bg-gray-50 text-gray-700">
                                <span>{{ $menu->title }}</span>
                                <svg class="w-4 h-4 transition-transform" :class="{ 'rotate-180': subOpen }" fill="none"
                                    stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7">
                                    </path>
                                </svg>
                            </button>
                            <div x-show="subOpen" class="pl-4 space-y-1">
                                @foreach($menu->children as $child)
                                    <a href="{{ $child->final_url }}" target="{{ $child->target }}"
                                        class="block px-4 py-2 text-sm text-gray-600 hover:text-primary">
                                        {{ $child->title }}
                                    </a>
                                @endforeach
                            </div>
                        </div>
                    @else
                        <a href="{{ $menu->final_url }}" target="{{ $menu->target }}"
                            class="block px-4 py-3 rounded-lg font-medium hover:bg-gray-50 text-gray-700">
                            {{ $menu->title }}
                        </a>
                    @endif
                @endforeach
            </div>
        </div>
        <!-- Search Modal -->
        <div x-show="searchOpen" x-transition:enter="transition ease-out duration-300"
            x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100"
            x-transition:leave="transition ease-in duration-200" x-transition:leave-start="opacity-100 scale-100"
            x-transition:leave-end="opacity-0 scale-95"
            class="fixed inset-0 z-50 flex items-center justify-center bg-gray-900/90 p-4" style="display: none;">
            <div class="absolute inset-0" @click="searchOpen = false"></div>
            <div class="relative w-full max-w-2xl bg-white rounded-2xl shadow-2xl overflow-hidden">
                <form action="{{ route('frontend.search') }}" method="GET" class="flex items-center p-2">
                    <div class="p-4 text-gray-400">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                        </svg>
                    </div>
                    <input type="text" name="q"
                        class="w-full text-lg border-none focus:ring-0 px-2 py-4 text-gray-800 placeholder-gray-400 font-medium"
                        placeholder="Pencarian..." autofocus>
                    <button type="button" @click="searchOpen = false"
                        class="p-4 text-gray-400 hover:text-red-500 transition-colors">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </form>
            </div>
        </div>
    </header>

    {{-- Main Content --}}
    <main class="flex-grow pt-20"> <!-- pt-20 to offset fixed header -->
        @yield('content')
    </main>

    {{-- Footer --}}
    <footer class="bg-secondary text-white pt-16 pb-8">
        <div class="container mx-auto px-4 md:px-6">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-12 mb-12">
                <!-- Footer Column 1: Info -->
                <div data-aos="fade-up">
                    <div class="flex items-center gap-2 mb-6">
                        @if(\App\Models\SiteSetting::getValue('logo'))
                            <img src="{{ Storage::url(\App\Models\SiteSetting::getValue('logo')) }}" alt="Logo"
                                class="h-10 bg-white rounded p-1">
                        @endif
                        <span
                            class="text-xl font-bold font-serif">{{ \App\Models\SiteSetting::getValue('site_name') }}</span>
                    </div>
                    <p class="text-gray-400 mb-6 leading-relaxed">
                        {{ Str::limit(\App\Models\SiteSetting::getValue('site_description'), 150) }}
                    </p>
                    <div class="flex space-x-4">
                        @if(\App\Models\SiteSetting::getValue('facebook_url'))
                            <a href="{{ \App\Models\SiteSetting::getValue('facebook_url') }}"
                                class="w-10 h-10 rounded-full bg-white/10 flex items-center justify-center hover:bg-primary transition-colors text-white">
                                <i class="fab fa-facebook-f"></i>
                            </a>
                        @endif
                        @if(\App\Models\SiteSetting::getValue('instagram_url'))
                            <a href="{{ \App\Models\SiteSetting::getValue('instagram_url') }}"
                                class="w-10 h-10 rounded-full bg-white/10 flex items-center justify-center hover:bg-primary transition-colors text-white">
                                <i class="fab fa-instagram"></i>
                            </a>
                        @endif
                        @if(\App\Models\SiteSetting::getValue('twitter_url'))
                            <a href="{{ \App\Models\SiteSetting::getValue('twitter_url') }}"
                                class="w-10 h-10 rounded-full bg-white/10 flex items-center justify-center hover:bg-primary transition-colors text-white">
                                <i class="fab fa-twitter"></i>
                            </a>
                        @endif
                        @if(\App\Models\SiteSetting::getValue('youtube_url'))
                            <a href="{{ \App\Models\SiteSetting::getValue('youtube_url') }}"
                                class="w-10 h-10 rounded-full bg-white/10 flex items-center justify-center hover:bg-primary transition-colors text-white">
                                <i class="fab fa-youtube"></i>
                            </a>
                        @endif
                    </div>
                </div>

                <!-- Footer Column 2: Links -->
                <div data-aos="fade-up" data-aos-delay="100">
                    <h3 class="text-lg font-bold mb-6 border-b border-gray-700 pb-2 inline-block">Tautan Cepat</h3>
                    <ul class="space-y-3">
                        @php
                            $footerMenus = \App\Models\NavigationMenu::getMenuTree('quicklink');
                        @endphp

                        @foreach($footerMenus as $menu)
                            <li>
                                <a href="{{ $menu->final_url }}" target="{{ $menu->target }}"
                                    class="text-gray-400 hover:text-primary transition-colors flex items-center gap-2">
                                    <span class="w-1.5 h-1.5 rounded-full bg-primary"></span> {{ $menu->title }}
                                </a>
                            </li>
                        @endforeach
                    </ul>
                </div>

                <!-- Footer Column 3: Contact -->
                <div data-aos="fade-up" data-aos-delay="200">
                    <h3 class="text-lg font-bold mb-6 border-b border-gray-700 pb-2 inline-block">Hubungi Kami</h3>
                    <ul class="space-y-4">
                        <li class="flex items-start gap-3 text-gray-400">
                            <svg class="w-5 h-5 text-primary mt-1 shrink-0" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z">
                                </path>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                            </svg>
                            <span>{{ \App\Models\SiteSetting::getValue('contact_address', 'Alamat belum diatur') }}</span>
                        </li>
                        <li class="flex items-center gap-3 text-gray-400">
                            <svg class="w-5 h-5 text-primary shrink-0" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z">
                                </path>
                            </svg>
                            <span>{{ \App\Models\SiteSetting::getValue('contact_email', 'admin@example.com') }}</span>
                        </li>
                        <li class="flex items-center gap-3 text-gray-400">
                            <svg class="w-5 h-5 text-primary shrink-0" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z">
                                </path>
                            </svg>
                            <span>{{ \App\Models\SiteSetting::getValue('contact_phone', '0812-3456-7890') }}</span>
                        </li>
                    </ul>
                </div>
            </div>

            <div
                class="border-t border-gray-700 pt-8 flex flex-col md:flex-row justify-between items-center text-gray-500 text-sm">
                <p>&copy; {{ date('Y') }} {{ \App\Models\SiteSetting::getValue('site_name') }}. All rights reserved.</p>
                <div class="flex space-x-4 mt-4 md:mt-0">
                    @php
                        $bottomMenus = \App\Models\NavigationMenu::getMenuTree('bottom');
                    @endphp
                    @foreach($bottomMenus as $menu)
                        <a href="{{ $menu->final_url }}" class="hover:text-white transition-colors">{{ $menu->title }}</a>
                    @endforeach
                </div>
            </div>
        </div>
    </footer>

    {{-- FontAwesome --}}
    <script src="https://kit.fontawesome.com/your-code.js" crossorigin="anonymous"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    {{-- AOS Script --}}
    <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
    <script>
        AOS.init({
            duration: 800,
            once: true,
            offset: 50,
        });
    </script>

    <script src="{{ \App\Helpers\TemplateHelper::asset('js/script.js') }}"></script>
    @stack('scripts')
</body>

</html>