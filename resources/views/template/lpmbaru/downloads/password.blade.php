@extends('template.lpmbaru.layouts.app')

@section('title', 'Protected File - ' . $download->title)

@section('content')
    <div class="relative bg-secondary py-20 overflow-hidden">
        <div class="absolute inset-0 opacity-10"
            style="background-image: url('{{ asset('images/pattern.svg') }}'); background-size: cover;"></div>
        <div class="container mx-auto px-4 relative z-10 text-center">
            <h1 class="text-3xl md:text-5xl font-serif font-bold text-white mb-4">File Terlindungi</h1>
        </div>
    </div>

    <section class="py-16 bg-gray-50 min-h-[60vh] flex items-center justify-center">
        <div class="container mx-auto px-4">
            <div class="max-w-md mx-auto bg-white rounded-2xl shadow-xl overflow-hidden">
                <div class="p-8 text-center">
                    <div
                        class="w-20 h-20 bg-primary/10 rounded-full flex items-center justify-center mx-auto mb-6 text-primary">
                        <i class="fas fa-lock text-4xl"></i>
                    </div>

                    <h2 class="text-xl font-bold text-gray-900 mb-2">Password Diperlukan</h2>
                    <p class="text-gray-600 mb-8 text-sm">
                        Dokumen <strong>"{{ $download->title }}"</strong> dilindungi kata sandi. Silakan masukkan password
                        yang benar untuk melanjutkan.
                    </p>

                    <form action="{{ route('frontend.downloads.download', $download) }}" method="POST" class="text-left">
                        @csrf
                        <div class="mb-6">
                            <label class="block text-gray-700 text-sm font-bold mb-2 ml-1" for="password">Enter
                                Password</label>
                            <div class="relative">
                                <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-gray-400">
                                    <i class="fas fa-key"></i>
                                </span>
                                <input type="password" name="password" id="password" required
                                    class="w-full pl-10 pr-4 py-3 bg-gray-50 border border-gray-200 rounded-xl focus:ring-2 focus:ring-primary focus:border-transparent transition-all outline-none"
                                    placeholder="••••••••">
                            </div>
                            @error('password')
                                <p class="text-red-500 text-xs mt-2 ml-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <button type="submit"
                            class="w-full bg-primary text-white font-bold py-3 rounded-xl hover:bg-secondary transition-colors shadow-lg shadow-primary/30">
                            Buka & Download
                        </button>
                    </form>

                    <div class="mt-6 pt-6 border-t border-gray-100">
                        <a href="{{ route('frontend.downloads.show', $download) }}"
                            class="text-gray-400 hover:text-gray-600 text-sm font-medium transition-colors">
                            <i class="fas fa-arrow-left mr-1"></i> Kembali ke Detail
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection