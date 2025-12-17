@extends('template.university.layouts.app')

@section('content')

    {{-- Full Width Hero Section --}}
    @php
        $heroBg = \App\Models\SiteSetting::getValue('theme_university_hero_sidebar_background');
        if (!$heroBg) {
            $heroBg = \App\Helpers\TemplateHelper::getThemeConfig('hero.sidebar_background');
        }
        // Handle path vs URL
        if ($heroBg && !str_starts_with($heroBg, 'http')) {
            $heroBg = \Illuminate\Support\Facades\Storage::url($heroBg);
        }
    @endphp


    {{-- Full Width Hero Section Component --}}
    @include('template.university.components.hero_section', [
        'heroBg' => $heroBg,
        'featuredPosts' => $featuredPosts,
        'sliderPosts' => $sliderPosts
    ])

    {{-- Program Studi Slider Section --}}
    @include('template.university.components.program_studi_section', ['programStudis' => $programStudis ?? collect([])])



    {{-- Facilities Section (Dark Mode with Map Pattern) --}}
    @if(isset($facilities) && $facilities->count() > 0)
        <div class="mt-0 mb-16 relative bg-[#111] text-white py-20 overflow-hidden w-full shadow-inner">
            {{-- Background Pattern (Dot Map Simulation) --}}
            <div class="absolute inset-0 opacity-20 pointer-events-none">
                <svg width="100%" height="100%" xmlns="http://www.w3.org/2000/svg">
                    <defs>
                        <pattern id="dotPattern" x="0" y="0" width="20" height="20" patternUnits="userSpaceOnUse">
                            <circle cx="2" cy="2" r="1.5" fill="#555" />
                        </pattern>
                    </defs>
                    <rect width="100%" height="100%" fill="url(#dotPattern)" />
                </svg>
                {{-- Vignette --}}
                <div class="absolute inset-0 bg-gradient-to-t from-[#111] via-transparent to-[#111]"></div>
            </div>

            <div class="container mx-auto px-4 md:px-6 relative z-10">
                <div class="text-center mb-16">
                    <h3 class="text-3xl md:text-4xl font-heading font-medium tracking-wide text-white uppercase mb-2">
                        Fasilitas <span class="font-light text-gray-400">Kami</span>
                    </h3>
                    <div class="h-1 w-20 bg-cyan-600 mx-auto"></div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                    @foreach($facilities as $facility)
                        <div class="group cursor-pointer">
                            {{-- Image Card --}}
                            <div class="relative h-64 overflow-hidden rounded-md mb-6 shadow-lg border border-gray-800">
                                <img src="{{ !empty($facility->featured_image_url) ? $facility->featured_image_url : asset('images/default-post.jpg') }}"
                                    alt="{{ $facility->title }}"
                                    class="w-full h-full object-cover transition-transform duration-700 group-hover:scale-110 opacity-90 group-hover:opacity-100">

                                {{-- Hover Overlay --}}
                                <div class="absolute inset-0 bg-black/20 group-hover:bg-transparent transition-colors duration-300">
                                </div>
                            </div>

                            {{-- Content --}}
                            <div class="text-left px-2">
                                <h4 class="text-xl font-bold text-white mb-2 group-hover:text-cyan-500 transition-colors">
                                    {{ $facility->title }}
                                </h4>
                                <p class="text-gray-500 text-sm leading-relaxed mb-4 line-clamp-2">
                                    {{ $facility->excerpt ?? Str::limit(strip_tags($facility->content), 100) }}
                                </p>
                            </div>
                        </div>
                    @endforeach
                </div>

                {{-- Action Buttons --}}
                <div class="text-center mt-12 flex flex-col md:flex-row justify-center items-center gap-4">
                    <a href="{{ route('frontend.facilities') }}"
                        class="min-w-[200px] border border-white text-white px-8 py-3 text-sm font-bold uppercase tracking-wider hover:bg-white hover:text-black transition-all">
                        Lihat Semua Fasilitas <i class="fas fa-chevron-right ml-2 text-xs"></i>
                    </a>
                    <span class="text-gray-500 text-xs font-bold uppercase">OR</span>
                    <button @click="$dispatch('open-tour')"
                        class="min-w-[200px] bg-cyan-600 border border-cyan-600 text-white px-8 py-3 text-sm font-bold uppercase tracking-wider hover:bg-cyan-700 hover:border-cyan-700 transition-all shadow-lg shadow-cyan-900/50">
                        Campus Tour <i class="fas fa-play ml-2 text-xs"></i>
                    </button>
                </div>

            </div>
        </div>
    @endif

    @push('modals')
        {{-- Hidden 3D Swiper Modal --}}
        <div x-data="{ 
                                                                                                                                                                                    tourOpen: false, 
                                                                                                                                                                                    facilities: [], 
                                                                                                                                                                                    loaded: false,
                                                                                                                                                                                    async fetchFacilities() {
                                                                                                                                                                                        if (this.loaded) return;
                                                                                                                                                                                        try {
                                                                                                                                                                                            const response = await fetch('{{ route('frontend.ajax.facilities') }}');
                                                                                                                                                                                            const data = await response.json();
                                                                                                                                                                                            this.facilities = data;
                                                                                                                                                                                            this.loaded = true;

                                                                                                                                                                                            // Force Swiper update after DOM change
                                                                                                                                                                                            this.$nextTick(() => {
                                                                                                                                                                                                if (document.querySelector('.mySwiper').swiper) {
                                                                                                                                                                                                    document.querySelector('.mySwiper').swiper.update();
                                                                                                                                                                                                    document.querySelector('.mySwiper').swiper.slideTo(0);
                                                                                                                                                                                                }
                                                                                                                                                                                            });
                                                                                                                                                                                        } catch (error) {
                                                                                                                                                                                            console.error('Error loading facilities:', error);
                                                                                                                                                                                        }
                                                                                                                                                                                    } 
                                                                                                                                                                                }"
            @keydown.escape.window="tourOpen = false" @open-tour.window="tourOpen = true; fetchFacilities()">

            {{-- Full Screen Modal --}}
            <div x-show="tourOpen" x-cloak
                class="fixed inset-0 z-[9999] flex items-center justify-center bg-black/95 backdrop-blur-md"
                x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0"
                x-transition:enter-end="opacity-100" x-transition:leave="transition ease-in duration-200"
                x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0">

                {{-- Close Button --}}
                <button @click="tourOpen = false"
                    class="absolute top-6 right-6 text-white hover:text-cyan-400 text-3xl z-[110] transition-colors">
                    <i class="fas fa-times"></i>
                </button>

                {{-- Swiper Container --}}
                <div class="w-full h-full max-w-[90vw] md:max-w-[1200px] py-20">
                    <!-- Swiper -->
                    <div class="swiper mySwiper w-full h-full pt-12 pb-12">
                        <div class="swiper-wrapper">
                            {{-- Dynamic Slides --}}
                            <template x-if="loaded">
                                <template x-for="facility in facilities" :key="facility.id">
                                    <div
                                        class="swiper-slide bg-center bg-cover w-[300px] h-[300px] md:w-[400px] md:h-[500px] rounded-xl overflow-hidden shadow-2xl relative border-2 border-gray-800">
                                        <img :src="facility.featured_image_url" class="block w-full h-full object-cover" />
                                        <div
                                            class="absolute bottom-0 left-0 right-0 bg-gradient-to-t from-black via-black/70 to-transparent p-6 text-white">
                                            <h3 class="text-xl font-bold font-heading mb-1" x-text="facility.title">
                                            </h3>
                                            <p class="text-xs text-gray-300 line-clamp-2" x-text="facility.excerpt"></p>
                                        </div>
                                    </div>
                                </template>
                            </template>
                            {{-- Loading State --}}
                            <template x-if="!loaded">
                                <div class="swiper-slide w-[300px] h-[300px] flex items-center justify-center text-white">
                                    Loading...
                                </div>
                            </template>
                        </div>
                        <div class="swiper-pagination"></div>
                    </div>
                </div>
            </div>
        </div>
    @endpush

    @push('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', () => {
                var swiper = new Swiper(".mySwiper", {
                    observer: true,
                    observeParents: true,
                    effect: "coverflow",
                    grabCursor: true,
                    centeredSlides: true,
                    slidesPerView: "auto",
                    coverflowEffect: {
                        rotate: 50,
                        stretch: 0,
                        depth: 100,
                        modifier: 1,
                        slideShadows: true,
                    },
                    autoplay: {
                        delay: 2500,
                        disableOnInteraction: false,
                    },
                    pagination: {
                        el: ".swiper-pagination",
                        clickable: true,
                    },
                    loop: true
                });
            });
        </script>
    @endpush

    <div class="container mx-auto px-4 md:px-6 py-8">

        {{-- More News Section --}}
        <div class="mt-16">
            <div class="flex items-center justify-center border-b-2 border-gray-100 mb-8 pb-4 relative">
                <div class="flex flex-col items-center gap-2">
                    <h3 class="text-2xl font-bold font-heading text-secondary text-center">
                        Berita <span class="text-primary">Terbaru</span>
                    </h3>
                    <div class="w-12 h-1 bg-primary rounded-full"></div>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                @foreach($latestPosts as $post)
                    <div
                        class="group bg-white rounded-2xl shadow-lg border border-gray-100 overflow-hidden hover:shadow-xl transition-shadow duration-300 flex flex-col h-full">
                        {{-- Image --}}
                        <div class="relative h-56 overflow-hidden">
                            <img src="{{ !empty($post->featured_image_url) ? $post->featured_image_url : asset('images/default-post.jpg') }}"
                                class="w-full h-full object-cover transition-transform duration-500 group-hover:scale-105">
                            <div class="absolute inset-0 bg-gradient-to-t from-black/50 to-transparent opacity-60">
                            </div>
                        </div>

                        {{-- Content --}}
                        <div class="p-6 flex-grow flex flex-col">
                            <h4
                                class="text-lg font-bold text-gray-800 mb-2 leading-tight group-hover:text-[#1e3a8a] transition-colors font-heading">
                                <a href="{{ route('frontend.post', $post->slug) }}">
                                    {{ $post->title }}
                                </a>
                            </h4>

                            {{-- Date --}}
                            <div class="text-xs text-gray-500 mb-3 font-medium uppercase tracking-wide">
                                {{ $post->published_at ? $post->published_at->format('d F Y') : $post->created_at->format('d F Y') }}
                            </div>

                            <p class="text-gray-600 text-sm line-clamp-3 leading-relaxed mb-4 flex-grow">
                                {{ $post->excerpt ?? Str::limit(strip_tags($post->content), 100) }}
                            </p>

                            <div class="mt-auto pt-4 border-t border-gray-100">
                                <span class="text-[#1e3a8a] text-xs font-bold uppercase tracking-wider group-hover:underline">
                                    {{ $post->category->name ?? 'News' }}
                                </span>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            {{-- View All Button (Bottom) --}}
            <div class="mt-10 text-center">
                <a href="{{ route('frontend.posts') }}"
                    class="inline-block px-8 py-3 bg-primary text-white font-bold rounded-full shadow-lg hover:bg-blue-800 transition-colors transform hover:-translate-y-1">
                    Lihat Semua Berita <i class="fas fa-arrow-right ml-2"></i>
                </a>
            </div>
        </div>

        {{-- Events Section --}}
        @if(isset($events) && $events->count() > 0)
            <div class="mt-16">
                <div class="grid grid-cols-2 md:grid-cols-2 lg:grid-cols-4 gap-4 md:gap-0">
                    {{-- Static Info Box --}}
                    <div
                        class="bg-cyan-500 p-8 md:p-10 flex flex-col justify-center items-start text-white h-full min-h-[300px] relative overflow-hidden group">
                        <div class="relative z-10">
                            <h3 class="text-3xl md:text-4xl font-bold font-heading mb-4 leading-tight">Next<br>Events
                            </h3>
                            <p class="text-cyan-100 mb-8 text-sm leading-relaxed max-w-[200px]">
                                Ikuti berbagai kegiatan seru dan informatif yang akan datang.
                            </p>
                            <a href="{{ route('frontend.posts', ['type' => 'event']) }}"
                                class="inline-flex items-center px-6 py-3 border-2 border-white text-white font-bold text-sm hover:bg-white hover:text-cyan-600 transition-colors uppercase tracking-wider">
                                View All Events <i class="fas fa-arrow-right ml-2"></i>
                            </a>
                        </div>
                        {{-- Decorative Circle --}}
                        <div
                            class="absolute -bottom-10 -right-10 w-40 h-40 bg-white/10 rounded-full blur-2xl group-hover:scale-150 transition-transform duration-700">
                        </div>
                    </div>

                    {{-- Dynamic Events --}}
                    @foreach($events as $event)
                        <div
                            class="group flex flex-col bg-white border border-gray-100/50 shadow-sm hover:shadow-xl transition-all duration-300 overflow-hidden hover:-translate-y-1">
                            {{-- Image Section --}}
                            <div class="relative aspect-[4/3] overflow-hidden">
                                <img src="{{ !empty($event->featured_image_url) ? $event->featured_image_url : asset('images/default-post.jpg') }}"
                                    alt="{{ $event->title }}"
                                    class="w-full h-full object-cover transition-transform duration-700 group-hover:scale-110">

                                {{-- Date Badge --}}
                                <div
                                    class="absolute top-3 right-3 bg-white/95 backdrop-blur-sm shadow-md text-center overflow-hidden min-w-[60px]">
                                    <div class="bg-cyan-600 text-white text-[10px] uppercase py-1 font-bold tracking-wider">
                                        {{ $event->published_at ? $event->published_at->format('M') : $event->created_at->format('M') }}
                                    </div>
                                    <div class="text-xl font-bold font-heading text-gray-800 leading-none">
                                        {{ $event->published_at ? $event->published_at->format('d') : $event->created_at->format('d') }}
                                    </div>
                                </div>

                                {{-- Category Tag --}}
                                <div class="absolute bottom-3 left-3">
                                    <span
                                        class="bg-cyan-500/90 backdrop-blur-md text-white text-[10px] font-bold px-3 py-1 shadow-sm">
                                        {{ $event->category->name ?? 'Event' }}
                                    </span>
                                </div>
                            </div>

                            {{-- Content Section --}}
                            <div class="p-5 flex flex-col flex-grow relative">
                                <h4
                                    class="font-bold text-gray-800 text-lg leading-snug mb-3 group-hover:text-cyan-600 transition-colors line-clamp-2">
                                    <a href="{{ route('frontend.post', $event->slug) }}" class="before:absolute before:inset-0">
                                        {{ $event->title }}
                                    </a>
                                </h4>

                                <div
                                    class="mt-auto pt-4 border-t border-dashed border-gray-100 flex items-center justify-between text-xs text-gray-500">
                                    <div class="flex items-center gap-2">
                                        <i class="far fa-clock text-cyan-500"></i>
                                        <span>{{ $event->published_at ? $event->published_at->format('H:i') : $event->created_at->format('H:i') }} WIB</span>
                                    </div>
                                    <div class="group-hover:translate-x-1 transition-transform duration-300">
                                        <i class="fas fa-arrow-right text-cyan-500"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif

    </div> {{-- End Main Container --}}

    {{-- Testimonials Section (Full Width) --}}
    @include('template.university.components.testimonial_section', ['testimonials' => $testimonials])

    {{-- Team / Data Dosen Section (Full Width) --}}
    @include('template.university.components.team_section', ['teamMembers' => $teamMembers])

    <div class="container mx-auto px-4 md:px-6"> {{-- Re-open Main Container --}}

        {{-- Partners Section --}}
        @if(isset($partners) && $partners->count() > 0)
            <div class="mt-20 mb-8 border-t border-dashed border-gray-200 pt-10">
                <div class="text-center mb-8">
                    <h3 class="text-lg font-bold font-heading text-gray-400 uppercase tracking-widest">Mitra Kami</h3>
                </div>
                <div class="bg-white rounded-xl p-8">
                    <div
                        class="flex flex-wrap items-center justify-center gap-8 md:gap-16 grayscale opacity-70 hover:grayscale-0 hover:opacity-100 transition-all duration-500">
                        @foreach($partners as $partner)
                            <a href="{{ $partner->meta_description ?? '#' }}" target="_blank" class="block group"
                                title="{{ $partner->title }}">
                                <img src="{{ $partner->featured_image_url }}" alt="{{ $partner->title }}"
                                    class="h-10 md:h-14 w-auto object-contain transition-transform group-hover:scale-110">
                            </a>
                        @endforeach
                    </div>

                   

                </div>
            </div>
        @endif

    </div>

@endsection