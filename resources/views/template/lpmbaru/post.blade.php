@extends('template.lpmbaru.layouts.app')

@section('title', $post->title . ' - ' . \App\Models\SiteSetting::getValue('site_name'))
@section('description', $post->meta_description ?? Str::limit(strip_tags($post->content), 160))
@section('keywords', $post->meta_keywords ?? '')
@section('author', $post->user->name ?? 'Admin')
@section('og_image', $post->featured_image ? Storage::url($post->featured_image) : asset('images/default-og.jpg'))
@section('og_type', 'article')

@section('content')

    {{-- Article Header --}}
    <div class="relative bg-secondary py-24 md:py-32 overflow-hidden">
        <div class="absolute inset-0 opacity-20 bg-center bg-cover"
            style="background-image: url('{{ $post->featured_image ? Storage::url($post->featured_image) : asset('images/default-hero.jpg') }}'); filter: blur(8px);">
        </div>
        <div class="absolute inset-0 bg-black/60 z-0"></div>

        <div class="container mx-auto px-4 relative z-10 text-center max-w-4xl">
            <span
                class="inline-block px-3 py-1 bg-primary text-white text-xs font-semibold rounded-full mb-6 uppercase tracking-wider"
                data-aos="fade-down">
                {{ $post->category->name }}
            </span>
            <h1 class="text-3xl md:text-5xl md:leading-tight font-serif font-bold text-white mb-6" data-aos="zoom-in"
                data-aos-delay="100">
                {{ $post->title }}
            </h1>
            <div class="flex flex-wrap justify-center items-center text-gray-300 text-sm gap-6" data-aos="fade-up"
                data-aos-delay="200">
                <span class="flex items-center gap-2">
                    <div class="w-8 h-8 rounded-full bg-white/20 flex items-center justify-center">
                        <i class="far fa-user"></i>
                    </div>
                    {{ $post->user->name }}
                </span>
                <span class="flex items-center gap-2">
                    <div class="w-8 h-8 rounded-full bg-white/20 flex items-center justify-center">
                        <i class="far fa-calendar-alt"></i>
                    </div>
                    {{ $post->created_at->format('d F Y') }}
                </span>
                <span class="flex items-center gap-2">
                    <div class="w-8 h-8 rounded-full bg-white/20 flex items-center justify-center">
                        <i class="far fa-eye"></i>
                    </div>
                    {{ $post->views }} Views
                </span>
            </div>
        </div>
    </div>

    {{-- Main Content --}}
    <section class="py-16 md:py-20 bg-white">
        <div class="container mx-auto px-4">
            <div class="flex flex-col lg:flex-row gap-12">

                {{-- Article Content --}}
                <article class="w-full lg:w-2/3">
                    <div class="prose prose-lg max-w-none text-gray-800 leading-relaxed mb-12" data-aos="fade-up">
                        {!! $post->content !!}
                    </div>

                    {{-- Tags --}}
                    @if($post->tags)
                        <div class="flex flex-wrap gap-2 mb-12 border-t border-gray-100 pt-6">
                            <span class="text-gray-500 font-medium mr-2">Tags:</span>
                            @foreach(explode(',', $post->tags) as $tag)
                                <a href="{{ route('frontend.posts', ['search' => trim($tag)]) }}"
                                    class="px-3 py-1 bg-gray-100 text-gray-600 rounded-md text-sm hover:bg-primary hover:text-white transition-colors">#{{ trim($tag) }}</a>
                            @endforeach
                        </div>
                    @endif

                    {{-- Share Buttons --}}
                    <div
                        class="bg-gray-50 p-6 rounded-xl border border-gray-100 mb-12 flex flex-col sm:flex-row items-center justify-between gap-4">
                        <span class="font-bold text-gray-900">Bagikan Artikel:</span>
                        <div class="flex gap-2">
                            <a href="https://www.facebook.com/sharer/sharer.php?u={{ urlencode(request()->fullUrl()) }}"
                                target="_blank"
                                class="w-10 h-10 rounded-full bg-[#1877F2] text-white flex items-center justify-center hover:opacity-90 transition-opacity"><i
                                    class="fab fa-facebook-f"></i></a>
                            <a href="https://twitter.com/intent/tweet?url={{ urlencode(request()->fullUrl()) }}&text={{ urlencode($post->title) }}"
                                target="_blank"
                                class="w-10 h-10 rounded-full bg-[#1DA1F2] text-white flex items-center justify-center hover:opacity-90 transition-opacity"><i
                                    class="fab fa-twitter"></i></a>
                            <a href="https://wa.me/?text={{ urlencode($post->title . ' ' . request()->fullUrl()) }}"
                                target="_blank"
                                class="w-10 h-10 rounded-full bg-[#25D366] text-white flex items-center justify-center hover:opacity-90 transition-opacity"><i
                                    class="fab fa-whatsapp"></i></a>
                            <a href="javascript:void(0)"
                                onclick="navigator.clipboard.writeText(window.location.href); alert('Link disalin!');"
                                class="w-10 h-10 rounded-full bg-gray-600 text-white flex items-center justify-center hover:opacity-90 transition-opacity"><i
                                    class="fas fa-link"></i></a>
                        </div>
                    </div>

                    {{-- Related Posts --}}
                    @if($relatedPosts->count() > 0)
                        <div class="mb-12">
                            <h3 class="text-2xl font-bold text-gray-900 mb-6 border-l-4 border-primary pl-4">Artikel Terkait
                            </h3>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                @foreach($relatedPosts as $related)
                                    <a href="{{ route('frontend.post', $related->slug) }}"
                                        class="group flex gap-4 p-4 rounded-xl border border-gray-100 hover:shadow-md transition-all">
                                        <div class="shrink-0 w-24 h-24 rounded-lg overflow-hidden">
                                            <img src="{{ $related->featured_image ? Storage::url($related->featured_image) : asset('images/default-post.jpg') }}"
                                                alt="{{ $related->title }}"
                                                class="w-full h-full object-cover group-hover:scale-110 transition-transform">
                                        </div>
                                        <div>
                                            <h4
                                                class="font-bold text-gray-900 mb-2 line-clamp-2 group-hover:text-primary transition-colors">
                                                {{ $related->title }}
                                            </h4>
                                            <span class="text-xs text-gray-500">{{ $related->created_at->format('d M Y') }}</span>
                                        </div>
                                    </a>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    {{-- Comments (Placeholder) --}}
                    @if(\App\Models\SiteSetting::getValue('enable_comments'))
                        <div>
                            <h3 class="text-2xl font-bold text-gray-900 mb-6 border-l-4 border-primary pl-4">Komentar</h3>
                            <div class="bg-gray-50 p-8 rounded-xl text-center border border-gray-100">
                                <p class="text-gray-500">Fitur komentar belum tersedia.</p>
                            </div>
                        </div>
                    @endif

                </article>

                {{-- Sidebar --}}
                <aside class="w-full lg:w-1/3 space-y-8">
                    {{-- Latest News Widget --}}
                    <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100 sticky top-24">
                        <h3 class="text-lg font-bold text-gray-900 mb-4 pb-2 border-b border-gray-100">Berita Terbaru</h3>
                        <div class="space-y-4">
                            @foreach(\App\Models\Post::published()->where('id', '!=', $post->id)->limit(5)->get() as $latest)
                                <div class="flex gap-4 group">
                                    <div class="shrink-0 w-16 h-16 rounded-lg overflow-hidden relative">
                                        <img src="{{ $latest->featured_image ? Storage::url($latest->featured_image) : asset('images/default-post.jpg') }}"
                                            alt="{{ $latest->title }}"
                                            class="w-full h-full object-cover transition-transform group-hover:scale-110">
                                    </div>
                                    <div>
                                        <h4
                                            class="text-sm font-bold text-gray-900 mb-1 line-clamp-2 group-hover:text-primary transition-colors">
                                            <a href="{{ route('frontend.post', $latest->slug) }}">{{ $latest->title }}</a>
                                        </h4>
                                        <span class="text-xs text-gray-500">{{ $latest->created_at->format('d M Y') }}</span>
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

@push('structured_data')
    <script type="application/ld+json">
        {
          "@context": "https://schema.org",
          "@type": "BlogPosting",
          "mainEntityOfPage": {
            "@type": "WebPage",
            "@id": "{{ route('frontend.post', $post->slug) }}"
          },
          "headline": "{{ $post->title }}",
          "image": "{{ $post->featured_image ? Storage::url($post->featured_image) : asset('images/default-og.jpg') }}",
          "author": {
            "@type": "Person",
            "name": "{{ $post->user->name ?? 'Admin' }}"
          },
          "publisher": {
            "@type": "Organization",
            "name": "{{ \App\Models\SiteSetting::getValue('site_name') }}",
            "logo": {
              "@type": "ImageObject",
              "url": "{{ \App\Models\SiteSetting::getValue('logo') ? Storage::url(\App\Models\SiteSetting::getValue('logo')) : '' }}"
            }
          },
          "datePublished": "{{ $post->published_at ? $post->published_at->toIso8601String() : $post->created_at->toIso8601String() }}",
          "dateModified": "{{ $post->updated_at->toIso8601String() }}"
        }
        </script>
@endpush