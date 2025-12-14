@extends('layouts.app')

@section('title', $download->title . ' - Download Area')

@section('meta')
    <meta name="description"
        content="{{ $download->description ?: 'Download ' . $download->title . ' dari LPM Institut Islam Al-Mujaddid Sabak' }}">
    <meta name="keywords" content="download, {{ $download->category }}, {{ $download->title }}, LPM, IIMS">
@endsection

@section('content')
    {{-- Breadcrumb --}}
    <div class="bg-gray-50 border-b">
        <div class="container mx-auto px-4 py-4">
            <nav class="flex" aria-label="Breadcrumb">
                <ol class="inline-flex items-center space-x-1 md:space-x-3">
                    <li class="inline-flex items-center">
                        <a href="{{ route('frontend.index') }}"
                            class="inline-flex items-center text-sm font-medium text-gray-700 hover:text-blue-600">
                            <i class="fas fa-home mr-2"></i>
                            Beranda
                        </a>
                    </li>
                    <li>
                        <div class="flex items-center">
                            <i class="fas fa-chevron-right text-gray-400 mx-2"></i>
                            <a href="{{ route('frontend.downloads') }}"
                                class="text-sm font-medium text-gray-700 hover:text-blue-600">
                                Download Area
                            </a>
                        </div>
                    </li>
                    <li aria-current="page">
                        <div class="flex items-center">
                            <i class="fas fa-chevron-right text-gray-400 mx-2"></i>
                            <span class="text-sm font-medium text-gray-500 truncate max-w-xs">
                                {{ $download->title }}
                            </span>
                        </div>
                    </li>
                </ol>
            </nav>
        </div>
    </div>

    {{-- Main Content --}}
    <main class="container mx-auto px-4 py-12">
        <div class="max-w-4xl mx-auto">
            <div class="bg-white rounded-lg shadow-lg overflow-hidden">
                {{-- Header --}}
                <div class="bg-gradient-to-r from-blue-600 to-indigo-600 text-white p-8">
                    <div class="flex items-start justify-between">
                        <div class="flex-1">
                            <h1 class="text-3xl font-bold mb-4">{{ $download->title }}</h1>

                            <div class="flex flex-wrap items-center space-x-6 text-blue-100">
                                @if($download->category_name)
                                    <div class="flex items-center">
                                        <i class="fas fa-folder mr-2"></i>
                                        <span>{{ $download->category_name }}</span>
                                    </div>
                                @endif

                                <div class="flex items-center">
                                    <i class="fas fa-calendar mr-2"></i>
                                    <span>{{ ($download->published_at ?? $download->created_at)->format('d F Y') }}</span>
                                </div>

                                <div class="flex items-center">
                                    <i class="fas fa-download mr-2"></i>
                                    <span>{{ $download->download_count }} unduhan</span>
                                </div>
                            </div>
                        </div>

                        <div class="ml-6 text-center">
                            @if($download->is_protected)
                                <div class="bg-orange-500 bg-opacity-20 rounded-full p-4 mb-2">
                                    <i class="fas fa-lock text-2xl"></i>
                                </div>
                                <span class="text-sm font-medium">File Terlindungi</span>
                            @else
                                <div class="bg-green-500 bg-opacity-20 rounded-full p-4 mb-2">
                                    <i class="fas fa-unlock text-2xl"></i>
                                </div>
                                <span class="text-sm font-medium">File Publik</span>
                            @endif
                        </div>
                    </div>
                </div>

                {{-- Content --}}
                <div class="p-8">
                    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                        {{-- Description --}}
                        <div class="lg:col-span-2">
                            <h2 class="text-xl font-bold text-gray-900 mb-4">Deskripsi</h2>
                            @if($download->description)
                                <div class="prose max-w-none text-gray-700">
                                    {!! nl2br(e($download->description)) !!}
                                </div>
                            @else
                                <p class="text-gray-500 italic">Tidak ada deskripsi tersedia untuk file ini.</p>
                            @endif

                            {{-- Additional Info --}}
                            <div class="mt-8">
                                <h3 class="text-lg font-semibold text-gray-900 mb-4">Informasi Tambahan</h3>
                                <div class="bg-gray-50 rounded-lg p-6">
                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                        <div>
                                            <span class="text-sm font-medium text-gray-500">Diupload oleh:</span>
                                            <p class="text-gray-900">{{ $download->user->name ?? 'Admin' }}</p>
                                        </div>
                                        <div>
                                            <span class="text-sm font-medium text-gray-500">Tanggal upload:</span>
                                            <p class="text-gray-900">
                                                {{ ($download->published_at ?? $download->created_at)->format('d F Y, H:i') }}
                                            </p>
                                        </div>
                                        <div>
                                            <span class="text-sm font-medium text-gray-500">Terakhir diperbarui:</span>
                                            <p class="text-gray-900">{{ $download->updated_at->format('d F Y, H:i') }}</p>
                                        </div>
                                        <div>
                                            <span class="text-sm font-medium text-gray-500">Total unduhan:</span>
                                            <p class="text-gray-900">{{ $download->download_count }} kali</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- Sidebar --}}
                        <div class="lg:col-span-1">
                            {{-- File Info Card --}}
                            <div class="bg-gray-50 rounded-lg p-6 mb-6">
                                <h3 class="text-lg font-semibold text-gray-900 mb-4">Informasi File</h3>

                                <div class="space-y-3">
                                    <div class="flex items-center justify-between">
                                        <span class="text-sm text-gray-500">Nama file:</span>
                                        <span class="text-sm font-medium text-gray-900 truncate ml-2"
                                            title="{{ $download->file_name }}">
                                            {{ Str::limit($download->file_name, 20) }}
                                        </span>
                                    </div>

                                    <div class="flex items-center justify-between">
                                        <span class="text-sm text-gray-500">Ukuran:</span>
                                        <span
                                            class="text-sm font-medium text-gray-900">{{ $download->formatted_file_size }}</span>
                                    </div>

                                    <div class="flex items-center justify-between">
                                        <span class="text-sm text-gray-500">Tipe:</span>
                                        <span
                                            class="text-sm font-medium text-gray-900">{{ strtoupper(pathinfo($download->file_name, PATHINFO_EXTENSION)) }}</span>
                                    </div>

                                    <div class="flex items-center justify-between">
                                        <span class="text-sm text-gray-500">Status:</span>
                                        @if($download->is_protected)
                                            <span
                                                class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-orange-100 text-orange-800">
                                                <i class="fas fa-lock mr-1"></i>
                                                Terlindungi
                                            </span>
                                        @else
                                            <span
                                                class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                                <i class="fas fa-unlock mr-1"></i>
                                                Publik
                                            </span>
                                        @endif
                                    </div>
                                </div>
                            </div>

                            {{-- Download Button --}}
                            <div class="space-y-3">
                                @if($download->is_protected)
                                    <a href="{{ route('frontend.downloads.password', $download) }}"
                                        class="w-full bg-orange-600 text-white text-center py-3 px-6 rounded-lg hover:bg-orange-700 transition-colors font-medium flex items-center justify-center">
                                        <i class="fas fa-lock mr-2"></i>
                                        Masukkan Password untuk Unduh
                                    </a>
                                    <p class="text-sm text-gray-500 text-center">
                                        File ini dilindungi password. Anda perlu memasukkan password yang benar untuk mengunduh.
                                    </p>
                                @else
                                    <form action="{{ route('frontend.downloads.download', $download) }}" method="POST">
                                        @csrf
                                        <button type="submit"
                                            class="w-full bg-green-600 text-white py-3 px-6 rounded-lg hover:bg-green-700 transition-colors font-medium flex items-center justify-center">
                                            <i class="fas fa-download mr-2"></i>
                                            Unduh File ({{ $download->formatted_file_size }})
                                        </button>
                                    </form>
                                    <p class="text-sm text-gray-500 text-center">
                                        File ini dapat diunduh secara gratis tanpa perlu login.
                                    </p>
                                @endif

                                <a href="{{ route('frontend.downloads') }}"
                                    class="w-full bg-gray-600 text-white text-center py-3 px-6 rounded-lg hover:bg-gray-700 transition-colors font-medium flex items-center justify-center">
                                    <i class="fas fa-arrow-left mr-2"></i>
                                    Kembali ke Download Area
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Related Downloads --}}
            @if($download->category)
                @php
                    $relatedDownloads = App\Models\Download::active()
                        ->byCategory($download->category)
                        ->where('id', '!=', $download->id)
                        ->limit(3)
                        ->get();
                @endphp

                @if($relatedDownloads->count() > 0)
                    <div class="mt-12">
                        <h2 class="text-2xl font-bold text-gray-900 mb-6">File Terkait</h2>
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                            @foreach($relatedDownloads as $related)
                                <div class="bg-white rounded-lg shadow-md hover:shadow-lg transition-shadow border">
                                    <div class="p-6">
                                        <h3 class="text-lg font-semibold text-gray-900 mb-2 line-clamp-2">
                                            {{ $related->title }}
                                        </h3>
                                        <p class="text-gray-600 text-sm mb-4 line-clamp-2">
                                            {{ $related->description ?: 'Tidak ada deskripsi' }}
                                        </p>
                                        <div class="flex items-center justify-between text-sm text-gray-500 mb-4">
                                            <span>{{ $related->formatted_file_size }}</span>
                                            <span>{{ $related->download_count }} unduhan</span>
                                        </div>
                                        <a href="{{ route('frontend.downloads.show', $related) }}"
                                            class="w-full bg-blue-600 text-white text-center py-2 px-4 rounded-lg hover:bg-blue-700 transition-colors text-sm font-medium">
                                            Lihat Detail
                                        </a>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif
            @endif
        </div>
    </main>
@endsection