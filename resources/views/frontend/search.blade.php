@extends('layouts.app')

@section('meta_title', 'Search Results for "' . $query . '" - ' . config('app.name'))
@section('meta_description', 'Search results for "' . $query . '" on ' . config('app.name') . '. Find articles, news, and content related to your search query.')
@section('meta_keywords', 'search, ' . $query . ', articles, news, ' . config('app.name'))

@section('canonical', url()->current())

@section('og_title', 'Search Results for "' . $query . '" - ' . config('app.name'))
@section('og_description', 'Search results for "' . $query . '" on ' . config('app.name') . '. Find articles, news, and content related to your search query.')
@section('og_url', url()->current())
@section('og_type', 'website')
@section('og_site_name', config('app.name'))

@section('twitter_card', 'summary_large_image')
@section('twitter_title', 'Search Results for "' . $query . '" - ' . config('app.name'))
@section('twitter_description', 'Search results for "' . $query . '" on ' . config('app.name') . '. Find articles, news, and content related to your search query.')

@section('structured_data')
<script type="application/ld+json">
{
    "@context": "https://schema.org",
    "@type": "SearchResultsPage",
    "name": "Search Results for \"{{ $query }}\"",
    "description": "Search results for \"{{ $query }}\" on {{ config('app.name') }}",
    "url": "{{ url()->current() }}",
    "mainEntity": {
        "@type": "ItemList",
        "numberOfItems": {{ $posts->total() }},
        "itemListElement": [
            @foreach($posts->take(5) as $index => $post)
            {
                "@type": "ListItem",
                "position": {{ $index + 1 }},
                "item": {
                    "@type": "Article",
                    "headline": "{{ $post->title }}",
                    "description": "{{ $post->excerpt }}",
                    "url": "{{ route('frontend.post', $post->slug) }}",
                    "datePublished": "{{ $post->created_at->toISOString() }}",
                    "author": {
                        "@type": "Person",
                        "name": "{{ $post->user->name ?? 'Admin' }}"
                    }
                }
            }@if(!$loop->last),@endif
            @endforeach
        ]
    }
}
</script>
@endsection

@section('content')
<div class="container mx-auto px-4 py-8">
    <!-- Search Header -->
    <div class="mb-8">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between mb-6">
            <div>
                <h1 class="text-2xl md:text-3xl font-bold mb-2">Search Results</h1>
                <p class="text-gray-600">
                    Found <span class="font-semibold text-primary">{{ $posts->total() }}</span> 
                    {{ Str::plural('result', $posts->total()) }} for 
                    <span class="font-semibold">"{{ $query }}"</span>
                </p>
            </div>
            
            <!-- Search Form -->
            <div class="mt-4 md:mt-0">
                <form action="{{ route('frontend.search') }}" method="GET" class="flex">
                    <div class="relative flex-1 max-w-md">
                        <input type="text" 
                               name="q" 
                               value="{{ $query }}" 
                               placeholder="Search articles..." 
                               class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-l-lg focus:ring-2 focus:ring-primary focus:border-transparent">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                            </svg>
                        </div>
                    </div>
                    <button type="submit" class="bg-primary text-white px-6 py-2 rounded-r-lg hover:bg-primary-dark transition-colors">
                        Search
                    </button>
                </form>
            </div>
        </div>
        
        <!-- Breadcrumb -->
        <nav class="text-sm text-gray-500 mb-6">
            <ol class="flex items-center space-x-2">
                <li><a href="{{ route('frontend.index') }}" class="hover:text-primary">Home</a></li>
                <li><span class="mx-2">/</span></li>
                <li class="text-gray-700">Search Results</li>
            </ol>
        </nav>
    </div>

    <div class="grid lg:grid-cols-4 gap-8">
        <!-- Main Content -->
        <div class="lg:col-span-3">
            @if($posts->count() > 0)
                <!-- Search Results -->
                <div class="space-y-6">
                    @foreach($posts as $post)
                    <article class="bg-white rounded-lg shadow-md overflow-hidden hover:shadow-lg transition-shadow duration-300">
                        <div class="md:flex">
                            <!-- Featured Image -->
                            <div class="md:w-1/3">
                                @if($post->featured_image)
                                <img src="{{ $post->featured_image }}" 
                                     alt="{{ $post->title }}" 
                                     class="w-full h-48 md:h-full object-cover">
                                @else
                                <div class="w-full h-48 md:h-full bg-gray-200 flex items-center justify-center">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                    </svg>
                                </div>
                                @endif
                            </div>
                            
                            <!-- Content -->
                            <div class="md:w-2/3 p-6">
                                <!-- Category -->
                                @if($post->category)
                                <div class="mb-2">
                                    <a href="{{ route('frontend.category', $post->category->slug) }}" 
                                       class="inline-block bg-primary/10 text-primary text-xs font-medium px-2 py-1 rounded hover:bg-primary hover:text-white transition-colors">
                                        {{ $post->category->name }}
                                    </a>
                                </div>
                                @endif
                                
                                <!-- Title -->
                                <h2 class="text-xl font-bold mb-3 line-clamp-2">
                                    <a href="{{ route('frontend.post', $post->slug) }}" class="hover:text-primary transition-colors">
                                        {{ $post->title }}
                                    </a>
                                </h2>
                                
                                <!-- Excerpt -->
                                <p class="text-gray-600 mb-4 line-clamp-3">{{ $post->excerpt }}</p>
                                
                                <!-- Meta Info -->
                                <div class="flex items-center justify-between text-sm text-gray-500">
                                    <div class="flex items-center space-x-4">
                                        <span class="flex items-center">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                            </svg>
                                            {{ $post->user->name ?? 'Admin' }}
                                        </span>
                                        <span class="flex items-center">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                            </svg>
                                            {{ $post->created_at->format('M d, Y') }}
                                        </span>
                                        <span class="flex items-center">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                            </svg>
                                            {{ number_format($post->views) }} views
                                        </span>
                                    </div>
                                    
                                    <a href="{{ route('frontend.post', $post->slug) }}" 
                                       class="text-primary hover:text-primary-dark font-medium">
                                        Read More →
                                    </a>
                                </div>
                            </div>
                        </div>
                    </article>
                    @endforeach
                </div>
                
                <!-- Pagination -->
                @if($posts->hasPages())
                <div class="mt-8">
                    {{ $posts->appends(request()->query())->links() }}
                </div>
                @endif
            @else
                <!-- No Results -->
                <div class="text-center py-12">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-24 w-24 text-gray-300 mx-auto mb-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                    </svg>
                    <h2 class="text-2xl font-bold text-gray-700 mb-2">No Results Found</h2>
                    <p class="text-gray-500 mb-6">We couldn't find any articles matching "{{ $query }}". Try different keywords or browse our categories.</p>
                    
                    <!-- Search Suggestions -->
                    <div class="max-w-md mx-auto">
                        <h3 class="text-lg font-semibold mb-4">Search Suggestions:</h3>
                        <ul class="text-left space-y-2 text-gray-600">
                            <li>• Check your spelling</li>
                            <li>• Try more general keywords</li>
                            <li>• Use fewer keywords</li>
                            <li>• Browse our categories below</li>
                        </ul>
                    </div>
                </div>
            @endif
        </div>
        
        <!-- Sidebar -->
        <div class="lg:col-span-1">
            <div class="space-y-6">
                <!-- Categories -->
                <div class="bg-white p-6 rounded-lg shadow-md">
                    <h3 class="text-lg font-bold mb-4">Browse Categories</h3>
                    <div class="space-y-2">
                        @foreach($categories as $category)
                        <a href="{{ route('frontend.category', $category->slug) }}" 
                           class="flex items-center justify-between p-2 rounded hover:bg-gray-50 transition-colors">
                            <span class="text-gray-700">{{ $category->name }}</span>
                            <span class="text-xs bg-gray-100 text-gray-600 px-2 py-1 rounded-full">{{ $category->posts_count }}</span>
                        </a>
                        @endforeach
                    </div>
                </div>
                
                <!-- Popular Posts -->
                @if($popularPosts->count() > 0)
                <div class="bg-white p-6 rounded-lg shadow-md">
                    <h3 class="text-lg font-bold mb-4">Popular Articles</h3>
                    <div class="space-y-4">
                        @foreach($popularPosts as $popularPost)
                        <article class="flex space-x-3">
                            @if($popularPost->featured_image)
                            <img src="{{ $popularPost->featured_image }}" 
                                 alt="{{ $popularPost->title }}" 
                                 class="w-16 h-16 object-cover rounded">
                            @else
                            <div class="w-16 h-16 bg-gray-200 rounded flex items-center justify-center">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                </svg>
                            </div>
                            @endif
                            <div class="flex-1 min-w-0">
                                <h4 class="text-sm font-medium line-clamp-2 mb-1">
                                    <a href="{{ route('frontend.post', $popularPost->slug) }}" class="hover:text-primary transition-colors">
                                        {{ $popularPost->title }}
                                    </a>
                                </h4>
                                <div class="flex items-center text-xs text-gray-500">
                                    <span>{{ $popularPost->created_at->format('M d') }}</span>
                                    <span class="mx-1">•</span>
                                    <span>{{ number_format($popularPost->views) }} views</span>
                                </div>
                            </div>
                        </article>
                        @endforeach
                    </div>
                </div>
                @endif
                
                <!-- Search Tips -->
                <div class="bg-primary/5 p-6 rounded-lg">
                    <h3 class="text-lg font-bold mb-4 text-primary">Search Tips</h3>
                    <ul class="text-sm text-gray-600 space-y-2">
                        <li>• Use quotes for exact phrases</li>
                        <li>• Try synonyms or related terms</li>
                        <li>• Check category filters</li>
                        <li>• Browse recent articles</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection