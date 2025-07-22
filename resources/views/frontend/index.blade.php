@extends('layouts.app')

@section('title', ($siteSettings['site_name'] ?? 'Home') . ' - ' . ($siteSettings['site_description'] ?? 'Latest News & Updates'))
@section('meta_description', $siteSettings['seo_meta_description'] ?? 'Stay updated with the latest news, featured articles, and trending stories. Your trusted source for comprehensive news coverage and insights.')
@section('meta_keywords', $siteSettings['seo_meta_keywords'] ?? 'news, latest news, featured articles, breaking news, updates, current events')

@section('structured_data')
<script type="application/ld+json">
{
  "@context": "https://schema.org",
  "@type": "WebSite",
  "name": "{{ $siteSettings['site_name'] ?? config('app.name') }}",
  "url": "{{ $siteSettings['site_url'] ?? url('/') }}",
  "description": "{{ $siteSettings['seo_meta_description'] ?? 'Your trusted source for the latest news, insights, and stories that matter.' }}",
  "potentialAction": {
    "@type": "SearchAction",
    "target": {
      "@type": "EntryPoint",
      "urlTemplate": "{{ route('frontend.posts') }}?search={search_term_string}"
    },
    "query-input": "required name=search_term_string"
  },
  "publisher": {
    "@type": "Organization",
    "name": "{{ $siteSettings['site_name'] ?? config('app.name') }}",
    "url": "{{ $siteSettings['site_url'] ?? url('/') }}"
  }
}
</script>

@if($sliderPosts->count() > 0)
<script type="application/ld+json">
{
  "@context": "https://schema.org",
  "@type": "ItemList",
  "name": "Featured News Articles",
  "itemListElement": [
    @foreach($sliderPosts as $index => $post)
    {
      "@type": "ListItem",
      "position": {{ $index + 1 }},
      "item": {
        "@type": "NewsArticle",
        "headline": "{{ $post->title }}",
        "description": "{{ Str::limit(strip_tags($post->content), 160) }}",
        "url": "{{ route('frontend.post', $post->slug) }}",
        "datePublished": "{{ ($post->published_at ?? $post->created_at)->toISOString() }}",
        "dateModified": "{{ $post->updated_at->toISOString() }}",
        "author": {
          "@type": "Person",
          "name": "{{ $post->user->name ?? 'Admin' }}"
        },
        "publisher": {
          "@type": "Organization",
          "name": "{{ $siteSettings['site_name'] ?? config('app.name') }}",
          "url": "{{ $siteSettings['site_url'] ?? url('/') }}"
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
    }@if(!$loop->last),@endif
    @endforeach
  ]
}
</script>
@endif
@endsection

@section('content')
{{-- Hero Slider Section --}}
@if($sliderPosts->count() > 0)
    @include('components.slider', ['posts' => $sliderPosts])
@endif

{{-- Featured Posts Section --}}
@if($featuredPosts->count() > 0)
    @include('components.hero', ['posts' => $featuredPosts])
@endif

{{-- Main Content Area --}}
<main class="container mx-auto px-4 py-12">
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        {{-- Latest Posts --}}
        <div class="lg:col-span-2">
            <div class="flex items-center justify-between mb-8">
                <h2 class="text-3xl font-bold text-gray-900">
                    Latest Articles
                </h2>
                <a href="{{ route('frontend.posts') }}" 
                   class="inline-flex items-center text-blue-600 hover:text-blue-800 font-medium transition-colors">
                    View All
                    <svg class="w-5 h-5 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"></path>
                    </svg>
                </a>
            </div>
            
            @if($latestPosts->count() > 0)
                <div class="space-y-8">
                    @foreach($latestPosts as $post)
                        <article class="bg-white rounded-lg shadow-md overflow-hidden hover:shadow-lg transition-shadow">
                            <div class="md:flex">
                                @if($post->featured_image)
                                    <div class="md:w-1/3">
                                        <img src="{{ $post->featured_image_url }}" 
                                             alt="{{ $post->title }}"
                                             class="w-full h-48 md:h-full object-cover">
                                    </div>
                                @endif
                                
                                <div class="p-6 {{ $post->featured_image ? 'md:w-2/3' : 'w-full' }}">
                                    {{-- Category Badge --}}
                                    @if($post->category)
                                        <span class="inline-block bg-blue-100 text-blue-800 text-xs font-semibold px-2.5 py-0.5 rounded-full mb-3">
                                            {{ $post->category->name }}
                                        </span>
                                    @endif
                                    
                                    <h3 class="text-xl font-bold text-gray-900 mb-3 hover:text-blue-600 transition-colors">
                                        <a href="{{ route('frontend.post', $post->slug) }}">
                                            {{ $post->title }}
                                        </a>
                                    </h3>
                                    
                                    <p class="text-gray-600 mb-4 leading-relaxed">
                                        {{ Str::limit(strip_tags($post->content), 150) }}
                                    </p>
                                    
                                    {{-- Meta Info --}}
                                    <div class="flex items-center justify-between text-sm text-gray-500">
                                        <div class="flex items-center space-x-4">
                                            <span class="flex items-center">
                                                <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                                    <path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd"></path>
                                                </svg>
                                                {{ $post->user->name ?? 'Admin' }}
                                            </span>
                                            <span class="flex items-center">
                                                <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                                    <path fill-rule="evenodd" d="M6 2a1 1 0 00-1 1v1H4a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V6a2 2 0 00-2-2h-1V3a1 1 0 10-2 0v1H7V3a1 1 0 00-1-1zm0 5a1 1 0 000 2h8a1 1 0 100-2H6z" clip-rule="evenodd"></path>
                                                </svg>
                                                {{ ($post->published_at ?? $post->created_at)->format('M d, Y') }}
                                            </span>
                                            <span class="flex items-center">
                                                <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                                    <path d="M10 12a2 2 0 100-4 2 2 0 000 4z"></path>
                                                    <path fill-rule="evenodd" d="M.458 10C1.732 5.943 5.522 3 10 3s8.268 2.943 9.542 7c-1.274 4.057-5.064 7-9.542 7S1.732 14.057.458 10zM14 10a4 4 0 11-8 0 4 4 0 018 0z" clip-rule="evenodd"></path>
                                                </svg>
                                                {{ $post->views ?? 0 }} views
                                            </span>
                                        </div>
                                        
                                        <a href="{{ route('frontend.post', $post->slug) }}" 
                                           class="text-blue-600 hover:text-blue-800 font-medium transition-colors">
                                            Read More →
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </article>
                    @endforeach
                </div>
            @else
                <div class="text-center py-12">
                    <svg class="w-16 h-16 mx-auto text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z"></path>
                    </svg>
                    <h3 class="text-lg font-medium text-gray-900 mb-2">No Articles Yet</h3>
                    <p class="text-gray-500">Check back later for the latest news and updates.</p>
                </div>
            @endif
        </div>
        
        {{-- Sidebar --}}
        <div class="lg:col-span-1">
            {{-- Popular Posts --}}
            @if($popularPosts->count() > 0)
                <div class="bg-white rounded-lg shadow-md p-6 mb-8">
                    <h3 class="text-xl font-bold text-gray-900 mb-6 flex items-center">
                        <svg class="w-6 h-6 mr-2 text-red-500" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path>
                        </svg>
                        Popular Posts
                    </h3>
                    
                    <div class="space-y-4">
                        @foreach($popularPosts as $index => $post)
                            <div class="flex items-start space-x-3">
                                <span class="flex-shrink-0 w-8 h-8 bg-blue-600 text-white rounded-full flex items-center justify-center text-sm font-bold">
                                    {{ $index + 1 }}
                                </span>
                                <div class="flex-1 min-w-0">
                                    <h4 class="text-sm font-medium text-gray-900 hover:text-blue-600 transition-colors">
                                        <a href="{{ route('frontend.post', $post->slug) }}" class="line-clamp-2">
                                            {{ $post->title }}
                                        </a>
                                    </h4>
                                    <div class="flex items-center mt-1 text-xs text-gray-500">
                                        <span>{{ ($post->published_at ?? $post->created_at)->format('M d') }}</span>
                                        <span class="mx-1">•</span>
                                        <span>{{ $post->views ?? 0 }} views</span>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif
            
            {{-- Categories --}}
            @if($categories->count() > 0)
                <div class="bg-white rounded-lg shadow-md p-6 mb-8">
                    <h3 class="text-xl font-bold text-gray-900 mb-6 flex items-center">
                        <svg class="w-6 h-6 mr-2 text-green-500" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M7 3a1 1 0 000 2h6a1 1 0 100-2H7zM4 7a1 1 0 011-1h10a1 1 0 110 2H5a1 1 0 01-1-1zM2 11a2 2 0 012-2h12a2 2 0 012 2v4a2 2 0 01-2 2H4a2 2 0 01-2-2v-4z"></path>
                        </svg>
                        Categories
                    </h3>
                    
                    <div class="space-y-2">
                        @foreach($categories as $category)
                            <a href="{{ route('frontend.category', $category->slug) }}" 
                               class="flex items-center justify-between p-3 rounded-lg hover:bg-gray-50 transition-colors group">
                                <span class="text-gray-700 group-hover:text-blue-600 transition-colors">
                                    {{ $category->name }}
                                </span>
                                <span class="text-sm text-gray-500 bg-gray-100 px-2 py-1 rounded-full">
                                    {{ $category->posts_count ?? 0 }}
                                </span>
                            </a>
                        @endforeach
                    </div>
                </div>
            @endif
            
            {{-- Newsletter Signup --}}
            <div class="bg-gradient-to-br from-blue-600 to-blue-700 rounded-lg shadow-md p-6 text-white">
                <h3 class="text-xl font-bold mb-4">Stay Updated</h3>
                <p class="text-blue-100 mb-6">Subscribe to our newsletter and never miss the latest news and updates.</p>
                
                <form action="#" method="POST" class="space-y-4">
                    @csrf
                    <div>
                        <input type="email" 
                               name="email" 
                               placeholder="Your email address"
                               class="w-full px-4 py-3 rounded-lg bg-white/10 border border-white/20 text-white placeholder-blue-200 focus:outline-none focus:ring-2 focus:ring-white/50 focus:border-transparent">
                    </div>
                    <button type="submit" 
                            class="w-full bg-white text-blue-600 font-semibold py-3 px-4 rounded-lg hover:bg-blue-50 transition-colors">
                        Subscribe Now
                    </button>
                </form>
                
                <p class="text-xs text-blue-200 mt-4">
                    We respect your privacy. Unsubscribe at any time.
                </p>
            </div>
        </div>
    </div>
</main>

{{-- Photo Gallery Section --}}
@if($galleryImages->count() > 0)
    @include('components.photo-gallery', ['images' => $galleryImages])
@endif
@endsection

@push('styles')
<style>
.line-clamp-2 {
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
}
</style>
@endpush