@extends('template.university.layouts.app')

@section('title', 'Galeri Foto - ' . \App\Models\SiteSetting::getValue('site_name'))

@section('content')

    {{-- Page Header --}}
    <div class="relative bg-secondary py-16 lg:py-24 overflow-hidden">
        {{-- Abstract Pattern --}}
        <div class="absolute inset-0 opacity-10"
            style="background-image: radial-gradient(#10b981 1px, transparent 1px); background-size: 24px 24px;"></div>

        <div class="container mx-auto px-4 relative z-10 text-center">
            <h1 class="text-3xl md:text-5xl font-bold font-heading text-white mb-4">Galeri Foto</h1>
            <nav class="flex justify-center items-center text-green-100 text-sm md:text-base gap-2">
                <a href="{{ route('frontend.index') }}" class="hover:text-white transition-colors">Beranda</a>
                <span class="text-green-500">/</span>
                <span class="font-medium text-white">Galeri</span>
            </nav>
        </div>
    </div>

    <section class="py-16 bg-gray-50">
        <div class="container mx-auto px-4">

            @if($images->count() > 0)
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
                    @foreach($images as $image)
                        <div
                            class="group bg-white rounded-xl overflow-hidden shadow-sm hover:shadow-xl transition-all duration-300">
                            <div class="relative aspect-[4/3] overflow-hidden">
                                <img src="{{ $image->featured_image ? Storage::url($image->featured_image) : asset('images/default-gallery.jpg') }}"
                                    alt="{{ $image->title }}"
                                    class="w-full h-full object-cover transition-transform duration-500 group-hover:scale-110">

                                {{-- Overlay --}}
                                <div
                                    class="absolute inset-0 bg-gradient-to-t from-black/80 via-transparent to-transparent opacity-60 group-hover:opacity-80 transition-opacity">
                                </div>

                                <div
                                    class="absolute bottom-0 left-0 right-0 p-4 transform translate-y-2 group-hover:translate-y-0 transition-transform">
                                    <span class="text-xs text-primary font-bold bg-white/90 px-2 py-0.5 rounded mb-2 inline-block">
                                        {{ $image->category->name ?? 'Umum' }}
                                    </span>
                                    <h3 class="text-white font-bold leading-tight">{{ $image->title }}</h3>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

                {{-- Pagination --}}
                <div class="mt-12 flex justify-center">
                    {{ $images->links() }}
                </div>
            @else
                <div class="text-center py-20">
                    <div
                        class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4 text-gray-400 text-2xl">
                        <i class="far fa-images"></i>
                    </div>
                    <h3 class="text-lg font-bold text-gray-900">Belum ada foto</h3>
                    <p class="text-gray-500">Galeri foto belum tersedia saat ini.</p>
                </div>
            @endif

        </div>
    </section>

@endsection