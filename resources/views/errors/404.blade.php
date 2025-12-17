@extends('template.university.layouts.app')

@section('title', '404 - Halaman Tidak Ditemukan')
@section('description', 'Maaf, halaman yang Anda cari tidak dapat ditemukan.')

@section('content')
    <div class="bg-gray-50 py-16 lg:py-24 min-h-[60vh] flex items-center">
        <div class="container mx-auto px-4">
            <div class="max-w-2xl mx-auto text-center">

                {{-- 404 Graphic/Text --}}
                <div class="mb-8">
                    <span class="text-9xl font-bold font-heading text-primary opacity-10">404</span>
                    <div class="-mt-16">
                        <h1 class="text-3xl md:text-4xl font-bold font-heading text-gray-900 mb-4">
                            Halaman Tidak Ditemukan
                        </h1>
                        <p class="text-gray-600 text-lg">
                            Maaf, halaman yang Anda cari mungkin telah dihapus, namanya diganti, atau tidak tersedia untuk
                            sementara waktu.
                        </p>
                    </div>
                </div>

                {{-- Search Form --}}
                <div class="max-w-md mx-auto mb-8">
                    <form action="{{ route('frontend.search') }}" method="GET" class="relative">
                        <input type="text" name="q" placeholder="Cari di website..."
                            class="w-full px-5 py-3 rounded-full border border-gray-300 focus:outline-none focus:border-primary focus:ring-1 focus:ring-primary pl-12 shadow-sm transition-all">
                        <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                            <i class="fas fa-search text-gray-400"></i>
                        </div>
                        <button type="submit"
                            class="absolute inset-y-1 right-1 px-4 bg-primary text-white rounded-full text-sm font-medium hover:bg-secondary transition-colors">
                            Cari
                        </button>
                    </form>
                </div>

                {{-- Back Button --}}
                <div>
                    <a href="{{ route('frontend.index') }}"
                        class="inline-flex items-center gap-2 px-6 py-3 bg-white text-primary border border-primary rounded-full font-medium hover:bg-primary hover:text-white transition-all duration-300 group">
                        <i class="fas fa-arrow-left group-hover:-translate-x-1 transition-transform"></i>
                        Kembali ke Beranda
                    </a>
                </div>

            </div>
        </div>
    </div>
@endsection