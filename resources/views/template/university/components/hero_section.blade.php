@props(['heroBg', 'featuredPosts', 'sliderPosts'])

<div class="w-full pb-8 {{ $heroBg ? '' : 'bg-white' }}"
    style="{{ $heroBg ? 'background-image: url(' . $heroBg . '); background-size: cover; background-position: center;' : '' }}">

    {{-- Main Container --}}
    <div class="container mx-auto px-4 md:px-6 pt-0">

        <div class="overflow-hidden shadow-2xl rounded-sm">
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-0">

                {{-- Left Column: Featured News List --}}
                <div class="lg:col-span-4 order-2 lg:order-1 relative z-10 bg-white/90 backdrop-blur-sm">
                    <div class="flex flex-col h-full divide-y divide-gray-100">
                        @foreach($featuredPosts->take(4) as $index => $post)
                            <div
                                class="group relative p-4 lg:p-6 transition-all duration-300 hover:bg-gray-50 flex gap-4 items-start cursor-pointer h-full">
                                {{-- Thumbnail --}}
                                <div class="shrink-0 w-20 h-20 overflow-hidden rounded-sm shadow-sm relative">
                                    <img src="{{ $post->featured_image ? Storage::url($post->featured_image) : asset('images/default-post.jpg') }}"
                                        class="w-full h-full object-cover transition-transform duration-500 group-hover:scale-110">
                                </div>
                                {{-- Content --}}
                                <div class="flex-grow">
                                    <h4
                                        class="font-bold text-gray-800 text-sm md:text-base leading-tight mb-2 group-hover:text-[#1e3a8a] transition-colors font-heading">
                                        <a href="{{ route('frontend.post', $post->slug) }}"
                                            class="before:absolute before:inset-0">
                                            {{ $post->title }}
                                        </a>
                                    </h4>
                                    <p class="text-xs text-gray-400 line-clamp-2 leading-relaxed font-light">
                                        {{ Str::limit(strip_tags($post->content), 70) }}
                                    </p>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>

                {{-- Right Column: Main Slider --}}
                <div class="lg:col-span-8 order-1 lg:order-2 relative h-[450px] lg:h-auto min-h-[500px]">
                    @php
                        $welcomeEnabled = \App\Models\SiteSetting::getValue('welcome_slider_enabled') == '1';
                    @endphp

                    @if($sliderPosts->count() > 0 || $welcomeEnabled)
                        <!-- Swiper -->
                        <div class="swiper heroSwiper w-full h-full">
                            <div class="swiper-wrapper">
                                {{-- Welcome Slide --}}
                                @if($welcomeEnabled)
                                    @php
                                        $welcomeBg = \App\Models\SiteSetting::getValue('welcome_slider_background');
                                        if ($welcomeBg && !str_starts_with($welcomeBg, 'http')) {
                                            $welcomeBg = Storage::url($welcomeBg);
                                        }
                                        $welcomeTitle = \App\Models\SiteSetting::getValue('welcome_slider_title', 'Welcome');
                                        $welcomeSubtitle = \App\Models\SiteSetting::getValue('welcome_slider_subtitle', '');
                                        $btnText = \App\Models\SiteSetting::getValue('welcome_slider_button_text');
                                        $btnLink = \App\Models\SiteSetting::getValue('welcome_slider_button_link', '#');
                                    @endphp
                                    <div class="swiper-slide relative w-full h-full bg-gray-900">
                                        <img src="{{ $welcomeBg ?? asset('images/default-hero.jpg') }}"
                                            class="w-full h-full object-cover">

                                        {{-- Overlay Content --}}
                                        <div
                                            class="absolute bottom-10 right-10 left-auto max-w-xl bg-white/95 backdrop-blur-sm p-8 shadow-lg border-l-4 border-cyan-500 hidden md:block">
                                            <h2 class="text-3xl font-bold text-[#1e3a8a] mb-3 font-heading">
                                                {{ $welcomeTitle }}
                                            </h2>
                                            @if($welcomeSubtitle)
                                                <p class="text-gray-600 text-base mb-6 leading-relaxed">
                                                    {{ $welcomeSubtitle }}
                                                </p>
                                            @endif
                                            @if($btnText)
                                                <a href="{{ $btnLink }}"
                                                    class="inline-block bg-[#1e3a8a] text-white font-bold py-2 px-6 rounded hover:bg-cyan-600 transition-colors uppercase tracking-wider text-sm">
                                                    {{ $btnText }} <i class="fas fa-arrow-right ml-2"></i>
                                                </a>
                                            @endif
                                        </div>
                                    </div>
                                @endif

                                @foreach($sliderPosts as $post)
                                    <div class="swiper-slide relative w-full h-full bg-gray-100">
                                        <img src="{{ $post->featured_image_url ? $post->featured_image_url : asset('images/default-hero.jpg') }}"
                                            class="w-full h-full object-cover">

                                        {{-- Overlay Content --}}
                                        <div
                                            class="absolute bottom-10 right-10 left-auto max-w-md bg-white/95 backdrop-blur-sm p-8 shadow-lg border-l-4 border-cyan-500 hidden md:block">
                                            <h2 class="text-2xl font-bold text-[#1e3a8a] mb-2 font-heading">
                                                <a href="{{ route('frontend.post', $post->slug) }}"
                                                    class="hover:text-cyan-600 transition-colors">
                                                    {{ $post->title }}
                                                </a>
                                            </h2>
                                            <p class="text-gray-600 text-sm mb-4 line-clamp-2">
                                                {{ $post->excerpt ?? Str::limit(strip_tags($post->content), 100) }}
                                            </p>
                                            <a href="{{ route('frontend.post', $post->slug) }}"
                                                class="text-cyan-600 font-bold text-xs uppercase tracking-wider hover:text-[#1e3a8a] transition-colors">
                                                Read More <i class="fas fa-arrow-right ml-1"></i>
                                            </a>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                            <div class="swiper-button-next text-white"></div>
                            <div class="swiper-button-prev text-white"></div>
                            <div class="swiper-pagination"></div>
                        </div>

                        <!-- Initialize Swiper -->
                        <script>
                            document.addEventListener('DOMContentLoaded', function () {
                                var swiper = new Swiper(".heroSwiper", {
                                    loop: true,
                                    autoplay: {
                                        delay: 5000,
                                        disableOnInteraction: false,
                                    },
                                    navigation: {
                                        nextEl: ".swiper-button-next",
                                        prevEl: ".swiper-button-prev",
                                    },
                                    pagination: {
                                        el: ".swiper-pagination",
                                        clickable: true,
                                    },
                                    effect: 'fade',
                                    fadeEffect: {
                                        crossFade: true
                                    }
                                });
                            });
                        </script>
                    @else
                        <div class="w-full h-full bg-gray-200 flex items-center justify-center">
                            <span class="text-gray-400 font-medium">No slider content available</span>
                        </div>
                    @endif
                </div>

            </div>
        </div>

        {{-- CTA Banner (Yellow) - Attached to Hero --}}
        <div class="container mx-auto px-4 md:px-6 -mt-8 relative z-20">
            <div
                class="bg-[#ffd700] rounded-b-[3rem] rounded-t-none p-8 flex flex-col md:flex-row items-center justify-between shadow-lg">
                <h2 class="text-xl md:text-2xl font-bold text-black mb-4 md:mb-0 font-heading">
                    {{ \App\Helpers\TemplateHelper::getThemeConfig('cta_banner.text', 'Melangkah menuju kesuksesan akademis dan karier cemerlang.') }}
                </h2>
                <a href="{{ \App\Helpers\TemplateHelper::getThemeConfig('cta_banner.button_link', '#') }}"
                    target="_blank"
                    class="bg-[#1e3a8a] border-2 border-[#1e3a8a] hover:brightness-110 text-white font-bold py-3 px-8 rounded transition-all uppercase tracking-wider shadow-md">
                    {{ \App\Helpers\TemplateHelper::getThemeConfig('cta_banner.button_text', 'INFO PENDAFTARAN') }}
                </a>
            </div>
        </div>

    </div>
</div>