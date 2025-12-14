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
    {{-- Welcome & Slider Section --}}
    <div class="container mx-auto px-4 py-8">
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            {{-- Welcome Section (1/3) --}}
            <div class="lg:col-span-1 bg-white rounded-2xl shadow-lg p-6 flex flex-col items-center justify-center text-center relative overflow-hidden">
                <div class="absolute top-0 left-0 w-full h-2 bg-blue-600"></div>
                
                {{-- Label --}}
                <span class="inline-block py-1 px-3 rounded-full bg-blue-100 text-blue-700 text-xs font-bold tracking-wider mb-4">
                    {{ $siteSettings['welcome_label'] ?? 'SAMBUTAN' }}
                </span>
                
                {{-- Leader Photo --}}
                <div class="relative w-32 h-32 mb-4 rounded-full overflow-hidden border-4 border-blue-50 shadow-md">
                    @if($siteSettings['leader_photo'] ?? false)
                        <img src="{{ Str::startsWith($siteSettings['leader_photo'], 'http') ? $siteSettings['leader_photo'] : Storage::disk('public')->url($siteSettings['leader_photo']) }}" 
                             alt="{{ $siteSettings['leader_name'] ?? 'Leader' }}" 
                             class="w-full h-full object-cover">
                    @else
                        <div class="w-full h-full bg-gray-200 flex items-center justify-center text-gray-400">
                            <svg class="w-12 h-12" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                            </svg>
                        </div>
                    @endif
                </div>
                
                {{-- Leader Name & Title --}}
                <h3 class="text-xl font-bold text-gray-900 mb-1 leading-tight">
                    {{ $siteSettings['leader_name'] ?? 'Nama Pimpinan' }}
                </h3>
                <p class="text-sm text-blue-600 font-semibold mb-4 uppercase tracking-wide">
                    {{ $siteSettings['leader_title'] ?? 'Ketua' }}
                </p>
                
                {{-- Welcome Text --}}
                <div class="prose prose-sm text-gray-600 leading-relaxed text-justify px-2 w-full">
                    <p>{!! nl2br(e($siteSettings['welcome_text'] ?? 'Selamat datang di website kami.')) !!}</p>
                </div>
            </div>

            {{-- Slider Section (2/3) --}}
            <div class="lg:col-span-2">
                @if($sliderPosts->count() > 0)
                    @include('components.slider', ['posts' => $sliderPosts])
                @endif
            </div>
        </div>
    </div>

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
                      Berita Terbaru
                    </h2>
                    <a href="{{ route('frontend.posts') }}"
                        class="inline-flex items-center text-blue-600 hover:text-blue-800 font-medium transition-colors">
                        Lihat Semua
                        <svg class="w-5 h-5 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M17 8l4 4m0 0l-4 4m4-4H3"></path>
                        </svg>
                    </a>
                </div>

                @if($latestPosts->count() > 0)
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                        @foreach($latestPosts as $post)
                            <article class="bg-white rounded-lg shadow-md overflow-hidden hover:shadow-lg transition-shadow flex flex-col h-full">
                                @if($post->featured_image)
                                    <div class="h-48 w-full relative overflow-hidden group">
                                        <a href="{{ route('frontend.post', $post->slug) }}" class="block w-full h-full">
                                            <img src="{{ $post->featured_image_url }}" alt="{{ $post->title }}"
                                                class="w-full h-full object-cover transform group-hover:scale-110 transition-transform duration-500">
                                            <div class="absolute inset-0 bg-black bg-opacity-0 group-hover:bg-opacity-10 transition-opacity duration-300"></div>
                                        </a>
                                        @if($post->category)
                                            <span class="absolute top-4 left-4 bg-blue-600 text-white text-xs font-semibold px-2.5 py-1 rounded">
                                                {{ $post->category->name }}
                                            </span>
                                        @endif
                                    </div>
                                @endif

                                <div class="p-6 flex flex-col flex-grow">
                                    {{-- If no image, show category here --}}
                                    @if(!$post->featured_image && $post->category)
                                        <div class="mb-3">
                                            <span class="inline-block bg-blue-100 text-blue-800 text-xs font-semibold px-2.5 py-0.5 rounded-full">
                                                {{ $post->category->name }}
                                            </span>
                                        </div>
                                    @endif

                                    <h3 class="text-xl font-bold text-gray-900 mb-3 hover:text-blue-600 transition-colors line-clamp-2">
                                        <a href="{{ route('frontend.post', $post->slug) }}">
                                            {{ $post->title }}
                                        </a>
                                    </h3>

                                    <p class="text-gray-600 mb-4 leading-relaxed line-clamp-3">
                                        {{ Str::limit(strip_tags($post->content), 120) }}
                                    </p>

                                    <div class="mt-auto pt-4 border-t border-gray-100 flex items-center justify-between text-sm text-gray-500">
                                        <span class="flex items-center">
                                            <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M6 2a1 1 0 00-1 1v1H4a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V6a2 2 0 00-2-2h-1V3a1 1 0 10-2 0v1H7V3a1 1 0 00-1-1zm0 5a1 1 0 000 2h8a1 1 0 100-2H6z" clip-rule="evenodd"></path>
                                            </svg>
                                            {{ ($post->published_at ?? $post->created_at)->format('M d') }}
                                        </span>
                                        
                                        <a href="{{ route('frontend.post', $post->slug) }}"
                                            class="text-blue-600 hover:text-blue-800 font-medium transition-colors">
                                            Read More →
                                        </a>
                                    </div>
                                </div>
                            </article>
                        @endforeach
                    </div>
                @else
                    <div class="text-center py-12">
                        <svg class="w-16 h-16 mx-auto text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z">
                            </path>
                        </svg>
                        <h3 class="text-lg font-medium text-gray-900 mb-2">No Articles Yet</h3>
                        <p class="text-gray-500">Check back later for the latest news and updates.</p>
                    </div>
                @endif
            </div>

            {{-- Sidebar --}}
            <div class="lg:col-span-1">
                {{-- Announcements --}}
                {{-- Announcements --}}
                @if(isset($announcements) && $announcements->count() > 0)
                    <div class="bg-white rounded-lg shadow-md p-6 mb-8">
                        <h3 class="text-xl font-bold text-gray-900 mb-6 flex items-center">
                            <svg class="w-6 h-6 mr-2 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5.882V19.24a1.76 1.76 0 01-3.417.592l-2.147-6.15M18 13a3 3 0 100-6M5.436 13.68c.406.88 1.488 1.32 2.259 1.32h1.996v-6.9H7.695a2.636 2.636 0 00-2.259 1.32l-1.92 4.16a.434.434 0 00.198.54l1.722.56z"></path>
                            </svg>
                            Pengumuman
                        </h3>

                        <div class="space-y-4">
                            @foreach($announcements as $announcement)
                                <div class="group">
                                    <a href="{{ route('frontend.post', $announcement->slug) }}" class="block p-3 rounded-lg hover:bg-gray-50 transition-colors border-l-4 border-transparent hover:border-blue-600">
                                        <h4 class="text-sm font-medium text-gray-900 group-hover:text-blue-600 transition-colors line-clamp-2">
                                            {{ $announcement->title }}
                                        </h4>
                                        <div class="flex items-center text-xs text-gray-500 mt-2">
                                            <svg class="w-3 h-3 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                            </svg>
                                            {{ ($announcement->published_at ?? $announcement->created_at)->format('d M Y') }}
                                        </div>
                                    </a>
                                </div>
                            @endforeach
                        </div>
                        
                        <div class="mt-6 pt-4 border-t border-gray-100">
                            <a href="{{ route('frontend.category', 'pengumuman') }}" class="flex items-center justify-center w-full px-4 py-2 border border-blue-600 text-sm font-medium text-blue-600 rounded-lg hover:bg-blue-600 hover:text-white transition-colors">
                                Lihat Semua Pengumuman
                                <svg class="w-4 h-4 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"></path>
                                </svg>
                            </a>
                        </div>
                    </div>
                @endif

                {{-- Latest Downloads --}}
                @if(isset($latestDownloads) && $latestDownloads->count() > 0)
                    <div class="bg-white rounded-lg shadow-md p-6 mb-8">
                        <h3 class="text-xl font-bold text-gray-900 mb-6 flex items-center">
                            <svg class="w-6 h-6 mr-2 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path>
                            </svg>
                            Download
                        </h3>

                        <div class="space-y-4">
                            @foreach($latestDownloads as $download)
                                <div class="flex items-start space-x-3">
                                    <div class="flex-shrink-0">
                                        <div class="w-8 h-8 rounded-full bg-blue-50 flex items-center justify-center text-blue-600">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path>
                                            </svg>
                                        </div>
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <h4 class="text-sm font-medium text-gray-900 hover:text-blue-600 transition-colors">
                                            <a href="{{ route('frontend.downloads.show', $download->id) }}" class="line-clamp-2">
                                                {{ $download->title }}
                                            </a>
                                        </h4>
                                        <div class="flex items-center mt-1 text-xs text-gray-500">
                                            <span class="bg-gray-100 px-2 py-0.5 rounded text-gray-600">{{ $download->file_type }}</span>
                                            <span class="mx-1">•</span>
                                            <span>{{ $download->formatted_file_size }}</span>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                        
                        <div class="mt-4 pt-4 border-t border-gray-100 text-center">
                            <a href="{{ route('frontend.downloads') }}" class="text-sm font-medium text-blue-600 hover:text-blue-800 flex items-center justify-center">
                                View All Downloads
                                <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"></path>
                                </svg>
                            </a>
                        </div>
                    </div>
                @endif

                {{-- Categories --}}
                @if($categories->count() > 0)
                    <div class="bg-white rounded-lg shadow-md p-6 mb-8">
                        <h3 class="text-xl font-bold text-gray-900 mb-6 flex items-center">
                            <svg class="w-6 h-6 mr-2 text-green-500" fill="currentColor" viewBox="0 0 20 20">
                                <path
                                    d="M7 3a1 1 0 000 2h6a1 1 0 100-2H7zM4 7a1 1 0 011-1h10a1 1 0 110 2H5a1 1 0 01-1-1zM2 11a2 2 0 012-2h12a2 2 0 012 2v4a2 2 0 01-2 2H4a2 2 0 01-2-2v-4z">
                                </path>
                            </svg>
                            Kategori Berita
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

                {{-- Latest Video --}}
                @if(isset($latestVideo) && $latestVideo)
                    <div class="bg-white rounded-lg shadow-md p-6 overflow-hidden">
                        <h3 class="text-xl font-bold text-gray-900 mb-4 flex items-center">
                            <svg class="w-6 h-6 mr-2 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z"></path>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            Latest Video
                        </h3>
                        
                        <div class="relative aspect-w-16 aspect-h-9 rounded-lg overflow-hidden group mb-4">
                            @if($latestVideo->featured_image_url)
                                <img src="{{ $latestVideo->featured_image_url }}" alt="{{ $latestVideo->title }}" class="w-full h-full object-cover">
                            @else
                                <div class="w-full h-48 bg-gray-900 flex items-center justify-center">
                                    <svg class="w-12 h-12 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z"></path>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                </div>
                            @endif
                            <a href="{{ route('frontend.post', $latestVideo->slug) }}" class="absolute inset-0 bg-black/30 group-hover:bg-black/40 transition-colors flex items-center justify-center">
                                <div class="w-12 h-12 bg-white/90 rounded-full flex items-center justify-center shadow-lg transform group-hover:scale-110 transition-transform">
                                    <svg class="w-6 h-6 text-red-600 ml-1" fill="currentColor" viewBox="0 0 20 20">
                                        <path d="M6.3 2.841A1.5 1.5 0 004 4.11V15.89a1.5 1.5 0 002.3 1.269l9.344-5.89a1.5 1.5 0 000-2.538L6.3 2.84z"></path>
                                    </svg>
                                </div>
                            </a>
                        </div>
                        
                        <h4 class="font-bold text-gray-900 leading-tight mb-2 hover:text-blue-600 transition-colors">
                            <a href="{{ route('frontend.post', $latestVideo->slug) }}">
                                {{ $latestVideo->title }}
                            </a>
                        </h4>
                        <p class="text-xs text-gray-500">
                            {{ ($latestVideo->published_at ?? $latestVideo->created_at)->diffForHumans() }}
                        </p>
                    </div>
                @endif
            </div>
        </div>
    </main>

    {{-- Photo Gallery Section --}}
    @if($galleryImages->count() > 0)
        @include('components.photo-gallery', ['images' => $galleryImages])
    @endif

    {{-- Partner Section (Kerja Sama) --}}
    @if(isset($partners) && $partners->count() > 0)
        <div class="bg-gray-50 py-12 border-t border-gray-200 mt-8">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="text-center mb-10">
                    <h2 class="text-2xl font-bold text-gray-900">Partner Kerja Sama</h2>
                    <div class="w-16 h-1 bg-blue-600 mx-auto mt-2 rounded-full"></div>
                </div>
                
                <div class="relative">
                    <div class="flex flex-wrap justify-center items-center gap-8 md:gap-12">
                        @foreach($partners as $partner)
                            <a href="{{ $partner->content }}" target="_blank" rel="noopener noreferrer" 
                               class="group block transition-all duration-300 transform hover:-translate-y-1 grayscale hover:grayscale-0 opacity-60 hover:opacity-100"
                               title="{{ $partner->title }}">
                                @if($partner->featured_image)
                                    <img src="{{ asset('storage/' . $partner->featured_image) }}" 
                                         alt="{{ $partner->title }}" 
                                         class="h-16 md:h-20 w-auto object-contain mix-blend-multiply">
                                @else
                                    <div class="h-16 px-6 flex items-center justify-center border-2 border-dashed border-gray-300 rounded-lg group-hover:border-blue-500 bg-white">
                                        <span class="text-sm font-bold text-gray-400 group-hover:text-blue-600">{{ $partner->title }}</span>
                                    </div>
                                @endif
                            </a>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
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