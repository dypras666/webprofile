@extends('template.university.layouts.app')

@section('title', 'Hasil Pencarian: ' . ($query ?? '') . ' - ' . \App\Models\SiteSetting::getValue('site_name'))

@section('content')

    {{-- Page Header --}}
    <div class="relative bg-secondary py-16 lg:py-24 overflow-hidden">
        {{-- Abstract Pattern --}}
        <div class="absolute inset-0 opacity-10"
            style="background-image: radial-gradient(#10b981 1px, transparent 1px); background-size: 24px 24px;"></div>

        <div class="container mx-auto px-4 relative z-10 text-center">
            <h1 class="text-3xl md:text-5xl font-bold font-heading text-white mb-4">Pencarian</h1>
            <nav class="flex justify-center items-center text-green-100 text-sm md:text-base gap-2">
                <a href="{{ route('frontend.index') }}" class="hover:text-white transition-colors">Beranda</a>
                <span class="text-green-500">/</span>
                <span class="font-medium text-white">Search</span>
            </nav>
        </div>
    </div>

    <section class="py-16 bg-gray-50">
        <div class="container mx-auto px-4">
            <div class="flex flex-col lg:flex-row gap-10">

                {{-- Main Content --}}
                <div class="w-full lg:w-2/3">
                    <div class="mb-8 p-6 bg-white rounded-lg shadow-sm border border-gray-100">
                        <p class="text-gray-600">Menampilkan hasil pencarian untuk kata kunci: <strong
                                class="text-primary text-lg">"{{ $query }}"</strong></p>
                    </div>

                    @if($posts->count() > 0)
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                            @foreach($posts as $post)
                                <article
                                    class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden flex flex-col h-full hover:shadow-lg transition-shadow duration-300">
                                    <a href="{{ route('frontend.post', $post->slug) }}"
                                        class="relative h-48 overflow-hidden group block">
                                        <img src="{{ $post->featured_image ? Storage::url($post->featured_image) : asset('images/default-post.jpg') }}"
                                            alt="{{ $post->title }}"
                                            class="w-full h-full object-cover transition-transform duration-500 group-hover:scale-110">
                                        <div class="absolute top-2 left-2">
                                            <span
                                                class="bg-white/90 backdrop-blur text-primary text-xs font-bold px-2 py-1 rounded shadow-sm">
                                                {{ $post->category->name ?? 'Umum' }}
                                            </span>
                                        </div>
                                    </a>

                                    <div class="p-5 flex flex-col flex-grow">
                                        <div class="flex items-center gap-4 text-xs text-gray-500 mb-3">
                                            <span class="flex items-center"><i class="far fa-calendar-alt mr-1"></i>
                                                {{ $post->created_at->format('d M Y') }}</span>
                                        </div>

                                        <h3
                                            class="text-lg font-bold text-gray-800 mb-2 leading-snug line-clamp-2 hover:text-primary transition-colors">
                                            <a href="{{ route('frontend.post', $post->slug) }}">{{ $post->title }}</a>
                                        </h3>

                                        <p class="text-sm text-gray-600 line-clamp-3 mb-4 flex-grow">
                                            {{ $post->excerpt ?? Str::limit(strip_tags($post->content), 100) }}
                                        </p>

                                        <a href="{{ route('frontend.post', $post->slug) }}"
                                            class="text-primary text-sm font-semibold hover:underline mt-auto inline-flex items-center">
                                            Baca Selengkapnya <i class="fas fa-arrow-right ml-1 text-xs"></i>
                                        </a>
                                    </div>
                                </article>
                            @endforeach
                        </div>

                        <div class="mt-12 flex justify-center">
                            {{ $posts->links() }}
                        </div>
                    @else
                        <div class="bg-white rounded-xl p-12 text-center border border-gray-100">
                            <div
                                class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4 text-gray-400 text-2xl">
                                <i class="fas fa-search-minus"></i>
                            </div>
                            <h3 class="text-lg font-bold text-gray-900">Tidak ditemukan</h3>
                            <p class="text-gray-500">Maaf, kami tidak menemukan artikel yang cocok dengan kata kunci tersebut.
                            </p>
                            <a href="{{ route('frontend.posts') }}"
                                class="mt-6 inline-block px-6 py-2 bg-primary text-white rounded-lg hover:bg-emerald-700 transition-colors">Lihat
                                Semua Berita</a>
                        </div>
                    @endif
                </div>

                {{-- Sidebar --}}
                <aside class="w-full lg:w-1/3 space-y-8">

                    {{-- Search Widget --}}
                    <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100">
                        <h3 class="font-bold text-lg text-gray-900 mb-4 border-b pb-2">Pencarian Baru</h3>
                        <form action="{{ route('frontend.search') }}" method="GET" class="relative">
                            <input type="text" name="q" value="{{ $query }}" placeholder="Cari artikel..."
                                class="w-full pl-4 pr-10 py-3 rounded-lg bg-gray-50 border border-gray-200 focus:bg-white focus:ring-2 focus:ring-primary/20 focus:border-primary transition-colors outline-none">
                            <button type="submit"
                                class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-primary">
                                <i class="fas fa-search"></i>
                            </button>
                        </form>
                    </div>

                    {{-- Popular Posts --}}
                    <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100">
                        <h3 class="font-bold text-lg text-gray-900 mb-4 border-b pb-2">Terpopuler</h3>
                        <div class="space-y-4">
                            @foreach($popularPosts as $pop)
                                <div class="flex gap-3">
                                    <a href="{{ route('frontend.post', $pop->slug) }}"
                                        class="w-16 h-16 rounded overflow-hidden shrink-0 bg-gray-100 group">
                                        <img src="{{ $pop->featured_image ? Storage::url($pop->featured_image) : asset('images/default-post.jpg') }}"
                                            class="w-full h-full object-cover transition-transform group-hover:scale-110">
                                    </a>
                                    <div>
                                        <h5 class="text-sm font-medium text-gray-800 line-clamp-2 mb-1 hover:text-primary">
                                            <a href="{{ route('frontend.post', $pop->slug) }}">{{ $pop->title }}</a>
                                        </h5>
                                        <div class="text-xs text-gray-500 flex items-center gap-1">
                                            <i class="far fa-eye"></i> {{ $pop->views }}
                                        </div>
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