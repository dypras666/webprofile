@props(['testimonials'])

@if(isset($testimonials) && $testimonials->count() > 0)
    <div class="w-full py-16 bg-gray-100">
        <div class="w-full px-0">

            <!-- Swiper -->
            <div class="swiper testimonialSwiper pb-12">
                <div class="swiper-wrapper">
                    @foreach($testimonials as $testimonial)
                        <div class="swiper-slide cursor-grab active:cursor-grabbing">
                            <div class="flex flex-col items-center text-center max-w-4xl mx-auto px-4">
                                {{-- Quote Icon --}}
                                <div class="text-4xl text-gray-300 mb-6">
                                    <i class="fas fa-quote-left"></i>
                                </div>

                                {{-- Quote Text --}}
                                <p class="text-lg md:text-xl lg:text-2xl text-gray-600 font-serif italic mb-8 leading-relaxed">
                                    "{{ strip_tags($testimonial->content) }}"
                                </p>

                                {{-- Author --}}
                                <div class="flex flex-col items-center">
                                    {{-- Avatar --}}
                                    <div class="w-16 h-16 rounded-full overflow-hidden shadow-md mb-3 border-2 border-white">
                                        <img src="{{ $testimonial->featured_image_url ? $testimonial->featured_image_url : 'https://ui-avatars.com/api/?name=' . urlencode($testimonial->title) . '&background=random' }}"
                                            alt="{{ $testimonial->title }}" class="w-full h-full object-cover">
                                    </div>

                                    {{-- Name & Role --}}
                                    <h4 class="font-bold text-gray-900 text-base uppercase tracking-wider">
                                        {{ $testimonial->title }}
                                    </h4>
                                    @php
                                        $rating = (int) $testimonial->excerpt;
                                        if ($rating < 1)
                                            $rating = 5; // Default to 5 if invalid
                                        if ($rating > 5)
                                            $rating = 5;
                                    @endphp
                                    <div class="flex items-center justify-center gap-1 mt-2 mb-1">
                                        @for($i = 1; $i <= 5; $i++)
                                            <i class="{{ $i <= $rating ? 'fas' : 'far' }} fa-star text-yellow-400 text-sm"></i>
                                        @endfor
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

                {{-- Pagination --}}
                <div class="swiper-pagination !bottom-0"></div>
            </div>

            <!-- Initialize Swiper -->
            <script>
                document.addEventListener('DOMContentLoaded', function () {
                    var testimonialSwiper = new Swiper(".testimonialSwiper", {
                        loop: true,
                        autoplay: {
                            delay: 5000,
                            disableOnInteraction: false,
                        },
                        pagination: {
                            el: ".swiper-pagination",
                            clickable: true,
                            dynamicBullets: true,
                        },
                        centeredSlides: true,
                        slidesPerView: 1,
                        spaceBetween: 30,
                    });
                });
            </script>

        </div>
    </div>
@endif