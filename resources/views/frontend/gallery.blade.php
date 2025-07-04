@extends('layouts.app')

@section('title', 'Photo Gallery - Latest Images')
@section('meta_description', 'Browse our photo gallery featuring the latest images, news photos, and visual stories. Discover compelling photography and visual content.')
@section('meta_keywords', 'photo gallery, images, photos, news photos, visual stories, photography')

@section('structured_data')
<script type="application/ld+json">
{
  "@context": "https://schema.org",
  "@type": "ImageGallery",
  "name": "{{ config('app.name') }} Photo Gallery",
  "description": "Browse our photo gallery featuring the latest images, news photos, and visual stories.",
  "url": "{{ route('frontend.gallery') }}",
  "publisher": {
    "@type": "Organization",
    "name": "{{ config('app.name') }}",
    "url": "{{ url('/') }}"
  }
  @if($images->count() > 0)
  ,"image": [
    @foreach($images->take(10) as $image)
    {
      "@type": "ImageObject",
      "url": "{{ $image->url }}",
      "name": "{{ $image->title ?: 'Gallery Image' }}",
      "description": "{{ $image->description ?: 'Image from our photo gallery' }}"
    }@if(!$loop->last),@endif
    @endforeach
  ]
  @endif
}
</script>
@endsection

@section('content')
{{-- Page Header --}}
<div class="bg-gradient-to-r from-purple-600 to-blue-600 text-white py-16">
    <div class="container mx-auto px-4">
        <div class="max-w-4xl mx-auto text-center">
            <h1 class="text-4xl md:text-5xl font-bold mb-4">Photo Gallery</h1>
            <p class="text-xl text-purple-100 mb-8">
                Discover our collection of stunning photography and visual stories
            </p>
            
            {{-- Stats --}}
            <div class="flex justify-center space-x-8 text-center">
                <div>
                    <div class="text-3xl font-bold">{{ $images->total() }}</div>
                    <div class="text-purple-200">Total Images</div>
                </div>
                <div>
                    <div class="text-3xl font-bold">{{ $categories->count() }}</div>
                    <div class="text-purple-200">Categories</div>
                </div>
                <div>
                    <div class="text-3xl font-bold">{{ $images->where('created_at', '>=', now()->subMonth())->count() }}</div>
                    <div class="text-purple-200">This Month</div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Filter Tabs --}}
<div class="bg-white border-b sticky top-0 z-30">
    <div class="container mx-auto px-4">
        <div class="flex flex-wrap items-center justify-between py-4">
            {{-- Category Filters --}}
            <div class="flex flex-wrap items-center space-x-2 mb-4 md:mb-0">
                <span class="text-gray-600 font-medium mr-4">Categories:</span>
                <a href="{{ route('frontend.gallery') }}" 
                   class="px-4 py-2 rounded-full text-sm font-medium transition-colors {{ !request('category') ? 'bg-purple-600 text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200' }}">
                    All Images
                </a>
                @foreach($categories as $category)
                    <a href="{{ route('frontend.gallery', ['category' => $category->slug]) }}" 
                       class="px-4 py-2 rounded-full text-sm font-medium transition-colors {{ request('category') == $category->slug ? 'bg-purple-600 text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200' }}">
                        {{ $category->name }}
                    </a>
                @endforeach
            </div>
            
            {{-- View Toggle --}}
            <div class="flex items-center space-x-2">
                <span class="text-gray-600 font-medium">View:</span>
                <button onclick="setView('grid')" 
                        class="p-2 rounded-lg transition-colors view-toggle {{ request('view', 'grid') == 'grid' ? 'bg-purple-600 text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200' }}">
                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M5 3a2 2 0 00-2 2v2a2 2 0 002 2h2a2 2 0 002-2V5a2 2 0 00-2-2H5zM5 11a2 2 0 00-2 2v2a2 2 0 002 2h2a2 2 0 002-2v-2a2 2 0 00-2-2H5zM11 5a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V5zM11 13a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"></path>
                    </svg>
                </button>
                <button onclick="setView('masonry')" 
                        class="p-2 rounded-lg transition-colors view-toggle {{ request('view') == 'masonry' ? 'bg-purple-600 text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200' }}">
                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M3 4a1 1 0 011-1h12a1 1 0 011 1v2a1 1 0 01-1 1H4a1 1 0 01-1-1V4zM3 10a1 1 0 011-1h6a1 1 0 011 1v6a1 1 0 01-1 1H4a1 1 0 01-1-1v-6zM14 9a1 1 0 00-1 1v6a1 1 0 001 1h2a1 1 0 001-1v-6a1 1 0 00-1-1h-2z"></path>
                    </svg>
                </button>
            </div>
        </div>
    </div>
</div>

{{-- Gallery Content --}}
<main class="container mx-auto px-4 py-12">
    @if($images->count() > 0)
        {{-- Grid View --}}
        <div id="gridView" class="gallery-view {{ request('view', 'grid') == 'grid' ? '' : 'hidden' }}">
            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">
                @foreach($images as $post)
                    <div class="group">
                        <div class="relative overflow-hidden rounded-lg bg-gray-200 aspect-square">
                            <img src="{{ $post->featured_image ? asset('storage/' . $post->featured_image) : asset('images/placeholder.jpg') }}" 
                                 alt="{{ $post->title }}"
                                 class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-300">
                            
                            {{-- Image Info --}}
                            <div class="absolute bottom-0 left-0 right-0 bg-gradient-to-t from-black/70 to-transparent p-4 opacity-0 group-hover:opacity-100 transition-opacity duration-300">
                                <h3 class="text-white font-semibold text-sm mb-1">{{ $post->title }}</h3>
                                <p class="text-white/80 text-xs">{{ $post->created_at->format('M d, Y') }}</p>
                                @if($post->category)
                                    <span class="inline-block bg-purple-600 text-white text-xs px-2 py-1 rounded mt-1">{{ $post->category->name }}</span>
                                @endif
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
        
        {{-- Masonry View --}}
        <div id="masonryView" class="gallery-view {{ request('view') == 'masonry' ? '' : 'hidden' }}">
            <div class="columns-1 sm:columns-2 md:columns-3 lg:columns-4 gap-6 space-y-6">
                @foreach($images as $post)
                    <div class="break-inside-avoid group">
                        <div class="relative overflow-hidden rounded-lg bg-gray-200">
                            <img src="{{ $post->featured_image ? asset('storage/' . $post->featured_image) : asset('images/placeholder.jpg') }}" 
                                 alt="{{ $post->title }}"
                                 class="w-full h-auto group-hover:scale-105 transition-transform duration-300">
                            
                            {{-- Image Info --}}
                            <div class="absolute bottom-0 left-0 right-0 bg-gradient-to-t from-black/70 to-transparent p-4 opacity-0 group-hover:opacity-100 transition-opacity duration-300">
                                <h3 class="text-white font-semibold text-sm mb-1">{{ $post->title }}</h3>
                                <p class="text-white/80 text-xs">{{ $post->created_at->format('M d, Y') }}</p>
                                @if($post->category)
                                    <span class="inline-block bg-purple-600 text-white text-xs px-2 py-1 rounded mt-1">{{ $post->category->name }}</span>
                                @endif
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
        
        {{-- Pagination --}}
        <div class="flex justify-center mt-12">
            {{ $images->appends(request()->query())->links() }}
        </div>
    @else
        {{-- No Images --}}
        <div class="text-center py-16">
            <svg class="w-24 h-24 mx-auto text-gray-400 mb-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
            </svg>
            <h3 class="text-2xl font-bold text-gray-900 mb-4">No Images Found</h3>
            <p class="text-gray-600 mb-8">
                @if(request('category'))
                    No images found in this category. Try browsing other categories.
                @else
                    The gallery is empty. Check back later for new images.
                @endif
            </p>
            <a href="{{ route('frontend.gallery') }}" class="inline-flex items-center px-6 py-3 bg-purple-600 text-white font-semibold rounded-lg hover:bg-purple-700 transition-colors">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                </svg>
                View All Images
            </a>
        </div>
    @endif
</main>


@endsection

@push('styles')
<style>
.columns-1 { column-count: 1; }
.columns-2 { column-count: 2; }
.columns-3 { column-count: 3; }
.columns-4 { column-count: 4; }

@media (min-width: 640px) {
    .sm\:columns-2 { column-count: 2; }
}

@media (min-width: 768px) {
    .md\:columns-3 { column-count: 3; }
}

@media (min-width: 1024px) {
    .lg\:columns-4 { column-count: 4; }
}

.break-inside-avoid {
    break-inside: avoid;
    page-break-inside: avoid;
}

.aspect-square {
    aspect-ratio: 1 / 1;
}
</style>
@endpush

@push('scripts')
<script>
function setView(view) {
    const url = new URL(window.location);
    url.searchParams.set('view', view);
    window.location.href = url.toString();
}
</script>
@endpush