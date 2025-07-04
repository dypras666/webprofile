{{-- Slider Component for is_slider posts --}}
@if($sliderPosts && $sliderPosts->count() > 0)
<section class="relative bg-gray-900 overflow-hidden" x-data="slider()">
    <div class="relative h-96 md:h-[500px] lg:h-[600px]">
        @foreach($sliderPosts as $index => $post)
            <div class="absolute inset-0 transition-opacity duration-1000 ease-in-out"
                 x-show="currentSlide === {{ $index }}"
                 x-transition:enter="transition-opacity duration-1000"
                 x-transition:enter-start="opacity-0"
                 x-transition:enter-end="opacity-100"
                 x-transition:leave="transition-opacity duration-1000"
                 x-transition:leave-start="opacity-100"
                 x-transition:leave-end="opacity-0">
                
                {{-- Background Image --}}
                @if($post->featured_image)
                    <div class="absolute inset-0">
                        <img src="{{ $post->featured_image }}" 
                             alt="{{ $post->title }}"
                             class="w-full h-full object-cover">
                        <div class="absolute inset-0 bg-black bg-opacity-50"></div>
                    </div>
                @else
                    <div class="absolute inset-0 bg-gradient-to-r from-blue-600 to-purple-700"></div>
                @endif
                
                {{-- Content --}}
                <div class="relative z-10 h-full flex items-center">
                    <div class="container mx-auto px-4">
                        <div class="max-w-4xl">
                            {{-- Category Badge --}}
                            @if($post->category)
                                <span class="inline-block bg-blue-600 text-white px-3 py-1 rounded-full text-sm font-medium mb-4">
                                    {{ $post->category->name }}
                                </span>
                            @endif
                            
                            {{-- Title --}}
                            <h2 class="text-3xl md:text-4xl lg:text-5xl font-bold text-white mb-4 leading-tight">
                                <a href="{{ route('frontend.post', $post->slug) }}" 
                                   class="hover:text-blue-300 transition-colors">
                                    {{ $post->title }}
                                </a>
                            </h2>
                            
                            {{-- Excerpt --}}
                            @if($post->excerpt)
                                <p class="text-lg md:text-xl text-gray-200 mb-6 leading-relaxed max-w-2xl">
                                    {{ Str::limit($post->excerpt, 150) }}
                                </p>
                            @endif
                            
                            {{-- Meta Info --}}
                            <div class="flex items-center text-gray-300 text-sm mb-6 space-x-4">
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
                                    {{ $post->created_at->format('M d, Y') }}
                                </span>
                                @if($post->views > 0)
                                    <span class="flex items-center">
                                        <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                            <path d="M10 12a2 2 0 100-4 2 2 0 000 4z"></path>
                                            <path fill-rule="evenodd" d="M.458 10C1.732 5.943 5.522 3 10 3s8.268 2.943 9.542 7c-1.274 4.057-5.064 7-9.542 7S1.732 14.057.458 10zM14 10a4 4 0 11-8 0 4 4 0 018 0z" clip-rule="evenodd"></path>
                                        </svg>
                                        {{ number_format($post->views) }} views
                                    </span>
                                @endif
                            </div>
                            
                            {{-- Read More Button --}}
                            <a href="{{ route('frontend.post', $post->slug) }}" 
                               class="inline-flex items-center bg-blue-600 hover:bg-blue-700 text-white font-medium py-3 px-6 rounded-lg transition-colors duration-200">
                                Read More
                                <svg class="ml-2 w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                                </svg>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        @endforeach
    </div>
    
    {{-- Navigation Arrows --}}
    @if($sliderPosts->count() > 1)
        <button @click="previousSlide()" 
                class="absolute left-4 top-1/2 transform -translate-y-1/2 bg-black bg-opacity-50 hover:bg-opacity-75 text-white p-3 rounded-full transition-all duration-200 z-20">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
            </svg>
        </button>
        
        <button @click="nextSlide()" 
                class="absolute right-4 top-1/2 transform -translate-y-1/2 bg-black bg-opacity-50 hover:bg-opacity-75 text-white p-3 rounded-full transition-all duration-200 z-20">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
            </svg>
        </button>
    @endif
    
    {{-- Dots Indicator --}}
    @if($sliderPosts->count() > 1)
        <div class="absolute bottom-6 left-1/2 transform -translate-x-1/2 flex space-x-2 z-20">
            @foreach($sliderPosts as $index => $post)
                <button @click="currentSlide = {{ $index }}" 
                        class="w-3 h-3 rounded-full transition-all duration-200"
                        :class="currentSlide === {{ $index }} ? 'bg-white' : 'bg-white bg-opacity-50 hover:bg-opacity-75'">
                </button>
            @endforeach
        </div>
    @endif
    
    {{-- Auto-play Progress Bar --}}
    @if($sliderPosts->count() > 1)
        <div class="absolute bottom-0 left-0 h-1 bg-blue-600 transition-all duration-1000 ease-linear z-20"
             :style="`width: ${((currentSlide + 1) / {{ $sliderPosts->count() }}) * 100}%`">
        </div>
    @endif
</section>

@push('scripts')
<script>
function slider() {
    return {
        currentSlide: 0,
        totalSlides: {{ $sliderPosts->count() }},
        autoplayInterval: null,
        
        init() {
            if (this.totalSlides > 1) {
                this.startAutoplay();
            }
        },
        
        nextSlide() {
            this.currentSlide = (this.currentSlide + 1) % this.totalSlides;
            this.resetAutoplay();
        },
        
        previousSlide() {
            this.currentSlide = this.currentSlide === 0 ? this.totalSlides - 1 : this.currentSlide - 1;
            this.resetAutoplay();
        },
        
        startAutoplay() {
            this.autoplayInterval = setInterval(() => {
                this.nextSlide();
            }, 5000); // Change slide every 5 seconds
        },
        
        resetAutoplay() {
            if (this.autoplayInterval) {
                clearInterval(this.autoplayInterval);
                this.startAutoplay();
            }
        },
        
        destroy() {
            if (this.autoplayInterval) {
                clearInterval(this.autoplayInterval);
            }
        }
    }
}
</script>
@endpush
@endif