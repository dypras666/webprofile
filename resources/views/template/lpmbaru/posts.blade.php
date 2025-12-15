@extends('template.lpmbaru.layouts.app')

@section('content')

    {{-- Page Header --}}
    <div class="relative bg-secondary py-20 overflow-hidden">
        <div class="absolute inset-0 opacity-10"
            style="background-image: url('{{ asset('images/pattern.svg') }}'); background-size: cover;"></div>
        <div class="container mx-auto px-4 relative z-10 text-center">
            <h1 class="text-3xl md:text-5xl font-serif font-bold text-white mb-4" data-aos="fade-down">Berita & Artikel</h1>
            <div class="flex justify-center items-center text-gray-300 text-sm md:text-base gap-2" data-aos="fade-up"
                data-aos-delay="100">
                <a href="{{ route('frontend.index') }}" class="hover:text-primary transition-colors">Beranda</a>
                <span>/</span>
                <span class="text-primary font-medium">Berita</span>
            </div>
        </div>
    </div>

    {{-- Main Content --}}
    <section class="py-16 md:py-20 bg-gray-50">
        <div class="container mx-auto px-4">
            <div class="flex flex-col lg:flex-row gap-12">

                {{-- Content Area --}}
                <div class="w-full lg:w-2/3">

                    {{-- Categories & Search --}}
                    <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100 mb-8" data-aos="fade-up">
                        <div class="flex flex-col md:flex-row gap-4 justify-between items-center">
                            <div class="flex flex-wrap gap-2">
                                <a href="{{ route('frontend.posts') }}"
                                    class="px-4 py-2 rounded-full text-sm font-medium transition-colors {{ !request('category') ? 'bg-primary text-white' : 'bg-gray-100 text-gray-600 hover:bg-gray-200' }}">Semua</a>
                                @foreach($categories as $cat)
                                    <a href="{{ route('frontend.posts', ['category' => $cat->slug]) }}"
                                        class="px-4 py-2 rounded-full text-sm font-medium transition-colors {{ request('category') == $cat->slug ? 'bg-primary text-white' : 'bg-gray-100 text-gray-600 hover:bg-gray-200' }}">
                                        {{ $cat->name }}
                                    </a>
                                @endforeach
                            </div>

                            <form action="{{ route('frontend.posts') }}" method="GET" class="w-full md:w-auto">
                                @if(request('category'))
                                    <input type="hidden" name="category" value="{{ request('category') }}">
                                @endif
                                <div class="relative">
                                    <input type="text" name="search" value="{{ request('search') }}"
                                        placeholder="Cari berita..."
                                        class="w-full md:w-64 pl-10 pr-4 py-2 rounded-lg border border-gray-200 focus:border-primary focus:ring focus:ring-primary/20 transition-all">
                                    <svg class="w-5 h-5 text-gray-400 absolute left-3 top-1/2 -translate-y-1/2" fill="none"
                                        stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                                    </svg>
                                </div>
                            </form>
                        </div>
                    </div>

                    {{-- Posts Grid --}}
                    @if($posts->count() > 0)
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-8 mb-12">
                            @foreach($posts as $post)
                                <article
                                    class="bg-white rounded-xl shadow-sm hover:shadow-xl transition-all duration-300 border border-gray-100 overflow-hidden group h-full flex flex-col"
                                    data-aos="fade-up" data-aos-delay="{{ $loop->index * 50 }}">
                                    <div class="relative h-48 overflow-hidden">
                                        <a href="{{ route('frontend.post', $post->slug) }}">
                                            <img src="{{ $post->featured_image ? Storage::url($post->featured_image) : asset('images/default-post.jpg') }}"
                                                alt="{{ $post->title }}"
                                                class="w-full h-full object-cover transition-transform duration-500 group-hover:scale-110">
                                        </a>
                                        <div class="absolute top-4 left-4">
                                            <span
                                                class="px-3 py-1 bg-white/90 backdrop-blur-sm text-primary text-xs font-bold rounded-full shadow-sm">{{ $post->category->name }}</span>
                                        </div>
                                    </div>
                                    <div class="p-6 flex flex-col flex-grow">
                                        <div class="flex items-center text-xs text-gray-500 gap-4 mb-3">
                                            <span><i class="far fa-calendar-alt mr-1"></i>
                                                {{ $post->created_at->format('d M Y') }}</span>
                                        </div>
                                        <h3
                                            class="text-lg font-bold text-gray-900 mb-3 line-clamp-2 group-hover:text-primary transition-colors">
                                            <a href="{{ route('frontend.post', $post->slug) }}">{{ $post->title }}</a>
                                        </h3>
                                        <p class="text-gray-600 text-sm mb-4 line-clamp-3 flex-grow">{{ $post->excerpt }}</p>
                                        <a href="{{ route('frontend.post', $post->slug) }}"
                                            class="inline-flex items-center text-primary text-sm font-semibold hover:underline mt-auto">
                                            Baca Selengkapnya <svg class="ml-1 w-4 h-4" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M14 5l7 7m0 0l-7 7m7-7H3"></path>
                                            </svg>
                                        </a>
                                    </div>
                                </article>
                            @endforeach
                        </div>

                        {{-- Pagination --}}
                        <div class="mt-8">
                            {{ $posts->links() }}
                        </div>
                    @else
                        <div class="text-center py-12 bg-white rounded-xl border border-gray-100">
                            <div
                                class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4 text-gray-400">
                                <i class="fas fa-search text-2xl"></i>
                            </div>
                            <h3 class="text-lg font-bold text-gray-900 mb-2">Tidak ditemukan</h3>
                            <p class="text-gray-500">Maaf, tidak ada berita yang sesuai dengan kriteria pencarian Anda.</p>
                        </div>
                    @endif
                </div>

                {{-- Sidebar --}}
                <aside class="w-full lg:w-1/3 space-y-8">

                    {{-- Popular Posts --}}
                    <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100 sticky top-24"
                        data-aos="fade-left">
                        <h3 class="text-lg font-bold text-gray-900 mb-4 pb-2 border-b border-gray-100">Berita Populer</h3>
                        <div class="space-y-4">
                            @foreach($popularPosts as $post)
                                <div class="flex gap-4 group">
                                    <div class="shrink-0 w-20 h-20 rounded-lg overflow-hidden relative">
                                        <img src="{{ $post->featured_image ? Storage::url($post->featured_image) : asset('images/default-post.jpg') }}"
                                            alt="{{ $post->title }}"
                                            class="w-full h-full object-cover transition-transform group-hover:scale-110">
                                    </div>
                                    <div>
                                        <h4
                                            class="text-sm font-bold text-gray-900 mb-1 line-clamp-2 group-hover:text-primary transition-colors">
                                            <a href="{{ route('frontend.post', $post->slug) }}">{{ $post->title }}</a>
                                        </h4>
                                        <span class="text-xs text-gray-500">{{ $post->created_at->format('d M Y') }}</span>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>

                </aside>
            </div>
        </div>
    </section>

@endsection