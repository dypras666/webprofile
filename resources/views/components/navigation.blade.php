{{-- Navigation Component --}}
@php
    $navigationMenus = \App\Models\NavigationMenu::getMenuTree('top');
@endphp

<nav class="bg-blue-600 shadow-lg sticky top-0 z-50 w-full" x-data="{ mobileMenuOpen: false, searchOpen: false }">
    <div class="container mx-auto px-4">
        {{-- Desktop Navigation --}}
        <div class="hidden md:flex items-center justify-between h-16">
            {{-- Logo --}}
            <div class="flex-shrink-0 flex items-center">
                <a href="{{ route('frontend.index') }}" class="flex items-center space-x-3">
                    <img src="{{ asset('images/logo.png') }}" alt="{{ config('app.name') }}"
                        class="h-10 w-auto rounded-full p-1">
                    <div class="hidden lg:block">
                        <h1 class="text-lg font-bold text-white leading-tight">
                            {{ $siteSettings['site_name'] ?? config('app.name') }}
                        </h1>
                        <p class="text-xs text-blue-100 uppercase tracking-wider">
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
            </div>
        </div>

        {{-- Mobile Navigation --}}
        <div class="md:hidden">
            <div class="flex items-center justify-between h-16">
                {{-- Mobile Logo --}}
                <div class="flex items-center">
                    <a href="{{ route('frontend.index') }}" class="flex items-center space-x-2">
                        <img src="{{ asset('images/logo.png') }}" alt="{{ config('app.name') }}"
                            class="h-8 w-auto rounded-full p-0.5">
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
            <div x-show="searchOpen" x-collapse x-cloak class="border-t border-blue-500 bg-blue-700 p-4 shadow-inner">
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

{{-- Alpine.js for mobile menu functionality --}}
@push('scripts')
    <script src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
@endpush