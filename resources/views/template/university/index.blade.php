@extends('template.university.layouts.app')

@section('content')

    <div class="container mx-auto px-4 md:px-6 py-8">

        {{-- Top Grid: News Selection & Slider --}}
        {{-- Top Grid: News Selection & Slider --}}
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-0 overflow-hidden shadow-2xl rounded-sm bg-white">

            {{-- Left Column: Berita Pilihan (Accordion/List Style like reference) --}}
            <div class="lg:col-span-4 order-2 lg:order-1 bg-white relative z-10">
                <div class="flex flex-col h-full divide-y divide-gray-100">
                    {{-- Loop through first 3 popular/pinned posts --}}
                    @foreach($popularPosts->take(4) as $index => $post)
                        <div
                            class="group relative p-4 lg:p-6 transition-all duration-300 hover:bg-gray-50 flex gap-4 items-start cursor-pointer h-full">
                            {{-- Thumbnail (Small Square) --}}
                            <div class="shrink-0 w-20 h-20 overflow-hidden rounded-sm shadow-sm relative">
                                <img src="{{ $post->featured_image ? Storage::url($post->featured_image) : asset('images/default-post.jpg') }}"
                                    class="w-full h-full object-cover transition-transform duration-500 group-hover:scale-110">
                            </div>

                            {{-- Text --}}
                            <div class="flex-grow">
                                <h4
                                    class="font-bold text-gray-800 text-sm md:text-base leading-tight mb-2 group-hover:text-[#1e3a8a] transition-colors font-heading">
                                    <a href="{{ route('frontend.post', $post->slug) }}"
                                        class="before:absolute before:inset-0">{{ $post->title }}</a>
                                </h4>
                                <p class="text-xs text-gray-400 line-clamp-2 leading-relaxed font-light">
                                    {{ Str::limit(strip_tags($post->content), 70) }}
                                </p>
                            </div>

                            {{-- Active Indicator (mocking selection behavior from image) --}}
                            @if($index === 0)
                                <div class="absolute left-0 top-0 bottom-0 w-1 bg-cyan-500"></div>
                            @endif
                        </div>
                    @endforeach
                </div>
            </div>

            {{-- Right Column: Main Slider (Full Height Hero) --}}
            <div class="lg:col-span-8 order-1 lg:order-2 relative h-[450px] lg:h-auto min-h-[500px]">
                @if($sliderPosts->count() > 0)
                    <div x-data="{ activeSlide: 0, total: {{ $sliderPosts->count() }}, timer: null }"
                        x-init="timer = setInterval(() => { activeSlide = activeSlide === total - 1 ? 0 : activeSlide + 1 }, 6000)"
                        @mouseenter="clearInterval(timer)"
                        @mouseleave="timer = setInterval(() => { activeSlide = activeSlide === total - 1 ? 0 : activeSlide + 1 }, 6000)"
                        class="absolute inset-0 w-full h-full">

                        @foreach($sliderPosts as $index => $post)
                            <div x-show="activeSlide === {{ $index }}" x-transition:enter="transition ease-in-out duration-1000"
                                x-transition:enter-start="opacity-0 lg:translate-x-full z-10"
                                x-transition:enter-end="opacity-100 lg:translate-x-0 z-20"
                                x-transition:leave="transition ease-in-out duration-1000"
                                x-transition:leave-start="opacity-100 lg:translate-x-0 z-20"
                                x-transition:leave-end="opacity-0 lg:-translate-x-full z-10"
                                class="absolute inset-0 w-full h-full bg-gray-100">
                                <img src="{{ $post->featured_image_url ? $post->featured_image_url : asset('images/default-hero.jpg') }}"
                                    class="w-full h-full object-cover">

                                {{-- Overlay Content Box (Bottom Right) --}}
                                <div
                                    class="absolute bottom-0 right-0 left-0 lg:left-auto lg:max-w-md bg-white/95 backdrop-blur-sm p-8 lg:p-10 shadow-lg border-t-4 border-cyan-500 m-4 lg:m-0">
                                    <h2 class="text-2xl md:text-3xl font-bold text-[#1e3a8a] mb-3 font-heading leading-tight">
                                        <a href="{{ route('frontend.post', $post->slug) }}"
                                            class="hover:text-cyan-600 transition-colors">
                                            {{ $post->title }}
                                        </a>
                                    </h2>
                                    <p class="text-gray-500 text-sm leading-relaxed mb-4 line-clamp-3">
                                        {{ $post->excerpt ?? Str::limit(strip_tags($post->content), 120) }}
                                    </p>
                                    <a href="{{ route('frontend.post', $post->slug) }}"
                                        class="text-cyan-600 font-bold text-sm uppercase tracking-wider hover:text-[#1e3a8a] transition-colors">
                                        Read More <i class="fas fa-arrow-right ml-1"></i>
                                    </a>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="w-full h-full bg-gray-200 flex items-center justify-center">
                        <span class="text-gray-400 font-medium">No slider content available</span>
                    </div>
                @endif
            </div>

        </div>

        {{-- More News Section --}}
        <div class="mt-16">
            <div class="flex items-center justify-between border-b-2 border-gray-100 mb-8 pb-4">
                <div class="flex flex-col md:flex-row md:items-center gap-2">
                    <h3 class="text-2xl font-bold font-heading text-secondary">
                        Berita <span class="text-primary">Terbaru</span>
                    </h3>
                    <div class="hidden md:block w-12 h-1 bg-primary rounded-full"></div>
                </div>
                <a href="{{ route('frontend.posts') }}"
                    class="group text-sm font-bold text-gray-500 hover:text-primary transition-colors flex items-center gap-1">
                    Lihat Semua <i
                        class="fas fa-arrow-right transition-transform group-hover:translate-x-1 text-primary"></i>
                </a>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 lg:gap-8">
                @foreach($latestPosts->take(8) as $post)
                    <article
                        class="group relative flex flex-col h-full bg-white rounded-2xl overflow-hidden hover:shadow-xl transition-all duration-300 border border-gray-100 hover:-translate-y-1">
                        {{-- Image --}}
                        <a href="{{ route('frontend.post', $post->slug) }}"
                            class="relative aspect-[16/10] overflow-hidden bg-gray-100">
                            <img src="{{ $post->featured_image_url ? $post->featured_image_url : asset('images/default-post.jpg') }}"
                                class="w-full h-full object-cover transition-transform duration-700 group-hover:scale-110">
                            <div class="absolute top-3 left-3">
                                <span
                                    class="bg-white/90 backdrop-blur-sm text-primary text-[10px] font-bold px-2 py-1 rounded-md shadow-sm border border-primary/10">
                                    {{ $post->category->name ?? 'Umum' }}
                                </span>
                            </div>
                        </a>
                        {{-- Content --}}
                        <div class="p-5 flex flex-col flex-grow">
                            <div class="text-xs text-gray-400 flex items-center gap-3 mb-3 font-medium">
                                <span class="flex items-center gap-1"><i class="far fa-calendar text-primary"></i>
                                    {{ $post->created_at->format('d M Y') }}</span>
                                <span class="flex items-center gap-1"><i class="far fa-eye text-primary"></i>
                                    {{ $post->views }}</span>
                            </div>
                            <h4
                                class="font-bold text-gray-800 text-lg leading-snug mb-3 group-hover:text-primary transition-colors line-clamp-2">
                                <a href="{{ route('frontend.post', $post->slug) }}">{{ $post->title }}</a>
                            </h4>
                            <div class="mt-auto pt-4 border-t border-gray-50">
                                <a href="{{ route('frontend.post', $post->slug) }}"
                                    class="text-xs font-bold text-gray-500 uppercase tracking-wide group-hover:text-primary transition-colors inline-flex items-center gap-1">
                                    Baca <i
                                        class="fas fa-arrow-right text-xs opacity-0 -ml-2 group-hover:opacity-100 group-hover:ml-0 transition-all"></i>
                                </a>
                            </div>
                        </div>
                    </article>
                @endforeach
            </div>
        </div>

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