{{-- Navigation Component --}}
<nav class="bg-blue-600 shadow-lg" x-data="{ mobileMenuOpen: false }">
    <div class="container mx-auto px-4">
        {{-- Desktop Navigation --}}
        <div class="hidden md:flex items-center justify-between h-16">
            <div class="flex items-center space-x-8">
                <a href="{{ route('frontend.index') }}" 
                   class="text-white hover:text-blue-200 px-3 py-2 rounded-md text-sm font-medium transition-colors {{ request()->routeIs('frontend.index') ? 'bg-blue-700' : '' }}">
                    Home
                </a>
                
                <div class="relative group">
                    <button class="text-white hover:text-blue-200 px-3 py-2 rounded-md text-sm font-medium transition-colors flex items-center">
                        Categories
                        <svg class="ml-1 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                        </svg>
                    </button>
                    
                    {{-- Categories Dropdown --}}
                    <div class="absolute left-0 mt-2 w-48 bg-white rounded-md shadow-lg opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all duration-200 z-50">
                        <div class="py-1">
                            @php
                                $categories = \App\Models\Category::active()->ordered()->get();
                            @endphp
                            @foreach($categories as $category)
                                <a href="{{ route('frontend.category', $category->slug) }}" 
                                   class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 hover:text-gray-900 transition-colors">
                                    {{ $category->name }}
                                    <span class="text-xs text-gray-500 ml-1">({{ $category->posts_count ?? 0 }})</span>
                                </a>
                            @endforeach
                            <div class="border-t border-gray-100"></div>
                            <a href="{{ route('frontend.posts') }}" 
                               class="block px-4 py-2 text-sm text-blue-600 hover:bg-gray-100 font-medium">
                                View All Posts
                            </a>
                        </div>
                    </div>
                </div>
                
                <a href="{{ route('frontend.posts') }}" 
                   class="text-white hover:text-blue-200 px-3 py-2 rounded-md text-sm font-medium transition-colors {{ request()->routeIs('frontend.posts') ? 'bg-blue-700' : '' }}">
                    Articles
                </a>
                
                <a href="{{ route('frontend.gallery') }}" 
                   class="text-white hover:text-blue-200 px-3 py-2 rounded-md text-sm font-medium transition-colors {{ request()->routeIs('frontend.gallery') ? 'bg-blue-700' : '' }}">
                    Gallery
                </a>
                
                <a href="{{ route('frontend.about') }}" 
                   class="text-white hover:text-blue-200 px-3 py-2 rounded-md text-sm font-medium transition-colors {{ request()->routeIs('frontend.about') ? 'bg-blue-700' : '' }}">
                    About
                </a>
                
                <a href="{{ route('frontend.contact') }}" 
                   class="text-white hover:text-blue-200 px-3 py-2 rounded-md text-sm font-medium transition-colors {{ request()->routeIs('frontend.contact') ? 'bg-blue-700' : '' }}">
                    Contact
                </a>
            </div>
            
            {{-- Right Side Menu --}}
            <div class="flex items-center space-x-4">
                @auth
                    <div class="relative group">
                        <button class="text-white hover:text-blue-200 px-3 py-2 rounded-md text-sm font-medium transition-colors flex items-center">
                            {{ Auth::user()->name }}
                            <svg class="ml-1 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                            </svg>
                        </button>
                        
                        <div class="absolute right-0 mt-2 w-48 bg-white rounded-md shadow-lg opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all duration-200 z-50">
                            <div class="py-1">
                                <a href="{{ route('admin.dashboard') }}" 
                                   class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                    Dashboard
                                </a>
                                <a href="{{ route('admin.profile') }}" 
                                   class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                    Profile
                                </a>
                                <div class="border-t border-gray-100"></div>
                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <button type="submit" 
                                            class="block w-full text-left px-4 py-2 text-sm text-red-600 hover:bg-gray-100">
                                        Logout
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                @else
                    <a href="{{ route('login') }}" 
                       class="text-white hover:text-blue-200 px-3 py-2 rounded-md text-sm font-medium transition-colors">
                        Login
                    </a>
                @endauth
            </div>
        </div>
        
        {{-- Mobile Navigation --}}
        <div class="md:hidden">
            <div class="flex items-center justify-between h-16">
                <button @click="mobileMenuOpen = !mobileMenuOpen" 
                        class="text-white hover:text-blue-200 p-2 rounded-md transition-colors">
                    <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path x-show="!mobileMenuOpen" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
                        <path x-show="mobileMenuOpen" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>
            
            {{-- Mobile Menu --}}
            <div x-show="mobileMenuOpen" 
                 x-transition:enter="transition ease-out duration-200"
                 x-transition:enter-start="opacity-0 transform scale-95"
                 x-transition:enter-end="opacity-100 transform scale-100"
                 x-transition:leave="transition ease-in duration-150"
                 x-transition:leave-start="opacity-100 transform scale-100"
                 x-transition:leave-end="opacity-0 transform scale-95"
                 class="bg-blue-700 border-t border-blue-500">
                <div class="px-2 pt-2 pb-3 space-y-1">
                    <a href="{{ route('frontend.index') }}" 
                       class="text-white hover:text-blue-200 block px-3 py-2 rounded-md text-base font-medium transition-colors {{ request()->routeIs('frontend.index') ? 'bg-blue-800' : '' }}">
                        Home
                    </a>
                    
                    {{-- Mobile Categories --}}
                    <div x-data="{ categoriesOpen: false }">
                        <button @click="categoriesOpen = !categoriesOpen" 
                                class="text-white hover:text-blue-200 w-full text-left px-3 py-2 rounded-md text-base font-medium transition-colors flex items-center justify-between">
                            Categories
                            <svg class="h-4 w-4 transform transition-transform" :class="{ 'rotate-180': categoriesOpen }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                            </svg>
                        </button>
                        
                        <div x-show="categoriesOpen" 
                             x-transition:enter="transition ease-out duration-200"
                             x-transition:enter-start="opacity-0 transform scale-95"
                             x-transition:enter-end="opacity-100 transform scale-100"
                             class="ml-4 space-y-1">
                            @foreach($categories as $category)
                                <a href="{{ route('frontend.category', $category->slug) }}" 
                                   class="text-blue-200 hover:text-white block px-3 py-2 rounded-md text-sm transition-colors">
                                    {{ $category->name }}
                                </a>
                            @endforeach
                        </div>
                    </div>
                    
                    <a href="{{ route('frontend.posts') }}" 
                       class="text-white hover:text-blue-200 block px-3 py-2 rounded-md text-base font-medium transition-colors {{ request()->routeIs('frontend.posts') ? 'bg-blue-800' : '' }}">
                        Articles
                    </a>
                    
                    <a href="{{ route('frontend.gallery') }}" 
                       class="text-white hover:text-blue-200 block px-3 py-2 rounded-md text-base font-medium transition-colors {{ request()->routeIs('frontend.gallery') ? 'bg-blue-800' : '' }}">
                        Gallery
                    </a>
                    
                    <a href="{{ route('frontend.about') }}" 
                       class="text-white hover:text-blue-200 block px-3 py-2 rounded-md text-base font-medium transition-colors {{ request()->routeIs('frontend.about') ? 'bg-blue-800' : '' }}">
                        About
                    </a>
                    
                    <a href="{{ route('frontend.contact') }}" 
                       class="text-white hover:text-blue-200 block px-3 py-2 rounded-md text-base font-medium transition-colors {{ request()->routeIs('frontend.contact') ? 'bg-blue-800' : '' }}">
                        Contact
                    </a>
                    
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