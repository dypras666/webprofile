@extends('layouts.app')

@section('title', 'Password Required - ' . $download->title)

@section('meta')
<meta name="description" content="Masukkan password untuk mengunduh {{ $download->title }}">
<meta name="robots" content="noindex, nofollow">
@endsection

@section('content')
{{-- Breadcrumb --}}
<div class="bg-gray-50 border-b">
    <div class="container mx-auto px-4 py-4">
        <nav class="flex" aria-label="Breadcrumb">
            <ol class="inline-flex items-center space-x-1 md:space-x-3">
                <li class="inline-flex items-center">
                    <a href="{{ route('frontend.index') }}" class="inline-flex items-center text-sm font-medium text-gray-700 hover:text-blue-600">
                        <i class="fas fa-home mr-2"></i>
                        Beranda
                    </a>
                </li>
                <li>
                    <div class="flex items-center">
                        <i class="fas fa-chevron-right text-gray-400 mx-2"></i>
                        <a href="{{ route('frontend.downloads') }}" class="text-sm font-medium text-gray-700 hover:text-blue-600">
                            Download Area
                        </a>
                    </div>
                </li>
                <li>
                    <div class="flex items-center">
                        <i class="fas fa-chevron-right text-gray-400 mx-2"></i>
                        <a href="{{ route('frontend.downloads.show', $download) }}" class="text-sm font-medium text-gray-700 hover:text-blue-600">
                            {{ Str::limit($download->title, 30) }}
                        </a>
                    </div>
                </li>
                <li aria-current="page">
                    <div class="flex items-center">
                        <i class="fas fa-chevron-right text-gray-400 mx-2"></i>
                        <span class="text-sm font-medium text-gray-500">
                            Password Required
                        </span>
                    </div>
                </li>
            </ol>
        </nav>
    </div>
</div>

{{-- Main Content --}}
<main class="container mx-auto px-4 py-12">
    <div class="max-w-md mx-auto">
        {{-- Security Icon --}}
        <div class="text-center mb-8">
            <div class="inline-flex items-center justify-center w-20 h-20 bg-orange-100 rounded-full mb-4">
                <i class="fas fa-lock text-3xl text-orange-600"></i>
            </div>
            <h1 class="text-2xl font-bold text-gray-900 mb-2">File Terlindungi</h1>
            <p class="text-gray-600">
                File ini memerlukan password untuk diunduh
            </p>
        </div>
        
        {{-- File Info Card --}}
        <div class="bg-white rounded-lg shadow-lg border overflow-hidden mb-6">
            <div class="bg-gray-50 px-6 py-4 border-b">
                <h2 class="text-lg font-semibold text-gray-900">Informasi File</h2>
            </div>
            <div class="p-6">
                <h3 class="font-semibold text-gray-900 mb-2 line-clamp-2">
                    {{ $download->title }}
                </h3>
                
                @if($download->description)
                    <p class="text-gray-600 text-sm mb-4 line-clamp-3">
                        {{ $download->description }}
                    </p>
                @endif
                
                <div class="grid grid-cols-2 gap-4 text-sm">
                    <div>
                        <span class="text-gray-500">Nama file:</span>
                        <p class="font-medium text-gray-900 truncate" title="{{ $download->file_name }}">
                            {{ $download->file_name }}
                        </p>
                    </div>
                    <div>
                        <span class="text-gray-500">Ukuran:</span>
                        <p class="font-medium text-gray-900">{{ $download->formatted_file_size }}</p>
                    </div>
                    @if($download->category)
                        <div>
                            <span class="text-gray-500">Kategori:</span>
                            <p class="font-medium text-gray-900">{{ $download->category }}</p>
                        </div>
                    @endif
                    <div>
                        <span class="text-gray-500">Unduhan:</span>
                        <p class="font-medium text-gray-900">{{ $download->download_count }} kali</p>
                    </div>
                </div>
            </div>
        </div>
        
        {{-- Password Form --}}
        <div class="bg-white rounded-lg shadow-lg border overflow-hidden">
            <div class="bg-orange-50 px-6 py-4 border-b">
                <h2 class="text-lg font-semibold text-orange-900 flex items-center">
                    <i class="fas fa-key mr-2"></i>
                    Masukkan Password
                </h2>
            </div>
            
            <form action="{{ route('frontend.downloads.download', $download) }}" method="POST" class="p-6">
                @csrf
                
                {{-- Error Messages --}}
                @if($errors->any())
                    <div class="bg-red-50 border border-red-200 rounded-lg p-4 mb-6">
                        <div class="flex items-center">
                            <i class="fas fa-exclamation-triangle text-red-500 mr-2"></i>
                            <span class="text-red-800 font-medium">Password salah!</span>
                        </div>
                        <p class="text-red-700 text-sm mt-1">
                            Silakan periksa kembali password yang Anda masukkan.
                        </p>
                    </div>
                @endif
                
                {{-- Password Input --}}
                <div class="mb-6">
                    <label for="password" class="block text-sm font-medium text-gray-700 mb-2">
                        Password <span class="text-red-500">*</span>
                    </label>
                    <div class="relative">
                        <input type="password" 
                               id="password" 
                               name="password" 
                               required
                               class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-orange-500 pr-12 @error('password') border-red-300 @enderror"
                               placeholder="Masukkan password file">
                        <button type="button" 
                                onclick="togglePassword()" 
                                class="absolute inset-y-0 right-0 pr-3 flex items-center">
                            <i id="password-icon" class="fas fa-eye text-gray-400 hover:text-gray-600"></i>
                        </button>
                    </div>
                    <p class="text-sm text-gray-500 mt-2">
                        Hubungi administrator jika Anda tidak memiliki password.
                    </p>
                </div>
                
                {{-- Action Buttons --}}
                <div class="space-y-3">
                    <button type="submit" 
                            class="w-full bg-orange-600 text-white py-3 px-6 rounded-lg hover:bg-orange-700 transition-colors font-medium flex items-center justify-center">
                        <i class="fas fa-download mr-2"></i>
                        Unduh File
                    </button>
                    
                    <a href="{{ route('frontend.downloads.show', $download) }}" 
                       class="w-full bg-gray-600 text-white text-center py-3 px-6 rounded-lg hover:bg-gray-700 transition-colors font-medium flex items-center justify-center">
                        <i class="fas fa-arrow-left mr-2"></i>
                        Kembali ke Detail
                    </a>
                </div>
            </form>
        </div>
        
        {{-- Help Section --}}
        <div class="mt-6 bg-blue-50 border border-blue-200 rounded-lg p-4">
            <div class="flex items-start">
                <i class="fas fa-info-circle text-blue-500 mr-3 mt-0.5"></i>
                <div>
                    <h3 class="text-blue-900 font-medium mb-1">Butuh Bantuan?</h3>
                    <p class="text-blue-800 text-sm">
                        Jika Anda tidak memiliki password untuk file ini, silakan hubungi administrator LPM melalui halaman kontak atau email resmi.
                    </p>
                    <div class="mt-2">
                        <a href="{{ route('frontend.contact') }}" 
                           class="text-blue-600 hover:text-blue-800 text-sm font-medium">
                            Hubungi Administrator →
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</main>
@endsection

@push('scripts')
<script>
function togglePassword() {
    const passwordInput = document.getElementById('password');
    const passwordIcon = document.getElementById('password-icon');
    
    if (passwordInput.type === 'password') {
        passwordInput.type = 'text';
        passwordIcon.classList.remove('fa-eye');
        passwordIcon.classList.add('fa-eye-slash');
    } else {
        passwordInput.type = 'password';
        passwordIcon.classList.remove('fa-eye-slash');
        passwordIcon.classList.add('fa-eye');
    }
}

// Auto focus on password input
document.addEventListener('DOMContentLoaded', function() {
    document.getElementById('password').focus();
});
</script>
@endpush