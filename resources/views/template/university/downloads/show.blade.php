@extends('template.university.layouts.app')

@section('title', $download->title . ' - Download')

@section('content')

    {{-- Page Header --}}
    <div class="relative bg-secondary py-16 overflow-hidden">
        <div class="absolute inset-0 opacity-10"
            style="background-image: radial-gradient(#10b981 1px, transparent 1px); background-size: 24px 24px;"></div>

        <div class="container mx-auto px-4 relative z-10 text-center">
            <h1 class="text-2xl md:text-3xl font-bold font-heading text-white">Detail Download</h1>
        </div>
    </div>

    <section class="py-16 bg-gray-50">
        <div class="container mx-auto px-4">
            <div class="max-w-3xl mx-auto bg-white rounded-xl shadow-md border border-gray-100 overflow-hidden">
                <div class="p-8 md:p-10">
                    <div class="flex flex-col md:flex-row gap-8 items-start">

                        {{-- Icon --}}
                        <div
                            class="w-24 h-24 bg-red-50 text-red-500 rounded-2xl flex items-center justify-center text-5xl shrink-0 mx-auto md:mx-0">
                            <i class="fas fa-file-pdf"></i>
                        </div>

                        {{-- Details --}}
                        <div class="flex-grow text-center md:text-left">
                            <h2 class="text-2xl font-bold text-gray-900 mb-3">{{ $download->title }}</h2>
                            <p class="text-gray-600 mb-6 leading-relaxed">
                                {{ $download->description ?? 'Tidak ada deskripsi untuk dokumen ini.' }}
                            </p>

                            <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-8 text-sm">
                                <div class="bg-gray-50 p-3 rounded">
                                    <span class="block text-gray-500 text-xs uppercase mb-1">Ukuran</span>
                                    <span class="font-bold text-gray-800">{{ $download->file_size ?? 'Unknown' }}</span>
                                </div>
                                <div class="bg-gray-50 p-3 rounded">
                                    <span class="block text-gray-500 text-xs uppercase mb-1">Tipe</span>
                                    <span class="font-bold text-gray-800">{{ $download->file_type ?? 'PDF' }}</span>
                                </div>
                                <div class="bg-gray-50 p-3 rounded">
                                    <span class="block text-gray-500 text-xs uppercase mb-1">Diunduh</span>
                                    <span class="font-bold text-gray-800">{{ $download->downloads_count }}x</span>
                                </div>
                                <div class="bg-gray-50 p-3 rounded">
                                    <span class="block text-gray-500 text-xs uppercase mb-1">Tanggal</span>
                                    <span
                                        class="font-bold text-gray-800">{{ $download->created_at->format('d/m/Y') }}</span>
                                </div>
                            </div>

                            {{-- Action --}}
                            <div class="flex justify-center md:justify-start">
                                <form action="{{ route('frontend.downloads.download', $download) }}" method="POST">
                                    @csrf
                                    <button type="submit"
                                        class="px-8 py-3 bg-primary text-white font-bold rounded-lg hover:bg-emerald-700 transition-all shadow-lg shadow-primary/30 flex items-center gap-2 transform hover:-translate-y-1">
                                        <i class="fas fa-download"></i> Download Sekarang
                                    </button>
                                </form>
                            </div>
                        </div>

                    </div>
                </div>
                <div
                    class="bg-gray-50 px-8 py-4 border-t border-gray-100 flex justify-between items-center text-sm text-gray-500">
                    <a href="{{ route('frontend.downloads') }}" class="hover:text-primary transition-colors">&larr;
                        Kembali ke Daftar</a>
                </div>
            </div>
        </div>
    </section>

@endsection