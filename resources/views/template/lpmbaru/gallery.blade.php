@extends('template.lpmbaru.layouts.app')

@section('content')
    <div class="relative bg-secondary py-20 overflow-hidden">
        <div class="absolute inset-0 opacity-10"
            style="background-image: url('{{ asset('images/pattern.svg') }}'); background-size: cover;"></div>
        <div class="container mx-auto px-4 relative z-10 text-center">
            <h1 class="text-3xl md:text-5xl font-serif font-bold text-white mb-4" data-aos="fade-down">Galeri Foto</h1>
            <div class="flex justify-center items-center text-gray-300 text-sm md:text-base gap-2" data-aos="fade-up"
                data-aos-delay="100">
                <a href="{{ route('frontend.index') }}" class="hover:text-primary transition-colors">Beranda</a>
                <span>/</span>
                <span class="text-primary font-medium">Galeri</span>
            </div>
        </div>
    </div>

    <section class="py-16 md:py-20 bg-white">
        <div class="container mx-auto px-4">

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
                @foreach($images as $image)
                    <div class="group relative rounded-xl overflow-hidden shadow-sm hover:shadow-xl transition-all"
                        data-aos="fade-up" data-aos-delay="{{ $loop->index * 50 }}">
                        <div class="aspect-w-4 aspect-h-3">
                            <img src="{{ $image->featured_image ? Storage::url($image->featured_image) : asset('images/default-gallery.jpg') }}"
                                alt="{{ $image->title }}"
                                class="w-full h-full object-cover transition-transform duration-500 group-hover:scale-110">
                        </div>
                        <div
                            class="absolute inset-0 bg-gradient-to-t from-black/80 via-black/20 to-transparent opacity-0 group-hover:opacity-100 transition-opacity p-6 flex flex-col justify-end">
                            <h3
                                class="text-white font-bold text-lg mb-1 transform translate-y-4 group-hover:translate-y-0 transition-transform duration-300">
                                {{ $image->title }}</h3>
                            <p
                                class="text-gray-300 text-sm transform translate-y-4 group-hover:translate-y-0 transition-transform duration-300 delay-75">
                                {{ $image->category->name ?? 'Umum' }}</p>
                        </div>
                    </div>
                @endforeach
            </div>

            <div class="mt-12">
                {{ $images->links() }}
            </div>

        </div>
    </section>
@endsection