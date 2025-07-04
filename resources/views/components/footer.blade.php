{{-- Footer Component --}}
<footer class="bg-gray-900 text-white">
    {{-- Main Footer Content --}}
    <div class="container mx-auto px-4 py-12">
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8">
            {{-- Company Info --}}
            <div class="lg:col-span-1">
                <div class="flex items-center mb-6">
                    @if($siteSettings['logo'] ?? false)
                        <img src="{{ asset('storage/' . $siteSettings['logo']) }}" alt="{{ $siteSettings['site_name'] ?? config('app.name') }}" class="h-10 w-auto mr-3">
                    @else
                        <img src="{{ asset('images/logo-white.png') }}" alt="{{ $siteSettings['site_name'] ?? config('app.name') }}" class="h-10 w-auto mr-3">
                    @endif
                    <h3 class="text-xl font-bold">{{ $siteSettings['site_name'] ?? config('app.name') }}</h3>
                </div>
                <p class="text-gray-300 mb-6 leading-relaxed">
                    {{ $siteSettings['site_description'] ?? 'Your trusted source for the latest news, insights, and stories that matter. Stay informed with our comprehensive coverage of current events and trending topics.' }}
                </p>
                
                {{-- Social Media Links --}}
                <div class="flex space-x-4">
                    @if($siteSettings['social_facebook'] ?? false)
                    <a href="{{ $siteSettings['social_facebook'] }}" class="text-gray-400 hover:text-white transition-colors" aria-label="Facebook" target="_blank">
                        <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/>
                        </svg>
                    </a>
                    @endif
                    @if($siteSettings['social_twitter'] ?? false)
                    <a href="{{ $siteSettings['social_twitter'] }}" class="text-gray-400 hover:text-white transition-colors" aria-label="Twitter" target="_blank">
                        <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M23.953 4.57a10 10 0 01-2.825.775 4.958 4.958 0 002.163-2.723c-.951.555-2.005.959-3.127 1.184a4.92 4.92 0 00-8.384 4.482C7.69 8.095 4.067 6.13 1.64 3.162a4.822 4.822 0 00-.666 2.475c0 1.71.87 3.213 2.188 4.096a4.904 4.904 0 01-2.228-.616v.06a4.923 4.923 0 003.946 4.827 4.996 4.996 0 01-2.212.085 4.936 4.936 0 004.604 3.417 9.867 9.867 0 01-6.102 2.105c-.39 0-.779-.023-1.17-.067a13.995 13.995 0 007.557 2.209c9.053 0 13.998-7.496 13.998-13.985 0-.21 0-.42-.015-.63A9.935 9.935 0 0024 4.59z"/>
                        </svg>
                    </a>
                    @endif
                    @if($siteSettings['social_instagram'] ?? false)
                    <a href="{{ $siteSettings['social_instagram'] }}" class="text-gray-400 hover:text-white transition-colors" aria-label="Instagram" target="_blank">
                        <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M12.017 0C5.396 0 .029 5.367.029 11.987c0 6.62 5.367 11.987 11.988 11.987 6.62 0 11.987-5.367 11.987-11.987C24.014 5.367 18.637.001 12.017.001zM8.449 16.988c-1.297 0-2.448-.49-3.323-1.297C4.198 14.895 3.708 13.744 3.708 12.447s.49-2.448 1.297-3.323C5.902 8.198 7.053 7.708 8.35 7.708s2.448.49 3.323 1.297c.897.875 1.387 2.026 1.387 3.323s-.49 2.448-1.297 3.323c-.875.897-2.026 1.387-3.323 1.387zm7.718 0c-1.297 0-2.448-.49-3.323-1.297-.897-.875-1.387-2.026-1.387-3.323s.49-2.448 1.297-3.323c.875-.897 2.026-1.387 3.323-1.387s2.448.49 3.323 1.297c.897.875 1.387 2.026 1.387 3.323s-.49 2.448-1.297 3.323c-.875.897-2.026 1.387-3.323 1.387z"/>
                        </svg>
                    </a>
                    @endif
                    @if($siteSettings['social_linkedin'] ?? false)
                    <a href="{{ $siteSettings['social_linkedin'] }}" class="text-gray-400 hover:text-white transition-colors" aria-label="LinkedIn" target="_blank">
                        <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M20.447 20.452h-3.554v-5.569c0-1.328-.027-3.037-1.852-3.037-1.853 0-2.136 1.445-2.136 2.939v5.667H9.351V9h3.414v1.561h.046c.477-.9 1.637-1.85 3.37-1.85 3.601 0 4.267 2.37 4.267 5.455v6.286zM5.337 7.433c-1.144 0-2.063-.926-2.063-2.065 0-1.138.92-2.063 2.063-2.063 1.14 0 2.064.925 2.064 2.063 0 1.139-.925 2.065-2.064 2.065zm1.782 13.019H3.555V9h3.564v11.452zM22.225 0H1.771C.792 0 0 .774 0 1.729v20.542C0 23.227.792 24 1.771 24h20.451C23.2 24 24 23.227 24 22.271V1.729C24 .774 23.2 0 22.222 0h.003z"/>
                        </svg>
                    </a>
                    @endif
                    @if($siteSettings['social_youtube'] ?? false)
                    <a href="{{ $siteSettings['social_youtube'] }}" class="text-gray-400 hover:text-white transition-colors" aria-label="YouTube" target="_blank">
                        <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M23.498 6.186a3.016 3.016 0 0 0-2.122-2.136C19.505 3.545 12 3.545 12 3.545s-7.505 0-9.377.505A3.017 3.017 0 0 0 .502 6.186C0 8.07 0 12 0 12s0 3.93.502 5.814a3.016 3.016 0 0 0 2.122 2.136c1.871.505 9.376.505 9.376.505s7.505 0 9.377-.505a3.015 3.015 0 0 0 2.122-2.136C24 15.93 24 12 24 12s0-3.93-.502-5.814zM9.545 15.568V8.432L15.818 12l-6.273 3.568z"/>
                        </svg>
                    </a>
                    @endif
                </div>
            </div>
            
            {{-- Quick Links --}}
            <div>
                <h4 class="text-lg font-semibold mb-6">Quick Links</h4>
                <ul class="space-y-3">
                    <li>
                        <a href="{{ route('frontend.index') }}" class="text-gray-300 hover:text-white transition-colors flex items-center">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                            </svg>
                            Home
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('frontend.posts') }}" class="text-gray-300 hover:text-white transition-colors flex items-center">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                            </svg>
                            All Articles
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('frontend.posts', ['type' => 'featured']) }}" class="text-gray-300 hover:text-white transition-colors flex items-center">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                            </svg>
                            Featured Articles
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('frontend.gallery') }}" class="text-gray-300 hover:text-white transition-colors flex items-center">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                            </svg>
                            Photo Gallery
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('frontend.about') }}" class="text-gray-300 hover:text-white transition-colors flex items-center">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                            </svg>
                            About Us
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('frontend.contact') }}" class="text-gray-300 hover:text-white transition-colors flex items-center">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                            </svg>
                            Contact Us
                        </a>
                    </li>
                </ul>
            </div>
            
            {{-- Categories --}}
            <div>
                <h4 class="text-lg font-semibold mb-6">Categories</h4>
                <ul class="space-y-3">
                    @php
                        $footerCategories = \App\Models\Category::active()->ordered()->limit(6)->get();
                    @endphp
                    @foreach($footerCategories as $category)
                        <li>
                            <a href="{{ route('frontend.category', $category->slug) }}" class="text-gray-300 hover:text-white transition-colors flex items-center">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                                </svg>
                                {{ $category->name }}
                                <span class="text-xs text-gray-500 ml-2">({{ $category->posts_count ?? 0 }})</span>
                            </a>
                        </li>
                    @endforeach
                </ul>
            </div>
            
            {{-- Contact Info & Newsletter --}}
            <div>
                <h4 class="text-lg font-semibold mb-6">Stay Connected</h4>
                
                {{-- Contact Info --}}
                <div class="space-y-3 mb-6">
                    @if($siteSettings['contact_email'] ?? false)
                    <div class="flex items-center text-gray-300">
                        <svg class="w-5 h-5 mr-3 text-blue-400" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M2.003 5.884L10 9.882l7.997-3.998A2 2 0 0016 4H4a2 2 0 00-1.997 1.884z"></path>
                            <path d="M18 8.118l-8 4-8-4V14a2 2 0 002 2h12a2 2 0 002-2V8.118z"></path>
                        </svg>
                        <span class="text-sm">{{ $siteSettings['contact_email'] }}</span>
                    </div>
                    @endif
                    @if($siteSettings['contact_address'] ?? false)
                    <div class="flex items-center text-gray-300">
                        <svg class="w-5 h-5 mr-3 text-blue-400" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M5.05 4.05a7 7 0 119.9 9.9L10 18.9l-4.95-4.95a7 7 0 010-9.9zM10 11a2 2 0 100-4 2 2 0 000 4z" clip-rule="evenodd"></path>
                        </svg>
                        <span class="text-sm">{{ $siteSettings['contact_address'] }}</span>
                    </div>
                    @endif
                    @if($siteSettings['contact_phone'] ?? false)
                    <div class="flex items-center text-gray-300">
                        <svg class="w-5 h-5 mr-3 text-blue-400" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M2 3a1 1 0 011-1h2.153a1 1 0 01.986.836l.74 4.435a1 1 0 01-.54 1.06l-1.548.773a11.037 11.037 0 006.105 6.105l.774-1.548a1 1 0 011.059-.54l4.435.74a1 1 0 01.836.986V17a1 1 0 01-1 1h-2C7.82 18 2 12.18 2 5V3z"></path>
                        </svg>
                        <span class="text-sm">{{ $siteSettings['contact_phone'] }}</span>
                    </div>
                    @endif
                </div>
                
                {{-- Newsletter Signup --}}
                <div>
                    <h5 class="text-sm font-semibold mb-3">Subscribe to Newsletter</h5>
                    <form action="#" method="POST" class="space-y-3">
                        @csrf
                        <div class="flex">
                            <input type="email" 
                                   name="email" 
                                   placeholder="Your email address"
                                   class="flex-1 px-3 py-2 bg-gray-800 border border-gray-700 rounded-l-md text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                            <button type="submit" 
                                    class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-r-md transition-colors">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"></path>
                                </svg>
                            </button>
                        </div>
                        <p class="text-xs text-gray-400">
                            Get the latest news and updates delivered to your inbox.
                        </p>
                    </form>
                </div>
            </div>
        </div>
    </div>
    
    {{-- Bottom Footer --}}
    <div class="border-t border-gray-800">
        <div class="container mx-auto px-4 py-6">
            <div class="flex flex-col md:flex-row justify-between items-center">
                <div class="text-gray-400 text-sm mb-4 md:mb-0">
                    <p>&copy; {{ date('Y') }} {{ $siteSettings['site_name'] ?? config('app.name') }}. All rights reserved.</p>
                </div>
                
                {{-- Footer Links --}}
                <div class="flex flex-wrap justify-center md:justify-end space-x-6 text-sm">
                    <a href="#" class="text-gray-400 hover:text-white transition-colors">Privacy Policy</a>
                    <a href="#" class="text-gray-400 hover:text-white transition-colors">Terms of Service</a>
                    <a href="#" class="text-gray-400 hover:text-white transition-colors">Cookie Policy</a>
                    <a href="{{ route('frontend.contact') }}" class="text-gray-400 hover:text-white transition-colors">Support</a>
                </div>
            </div>
            
            {{-- Additional SEO Info --}}
            <div class="mt-4 pt-4 border-t border-gray-800 text-center">
                <p class="text-xs text-gray-500">
                    Powered by Laravel {{ app()->version() }} | 
                    <a href="https://laravel.com" class="hover:text-gray-400 transition-colors" target="_blank" rel="noopener">Built with Laravel</a>
                </p>
            </div>
        </div>
    </div>
</footer>

{{-- Back to Top Button --}}
<button id="back-to-top" 
        class="fixed bottom-8 right-8 bg-blue-600 hover:bg-blue-700 text-white p-3 rounded-full shadow-lg transition-all duration-300 opacity-0 invisible z-40"
        onclick="scrollToTop()">
    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 10l7-7m0 0l7 7m-7-7v18"></path>
    </svg>
</button>

@push('scripts')
<script>
// Back to top functionality
window.addEventListener('scroll', function() {
    const backToTopButton = document.getElementById('back-to-top');
    if (window.pageYOffset > 300) {
        backToTopButton.classList.remove('opacity-0', 'invisible');
        backToTopButton.classList.add('opacity-100', 'visible');
    } else {
        backToTopButton.classList.add('opacity-0', 'invisible');
        backToTopButton.classList.remove('opacity-100', 'visible');
    }
});

function scrollToTop() {
    window.scrollTo({
        top: 0,
        behavior: 'smooth'
    });
}
</script>
@endpush