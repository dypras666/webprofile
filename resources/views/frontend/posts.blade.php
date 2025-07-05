@extends('layouts.app')

@section('title')
    @if(request('category'))
        {{ $category->name ?? 'Category' }} - @switch(request('type'))
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
    @elseif(request('type'))
        @switch(request('type'))
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
                {{ ucfirst(request('type')) }} Articles
        @endswitch
    @elseif(request('search'))
        Search Results for "{{ request('search') }}"
    @else
        All Articles
    @endif
@endsection

@section('meta_description')
    @if(request('category'))
        Browse {{ $category->name ?? 'category' }} @switch(request('type'))
            @case('page')
                pages
                @break
            @case('video')
                videos
                @break
            @case('gallery')
                gallery
                @break
            @default
                articles
        @endswitch and stay updated with the latest content.
    @elseif(request('search'))
        Search results for "{{ request('search') }}" - Find relevant @switch(request('type'))
            @case('page')
                pages
                @break
            @case('video')
                videos
                @break
            @case('gallery')
                gallery
                @break
            @default
                articles and news
        @endswitch.
    @else
        Browse all @switch(request('type'))
            @case('page')
                pages
                @break
            @case('video')
                videos
                @break
            @case('gallery')
                gallery
                @break
            @default
                articles, news, and insights
        @endswitch. Stay informed with our comprehensive coverage.
    @endif
@endsection

@section('content')
{{-- Page Header --}}
<div class="bg-gradient-to-r from-blue-600 to-blue-800 text-white py-16">
    <div class="container mx-auto px-4">
        <div class="max-w-4xl mx-auto text-center">
            <h1 class="text-4xl md:text-5xl font-bold mb-4">
                @if(request('category'))
                    {{ $category->name ?? 'Category' }}
                @elseif(request('type'))
                    @switch(request('type'))
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
                            {{ ucfirst(request('type')) }} Articles
                    @endswitch
                @elseif(request('search'))
                    Search Results
                @else
                    All Articles
                @endif
            </h1>
            
            @if(request('search'))
                <p class="text-xl text-blue-100 mb-6">
                    Found {{ $posts->total() }} results for "{{ request('search') }}"
                </p>
            @elseif(request('category') && isset($category))
                <p class="text-xl text-blue-100 mb-6">
                    {{ $category->description ?? 'Explore ' }}
                    @switch(request('type'))
                        @case('page')
                            pages
                            @break
                        @case('video')
                            videos
                            @break
                        @case('gallery')
                            gallery
                            @break
                        @default
                            articles
                    @endswitch
                    {{ $category->description ? '' : ' in this category' }}
                </p>
            @else
                <p class="text-xl text-blue-100 mb-6">
                    Discover our latest @switch(request('type'))
                        @case('page')
                            pages
                            @break
                        @case('video')
                            videos
                            @break
                        @case('gallery')
                            gallery
                            @break
                        @default
                            articles, news, and insights
                    @endswitch
                </p>
            @endif
            
            {{-- Search Form --}}
            <div class="max-w-2xl mx-auto">
                <form action="{{ route('frontend.posts') }}" method="GET" class="flex">
                    @if(request('category'))
                        <input type="hidden" name="category" value="{{ request('category') }}">
                    @endif
                    @if(request('type'))
                        <input type="hidden" name="type" value="{{ request('type') }}">
                    @endif
                    
                    <div class="flex-1 relative">
                        <input type="text" 
                               name="search" 
                               value="{{ request('search') }}"
                               placeholder="Search @switch(request('type'))
                                   @case('page')
                                       pages
                                       @break
                                   @case('video')
                                       videos
                                       @break
                                   @case('gallery')
                                       gallery
                                       @break
                                   @default
                                       articles
                               @endswitch..."
                               class="w-full px-6 py-4 rounded-l-lg text-gray-900 placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-white/50">
                        <svg class="absolute right-4 top-1/2 transform -translate-y-1/2 w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                        </svg>
                    </div>
                    <button type="submit" class="px-8 py-4 bg-white text-blue-600 font-semibold rounded-r-lg hover:bg-blue-50 transition-colors">
                        Search
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

{{-- Filters & Sorting --}}
<div class="bg-white border-b">
    <div class="container mx-auto px-4 py-6">
        <div class="flex flex-col md:flex-row justify-between items-center space-y-4 md:space-y-0">
            {{-- Category Filter --}}
            <div class="flex flex-wrap items-center space-x-2">
                <span class="text-gray-600 font-medium mr-4">Categories:</span>
                <a href="{{ route('frontend.posts') }}" 
                   class="px-4 py-2 rounded-full text-sm font-medium transition-colors {{ !request('category') ? 'bg-blue-600 text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200' }}">
                    All
                </a>
                @foreach($categories as $cat)
                    <a href="{{ route('frontend.posts', ['category' => $cat->slug]) }}" 
                       class="px-4 py-2 rounded-full text-sm font-medium transition-colors {{ request('category') == $cat->slug ? 'bg-blue-600 text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200' }}">
                        {{ $cat->name }}
                        <span class="ml-1 text-xs opacity-75">({{ $cat->posts_count }})</span>
                    </a>
                @endforeach
            </div>
            
            {{-- Sort Options --}}
            <div class="flex items-center space-x-4">
                <span class="text-gray-600 font-medium">Sort by:</span>
                <select onchange="updateSort(this.value)" class="px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <option value="latest" {{ request('sort') == 'latest' || !request('sort') ? 'selected' : '' }}>Latest</option>
                    <option value="popular" {{ request('sort') == 'popular' ? 'selected' : '' }}>Most Popular</option>
                    <option value="title" {{ request('sort') == 'title' ? 'selected' : '' }}>Title A-Z</option>
                </select>
            </div>
        </div>
    </div>
</div>

{{-- Main Content --}}
<main class="container mx-auto px-4 py-12">
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        {{-- Posts Grid --}}
        <div class="lg:col-span-2">
            @if($posts->count() > 0)
                <div class="grid grid-cols-1 md:grid-cols-2 gap-8 mb-12">
                    @foreach($posts as $post)
                        <article class="bg-white rounded-lg shadow-md overflow-hidden hover:shadow-lg transition-shadow group">
                            @if($post->featured_image)
                                <div class="relative overflow-hidden">
                                    <img src="{{ $post->featured_image_url }}" 
                                         alt="{{ $post->title }}"
                                         class="w-full h-48 object-cover group-hover:scale-105 transition-transform duration-300">
                                    
                                    {{-- Category Badge --}}
                                    @if($post->category)
                                        <span class="absolute top-4 left-4 bg-blue-600 text-white text-xs font-semibold px-3 py-1 rounded-full">
                                            {{ $post->category->name }}
                                        </span>
                                    @endif
                                    
                                    {{-- Featured Badge --}}
                                    @if($post->is_featured)
                                        <span class="absolute top-4 right-4 bg-yellow-500 text-white text-xs font-semibold px-3 py-1 rounded-full">
                                            Featured
                                        </span>
                                    @endif
                                </div>
                            @endif
                            
                            <div class="p-6">
                                <h3 class="text-xl font-bold text-gray-900 mb-3 group-hover:text-blue-600 transition-colors">
                                    <a href="{{ route('frontend.post', $post->slug) }}" class="line-clamp-2">
                                        {{ $post->title }}
                                    </a>
                                </h3>
                                
                                <p class="text-gray-600 mb-4 line-clamp-3">
                                    {{ $post->excerpt ?: Str::limit(strip_tags($post->content), 120) }}
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
                                            {{ $post->created_at->format('M d, Y') }}
                                        </span>
                                    </div>
                                    
                                    <span class="flex items-center text-blue-600">
                                        <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                            <path d="M10 12a2 2 0 100-4 2 2 0 000 4z"></path>
                                            <path fill-rule="evenodd" d="M.458 10C1.732 5.943 5.522 3 10 3s8.268 2.943 9.542 7c-1.274 4.057-5.064 7-9.542 7S1.732 14.057.458 10zM14 10a4 4 0 11-8 0 4 4 0 018 0z" clip-rule="evenodd"></path>
                                        </svg>
                                        {{ $post->views ?? 0 }}
                                    </span>
                                </div>
                            </div>
                        </article>
                    @endforeach
                </div>
                
                {{-- Pagination --}}
                <div class="flex justify-center">
                    {{ $posts->appends(request()->query())->links() }}
                </div>
            @else
                {{-- No Posts Found --}}
                <div class="text-center py-16">
                    <svg class="w-24 h-24 mx-auto text-gray-400 mb-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                    </svg>
                    <h3 class="text-2xl font-bold text-gray-900 mb-4">No @switch(request('type'))
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
                    @endswitch Found</h3>
                    <p class="text-gray-600 mb-8">
                        @if(request('search'))
                            No @switch(request('type'))
                                @case('page')
                                    pages
                                    @break
                                @case('video')
                                    videos
                                    @break
                                @case('gallery')
                                    gallery
                                    @break
                                @default
                                    articles
                            @endswitch match your search criteria. Try different keywords.
                        @else
                            There are no @switch(request('type'))
                                @case('page')
                                    pages
                                    @break
                                @case('video')
                                    videos
                                    @break
                                @case('gallery')
                                    gallery
                                    @break
                                @default
                                    articles
                            @endswitch in this category yet.
                        @endif
                    </p>
                    <a href="{{ route('frontend.posts') }}" class="inline-flex items-center px-6 py-3 bg-blue-600 text-white font-semibold rounded-lg hover:bg-blue-700 transition-colors">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                        </svg>
                        Browse All @switch(request('type'))
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
                        Trending
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
                                        <span>{{ $post->created_at->format('M d') }}</span>
                                        <span class="mx-1">•</span>
                                        <span>{{ $post->views ?? 0 }} views</span>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif
            
            {{-- Categories Widget --}}
            <div class="bg-white rounded-lg shadow-md p-6">
                <h3 class="text-xl font-bold text-gray-900 mb-6 flex items-center">
                    <svg class="w-6 h-6 mr-2 text-green-500" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M7 3a1 1 0 000 2h6a1 1 0 100-2H7zM4 7a1 1 0 011-1h10a1 1 0 110 2H5a1 1 0 01-1-1zM2 11a2 2 0 012-2h12a2 2 0 012 2v4a2 2 0 01-2 2H4a2 2 0 01-2-2v-4z"></path>
                    </svg>
                    Categories
                </h3>
                
                <div class="space-y-2">
                    @foreach($categories as $cat)
                        <a href="{{ route('frontend.posts', ['category' => $cat->slug]) }}" 
                           class="flex items-center justify-between p-3 rounded-lg hover:bg-gray-50 transition-colors group {{ request('category') == $cat->slug ? 'bg-blue-50 text-blue-600' : '' }}">
                            <span class="group-hover:text-blue-600 transition-colors">
                                {{ $cat->name }}
                            </span>
                            <span class="text-sm text-gray-500 bg-gray-100 px-2 py-1 rounded-full">
                                {{ $cat->posts_count ?? 0 }}
                            </span>
                        </a>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</main>
@endsection

@push('styles')
<style>
.line-clamp-2 {
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
}

.line-clamp-3 {
    display: -webkit-box;
    -webkit-line-clamp: 3;
    -webkit-box-orient: vertical;
    overflow: hidden;
}
</style>
@endpush

@push('scripts')
<script>
function updateSort(value) {
    const url = new URL(window.location);
    url.searchParams.set('sort', value);
    window.location.href = url.toString();
}
</script>
@endpush