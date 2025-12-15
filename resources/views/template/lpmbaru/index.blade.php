@extends('template.lpmbaru.layouts.app')

@section('content')

    {{-- Hero Section --}}
    <section class="relative h-[500px] md:h-[600px] overflow-hidden">
        <div class="absolute inset-0 bg-secondary/20 z-10"></div>

        <div x-data="{ activeSlide: 0, slides: {{ $sliderPosts->count() }} }" class="relative h-full w-full">
            @foreach($sliderPosts as $index => $post)
                <div x-show="activeSlide === {{ $index }}" x-transition:enter="transition transform ease-out duration-700"
                    x-transition:enter-start="opacity-0 scale-105" x-transition:enter-end="opacity-100 scale-100"
                    x-transition:leave="transition transform ease-in duration-700"
                    x-transition:leave-start="opacity-100 scale-100" x-transition:leave-end="opacity-0 scale-105"
                    class="absolute inset-0 w-full h-full bg-cover bg-center"
                    style="background-image: url('{{ $post->featured_image ? Storage::url($post->featured_image) : asset('images/default-hero.jpg') }}');">

                    <div class="absolute inset-0 bg-gradient-to-t from-black/80 via-black/40 to-transparent z-10"></div>

                    <div class="absolute bottom-0 left-0 w-full z-20 pb-16 md:pb-24 pt-20 px-4 md:px-0">
                        <div class="container mx-auto" data-aos="fade-up" data-aos-delay="200">
                            <span
                                class="inline-block px-3 py-1 bg-primary text-white text-xs font-semibold rounded-full mb-4 uppercase tracking-wider">
                                {{ $post->category->name ?? 'Berita' }}
                            </span>
                            <h1
                                class="text-3xl md:text-5xl lg:text-6xl font-serif font-bold text-white mb-4 leading-tight max-w-4xl drop-shadow-lg">
                                <a href="{{ route('frontend.post', $post->slug) }}"
                                    class="hover:text-primary transition-colors">
                                    {{ $post->title }}
                                </a>
                            </h1>
                            <div class="flex items-center text-gray-300 text-sm md:text-base gap-4">
                                <span><i class="far fa-calendar-alt mr-2"></i> {{ $post->created_at->format('d M Y') }}</span>
                                <span><i class="far fa-user mr-2"></i> {{ $post->user->name ?? 'Admin' }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach

            <!-- Slider Controls -->
            @if($sliderPosts->count() > 1)
                <div class="absolute bottom-6 right-6 md:right-12 z-30 flex gap-2">
                    @foreach($sliderPosts as $index => $post)
                        <button @click="activeSlide = {{ $index }}"
                            class="w-3 h-3 rounded-full transition-all duration-300 border border-white/50"
                            :class="activeSlide === {{ $index }} ? 'bg-primary w-8' : 'bg-white/50 hover:bg-white'">
                        </button>
                    @endforeach
                </div>

                <button @click="activeSlide = (activeSlide === 0 ? slides - 1 : activeSlide - 1)"
                    class="absolute left-4 top-1/2 -translate-y-1/2 z-30 w-10 h-10 md:w-12 md:h-12 rounded-full bg-white/10 hover:bg-primary text-white backdrop-blur-sm flex items-center justify-center transition-all group">
                    <svg class="w-6 h-6 group-hover:-translate-x-1 transition-transform" fill="none" stroke="currentColor"
                        viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                    </svg>
                </button>
                <button @click="activeSlide = (activeSlide === slides - 1 ? 0 : activeSlide + 1)"
                    class="absolute right-4 top-1/2 -translate-y-1/2 z-30 w-10 h-10 md:w-12 md:h-12 rounded-full bg-white/10 hover:bg-primary text-white backdrop-blur-sm flex items-center justify-center transition-all group">
                    <svg class="w-6 h-6 group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor"
                        viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                    </svg>
                </button>

                <!-- Auto play script for alpine -->
                <div x-init="setInterval(() => { activeSlide = activeSlide === slides - 1 ? 0 : activeSlide + 1 }, 5000)"></div>
            @endif
        </div>
    </section>

    {{-- Welcome Section --}}
    @if(\App\Models\SiteSetting::getValue('enable_welcome_section', true))
        <section class="py-16 md:py-24 bg-white relative overflow-hidden">
            <!-- Decorative Pattern -->
            <div class="absolute top-0 right-0 w-64 h-64 bg-primary/5 rounded-bl-full -mr-16 -mt-16 z-0"></div>
            <div class="absolute bottom-0 left-0 w-48 h-48 bg-secondary/5 rounded-tr-full -ml-12 -mb-12 z-0"></div>

            <div class="container mx-auto px-4 relative z-10">
                <div class="flex flex-col md:flex-row items-center gap-12 lg:gap-20">
                    <div class="w-full md:w-1/2" data-aos="fade-right">
                        <div class="relative">
                            <div class="absolute inset-0 bg-primary/20 transform translate-x-4 translate-y-4 rounded-2xl"></div>
                            @if(\App\Models\SiteSetting::getValue('leader_photo'))
                                <img src="{{ Storage::url(\App\Models\SiteSetting::getValue('leader_photo')) }}" alt="Pimpinan"
                                    class="relative rounded-2xl shadow-xl w-full object-cover h-[400px] md:h-[500px]">
                            @else
                                <img src="{{ asset('images/default-leader.jpg') }}" alt="Pimpinan"
                                    class="relative rounded-2xl shadow-xl w-full object-cover h-[400px] md:h-[500px]">
                            @endif

                            <div
                                class="absolute bottom-6 left-6 right-6 bg-white/95 backdrop-blur-sm p-4 rounded-xl shadow-lg border-l-4 border-primary">
                                <h3 class="text-xl font-bold text-gray-900">
                                    {{ \App\Models\SiteSetting::getValue('leader_name', 'Nama Pimpinan') }}</h3>
                                <p class="text-primary font-medium">
                                    {{ \App\Models\SiteSetting::getValue('leader_title', 'Kepala Instansi') }}</p>
                            </div>
                        </div>
                    </div>

                    <div class="w-full md:w-1/2" data-aos="fade-left">
                        <span
                            class="inline-block px-3 py-1 bg-primary/10 text-primary rounded-full text-sm font-semibold mb-4">{{ \App\Models\SiteSetting::getValue('welcome_label', 'SAMBUTAN') }}</span>
                        <h2 class="text-3xl md:text-4xl font-serif font-bold text-gray-900 mb-6 leading-tight">
                            Selamat Datang di Website Resmi <br class="hidden lg:block">
                            <span class="text-primary">{{ \App\Models\SiteSetting::getValue('site_name') }}</span>
                        </h2>
                        <div class="prose prose-lg text-gray-600 mb-8 leading-relaxed">
                            {!! nl2br(e(Str::limit(\App\Models\SiteSetting::getValue('welcome_text'), 600))) !!}
                        </div>
                        <div class="flex gap-4">
                            <a href="{{ route('frontend.about') }}"
                                class="inline-flex items-center justify-center px-6 py-3 border border-transparent text-base font-medium rounded-lg text-white bg-primary hover:bg-blue-700 transition-all shadow-lg hover:shadow-primary/30">
                                Selengkapnya
                                <svg class="ml-2 -mr-1 w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M14 5l7 7m0 0l-7 7m7-7H3"></path>
                                </svg>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    @endif

    {{-- Featured Categories/Services --}}
    <section class="py-16 bg-gray-50">
        <div class="container mx-auto px-4">
            <div class="text-center mb-12" data-aos="fade-up">
                <h2 class="text-3xl font-serif font-bold text-gray-900 mb-4">Informasi & Layanan</h2>
                <div class="w-20 h-1 bg-primary mx-auto rounded-full"></div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                @foreach($categories->filter(fn($c) => $c->slug !== 'pengumuman')->take(4) as $category)
                    <a href="{{ route('frontend.category', $category->slug) }}"
                        class="group bg-white p-8 rounded-2xl shadow-sm hover:shadow-xl transition-all duration-300 border border-gray-100 hover:border-primary/20 text-center relative overflow-hidden"
                        data-aos="fade-up" data-aos-delay="{{ $loop->index * 100 }}">
                        <div
                            class="absolute inset-0 bg-gradient-to-br from-primary/5 to-transparent opacity-0 group-hover:opacity-100 transition-opacity">
                        </div>
                        <div
                            class="w-16 h-16 bg-blue-50 text-primary rounded-2xl flex items-center justify-center mx-auto mb-6 text-2xl group-hover:scale-110 group-hover:bg-primary group-hover:text-white transition-all duration-300 shadow-sm">
                            <i class="fas fa-folder-open"></i> {{-- You can implement icon mapping here --}}
                        </div>
                        <h3 class="text-xl font-bold text-gray-900 mb-2 group-hover:text-primary transition-colors">
                            {{ $category->name }}</h3>
                        <p class="text-gray-500 text-sm mb-4">{{ $category->posts_count }} Artikel</p>
                    </a>
                @endforeach
            </div>
        </div>
    </section>

    {{-- Latest News --}}
    <section class="py-16 md:py-24 bg-white relative">
        <div class="container mx-auto px-4">
            <div class="flex flex-col md:flex-row justify-between items-end mb-12 gap-4" data-aos="fade-up">
                <div>
                    <span class="text-primary font-semibold tracking-wider uppercase text-sm">Update Terkini</span>
                    <h2 class="text-3xl md:text-4xl font-serif font-bold text-gray-900 mt-2">Berita & Artikel Terbaru</h2>
                </div>
                <a href="{{ route('frontend.posts') }}"
                    class="group inline-flex items-center font-semibold text-primary hover:text-secondary transition-colors">
                    Lihat Semua Berita
                    <svg class="ml-2 w-5 h-5 group-hover:translate-x-1 transition-transform" fill="none"
                        stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3">
                        </path>
                    </svg>
                </a>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                @foreach($latestPosts as $post)
                    <article
                        class="bg-white rounded-2xl shadow-sm hover:shadow-xl transition-all duration-300 border border-gray-100 overflow-hidden group flex flex-col h-full"
                        data-aos="fade-up" data-aos-delay="{{ $loop->index * 100 }}">
                        {{-- Image --}}
                        <div class="relative h-56 overflow-hidden">
                            <a href="{{ route('frontend.post', $post->slug) }}">
                                <img src="{{ $post->featured_image ? Storage::url($post->featured_image) : asset('images/default-post.jpg') }}"
                                    alt="{{ $post->title }}"
                                    class="w-full h-full object-cover transition-transform duration-500 group-hover:scale-110">
                            </a>
                            <div class="absolute top-4 left-4">
                                <span
                                    class="px-3 py-1 bg-white/90 backdrop-blur-sm text-primary text-xs font-bold rounded-full shadow-sm">
                                    {{ $post->category->name ?? 'Uncategorized' }}
                                </span>
                            </div>
                        </div>

                        {{-- Content --}}
                        <div class="p-6 flex flex-col flex-grow">
                            <div class="flex items-center text-xs text-gray-500 gap-4 mb-3">
                                <span class="flex items-center"><i class="far fa-calendar-alt mr-1"></i>
                                    {{ $post->created_at->format('d M Y') }}</span>
                                <span class="flex items-center"><i class="far fa-eye mr-1"></i> {{ $post->views }} Views</span>
                            </div>

                            <h3
                                class="text-xl font-bold text-gray-900 mb-3 line-clamp-2 group-hover:text-primary transition-colors">
                                <a href="{{ route('frontend.post', $post->slug) }}">{{ $post->title }}</a>
                            </h3>

                            <p class="text-gray-600 text-sm mb-4 line-clamp-3">
                                {{ $post->excerpt ?? Str::limit(strip_tags($post->content), 100) }}
                            </p>

                            <div class="mt-auto pt-4 border-t border-gray-100 flex items-center justify-between">
                                <div class="flex items-center gap-2">
                                    <div
                                        class="w-8 h-8 rounded-full bg-gray-200 flex items-center justify-center text-gray-500 text-xs">
                                        <i class="fas fa-user"></i>
                                    </div>
                                    <span class="text-xs font-medium text-gray-700">{{ $post->user->name ?? 'Admin' }}</span>
                                </div>
                                <a href="{{ route('frontend.post', $post->slug) }}"
                                    class="text-sm font-semibold text-primary hover:underline">Baca Selengkapnya</a>
                            </div>
                        </div>
                    </article>
                @endforeach
            </div>
        </div>
    </section>

    {{-- Stats Section --}}
    <section class="py-20 bg-secondary text-white relative overflow-hidden">
        <div class="absolute inset-0 opacity-10"
            style="background-image: url('{{ asset('images/pattern.svg') }}'); background-size: cover;"></div>
        <div class="container mx-auto px-4 relative z-10">
            <div class="grid grid-cols-2 md:grid-cols-4 gap-8 text-center">
                <div data-aos="zoom-in">
                    <div class="text-4xl md:text-5xl font-bold text-primary mb-2 count-up"
                        data-target="{{ \App\Models\Post::published()->count() }}">0</div>
                    <p class="text-gray-300 font-medium tracking-wide">Artikel & Berita</p>
                </div>
                <div data-aos="zoom-in" data-aos-delay="100">
                    <div class="text-4xl md:text-5xl font-bold text-primary mb-2 count-up"
                        data-target="{{ \App\Models\Media::count() }}">0</div>
                    <p class="text-gray-300 font-medium tracking-wide">Galeri Foto</p>
                </div>
                <div data-aos="zoom-in" data-aos-delay="200">
                    <div class="text-4xl md:text-5xl font-bold text-primary mb-2 count-up"
                        data-target="{{ \App\Models\Download::count() }}">0</div>
                    <p class="text-gray-300 font-medium tracking-wide">Dokumen</p>
                </div>
                <div data-aos="zoom-in" data-aos-delay="300">
                    <div class="text-4xl md:text-5xl font-bold text-primary mb-2 count-up"
                        data-target="{{ \App\Models\Post::sum('views') }}">0</div>
                    <p class="text-gray-300 font-medium tracking-wide">Total Pengunjung</p>
                </div>
            </div>
        </div>
    </section>

    {{-- Gallery Section --}}
    <section class="py-16 md:py-24 bg-gray-50">
        <div class="container mx-auto px-4">
            <div class="text-center mb-12" data-aos="fade-up">
                <h2 class="text-3xl font-serif font-bold text-gray-900 mb-4">Galeri Kegiatan</h2>
                <div class="w-20 h-1 bg-primary mx-auto rounded-full"></div>
            </div>

            <div class="columns-1 md:columns-2 lg:columns-4 gap-4 space-y-4">
                @foreach($galleryImages as $image)
                    <div class="break-inside-avoid rounded-xl overflow-hidden group relative" data-aos="fade-up">
                        <img src="{{ $image->featured_image ? Storage::url($image->featured_image) : asset('images/default-gallery.jpg') }}"
                            alt="{{ $image->title }}"
                            class="w-full h-auto transition-transform duration-500 group-hover:scale-110">
                        <div
                            class="absolute inset-0 bg-black/60 opacity-0 group-hover:opacity-100 transition-opacity flex items-center justify-center p-4">
                            <div class="text-center">
                                <h4
                                    class="text-white font-bold text-sm mb-2 transform translate-y-4 group-hover:translate-y-0 transition-transform duration-300">
                                    {{ $image->title }}</h4>
                                <a href="{{ route('frontend.post', $image->slug) }}"
                                    class="inline-block px-4 py-2 bg-primary text-white text-xs rounded hover:bg-blue-700 transition-colors transform translate-y-4 group-hover:translate-y-0 transition-transform duration-300 delay-75">
                                    Lihat Detail
                                </a>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            <div class="text-center mt-12">
                <a href="{{ route('frontend.gallery') }}"
                    class="btn-outline-primary inline-flex items-center justify-center px-6 py-3 border-2 border-primary text-base font-bold rounded-lg text-primary hover:bg-primary hover:text-white transition-all">
                    Lihat Semua Galeri
                    <svg class="ml-2 w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3">
                        </path>
                    </svg>
                </a>
            </div>
        </div>
    </section>

@endsection

@push('scripts')
    <script>
        // Counter Animation
        const counters = document.querySelectorAll('.count-up');
        const options = {
            threshold: 1,
            rootMargin: "0px"
        };

        const observer = new IntersectionObserver(function (entries, observer) {
            entries.forEach(entry => {
                if (!entry.isIntersecting) {
                    return;
                }
                const target = +entry.target.getAttribute('data-target');
                const duration = 2000; // ms
                const increment = target / (duration / 16); // 60fps

                let current = 0;
                const updateCounter = () => {
                    current += increment;
                    if (current < target) {
                        entry.target.innerText = Math.ceil(current);
                        requestAnimationFrame(updateCounter);
                    } else {
                        entry.target.innerText = target;
                    }
                };
                updateCounter();
                observer.unobserve(entry.target);
            });
        }, options);

        counters.forEach(counter => {
            observer.observe(counter);
        });
    </script>
@endpush