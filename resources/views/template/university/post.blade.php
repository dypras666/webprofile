@extends('template.university.layouts.app')

@section('title', $post->title)
@section('description', $post->excerpt)
@section('keywords', $post->tags)
@section('author', $post->user->name ?? 'Admin')
@section('og_image', $post->featured_image ? Storage::url($post->featured_image) : asset('images/default-post.jpg'))

@section('content')
    <div class="bg-gray-50 py-8 lg:py-12">
        <div class="container mx-auto px-4">

            {{-- Breadcrumb --}}
            <nav class="text-sm text-gray-500 mb-6 flex items-center gap-2 overflow-x-auto whitespace-nowrap">
                <a href="{{ route('frontend.index') }}" class="hover:text-primary">Beranda</a>
                <span>/</span>
                @if($post->category)
                    <a href="{{ route('frontend.category', $post->category->slug) }}"
                        class="hover:text-primary">{{ $post->category->name }}</a>
                    <span>/</span>
                @endif
                <span class="text-gray-900 truncate max-w-[200px]">{{ $post->title }}</span>
            </nav>

            <div class="flex flex-col lg:flex-row gap-8 lg:gap-12">

                <div class="w-full {{ $post->type === 'page' ? '' : 'lg:w-2/3' }}">
                    {{-- Main Content --}}
                    <article class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden mb-8">

                        {{-- Featured Image --}}
                        @if($post->featured_image)
                            <div class="w-full aspect-video relative">
                                <img src="{{ Storage::url($post->featured_image) }}" alt="{{ $post->title }}"
                                    class="absolute inset-0 w-full h-full object-cover">
                            </div>
                        @endif

                        <div class="p-6 md:p-8 lg:p-10">
                            {{-- Meta --}}
                            <div class="flex items-center gap-4 text-sm text-gray-500 mb-4 border-b border-gray-100 pb-4">
                                <span class="flex items-center text-primary font-medium">
                                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z">
                                        </path>
                                    </svg>
                                    {{ $post->category->name ?? ucfirst($post->type) }}
                                </span>
                                <span class="flex items-center">
                                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z">
                                        </path>
                                    </svg>
                                    {{ $post->created_at->isoFormat('D MMMM Y') }}
                                </span>
                                <span class="flex items-center">
                                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z">
                                        </path>
                                    </svg>
                                    {{ $post->views }} Views
                                </span>
                            </div>

                            {{-- Title --}}
                            <h1 class="text-3xl md:text-4xl font-bold font-heading text-gray-900 mb-6 leading-tight">
                                {{ $post->title }}
                            </h1>

                            {{-- Content --}}
                            <div class="prose prose-lg prose-green max-w-none text-gray-700 leading-relaxed mb-8">
                                {!! $post->content !!}
                            </div>

                            {{-- Tags --}}
                            @if($post->tags)
                                <div class="flex items-center gap-2 flex-wrap mb-8">
                                    <span class="text-sm font-bold text-gray-700 mr-2">Tags:</span>
                                    @foreach(explode(',', $post->tags) as $tag)
                                        <a href="{{ route('frontend.search', ['q' => trim($tag)]) }}"
                                            class="px-3 py-1 bg-gray-100 text-gray-600 rounded-full text-xs hover:bg-primary hover:text-white transition-colors">
                                            #{{ trim($tag) }}
                                        </a>
                                    @endforeach
                                </div>
                            @endif

                            {{-- Share --}}
                            <div class="flex items-center justify-between border-t border-b border-gray-100 py-4 mb-8">
                                <span class="font-bold text-gray-900">Bagikan:</span>
                                <div class="flex space-x-2">
                                    <a href="https://www.facebook.com/sharer/sharer.php?u={{ url()->current() }}"
                                        target="_blank"
                                        class="w-8 h-8 rounded bg-secondary text-white flex items-center justify-center hover:bg-primary transition-colors"><i
                                            class="fab fa-facebook-f"></i></a>
                                    <a href="https://twitter.com/intent/tweet?url={{ url()->current() }}&text={{ urlencode($post->title) }}"
                                        target="_blank"
                                        class="w-8 h-8 rounded bg-secondary text-white flex items-center justify-center hover:bg-primary transition-colors"><i
                                            class="fab fa-twitter"></i></a>
                                    <a href="https://wa.me/?text={{ urlencode($post->title . ' ' . url()->current()) }}"
                                        target="_blank"
                                        class="w-8 h-8 rounded bg-secondary text-white flex items-center justify-center hover:bg-primary transition-colors"><i
                                            class="fab fa-whatsapp"></i></a>
                                </div>
                            </div>

                            {{-- Prev/Next --}}
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                @if($previousPost)
                                    <a href="{{ route('frontend.post', $previousPost->slug) }}"
                                        class="block p-4 rounded-lg border border-gray-200 hover:border-primary hover:bg-green-50 transition-colors group">
                                        <div class="text-xs text-gray-500 mb-1 group-hover:text-primary">&larr; Sebelumnya</div>
                                        <div class="font-bold text-gray-800 line-clamp-1">{{ $previousPost->title }}</div>
                                    </a>
                                @else
                                    <div></div>
                                @endif

                                @if($nextPost)
                                    <a href="{{ route('frontend.post', $nextPost->slug) }}"
                                        class="block p-4 rounded-lg border border-gray-200 hover:border-primary hover:bg-green-50 transition-colors text-right group">
                                        <div class="text-xs text-gray-500 mb-1 group-hover:text-primary">Selanjutnya &rarr;
                                        </div>
                                        <div class="font-bold text-gray-800 line-clamp-1">{{ $nextPost->title }}</div>
                                    </a>
                                @else
                                    <div></div>
                                @endif
                            </div>

                        </div>
                    </article>

                    {{-- Comments Section --}}
                    @include('template.university.components.comment_section', ['post' => $post, 'comments' => $comments])
                </div>

                {{-- Sidebar --}}
                @if($post->type !== 'page')
                    @php
                        $sidebarBg = \App\Models\SiteSetting::getValue('theme_university_sidebar_background_image');
                        // Fallback to config if not in DB (though helper does this, doing it explicitly for logic)
                        if (!$sidebarBg) {
                            $sidebarBg = \App\Helpers\TemplateHelper::getThemeConfig('sidebar.background_image');
                        }
                    @endphp
                    <aside class="w-full lg:w-1/3 space-y-8 {{ $sidebarBg ? 'p-6 rounded-xl' : '' }}"
                        style="{{ $sidebarBg ? 'background-image: url(' . $sidebarBg . '); background-size: cover; background-position: center;' : '' }}">

                        {{-- Author Info --}}
                        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                            <h3 class="font-bold text-lg text-gray-900 mb-4 border-b pb-2">Penulis</h3>
                            <div class="flex items-center gap-4">
                                <div
                                    class="w-12 h-12 rounded-full bg-primary/10 flex items-center justify-center text-primary text-xl">
                                    <i class="fas fa-user"></i>
                                </div>
                                <div>
                                    <h4 class="font-bold text-gray-900">{{ $post->user->name ?? 'Admin' }}</h4>
                                    <p class="text-xs text-gray-500">Administrator</p>
                                </div>
                            </div>
                        </div>

                        {{-- Sidebar Ad --}}
                        @if(isset($adsSidebar) && $adsSidebar)
                            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4">
                                <a href="{{ $adsSidebar->excerpt ?? '#' }}"
                                    target="{{ $adsSidebar->excerpt ? '_blank' : '_self' }}">
                                    <img src="{{ Storage::url($adsSidebar->featured_image) }}" alt="{{ $adsSidebar->title }}"
                                        class="w-full h-auto rounded-lg">
                                </a>
                            </div>
                        @endif

                        {{-- Related Posts --}}
                        @if($relatedPosts->count() > 0)
                            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                                <h3 class="font-bold text-lg text-gray-900 mb-4 border-b pb-2">Berita Terkait</h3>
                                <div class="space-y-4">
                                    @foreach($relatedPosts as $related)
                                        <div class="flex gap-3">
                                            <a href="{{ route('frontend.post', $related->slug) }}"
                                                class="w-20 h-20 rounded overflow-hidden shrink-0 bg-gray-100 group">
                                                <img src="{{ $related->featured_image ? Storage::url($related->featured_image) : asset('images/default-post.jpg') }}"
                                                    class="w-full h-full object-cover transition-transform group-hover:scale-110">
                                            </a>
                                            <div>
                                                <h5 class="text-sm font-medium text-gray-800 line-clamp-2 mb-1 hover:text-primary">
                                                    <a href="{{ route('frontend.post', $related->slug) }}">{{ $related->title }}</a>
                                                </h5>
                                                <div class="text-xs text-gray-500">{{ $related->created_at->format('d M Y') }}</div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endif

                    </aside>
                @endif

            </div>
        </div>
    </div>
@endsection