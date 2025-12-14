@extends('layouts.app')

@section('title', $post->title)
@section('meta_description', $post->excerpt ?: Str::limit(strip_tags($post->content), 160))
@section('meta_keywords', $post->tags ?: ($post->category ? $post->category->name : 'news, article'))

@section('og_title', $post->title)
@section('og_description', $post->excerpt ?: Str::limit(strip_tags($post->content), 160))
@section('og_image', $post->featured_image_url)
@section('og_url', route('frontend.post', $post->slug))

@section('structured_data')
    <script type="application/ld+json">
    {
      "@context": "https://schema.org",
      "@type": "NewsArticle",
      "headline": "{{ $post->title }}",
      "description": "{{ $post->excerpt ?: Str::limit(strip_tags($post->content), 160) }}",
      "url": "{{ route('frontend.post', $post->slug) }}",
      "datePublished": "{{ ($post->published_at ?? $post->created_at)->toISOString() }}",
      "dateModified": "{{ $post->updated_at->toISOString() }}",
      "author": {
        "@type": "Person",
        "name": "{{ $post->user->name ?? 'Admin' }}"
      },
      "publisher": {
        "@type": "Organization",
        "name": "{{ config('app.name') }}",
        "logo": {
          "@type": "ImageObject",
          "url": "{{ asset('logo.png') }}"
        }
      },
      "mainEntityOfPage": {
        "@type": "WebPage",
        "@id": "{{ route('frontend.post', $post->slug) }}"
      }
      @if($post->featured_image)
          ,"image": {
            "@type": "ImageObject",
            "url": "{{ $post->featured_image_url }}",
            "width": 1200,
            "height": 630
          }
      @endif
    }
    </script>
@endsection

@section('content')
    <article class="bg-white min-h-screen font-sans antialiased text-gray-900">
        {{-- Progress Bar --}}
        <div id="progress-bar" class="fixed top-0 left-0 h-1 bg-blue-600 z-50 transition-all duration-300"
            style="width: 0%"></div>

        {{-- Main Header Section --}}
        <header class="pt-24 pb-12 bg-white">
            <div class="container mx-auto px-4 max-w-4xl text-center">
                {{-- Breadcrumb --}}
                <nav class="flex justify-center mb-8" aria-label="Breadcrumb">
                    <ol class="flex items-center space-x-2 text-sm text-gray-500">
                        <li>
                            <a href="{{ route('frontend.index') }}" class="hover:text-blue-600 transition-colors">Home</a>
                        </li>
                        <li>
                            <svg class="w-3 h-3 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path>
                            </svg>
                        </li>
                        <li>
                            <a href="{{ route('frontend.posts') }}" class="hover:text-blue-600 transition-colors">
                                @if($post->type === 'page') Pages
                                @elseif($post->type === 'video') Videos
                                @elseif($post->type === 'gallery') Gallery
                                @else Berita
                                @endif
                            </a>
                        </li>
                        @if($post->category)
                            <li>
                                <svg class="w-3 h-3 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path>
                                </svg>
                            </li>
                            <li>
                                <a href="{{ route('frontend.category', $post->category->slug) }}" class="text-gray-900 font-medium hover:text-blue-600 transition-colors">
                                    {{ $post->category->name }}
                                </a>
                            </li>
                        @endif
                    </ol>
                </nav>
                {{-- Category Badge --}}
                @if($post->category)
                    <a href="{{ route('frontend.category', $post->category->slug) }}"
                        class="inline-block px-4 py-1.5 mb-6 text-sm font-semibold tracking-wide text-blue-600 uppercase bg-blue-50 rounded-full hover:bg-blue-100 transition-colors">
                        {{ $post->category->name }}
                    </a>
                @endif

                {{-- Title --}}
                <h1 class="mb-6 text-4xl md:text-5xl lg:text-6xl font-extrabold tracking-tight leading-tight text-gray-900">
                    {{ $post->title }}
                </h1>

                {{-- Excerpt --}}
                @if($post->excerpt)
                    <p class="mb-8 text-xl md:text-2xl text-gray-500 max-w-2xl mx-auto leading-relaxed font-light">
                        {{ $post->excerpt }}
                    </p>
                @endif

                {{-- Meta Data --}}
                <div
                    class="flex items-center justify-center space-x-4 text-sm text-gray-600 border-t border-gray-100 pt-8 mt-8">
                    <div class="flex items-center">
                        <div
                            class="w-10 h-10 rounded-full bg-gradient-to-tr from-blue-500 to-indigo-600 flex items-center justify-center text-white font-bold text-lg shadow-md mr-3">
                            {{ substr($post->user->name ?? 'A', 0, 1) }}
                        </div>
                        <div class="text-left">
                            <p class="font-bold text-gray-900">{{ $post->user->name ?? 'Admin' }}</p>
                            <p class="text-xs text-gray-500">Author</p>
                        </div>
                    </div>
                    <span class="text-gray-300">|</span>
                    <div class="text-left">
                        <p class="font-medium text-gray-900">
                            {{ ($post->published_at ?? $post->created_at)->format('d M Y') }}</p>
                        <p class="text-xs text-gray-500">{{ ceil(str_word_count(strip_tags($post->content)) / 200) }} min
                            read</p>
                    </div>
                </div>
            </div>
        </header>

        {{-- Featured Image --}}
        @if($post->featured_image)
            <div class="container mx-auto px-4 mb-16">
                <div class="max-w-5xl mx-auto relative group">
                    <div
                        class="absolute -inset-1 bg-gradient-to-r from-blue-600 to-indigo-600 rounded-2xl blur opacity-20 group-hover:opacity-40 transition duration-1000 group-hover:duration-200">
                    </div>
                    <img src="{{ $post->featured_image_url }}" alt="{{ $post->title }}"
                        class="relative w-full h-auto max-h-[600px] object-cover rounded-xl shadow-2xl">
                </div>
            </div>
        @endif

        {{-- Content Body --}}
        <div class="container mx-auto px-4">
            <div class="max-w-3xl mx-auto">
                <div
                    class="prose prose-lg prose-slate prose-a:text-blue-600 prose-img:rounded-xl prose-img:shadow-lg prose-headings:font-bold prose-blockquote:border-l-4 prose-blockquote:border-blue-600 prose-blockquote:pl-4 prose-blockquote:italic prose-blockquote:bg-gray-50 prose-blockquote:py-2 prose-blockquote:pr-4">
                    {!! $post->content !!}
                </div>

                {{-- Tags --}}
                @if($post->tags)
                    <div class="mt-12 pt-8 border-t border-gray-200">
                        <h4 class="text-sm font-bold text-gray-900 uppercase tracking-wider mb-4">Tags</h4>
                        <div class="flex flex-wrap gap-2">
                            @foreach(explode(',', $post->tags) as $tag)
                                <a href="#"
                                    class="inline-block px-3 py-1 text-sm text-gray-600 bg-gray-100 rounded-full hover:bg-gray-200 transition-colors">
                                    #{{ trim($tag) }}
                                </a>
                            @endforeach
                        </div>
                    </div>
                @endif

                {{-- Share Buttons --}}
                <div class="mt-12 p-8 bg-gray-50 rounded-2xl border border-gray-100 text-center">
                    <h3 class="text-xl font-bold text-gray-900 mb-2">Share this article</h3>
                    <p class="text-gray-500 mb-6">If you liked this article, share it with your friends.</p>
                    <div class="flex justify-center space-x-4">
                        <a href="https://www.facebook.com/sharer/sharer.php?u={{ urlencode(request()->url()) }}"
                            target="_blank"
                            class="p-3 bg-blue-600 text-white rounded-full hover:bg-blue-700 transition-transform hover:-translate-y-1 shadow-md hover:shadow-lg">
                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                                <path
                                    d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z" />
                            </svg>
                        </a>
                        <a href="https://twitter.com/intent/tweet?url={{ urlencode(request()->url()) }}&text={{ urlencode($post->title) }}"
                            target="_blank"
                            class="p-3 bg-sky-500 text-white rounded-full hover:bg-sky-600 transition-transform hover:-translate-y-1 shadow-md hover:shadow-lg">
                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                                <path
                                    d="M23.953 4.57a10 10 0 01-2.825.775 4.958 4.958 0 002.163-2.723c-.951.555-2.005.959-3.127 1.184a4.92 4.92 0 00-8.384 4.482C7.69 8.095 4.067 6.13 1.64 3.162a4.822 4.822 0 00-.666 2.475c0 1.71.87 3.213 2.188 4.096a4.904 4.904 0 01-2.228-.616v.06a4.923 4.923 0 003.946 4.827 4.996 4.996 0 01-2.212.085 4.936 4.936 0 004.604 3.417 9.867 9.867 0 01-6.102 2.105c-.39 0-.779-.023-1.17-.067a13.995 13.995 0 007.557 2.209c9.053 0 13.998-7.496 13.998-13.985 0-.21 0-.42-.015-.63A9.935 9.935 0 0024 4.59z" />
                            </svg>
                        </a>
                        <a href="https://www.linkedin.com/sharing/share-offsite/?url={{ urlencode(request()->url()) }}"
                            target="_blank"
                            class="p-3 bg-blue-700 text-white rounded-full hover:bg-blue-800 transition-transform hover:-translate-y-1 shadow-md hover:shadow-lg">
                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                                <path
                                    d="M20.447 20.452h-3.554v-5.569c0-1.328-.027-3.037-1.852-3.037-1.853 0-2.136 1.445-2.136 2.939v5.667H9.351V9h3.414v1.561h.046c.477-.9 1.637-1.85 3.37-1.85 3.601 0 4.267 2.37 4.267 5.455v6.286zM5.337 7.433c-1.144 0-2.063-.926-2.063-2.065 0-1.138.92-2.063 2.063-2.063 1.14 0 2.064.925 2.064 2.063 0 1.139-.925 2.065-2.064 2.065zm1.782 13.019H3.555V9h3.564v11.452zM22.225 0H1.771C.792 0 0 .774 0 1.729v20.542C0 23.227.792 24 1.771 24h20.451C23.2 24 24 23.227 24 22.271V1.729C24 .774 23.2 0 22.222 0h.003z" />
                            </svg>
                        </a>
                        <button onclick="navigator.clipboard.writeText(window.location.href); alert('Link copied!');"
                            class="p-3 bg-gray-700 text-white rounded-full hover:bg-gray-800 transition-transform hover:-translate-y-1 shadow-md hover:shadow-lg">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1">
                                </path>
                            </svg>
                        </button>
                    </div>
                </div>
            </div>
        </div>

        {{-- Related Posts --}}
        @if($relatedPosts && $relatedPosts->count() > 0)
            <section class="bg-gray-50 py-20 mt-20 border-t border-gray-100">
                <div class="container mx-auto px-4 max-w-6xl">
                    <div class="flex items-center justify-between mb-12">
                        <h2 class="text-3xl font-bold text-gray-900">More stories for you</h2>
                        <a href="{{ route('frontend.posts') }}"
                            class="text-blue-600 hover:text-blue-800 font-medium flex items-center">
                            View all articles
                            <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M17 8l4 4m0 0l-4 4m4-4H3"></path>
                            </svg>
                        </a>
                    </div>

                    <div x-data="{
                            active: 0,
                            items: {{ $relatedPosts->count() }},
                            perPage: 3,
                            get maxIndex() {
                                return Math.max(0, this.items - this.perPage);
                            },
                            next() {
                                if (this.active < this.maxIndex) this.active++;
                            },
                            prev() {
                                if (this.active > 0) this.active--;
                            },
                            updatePerPage() {
                                if (window.innerWidth < 768) this.perPage = 1;
                                else if (window.innerWidth < 1024) this.perPage = 2;
                                else this.perPage = 3;
                                // Reset active if out of bounds
                                if (this.active > this.maxIndex) this.active = this.maxIndex;
                            }
                        }"
                        x-init="updatePerPage(); window.addEventListener('resize', () => updatePerPage())"
                        class="relative">
                        
                        <!-- Navigation Buttons -->
                        <div class="absolute -top-16 right-0 md:right-auto md:left-[200px] flex space-x-2">
                            <button @click="prev()" 
                                :class="{ 'opacity-50 cursor-not-allowed': active === 0, 'hover:bg-blue-50 text-blue-600': active > 0 }"
                                :disabled="active === 0"
                                class="p-2 rounded-full border border-gray-200 transition-colors">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                                </svg>
                            </button>
                            <button @click="next()" 
                                :class="{ 'opacity-50 cursor-not-allowed': active >= maxIndex, 'hover:bg-blue-50 text-blue-600': active < maxIndex }"
                                :disabled="active >= maxIndex"
                                class="p-2 rounded-full border border-gray-200 transition-colors">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                                </svg>
                            </button>
                        </div>

                        <!-- Carousel Track -->
                        <div class="overflow-hidden">
                            <div class="flex transition-transform duration-500 ease-in-out"
                                :style="`transform: translateX(-${active * (100 / perPage)}%)`">
                                @foreach($relatedPosts as $relatedPost)
                                    <div class="w-full md:w-1/2 lg:w-1/3 flex-shrink-0 px-4" style="flex: 0 0 auto;">
                                        <div class="h-full">
                                            <a href="{{ route('frontend.post', $relatedPost->slug) }}"
                                                class="group block bg-white rounded-2xl overflow-hidden shadow-sm hover:shadow-xl transition-all duration-300 h-full flex flex-col">
                                                @if($relatedPost->featured_image)
                                                    <div class="relative h-48 overflow-hidden">
                                                        <img src="{{ $relatedPost->featured_image_url }}" alt="{{ $relatedPost->title }}"
                                                            class="w-full h-full object-cover transform group-hover:scale-110 transition-transform duration-700">
                                                        @if($relatedPost->category)
                                                            <span
                                                                class="absolute top-4 left-4 bg-white/90 backdrop-blur-sm text-gray-900 text-xs font-bold px-3 py-1 rounded-full shadow-sm">
                                                                {{ $relatedPost->category->name }}
                                                            </span>
                                                        @endif
                                                    </div>
                                                @endif
                                                <div class="p-6 flex-1 flex flex-col">
                                                    <p class="text-xs text-gray-500 mb-2 font-medium">
                                                        {{ ($relatedPost->published_at ?? $relatedPost->created_at)->format('M d, Y') }}</p>
                                                    <h3
                                                        class="text-xl font-bold text-gray-900 mb-3 group-hover:text-blue-600 transition-colors line-clamp-2 leading-tight">
                                                        {{ $relatedPost->title }}
                                                    </h3>
                                                    <p class="text-gray-600 line-clamp-3 text-sm leading-relaxed mb-4 flex-1">
                                                        {{ $relatedPost->excerpt ?: Str::limit(strip_tags($relatedPost->content), 100) }}
                                                    </p>
                                                </div>
                                            </a>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>
            </section>
        @endif
    </article>
@endsection

@push('scripts')
    <script>
        // Progress Bar
        window.onscroll = function () {
            let winScroll = document.body.scrollTop || document.documentElement.scrollTop;
            let height = document.documentElement.scrollHeight - document.documentElement.clientHeight;
            let scrolled = (winScroll / height) * 100;
            document.getElementById("progress-bar").style.width = scrolled + "%";
        };

        // Update view count
        fetch('{{ route("api.posts.view", $post->id) }}', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Content-Type': 'application/json',
            },
        }).catch(error => console.log('View count update failed:', error));
    </script>
@endpush