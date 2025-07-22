{{-- Hero Component for is_featured posts --}}
@if($featuredPosts && $featuredPosts->count() > 0)
<section class="bg-white py-12">
    <div class="container mx-auto px-4">
        {{-- Section Header --}}
        <div class="text-center mb-12">
            <h2 class="text-3xl md:text-4xl font-bold text-gray-900 mb-4">
                Featured Articles
            </h2>
            <p class="text-lg text-gray-600 max-w-2xl mx-auto">
                Discover our most popular and trending articles handpicked by our editorial team
            </p>
            <div class="w-24 h-1 bg-blue-600 mx-auto mt-4"></div>
        </div>
        
        {{-- Featured Posts Grid --}}
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
            @foreach($featuredPosts->take(6) as $index => $post)
                <article class="group relative bg-white rounded-xl shadow-lg hover:shadow-xl transition-all duration-300 overflow-hidden {{ $index === 0 ? 'md:col-span-2 md:row-span-2' : '' }}">
                    {{-- Featured Image --}}
                    <div class="relative {{ $index === 0 ? 'h-64 md:h-80' : 'h-48' }} overflow-hidden">
                        @if($post->featured_image)
                            <img src="{{ $post->featured_image }}" 
                                 alt="{{ $post->title }}"
                                 class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300">
                        @else
                            <div class="w-full h-full bg-gradient-to-br from-blue-500 to-purple-600 flex items-center justify-center">
                                <svg class="w-16 h-16 text-white opacity-50" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M4 3a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V5a2 2 0 00-2-2H4zm12 12H4l4-8 3 6 2-4 3 6z" clip-rule="evenodd"></path>
                                </svg>
                            </div>
                        @endif
                        
                        {{-- Featured Badge --}}
                        <div class="absolute top-4 left-4">
                            <span class="bg-red-500 text-white px-3 py-1 rounded-full text-xs font-bold uppercase tracking-wide">
                                Featured
                            </span>
                        </div>
                        
                        {{-- Category Badge --}}
                        @if($post->category)
                            <div class="absolute top-4 right-4">
                                <span class="bg-blue-600 text-white px-3 py-1 rounded-full text-xs font-medium">
                                    {{ $post->category->name }}
                                </span>
                            </div>
                        @endif
                        
                        {{-- Overlay --}}
                        <div class="absolute inset-0 bg-black bg-opacity-0 group-hover:bg-opacity-20 transition-all duration-300"></div>
                    </div>
                    
                    {{-- Content --}}
                    <div class="p-6 {{ $index === 0 ? 'md:p-8' : '' }}">
                        {{-- Title --}}
                        <h3 class="{{ $index === 0 ? 'text-xl md:text-2xl' : 'text-lg' }} font-bold text-gray-900 mb-3 line-clamp-2 group-hover:text-blue-600 transition-colors">
                            <a href="{{ route('frontend.post', $post->slug) }}" class="stretched-link">
                                {{ $post->title }}
                            </a>
                        </h3>
                        
                        {{-- Excerpt --}}
                        @if($post->excerpt)
                            <p class="text-gray-600 mb-4 line-clamp-3 {{ $index === 0 ? 'text-base' : 'text-sm' }}">
                                {{ Str::limit($post->excerpt, $index === 0 ? 200 : 120) }}
                            </p>
                        @endif
                        
                        {{-- Meta Information --}}
                        <div class="flex items-center justify-between text-sm text-gray-500">
                            <div class="flex items-center space-x-4">
                                <span class="flex items-center">
                                    <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd"></path>
                                    </svg>
                                    {{ $post->user->name ?? 'Admin' }}
                                </span>
                                <span class="flex items-center">
                                    <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M6 2a1 1 0 00-1 1v1H4a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V6a2 2 0 00-2-2h-1V3a1 1 0 10-2 0v1H7V3a1 1 0 00-1-1zm0 5a1 1 0 000 2h8a1 1 0 100-2H6z" clip-rule="evenodd"></path>
                                    </svg>
                                    {{ ($post->published_at ?? $post->created_at)->format('M d') }}
                                </span>
                            </div>
                            
                            @if($post->views > 0)
                                <span class="flex items-center">
                                    <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                        <path d="M10 12a2 2 0 100-4 2 2 0 000 4z"></path>
                                        <path fill-rule="evenodd" d="M.458 10C1.732 5.943 5.522 3 10 3s8.268 2.943 9.542 7c-1.274 4.057-5.064 7-9.542 7S1.732 14.057.458 10zM14 10a4 4 0 11-8 0 4 4 0 018 0z" clip-rule="evenodd"></path>
                                    </svg>
                                    {{ number_format($post->views) }}
                                </span>
                            @endif
                        </div>
                        
                        {{-- Read More Link for Main Featured Post --}}
                        @if($index === 0)
                            <div class="mt-6">
                                <a href="{{ route('frontend.post', $post->slug) }}" 
                                   class="inline-flex items-center text-blue-600 hover:text-blue-700 font-medium transition-colors">
                                    Read Full Article
                                    <svg class="ml-2 w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                                    </svg>
                                </a>
                            </div>
                        @endif
                    </div>
                    
                    {{-- Reading Time Estimate --}}
                    <div class="absolute bottom-4 right-4 bg-white bg-opacity-90 backdrop-blur-sm rounded-full px-3 py-1">
                        <span class="text-xs text-gray-600 font-medium">
                            {{ ceil(str_word_count(strip_tags($post->content)) / 200) }} min read
                        </span>
                    </div>
                </article>
            @endforeach
        </div>
        
        {{-- View All Featured Posts Button --}}
        @if($featuredPosts->count() > 6)
            <div class="text-center mt-12">
                <a href="{{ route('frontend.posts', ['type' => 'featured']) }}" 
                   class="inline-flex items-center bg-blue-600 hover:bg-blue-700 text-white font-medium py-3 px-8 rounded-lg transition-colors duration-200">
                    View All Featured Articles
                    <svg class="ml-2 w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                    </svg>
                </a>
            </div>
        @endif
    </div>
</section>

@push('styles')
<style>
.line-clamp-2 {
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
}

.line-clamp-3 {
    display: -webkit-box;
    -webkit-line-clamp: 3;
    -webkit-box-orient: vertical;
    overflow: hidden;
}

.stretched-link::after {
    position: absolute;
    top: 0;
    right: 0;
    bottom: 0;
    left: 0;
    z-index: 1;
    content: "";
}
</style>
@endpush
@endif