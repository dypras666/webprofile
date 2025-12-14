{{-- Navigation Component --}}
@php
    $navigationMenus = \App\Models\NavigationMenu::getMenuTree('top');
@endphp

<nav class="bg-blue-600 shadow-lg sticky top-0 z-50 w-full" style="background-color: #2563eb;"
    x-data="{ mobileMenuOpen: false, searchOpen: false }">
    <div class="container mx-auto px-4">
        {{-- Desktop Navigation --}}
        <div class="hidden md:flex items-center justify-between h-16">
            {{-- Logo --}}
            <div class="flex-shrink-0 flex items-center">
                <a href="{{ route('frontend.index') }}" class="flex items-center space-x-3">
                    @if(isset($siteSettings['logo']) && $siteSettings['logo'])
                        <img src="{{ Str::startsWith($siteSettings['logo'], 'http') ? $siteSettings['logo'] : Storage::disk('public')->url($siteSettings['logo']) }}"
                            alt="{{ $siteSettings['site_name'] ?? config('app.name') }}"
                            class="h-10 w-auto rounded-full p-1">
                    @else
                        <img src="{{ asset('images/logo.png') }}"
                            alt="{{ $siteSettings['site_name'] ?? config('app.name') }}"
                            class="h-10 w-auto rounded-full p-1">
                    @endif
                    <div class="hidden md:block">
                        <span class="text-white font-bold text-lg leading-tight block">
                            {{ $siteSettings['site_name'] ?? config('app.name') }}
                        </span>
                        <p class="text-blue-200 text-xs font-medium">
                            {{ $siteSettings['site_description'] ?? 'Lembaga Penjaminan Mutu' }}
                        </p>
                    </div>
                </a>
            </div>

            {{-- Desktop Menu --}}
            <div class="hidden md:flex items-center space-x-1 ml-auto">
                @foreach($navigationMenus as $menu)
                    @if($menu->children->count() > 0)
                        <div class="relative group" x-data="{ open: false }" @mouseenter="open = true"
                            @mouseleave="open = false">
                            <button
                                class="flex items-center px-3 py-2 text-sm font-medium text-white hover:text-blue-200 hover:bg-blue-700/50 rounded-md transition-colors">
                                {{ $menu->title }}
                                <svg class="w-4 h-4 ml-1 transform transition-transform duration-200"
                                    :class="{ 'rotate-180': open }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7">
                                    </path>
                                </svg>
                            </button>
                            {{-- Dropdown --}}
                            <div x-show="open" x-transition:enter="transition ease-out duration-100"
                                x-transition:enter-start="transform opacity-0 scale-95"
                                x-transition:enter-end="transform opacity-100 scale-100"
                                x-transition:leave="transition ease-in duration-75"
                                x-transition:leave-start="transform opacity-100 scale-100"
                                x-transition:leave-end="transform opacity-0 scale-95" x-cloak
                                class="absolute right-0 mt-2 w-48 bg-white rounded-md shadow-lg py-1 z-50 ring-1 ring-black ring-opacity-5">
                                @foreach($menu->children as $child)
                                    <a href="{{ $child->url }}" target="{{ $child->target }}"
                                        class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 hover:text-blue-600">
                                        {{ $child->title }}
                                    </a>
                                @endforeach
                            </div>
                        </div>
                    @else
                        <a href="{{ $menu->url }}" target="{{ $menu->target }}"
                            class="px-3 py-2 text-sm font-medium text-white hover:text-blue-200 hover:bg-blue-700/50 rounded-md transition-colors">
                            {{ $menu->title }}
                        </a>
                    @endif
                @endforeach

                {{-- Search Button --}}
                {{-- Desktop Animated Search --}}
                <div class="relative ml-4 hidden md:block" x-data="{ expanded: false }">
                    <form action="{{ route('frontend.search') }}" method="GET">
                        <div class="flex items-center rounded-full transition-all duration-300 ease-in-out border"
                            :class="expanded ? 'w-[450px] bg-white border-white shadow-lg' : 'w-10 bg-transparent border-transparent hover:bg-white/10'">

                            <button type="button"
                                @click="expanded = !expanded; if(expanded) $nextTick(() => $refs.desktopSearchInput.focus())"
                                class="p-2 rounded-full flex-shrink-0 transition-colors duration-300 focus:outline-none z-10"
                                :class="expanded ? 'text-blue-600' : 'text-white'">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                                </svg>
                            </button>

                            <input type="text" name="search" x-ref="desktopSearchInput" x-cloak
                                class="w-full bg-transparent border-none text-gray-800 placeholder-gray-500 focus:ring-0 focus:outline-none px-2 h-10 transition-opacity duration-200"
                                :class="expanded ? 'opacity-100' : 'opacity-0 w-0 p-0 overflow-hidden'"
                                placeholder="Cari berita, agenda, galeri, atau dokumen..."
                                @click.away="if($el.value === '') expanded = false" @keydown.escape="expanded = false">

                            <button type="submit" x-show="expanded" x-cloak
                                x-transition:enter="transition ease-out duration-200"
                                x-transition:enter-start="opacity-0 transform scale-90"
                                x-transition:enter-end="opacity-100 transform scale-100"
                                class="mr-3 text-gray-400 hover:text-blue-600 focus:outline-none">
                                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M14 5l7 7m0 0l-7 7m7-7H3"></path>
                                </svg>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        {{-- Mobile Navigation --}}
        <div class="md:hidden">
            <div class="flex items-center justify-between h-16 text-white">
                {{-- Mobile Logo --}}
                <div class="flex items-center">
                    <a href="{{ route('frontend.index') }}" class="flex items-center space-x-2">
                        @if(isset($siteSettings['logo']) && $siteSettings['logo'])
                            <img src="{{ Str::startsWith($siteSettings['logo'], 'http') ? $siteSettings['logo'] : Storage::disk('public')->url($siteSettings['logo']) }}"
                                alt="{{ $siteSettings['site_name'] ?? config('app.name') }}"
                                class="h-8 w-auto rounded-full p-0.5">
                        @else
                            <img src="{{ asset('images/logo.png') }}"
                                alt="{{ $siteSettings['site_name'] ?? config('app.name') }}"
                                class="h-8 w-auto rounded-full p-0.5">
                        @endif
                        <span class="text-white font-bold text-sm leading-tight max-w-[150px]">
                            {{ $siteSettings['site_name'] ?? config('app.name') }}
                        </span>
                    </a>
                </div>

                <div class="flex items-center space-x-2">
                    <button
                        @click="searchOpen = !searchOpen; if(searchOpen) $nextTick(() => $refs.mobileSearchInput.focus())"
                        class="text-white p-2">
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                        </svg>
                    </button>
                    <button @click="mobileMenuOpen = !mobileMenuOpen"
                        class="text-white hover:text-blue-200 p-2 rounded-md transition-colors">
                        <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path x-show="!mobileMenuOpen" stroke-linecap="round" stroke-linejoin="round"
                                stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
                            <path x-show="mobileMenuOpen" stroke-linecap="round" stroke-linejoin="round"
                                stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>
            </div>

            {{-- Mobile Search Bar --}}
            <div x-show="searchOpen" x-collapse x-cloak
                class="md:hidden border-t border-blue-500 bg-blue-700 p-4 shadow-inner">
                <form action="{{ route('frontend.search') }}" method="GET">
                    <div class="relative">
                        <input type="text" name="search" x-ref="mobileSearchInput"
                            class="w-full pl-4 pr-10 py-2 rounded-lg text-gray-900 focus:outline-none focus:ring-2 focus:ring-white/50"
                            placeholder="Cari berita, artikel, atau dokumen...">
                        <button type="submit"
                            class="absolute right-2 top-1/2 transform -translate-y-1/2 text-gray-400 hover:text-blue-600">
                            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                            </svg>
                        </button>
                    </div>
                </form>
            </div>

            {{-- Mobile Menu --}}
            <div x-show="mobileMenuOpen" x-transition:enter="transition ease-out duration-200"
                x-transition:enter-start="opacity-0 transform scale-95"
                x-transition:enter-end="opacity-100 transform scale-100"
                x-transition:leave="transition ease-in duration-150"
                x-transition:leave-start="opacity-100 transform scale-100"
                x-transition:leave-end="opacity-0 transform scale-95" class="bg-blue-700 border-t border-blue-500">
                <div class="px-2 pt-2 pb-3 space-y-1">
                    @foreach($navigationMenus as $menu)
                        @if($menu->children->count() > 0)
                            {{-- Mobile menu with children --}}
                            <div x-data="{ submenuOpen: false }">
                                <button @click="submenuOpen = !submenuOpen"
                                    class="text-white hover:text-blue-200 w-full text-left px-3 py-2 rounded-md text-base font-medium transition-colors flex items-center justify-between {{ $menu->css_class }}">
                                    <span class="flex items-center">
                                        @if($menu->icon)
                                            <i class="{{ $menu->icon }} mr-2"></i>
                                        @endif
                                        {{ $menu->title }}
                                    </span>
                                    <svg class="h-4 w-4 transform transition-transform" :class="{ 'rotate-180': submenuOpen }"
                                        fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M19 9l-7 7-7-7"></path>
                                    </svg>
                                </button>

                                <div x-show="submenuOpen" x-transition:enter="transition ease-out duration-200"
                                    x-transition:enter-start="opacity-0 transform scale-95"
                                    x-transition:enter-end="opacity-100 transform scale-100" class="ml-4 space-y-1">
                                    @foreach($menu->children as $child)
                                        <a href="{{ $child->final_url }}"
                                            class="text-blue-200 hover:text-white block px-3 py-2 rounded-md text-sm transition-colors {{ $child->css_class }}"
                                            @if($child->target) target="{{ $child->target }}" @endif>
                                            @if($child->icon)
                                                <i class="{{ $child->icon }} mr-2"></i>
                                            @endif
                                            {{ $child->title }}
                                        </a>
                                    @endforeach
                                </div>
                            </div>
                        @else
                            {{-- Single mobile menu item --}}
                            <a href="{{ $menu->final_url }}"
                                class="text-white hover:text-blue-200 block px-3 py-2 rounded-md text-base font-medium transition-colors {{ $menu->css_class }}"
                                @if($menu->target) target="{{ $menu->target }}" @endif>
                                @if($menu->icon)
                                    <i class="{{ $menu->icon }} mr-2"></i>
                                @endif
                                {{ $menu->title }}
                            </a>
                        @endif
                    @endforeach

                    @auth
                        <div class="border-t border-blue-500 pt-2">
                            <a href="{{ route('admin.dashboard') }}"
                                class="text-white hover:text-blue-200 block px-3 py-2 rounded-md text-base font-medium transition-colors">
                                Dashboard
                            </a>
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit"
                                    class="text-red-300 hover:text-red-100 w-full text-left px-3 py-2 rounded-md text-base font-medium transition-colors">
                                    Logout
                                </button>
                            </form>
                        </div>
                    @else
                        <div class="border-t border-blue-500 pt-2">
                            <a href="{{ route('login') }}"
                                class="text-white hover:text-blue-200 block px-3 py-2 rounded-md text-base font-medium transition-colors">
                                Login
                            </a>
                        </div>
                    @endauth
                </div>
            </div>
        </div>
    </div>
</nav>

{{-- Alpine.js for navigation functionality --}}
@push('scripts')
    <!-- Alpine Collapse Plugin (must load before Alpine Core) -->
    <script defer src="https://cdn.jsdelivr.net/npm/@alpinejs/collapse@3.x.x/dist/cdn.min.js"></script>
    <!-- Alpine Core -->
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
@endpush