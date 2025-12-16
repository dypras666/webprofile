@extends('template.university.layouts.app')

@section('title', 'Fasilitas - ' . \App\Models\SiteSetting::getValue('site_name'))

@section('content')

    {{-- Facilities Header (Dark Theme) --}}
    <div class="bg-[#111] py-20 border-b border-gray-800 relative overflow-hidden text-white">
        {{-- Map Pattern --}}
        <div class="absolute inset-0 opacity-20 pointer-events-none">
            <svg width="100%" height="100%" xmlns="http://www.w3.org/2000/svg">
                <defs>
                    <pattern id="dotPattern" x="0" y="0" width="20" height="20" patternUnits="userSpaceOnUse">
                        <circle cx="2" cy="2" r="1.5" fill="#555" />
                    </pattern>
                </defs>
                <rect width="100%" height="100%" fill="url(#dotPattern)" />
            </svg>
            <div class="absolute inset-0 bg-gradient-to-t from-[#111] via-transparent to-[#111]"></div>
        </div>

        <div class="container mx-auto px-4 md:px-6 relative z-10 text-center">
            <h1 class="text-4xl md:text-5xl font-heading font-medium tracking-wide uppercase mb-4">
                Fasilitas <span class="font-light text-gray-400">Kami</span>
            </h1>
            <div class="h-1 w-20 bg-cyan-600 mx-auto mb-6"></div>
            <p class="text-gray-400 text-lg max-w-2xl mx-auto">
                Berbagai sarana dan prasarana modern untuk mendukung proses pembelajaran dan pengembangan diri mahasiswa.
            </p>
        </div>
    </div>

    {{-- Main Content --}}
    <div class="container mx-auto px-4 md:px-6 py-16">

        @if($posts->count() > 0)
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                @foreach($posts as $facility)
                    <div class="group cursor-pointer">
                        {{-- Image Card --}}
                        <div class="relative h-64 overflow-hidden rounded-md mb-6 shadow-lg border border-gray-100 bg-gray-100">
                            <img src="{{ !empty($facility->featured_image_url) ? $facility->featured_image_url : asset('images/default-post.jpg') }}"
                                alt="{{ $facility->title }}"
                                class="w-full h-full object-cover transition-transform duration-700 group-hover:scale-110">

                            {{-- Hover Overlay --}}
                            <div class="absolute inset-0 bg-black/0 group-hover:bg-black/20 transition-colors duration-300"></div>
                        </div>

                        {{-- Content --}}
                        <div class="text-left px-2">
                            <h4 class="text-xl font-bold text-gray-800 mb-2 group-hover:text-cyan-600 transition-colors">
                                {{ $facility->title }}</h4>
                            <p class="text-gray-500 text-sm leading-relaxed mb-4 line-clamp-3">
                                {{ $facility->excerpt ?? Str::limit(strip_tags($facility->content), 120) }}
                            </p>
                            <a href="{{ route('frontend.post', $facility->slug) }}"
                                class="inline-flex items-center text-xs font-bold uppercase tracking-wider text-cyan-600 hover:text-cyan-700">
                                Selengkapnya <i class="fas fa-arrow-right ml-2"></i>
                            </a>
                        </div>
                    </div>
                @endforeach
            </div>

            {{-- Pagination --}}
            <div class="mt-16 flex justify-center">
                {{ $posts->links() }}
            </div>

        @else
            <div class="bg-gray-50 p-12 text-center rounded-xl border border-gray-100">
                <div class="inline-flex items-center justify-center w-16 h-16 bg-gray-200 rounded-full mb-4 text-gray-400">
                    <i class="fas fa-building text-2xl"></i>
                </div>
                <h3 class="text-xl font-bold text-gray-800 mb-2">Belum ada fasilitas</h3>
                <p class="text-gray-500">Data fasilitas belum tersedia saat ini.</p>
            </div>
        @endif

    </div>

@endsection