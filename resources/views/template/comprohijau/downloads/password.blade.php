@extends('template.comprohijau.layouts.app')

@section('title', 'Dokumen Terproteksi')

@section('content')
    <div class="min-h-[60vh] flex items-center justify-center py-16 bg-gray-50">
        <div class="bg-white p-8 rounded-xl shadow-lg max-w-md w-full border border-gray-100 text-center">

            <div
                class="w-16 h-16 bg-orange-100 text-orange-500 rounded-full flex items-center justify-center mx-auto mb-6 text-2xl">
                <i class="fas fa-lock"></i>
            </div>

            <h2 class="text-2xl font-bold text-gray-900 mb-2">Dokumen Terproteksi</h2>
            <p class="text-gray-500 mb-6 font-medium">
                "{{ $download->title }}"
            </p>
            <p class="text-gray-500 text-sm mb-6">
                Silakan masukkan kata sandi untuk mengunduh dokumen ini.
            </p>

            {{-- Breadcrumb --}}
            <nav class="text-sm text-gray-500 mb-6 flex items-center gap-2 overflow-x-auto whitespace-nowrap">
                <a href="{{ route('frontend.index') }}" class="hover:text-primary">Beranda</a>
                <span>/</span>
                <a href="{{ route('frontend.downloads') }}" class="hover:text-primary">Download</a>
                <span>/</span>
                <span class="text-gray-900 font-bold">Password Diperlukan</span>
            </nav>

            @if(session('error'))
                <div class="bg-red-50 text-red-600 p-3 rounded text-sm mb-4">
                    {{ session('error') }}
                </div>
            @endif

            <form action="{{ route('frontend.downloads.download', $download) }}" method="POST">
                @csrf
                <div class="mb-6">
                    <input type="password" name="password" required autofocus
                        class="w-full px-4 py-3 rounded-lg bg-gray-50 border border-gray-200 focus:bg-white focus:ring-2 focus:ring-primary/20 focus:border-primary transition-colors outline-none text-center text-lg tracking-widest placeholder:tracking-normal"
                        placeholder="Kata Sandi">
                </div>

                <div class="flex gap-3">
                    <a href="{{ route('frontend.downloads') }}"
                        class="w-1/2 px-4 py-3 bg-gray-100 text-gray-700 font-bold rounded-lg hover:bg-gray-200 transition-colors">
                        Batal
                    </a>
                    <button type="submit"
                        class="w-1/2 px-4 py-3 bg-primary text-white font-bold rounded-lg hover:bg-emerald-700 transition-colors shadow-lg shadow-primary/30">
                        Buka
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection