{{-- Photo Gallery Component --}}
@if($galleryImages && $galleryImages->count() > 0)
<section class="bg-gray-50 py-16">
    <div class="container mx-auto px-4">
        {{-- Section Header --}}
        <div class="text-center mb-12">
            <h2 class="text-3xl md:text-4xl font-bold text-gray-900 mb-4">
                Photo Gallery
            </h2>
            <p class="text-lg text-gray-600 max-w-2xl mx-auto">
                Explore our collection of stunning photographs and visual stories
            </p>
            <div class="w-24 h-1 bg-blue-600 mx-auto mt-4"></div>
        </div>
        
        {{-- Gallery Grid --}}
        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4">
            @foreach($galleryImages->take(12) as $index => $post)
                <a href="{{ route('frontend.gallery') }}" class="group relative overflow-hidden rounded-lg shadow-md hover:shadow-xl transition-all duration-300 cursor-pointer">
                    
                    {{-- Image Container with different heights for masonry effect --}}
                    <div class="relative {{ $index % 7 === 0 ? 'h-80' : ($index % 5 === 0 ? 'h-64' : 'h-48') }} overflow-hidden">
                        <img src="{{ $post->featured_image ? asset('storage/' . $post->featured_image) : asset('images/placeholder.jpg') }}" 
                             alt="{{ $post->title }}"
                             class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-500"
                             loading="lazy">
                        
                        {{-- Overlay --}}
                        <div class="absolute inset-0 bg-black bg-opacity-0 group-hover:bg-opacity-40 transition-all duration-300 flex items-center justify-center">
                            <div class="opacity-0 group-hover:opacity-100 transition-opacity duration-300">
                                <svg class="w-12 h-12 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                                </svg>
                            </div>
                        </div>
                        
                        {{-- Image Info Badge --}}
                        <div class="absolute bottom-2 left-2 bg-black bg-opacity-70 text-white px-2 py-1 rounded text-xs opacity-0 group-hover:opacity-100 transition-opacity duration-300">
                            {{ $post->title }}
                        </div>
                    </div>
                </a>
            @endforeach
        </div>
        
        {{-- View All Gallery Button --}}
        @if($galleryImages->count() > 12)
            <div class="text-center mt-12">
                <a href="{{ route('frontend.gallery') }}" 
                   class="inline-flex items-center bg-blue-600 hover:bg-blue-700 text-white font-medium py-3 px-8 rounded-lg transition-colors duration-200">
                    View Full Gallery
                    <svg class="ml-2 w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                    </svg>
                </a>
            </div>
        @endif
        

    </div>
</section>


@endif