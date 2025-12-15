@extends('template.comprohijau.layouts.app')

@section('content')

    <div class="container mx-auto px-4 md:px-6 py-8">

        {{-- FOXIZ Style 3-Column Grid --}}
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 lg:gap-8">

            {{-- Left Column: Welcome Section (Sambutan) --}}
            <div class="lg:col-span-3 order-2 lg:order-1 space-y-6">
                {{-- Welcome Widget --}}
                 @if(\App\Models\SiteSetting::getValue('enable_welcome_section', true))
                    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5">
                         <div class="border-l-4 border-primary pl-3 mb-4">
                            <h3 class="font-bold font-heading text-lg uppercase tracking-wider text-gray-900 border-b border-gray-100 pb-2 mb-2">Sambutan</h3>
                        </div>
                        <div class="flex items-center gap-4 mb-4">
                            <div class="w-14 h-14 rounded-full overflow-hidden shrink-0 border-2 border-primary/20">
                                @if(\App\Models\SiteSetting::getValue('leader_photo'))
                                    <img src="{{ Storage::url(\App\Models\SiteSetting::getValue('leader_photo')) }}"
                                        class="w-full h-full object-cover">
                                @else
                                    <img src="{{ asset('images/default-leader.jpg') }}" class="w-full h-full object-cover">
                                @endif
                            </div>
                            <div>
                                <h4 class="font-bold text-gray-900 text-xs leading-tight">
                                    {{ \App\Models\SiteSetting::getValue('leader_name', 'Nama Pimpinan') }}</h4>
                                <p class="text-[10px] text-primary font-medium mt-0.5">
                                    {{ \App\Models\SiteSetting::getValue('leader_title', 'Kepala Instansi') }}</p>
                            </div>
                        </div>
                        <p class="text-gray-600 text-xs leading-relaxed mb-4 line-clamp-6">
                            {{ Str::limit(strip_tags(\App\Models\SiteSetting::getValue('welcome_text')), 200) }}
                        </p>
                        <a href="{{ route('frontend.about') }}"
                            class="block text-center text-xs font-bold text-primary border border-primary/20 rounded-lg py-1.5 hover:bg-primary hover:text-white transition-colors">
                            Selengkapnya
                        </a>
                    </div>
                @endif
            </div>

            {{-- Center Column: Main Headlines (Slider/Big Post) --}}
            <div class="lg:col-span-6 order-1 lg:order-2">
                 @if($sliderPosts->count() > 0)
                    {{-- Only First Slider Item as Main Highlight --}}
                    @php $mainPost = $sliderPosts->first(); @endphp
                    <div class="relative rounded-2xl overflow-hidden shadow-lg group aspect-w-16 aspect-h-10 md:aspect-h-9">
                        <img src="{{ $mainPost->featured_image_url ? $mainPost->featured_image_url : asset('images/default-hero.jpg') }}"
                             class="absolute inset-0 w-full h-full object-cover transition-transform duration-700 group-hover:scale-105">
                        <div class="absolute inset-0 bg-gradient-to-t from-secondary/90 via-secondary/20 to-transparent"></div>

                        <div class="absolute bottom-0 left-0 w-full p-6 md:p-8">
                            <span class="inline-block px-2 py-1 bg-primary text-white text-xs font-bold uppercase tracking-wider mb-3 rounded-sm">
                                    {{ $mainPost->category->name }}
                            </span>
                            <h2 class="text-2xl md:text-3xl lg:text-4xl font-bold text-white mb-3 font-heading leading-tight drop-shadow-md">
                                <a href="{{ route('frontend.post', $mainPost->slug) }}" class="hover:text-accent transition-colors">
                                    {{ $mainPost->title }}
                                </a>
                            </h2>
                            <p class="text-gray-200 text-sm md:text-base line-clamp-2 md:line-clamp-none mb-4 hidden md:block opacity-90">
                                {{ $mainPost->excerpt ?? Str::limit(strip_tags($mainPost->content), 120) }}
                            </p>
                            <div class="flex items-center text-gray-300 text-xs md:text-sm font-medium">
                                <span class="mr-4"><i class="far fa-user mr-2"></i> {{ $mainPost->user->name }}</span>
                                <span><i class="far fa-calendar-alt mr-2"></i> {{ $mainPost->created_at->format('d M Y') }}</span>
                            </div>
                        </div>
                    </div>

                    {{-- Sub-HL below Main --}}
                    @if($sliderPosts->count() > 1)
                        <div class="grid grid-cols-2 gap-4 mt-4">
                            @foreach($sliderPosts->skip(1)->take(2) as $subPost)
                                <article class="relative rounded-xl overflow-hidden group aspect-[16/9]">
                                     <img src="{{ $subPost->featured_image_url ? $subPost->featured_image_url : asset('images/default-post.jpg') }}"
                                          class="absolute inset-0 w-full h-full object-cover transition-transform duration-500 group-hover:scale-110">
                                    <div class="absolute inset-0 bg-gradient-to-t from-secondary/90 to-transparent"></div>
                                    <div class="absolute bottom-0 p-4">
                                        <h4 class="text-white text-sm md:text-base font-bold leading-snug group-hover:text-accent transition-colors line-clamp-2">
                                            <a href="{{ route('frontend.post', $subPost->slug) }}">{{ $subPost->title }}</a>
                                        </h4>
                                    </div>
                                </article>
                            @endforeach
                        </div>
                    @endif

                @endif
            </div>

            {{-- Right Column: Announcements & Popular --}}
            <div class="lg:col-span-3 order-3 space-y-8">

                {{-- Announcements (Moved Here) --}}
                <div>
                     <div class="border-l-4 border-primary pl-3 mb-4">
                        <h3 class="font-bold font-heading text-lg uppercase tracking-wider text-gray-900">Pengumuman</h3>
                    </div>
                     @if($announcements->count() > 0)
                        <div class="space-y-4">
                            @foreach($announcements->take(4) as $announcement)
                                 <div class="group border-b border-gray-100 pb-3 last:border-0 last:pb-0">
                                    <span class="text-[10px] uppercase font-bold text-primary mb-1 block">{{ $announcement->created_at->format('d M Y') }}</span>
                                    <h4 class="font-bold text-sm leading-snug text-gray-800 group-hover:text-primary transition-colors">
                                        <a href="{{ route('frontend.post', $announcement->slug) }}">{{ $announcement->title }}</a>
                                    </h4>
                                </div>
                            @endforeach
                        </div>
                        <a href="{{ route('frontend.category', 'pengumuman') }}" class="inline-block mt-4 text-xs font-bold text-white bg-primary px-3 py-1.5 rounded hover:bg-opacity-90 transition-colors">
                            Lihat Semua
                        </a>
                    @else
                        <p class="text-sm text-gray-500 italic">Belum ada pengumuman.</p>
                    @endif
                </div>

                 {{-- Popular (Limited to 3) --}}
                 <div>
                     <div class="border-l-4 border-secondary pl-3 mb-4">
                        <h3 class="font-bold font-heading text-lg uppercase tracking-wider text-gray-900">Populer</h3>
                    </div>

                    <div class="posts-counter-reset space-y-6 pl-2">
                        @foreach($popularPosts->take(3) as $post)
                            <article class="counter-badge relative pl-6 group">
                                <div class="flex gap-3 items-start">
                                    <div class="flex-grow">
                                        <a href="{{ route('frontend.category', $post->category->slug) }}" class="text-[10px] uppercase font-bold text-secondary mb-1 block">{{ $post->category->name }}</a>
                                        <h4 class="font-bold text-sm leading-snug text-gray-800 group-hover:text-primary transition-colors line-clamp-2">
                                            <a href="{{ route('frontend.post', $post->slug) }}">{{ $post->title }}</a>
                                        </h4>
                                         <span class="text-[10px] text-gray-400 mt-1 block">{{ $post->views }} Views</span>
                                    </div>
                                    <a href="{{ route('frontend.post', $post->slug) }}" class="shrink-0 w-16 h-16 rounded overflow-hidden">
                                        <img src="{{ $post->featured_image ? Storage::url($post->featured_image) : asset('images/default-post.jpg') }}" class="w-full h-full object-cover">
                                    </a>
                                </div>
                            </article>
                        @endforeach
                    </div>
                </div>

                {{-- Social Widget (Green Theme) --}}
                 <div class="bg-gray-50 border border-gray-200 p-4 rounded-xl">
                    <h5 class="font-bold text-sm mb-3 text-center uppercase text-gray-700">Ikuti Kami</h5>
                    <div class="flex justify-center gap-3">
                         @if(\App\Models\SiteSetting::getValue('facebook_url'))
                            <a href="{{ \App\Models\SiteSetting::getValue('facebook_url') }}" class="w-8 h-8 rounded bg-secondary text-white flex items-center justify-center hover:bg-primary transition-colors"><i class="fab fa-facebook-f"></i></a>
                        @endif
                        @if(\App\Models\SiteSetting::getValue('instagram_url'))
                            <a href="{{ \App\Models\SiteSetting::getValue('instagram_url') }}" class="w-8 h-8 rounded bg-secondary text-white flex items-center justify-center hover:bg-primary transition-colors"><i class="fab fa-instagram"></i></a>
                        @endif
                        @if(\App\Models\SiteSetting::getValue('twitter_url'))
                            <a href="{{ \App\Models\SiteSetting::getValue('twitter_url') }}" class="w-8 h-8 rounded bg-secondary text-white flex items-center justify-center hover:bg-primary transition-colors"><i class="fab fa-twitter"></i></a>
                        @endif
                        @if(\App\Models\SiteSetting::getValue('youtube_url'))
                            <a href="{{ \App\Models\SiteSetting::getValue('youtube_url') }}" class="w-8 h-8 rounded bg-secondary text-white flex items-center justify-center hover:bg-primary transition-colors"><i class="fab fa-youtube"></i></a>
                        @endif
                    </div>
                </div>

            </div>

        </div>

        {{-- More News Section (Grid) --}}
        <div class="mt-16">
            <div class="flex items-center justify-between border-b-2 border-gray-100 mb-6 pb-2">
                <h3
                    class="text-xl md:text-2xl font-bold font-heading text-gray-900 border-b-2 border-primary -mb-2.5 pb-2 inline-block pr-4">
                    Berita Terbaru</h3>
                <a href="{{ route('frontend.posts') }}" class="text-sm font-bold text-gray-500 hover:text-primary">Lihat
                    Semua <i class="fas fa-arrow-right ml-1"></i></a>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
                @foreach($latestPosts->take(8) as $post)
                    <article class="group">
                        <a href="{{ route('frontend.post', $post->slug) }}"
                            class="block relative aspect-video rounded-xl overflow-hidden mb-3">
                            <img src="{{ $post->featured_image_url ? $post->featured_image_url : asset('images/default-post.jpg') }}"
                                class="w-full h-full object-cover transition-transform duration-500 group-hover:scale-105">
                            <div class="absolute bottom-2 left-2">
                                <span class="bg-primary text-white text-[10px] font-bold px-1.5 py-0.5 rounded shadow-sm">
                                    {{ $post->category->name ?? 'Umum' }}
                                </span>
                            </div>
                        </a>
                        <h4
                            class="font-bold text-gray-800 leading-snug mb-2 group-hover:text-primary transition-colors line-clamp-2">
                            <a href="{{ route('frontend.post', $post->slug) }}">{{ $post->title }}</a>
                        </h4>
                        <div class="text-xs text-gray-500 flex items-center gap-2">
                            <span>{{ $post->created_at->format('d M Y') }}</span>
                            <span>&bull;</span>
                            <span>{{ $post->views }} baca</span>
                        </div>
                    </article>
                @endforeach
            </div>
        </div>

        {{-- Partners Section --}}
        @if(isset($partners) && $partners->count() > 0)
            <div class="mt-16 mb-8">
                <div class="flex items-center justify-between border-b-2 border-gray-100 mb-6 pb-2">
                    <h3
                        class="text-xl md:text-2xl font-bold font-heading text-gray-900 border-b-2 border-primary -mb-2.5 pb-2 inline-block pr-4">
                        Mitra Kami</h3>
                </div>

                <div class="bg-white border border-gray-100 rounded-xl p-8">
                    <div class="flex flex-wrap items-center justify-center gap-8 md:gap-12">
                        @foreach($partners as $partner)
                            <a href="{{ $partner->url ?? '#' }}" target="_blank"
                                class="block opacity-60 hover:opacity-100 transition-opacity grayscale hover:grayscale-0 group"
                                title="{{ $partner->name }}">
                                <img src="{{ Storage::url($partner->image) }}" alt="{{ $partner->name }}"
                                    class="h-12 md:h-20 w-auto object-contain transition-transform group-hover:scale-110">
                            </a>
                        @endforeach
                    </div>
                </div>
            </div>
        @endif

    </div>

@endsection