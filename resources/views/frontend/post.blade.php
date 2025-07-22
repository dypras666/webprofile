@extends('layouts.app')

@section('title', $post->title)
@section('meta_description', $post->excerpt ?: Str::limit(strip_tags($post->content), 160))
@section('meta_keywords', $post->tags ?: ($post->category ? $post->category->name : 'news, article'))

{{-- Open Graph Meta Tags --}}
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
    "url": "{{ url('/') }}"
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
  @if($post->category)
  ,"articleSection": "{{ $post->category->name }}"
  @endif
}
</script>
@endsection

@section('content')
{{-- Breadcrumb --}}
<nav class="bg-gray-50 py-4">
    <div class="container mx-auto px-4">
        <ol class="flex items-center space-x-2 text-sm text-gray-600">
            <li>
                <a href="{{ route('frontend.index') }}" class="hover:text-blue-600 transition-colors">
                    Home
                </a>
            </li>
            <li>
                <svg class="w-4 h-4 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path>
                </svg>
            </li>
            <li>
                <a href="{{ route('frontend.posts') }}" class="hover:text-blue-600 transition-colors">
                    @switch($post->type)
                        @case('page')
                            Pages
                            @break
                        @case('video')
                            Videos
                            @break
                        @case('gallery')
                            Gallery
                            @break
                        @default
                            Articles
                    @endswitch
                </a>
            </li>
            @if($post->category)
                <li>
                    <svg class="w-4 h-4 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path>
                    </svg>
                </li>
                <li>
                    <a href="{{ route('frontend.category', $post->category->slug) }}" class="hover:text-blue-600 transition-colors">
                        {{ $post->category->name }}
                    </a>
                </li>
            @endif
            <li>
                <svg class="w-4 h-4 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path>
                </svg>
            </li>
            <li class="text-gray-900 font-medium truncate">
                {{ Str::limit($post->title, 50) }}
            </li>
        </ol>
    </div>
</nav>

{{-- Article Header --}}
<article class="bg-white">
    <header class="container mx-auto px-4 py-12">
        <div class="max-w-4xl mx-auto">
            {{-- Category & Featured Badges --}}
            <div class="flex items-center space-x-3 mb-6">
                @if($post->category)
                    <span class="inline-block bg-blue-600 text-white text-sm font-semibold px-4 py-2 rounded-full">
                        {{ $post->category->name }}
                    </span>
                @endif
                
                @if($post->is_featured)
                    <span class="inline-block bg-yellow-500 text-white text-sm font-semibold px-4 py-2 rounded-full">
                        Featured
                    </span>
                @endif
                
                @if($post->is_slider)
                    <span class="inline-block bg-green-500 text-white text-sm font-semibold px-4 py-2 rounded-full">
                        Trending
                    </span>
                @endif
            </div>
            
            {{-- Title --}}
            <h1 class="text-4xl md:text-5xl font-bold text-gray-900 leading-tight mb-6">
                {{ $post->title }}
            </h1>
            
            {{-- Excerpt --}}
            @if($post->excerpt)
                <p class="text-xl text-gray-600 leading-relaxed mb-8">
                    {{ $post->excerpt }}
                </p>
            @endif
            
            {{-- Meta Information --}}
            <div class="flex flex-col md:flex-row md:items-center md:justify-between border-t border-b border-gray-200 py-6">
                <div class="flex items-center space-x-6 mb-4 md:mb-0">
                    {{-- Author --}}
                    <div class="flex items-center">
                        <div class="w-12 h-12 bg-blue-600 rounded-full flex items-center justify-center text-white font-bold text-lg mr-3">
                            {{ substr($post->user->name ?? 'A', 0, 1) }}
                        </div>
                        <div>
                            <p class="font-semibold text-gray-900">{{ $post->user->name ?? 'Admin' }}</p>
                            <p class="text-sm text-gray-500">Author</p>
                        </div>
                    </div>
                    
                    {{-- Date --}}
                    <div class="flex items-center text-gray-600">
                        <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M6 2a1 1 0 00-1 1v1H4a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V6a2 2 0 00-2-2h-1V3a1 1 0 10-2 0v1H7V3a1 1 0 00-1-1zm0 5a1 1 0 000 2h8a1 1 0 100-2H6z" clip-rule="evenodd"></path>
                        </svg>
                        <div>
                            <p class="font-medium">{{ ($post->published_at ?? $post->created_at)->format('F d, Y') }}</p>
                            <p class="text-sm text-gray-500">{{ ($post->published_at ?? $post->created_at)->diffForHumans() }}</p>
                        </div>
                    </div>
                </div>
                
                {{-- Stats & Share --}}
                <div class="flex items-center space-x-6">
                    {{-- Views --}}
                    <div class="flex items-center text-gray-600">
                        <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M10 12a2 2 0 100-4 2 2 0 000 4z"></path>
                            <path fill-rule="evenodd" d="M.458 10C1.732 5.943 5.522 3 10 3s8.268 2.943 9.542 7c-1.274 4.057-5.064 7-9.542 7S1.732 14.057.458 10zM14 10a4 4 0 11-8 0 4 4 0 018 0z" clip-rule="evenodd"></path>
                        </svg>
                        <span class="font-medium">{{ number_format($post->views ?? 0) }} views</span>
                    </div>
                    
                    {{-- Reading Time --}}
                    <div class="flex items-center text-gray-600">
                        <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd"></path>
                        </svg>
                        <span class="font-medium">{{ ceil(str_word_count(strip_tags($post->content)) / 200) }} min read</span>
                    </div>
                    
                    {{-- Share Button --}}
                    <button onclick="toggleShareMenu()" class="flex items-center px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.684 13.342C8.886 12.938 9 12.482 9 12c0-.482-.114-.938-.316-1.342m0 2.684a3 3 0 110-2.684m0 2.684l6.632 3.316m-6.632-6l6.632-3.316m0 0a3 3 0 105.367-2.684 3 3 0 00-5.367 2.684zm0 9.316a3 3 0 105.367 2.684 3 3 0 00-5.367-2.684z"></path>
                        </svg>
                        Share
                    </button>
                </div>
            </div>
        </div>
    </header>
    
    {{-- Featured Image --}}
    @if($post->featured_image)
        <div class="container mx-auto px-4 mb-12">
            <div class="max-w-4xl mx-auto">
                <img src="{{ $post->featured_image_url }}" 
                     alt="{{ $post->title }}"
                     class="w-full h-auto rounded-lg shadow-lg">
            </div>
        </div>
    @endif
    
    {{-- Article Content --}}
    <div class="container mx-auto px-4 pb-12">
        <div class="max-w-4xl mx-auto">
            <div class="prose prose-lg max-w-none">
                {!! $post->content !!}
            </div>
            
            {{-- Gallery Images --}}
            @if($post->type === 'gallery')
                {{-- Debug Info --}}
                @php
                    \Log::info('=== FRONTEND GALLERY DEBUG ===');
                    \Log::info('Post ID: ' . $post->id);
                    \Log::info('Post Type: ' . $post->type);
                    \Log::info('Gallery Images Raw: ' . $post->getRawOriginal('gallery_images'));
                    \Log::info('Gallery Images Cast: ', ['gallery_images' => $post->gallery_images]);
                    \Log::info('Gallery Images Count: ' . (is_array($post->gallery_images) ? count($post->gallery_images) : 'not array'));
                @endphp
                
                @if($post->gallery_images && count($post->gallery_images) > 0)
                    <div class="mt-12 pt-8 border-t border-gray-200">
                        <h3 class="text-lg font-semibold text-gray-900 mb-6">Gallery ({{ count($post->gallery_images) }} images)</h3>
                        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-4">
                            @foreach($post->gallery_images as $index => $image)
                                @php
                                    \Log::info('Image ' . $index . ': ' . $image);
                                @endphp
                                <div class="group relative overflow-hidden rounded-lg shadow-md hover:shadow-lg transition-all duration-300">
                                    <img src="{{ asset('storage/' . $image) }}" 
                                         alt="Gallery image {{ $index + 1 }}"
                                         class="w-full h-64 object-cover group-hover:scale-105 transition-transform duration-300"
                                         onerror="console.log('Failed to load image: {{ asset('storage/' . $image) }}')">
                                    
                                    {{-- Overlay --}}
                                    <div class="absolute inset-0 bg-black bg-opacity-0 group-hover:bg-opacity-40 transition-all duration-300 flex items-center justify-center">
                                        <div class="opacity-0 group-hover:opacity-100 transition-opacity duration-300">
                                            <svg class="w-12 h-12 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                                            </svg>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @else
                    <div class="mt-12 pt-8 border-t border-gray-200">
                        <h3 class="text-lg font-semibold text-gray-900 mb-6">Gallery</h3>
                        <p class="text-gray-500">No gallery images found. Debug: gallery_images = {{ json_encode($post->gallery_images) }}</p>
                    </div>
                @endif
            @endif
            
            {{-- Tags --}}
            @if($post->tags)
                <div class="mt-12 pt-8 border-t border-gray-200">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Tags</h3>
                    <div class="flex flex-wrap gap-2">
                        @foreach(explode(',', $post->tags) as $tag)
                            <span class="inline-block bg-gray-100 text-gray-700 text-sm px-3 py-1 rounded-full hover:bg-gray-200 transition-colors">
                                #{{ trim($tag) }}
                            </span>
                        @endforeach
                    </div>
                </div>
            @endif
            
            {{-- Share Menu --}}
            <div id="shareMenu" class="hidden mt-8 p-6 bg-gray-50 rounded-lg">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Share this article</h3>
                <div class="flex flex-wrap gap-4">
                    <a href="https://www.facebook.com/sharer/sharer.php?u={{ urlencode(route('frontend.post', $post->slug)) }}" 
                       target="_blank" 
                       class="flex items-center px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                        <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/>
                        </svg>
                        Facebook
                    </a>
                    
                    <a href="https://twitter.com/intent/tweet?url={{ urlencode(route('frontend.post', $post->slug)) }}&text={{ urlencode($post->title) }}" 
                       target="_blank" 
                       class="flex items-center px-4 py-2 bg-blue-400 text-white rounded-lg hover:bg-blue-500 transition-colors">
                        <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M23.953 4.57a10 10 0 01-2.825.775 4.958 4.958 0 002.163-2.723c-.951.555-2.005.959-3.127 1.184a4.92 4.92 0 00-8.384 4.482C7.69 8.095 4.067 6.13 1.64 3.162a4.822 4.822 0 00-.666 2.475c0 1.71.87 3.213 2.188 4.096a4.904 4.904 0 01-2.228-.616v.06a4.923 4.923 0 003.946 4.827 4.996 4.996 0 01-2.212.085 4.936 4.936 0 004.604 3.417 9.867 9.867 0 01-6.102 2.105c-.39 0-.779-.023-1.17-.067a13.995 13.995 0 007.557 2.209c9.053 0 13.998-7.496 13.998-13.985 0-.21 0-.42-.015-.63A9.935 9.935 0 0024 4.59z"/>
                        </svg>
                        Twitter
                    </a>
                    
                    <a href="https://www.linkedin.com/sharing/share-offsite/?url={{ urlencode(route('frontend.post', $post->slug)) }}" 
                       target="_blank" 
                       class="flex items-center px-4 py-2 bg-blue-700 text-white rounded-lg hover:bg-blue-800 transition-colors">
                        <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M20.447 20.452h-3.554v-5.569c0-1.328-.027-3.037-1.852-3.037-1.853 0-2.136 1.445-2.136 2.939v5.667H9.351V9h3.414v1.561h.046c.477-.9 1.637-1.85 3.37-1.85 3.601 0 4.267 2.37 4.267 5.455v6.286zM5.337 7.433c-1.144 0-2.063-.926-2.063-2.065 0-1.138.92-2.063 2.063-2.063 1.14 0 2.064.925 2.064 2.063 0 1.139-.925 2.065-2.064 2.065zm1.782 13.019H3.555V9h3.564v11.452zM22.225 0H1.771C.792 0 0 .774 0 1.729v20.542C0 23.227.792 24 1.771 24h20.451C23.2 24 24 23.227 24 22.271V1.729C24 .774 23.2 0 22.222 0h.003z"/>
                        </svg>
                        LinkedIn
                    </a>
                    
                    <button onclick="copyToClipboard()" class="flex items-center px-4 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700 transition-colors">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"></path>
                        </svg>
                        Copy Link
                    </button>
                </div>
            </div>
        </div>
    </div>
</article>

{{-- Related Posts --}}
@if($relatedPosts && $relatedPosts->count() > 0)
    <section class="bg-gray-50 py-16">
        <div class="container mx-auto px-4">
            <div class="max-w-6xl mx-auto">
                <h2 class="text-3xl font-bold text-gray-900 mb-8 text-center">Related Articles</h2>
                
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                    @foreach($relatedPosts as $relatedPost)
                        <article class="bg-white rounded-lg shadow-md overflow-hidden hover:shadow-lg transition-shadow group">
                            @if($relatedPost->featured_image)
                                <div class="relative overflow-hidden">
                                    <img src="{{ $relatedPost->featured_image_url }}" 
                                         alt="{{ $relatedPost->title }}"
                                         class="w-full h-48 object-cover group-hover:scale-105 transition-transform duration-300">
                                    
                                    @if($relatedPost->category)
                                        <span class="absolute top-4 left-4 bg-blue-600 text-white text-xs font-semibold px-3 py-1 rounded-full">
                                            {{ $relatedPost->category->name }}
                                        </span>
                                    @endif
                                </div>
                            @endif
                            
                            <div class="p-6">
                                <h3 class="text-lg font-bold text-gray-900 mb-3 group-hover:text-blue-600 transition-colors">
                                    <a href="{{ route('frontend.post', $relatedPost->slug) }}" class="line-clamp-2">
                                        {{ $relatedPost->title }}
                                    </a>
                                </h3>
                                
                                <p class="text-gray-600 mb-4 line-clamp-2">
                                    {{ $relatedPost->excerpt ?: Str::limit(strip_tags($relatedPost->content), 100) }}
                                </p>
                                
                                <div class="flex items-center justify-between text-sm text-gray-500">
                                    <span>{{ ($relatedPost->published_at ?? $relatedPost->created_at)->format('M d, Y') }}</span>
                                    <span>{{ $relatedPost->views ?? 0 }} views</span>
                                </div>
                            </div>
                        </article>
                    @endforeach
                </div>
            </div>
        </div>
    </section>
@endif
@endsection

@push('styles')
<style>
.prose {
    color: #374151;
    line-height: 1.75;
}

.prose h1, .prose h2, .prose h3, .prose h4, .prose h5, .prose h6 {
    color: #111827;
    font-weight: 700;
    margin-top: 2rem;
    margin-bottom: 1rem;
}

.prose h1 { font-size: 2.25rem; }
.prose h2 { font-size: 1.875rem; }
.prose h3 { font-size: 1.5rem; }
.prose h4 { font-size: 1.25rem; }

.prose p {
    margin-bottom: 1.5rem;
}

.prose a {
    color: #2563eb;
    text-decoration: underline;
}

.prose a:hover {
    color: #1d4ed8;
}

.prose ul, .prose ol {
    margin-bottom: 1.5rem;
    padding-left: 1.5rem;
}

.prose li {
    margin-bottom: 0.5rem;
}

.prose blockquote {
    border-left: 4px solid #e5e7eb;
    padding-left: 1rem;
    margin: 1.5rem 0;
    font-style: italic;
    color: #6b7280;
}

.prose img {
    margin: 2rem 0;
    border-radius: 0.5rem;
    box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1);
}

.prose code {
    background-color: #f3f4f6;
    padding: 0.25rem 0.5rem;
    border-radius: 0.25rem;
    font-size: 0.875rem;
}

.prose pre {
    background-color: #1f2937;
    color: #f9fafb;
    padding: 1rem;
    border-radius: 0.5rem;
    overflow-x: auto;
    margin: 1.5rem 0;
}

.prose pre code {
    background-color: transparent;
    padding: 0;
    color: inherit;
}

.line-clamp-2 {
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
}
</style>
@endpush

@push('scripts')
<script>
function toggleShareMenu() {
    const menu = document.getElementById('shareMenu');
    menu.classList.toggle('hidden');
}

function copyToClipboard() {
    const url = window.location.href;
    navigator.clipboard.writeText(url).then(function() {
        alert('Link copied to clipboard!');
    }, function(err) {
        console.error('Could not copy text: ', err);
    });
}

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