@extends('template.university.layouts.app')

@section('title', 'Blog - ' . \App\Models\SiteSetting::getValue('site_name'))

@section('content')

    {{-- Simple Gray Page Header --}}
    <div class="bg-gray-100 py-12 border-b border-gray-200">
        <div class="container mx-auto px-4 md:px-6">
            <h1 class="text-4xl font-heading font-medium text-gray-800">Blog</h1>
        </div>
    </div>

    <div class="container mx-auto px-4 md:px-6 py-16">
        <div class="flex flex-col lg:flex-row gap-12">

            {{-- Main Content --}}
            <div class="w-full lg:w-3/4">

                @if(isset($category))
                    <div
                        class="mb-8 p-4 bg-blue-50 border border-blue-100 rounded text-blue-900 flex justify-between items-center">
                        <span class="font-bold">Category: {{ $category->name }}</span>
                        <a href="{{ route('frontend.posts') }}" class="text-sm hover:underline"><i
                                class="fas fa-times mr-1"></i> Clear</a>
                    </div>
                @endif

                @if($posts->count() > 0)
                    <div class="space-y-12">
                        @foreach($posts as $post)
                            <article class="flex flex-col md:flex-row gap-6 md:gap-8 group">
                                {{-- Date Badge --}}
                                <div class="shrink-0">
                                    <div
                                        class="bg-[#1e3a8a] text-white w-16 h-16 flex flex-col items-center justify-center shadow-md">
                                        <span
                                            class="text-[10px] font-bold uppercase tracking-wider block leading-tight">{{ $post->published_at ? $post->published_at->format('M') : $post->created_at->format('M') }}</span>
                                        <span
                                            class="text-2xl font-bold block leading-none">{{ $post->published_at ? $post->published_at->format('d') : $post->created_at->format('d') }}</span>
                                    </div>
                                </div>

                                {{-- Image --}}
                                <a href="{{ route('frontend.post', $post->slug) }}"
                                    class="shrink-0 w-full md:w-64 h-48 block overflow-hidden relative">
                                    <img src="{{ $post->featured_image ? Storage::url($post->featured_image) : asset('images/default-post.jpg') }}"
                                        alt="{{ $post->title }}"
                                        class="w-full h-full object-cover transition-transform duration-500 group-hover:scale-110">
                                </a>

                                {{-- Content --}}
                                <div class="flex-grow pt-2">
                                    <h2
                                        class="text-2xl font-bold text-gray-800 mb-4 font-heading leading-tight group-hover:text-[#1e3a8a] transition-colors">
                                        <a href="{{ route('frontend.post', $post->slug) }}">{{ $post->title }}</a>
                                    </h2>
                                    <div class="text-xs text-gray-400 mb-4 font-medium uppercase tracking-wide">
                                        {{ $post->user->name ?? 'Admin' }} | {{ $post->category->name ?? 'Uncategorized' }}
                                    </div>
                                    <p class="text-gray-500 text-sm leading-relaxed line-clamp-3 mb-6 font-light">
                                        {{ $post->excerpt ?? Str::limit(strip_tags($post->content), 150) }}
                                    </p>
                                    <a href="{{ route('frontend.post', $post->slug) }}"
                                        class="inline-block px-6 py-2 border border-gray-300 text-xs font-bold text-gray-500 uppercase tracking-widest hover:border-[#1e3a8a] hover:bg-[#1e3a8a] hover:text-white transition-all">
                                        Detail <i class="fas fa-chevron-right ml-1 text-[10px]"></i>
                                    </a>
                                </div>
                            </article>
                            <hr class="border-gray-100 last:hidden">
                        @endforeach
                    </div>

                    {{-- Pagination --}}
                    <div class="mt-16">
                        {{ $posts->links() }}
                    </div>
                @else
                    <div class="bg-gray-50 p-12 text-center rounded">
                        <h3 class="text-xl font-bold text-gray-400">No posts found</h3>
                        <p class="text-gray-400 mt-2">Try searching for something else.</p>
                    </div>
                @endif
            </div>

            {{-- Sidebar --}}
            @php
                $sidebarBg = \App\Models\SiteSetting::getValue('theme_university_sidebar_background_image');
                if (!$sidebarBg) {
                    $sidebarBg = \App\Helpers\TemplateHelper::getThemeConfig('sidebar.background_image');
                }
            @endphp
            <aside
                class="w-full lg:w-1/4 space-y-8 {{ $sidebarBg ? 'p-6 rounded-xl' : 'pl-0 lg:pl-8 border-l border-transparent lg:border-gray-100' }}"
                style="{{ $sidebarBg ? 'background-image: url(' . $sidebarBg . '); background-size: cover; background-position: center;' : '' }}">

                {{-- Search Widget --}}
                <div>
                    <h3
                        class="text-lg font-bold font-heading text-gray-800 uppercase tracking-wider mb-6 pb-2 border-b border-gray-200">
                        Pencarian
                    </h3>
                    <form action="{{ route('frontend.search') }}" method="GET" class="relative">
                        <input type="text" name="q" placeholder="Cari Berita..."
                            class="w-full bg-gray-50 border border-gray-200 px-4 py-3 text-sm focus:outline-none focus:ring-1 focus:ring-[#1e3a8a] focus:bg-white transition-colors rounded">
                        <button type="submit"
                            class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-[#1e3a8a]">
                            <i class="fas fa-search"></i>
                        </button>
                    </form>
                </div>

                {{-- Quick Links Widget --}}
                <div>
                    <h3
                        class="text-lg font-bold font-heading text-gray-800 uppercase tracking-wider mb-6 pb-2 border-b border-gray-200">
                        Tautan Cepat
                    </h3>
                    <ul class="space-y-3">
                        @php $quickLinks = \App\Models\NavigationMenu::getMenuTree('quicklink'); @endphp
                        @foreach($quickLinks as $link)
                            <li>
                                <a href="{{ $link->final_url }}" target="{{ $link->target }}"
                                    class="flex items-center text-gray-600 hover:text-[#1e3a8a] transition-colors group">
                                    <span
                                        class="w-2 h-2 bg-gray-300 rounded-full mr-3 group-hover:bg-[#1e3a8a] transition-colors"></span>
                                    {{ $link->title }}
                                </a>
                            </li>
                        @endforeach
                        @if($quickLinks->isEmpty())
                            <li class="text-gray-500 italic text-sm">Belum ada tautan cepat.</li>
                        @endif
                    </ul>
                </div>

            </aside>
        </div>
    </div>

@endsection