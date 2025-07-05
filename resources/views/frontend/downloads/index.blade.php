@extends('layouts.app')

@section('title', 'Download Area - ' . config('app.name'))

@section('meta')
<meta name="description" content="Download berbagai dokumen, panduan, dan materi dari LPM Institut Islam Al-Mujaddid Sabak">
<meta name="keywords" content="download, dokumen, panduan, LPM, IIMS, mutu, ISO">
@endsection

@section('content')
{{-- Hero Section --}}
<div class="bg-gradient-to-br from-blue-600 via-blue-700 to-indigo-800 text-white">
    <div class="container mx-auto px-4 py-16">
        <div class="text-center">
            <h1 class="text-4xl md:text-5xl font-bold mb-6">
                Download Area
            </h1>
            <p class="text-xl text-blue-100 mb-8">
                Akses berbagai dokumen, panduan, dan materi penjaminan mutu
            </p>
            
            {{-- Search Form --}}
            <div class="max-w-2xl mx-auto">
                <form action="{{ route('frontend.downloads') }}" method="GET" class="flex">
                    @if(request('category'))
                        <input type="hidden" name="category" value="{{ request('category') }}">
                    @endif
                    @if(request('type'))
                        <input type="hidden" name="type" value="{{ request('type') }}">
                    @endif
                    
                    <div class="flex-1 relative">
                        <input type="text" 
                               name="search" 
                               value="{{ request('search') }}"
                               placeholder="Cari dokumen, panduan, atau materi..."
                               class="w-full px-6 py-4 rounded-l-lg text-gray-900 placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-white/50">
                        <svg class="absolute right-4 top-1/2 transform -translate-y-1/2 w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                        </svg>
                    </div>
                    <button type="submit" class="px-8 py-4 bg-white text-blue-600 font-semibold rounded-r-lg hover:bg-blue-50 transition-colors">
                        Cari
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

{{-- Filters & Stats --}}
<div class="bg-white border-b">
    <div class="container mx-auto px-4 py-6">
        <div class="flex flex-col lg:flex-row justify-between items-center space-y-4 lg:space-y-0">
            {{-- Category Filter --}}
            <div class="flex flex-wrap items-center space-x-2">
                <span class="text-gray-600 font-medium mr-4">Kategori:</span>
                <a href="{{ route('frontend.downloads') }}" 
                   class="px-4 py-2 rounded-full text-sm font-medium transition-colors {{ !request('category') ? 'bg-blue-600 text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200' }}">
                    Semua
                </a>
                @foreach($categories as $category)
                    <a href="{{ route('frontend.downloads', ['category' => $category]) }}" 
                       class="px-4 py-2 rounded-full text-sm font-medium transition-colors {{ request('category') == $category ? 'bg-blue-600 text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200' }}">
                        {{ $category }}
                    </a>
                @endforeach
            </div>
            
            {{-- Type Filter --}}
            <div class="flex items-center space-x-2">
                <span class="text-gray-600 font-medium mr-4">Tipe:</span>
                <a href="{{ route('frontend.downloads', array_merge(request()->query(), ['type' => 'public'])) }}" 
                   class="px-4 py-2 rounded-full text-sm font-medium transition-colors {{ request('type') == 'public' ? 'bg-green-600 text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200' }}">
                    <i class="fas fa-unlock mr-1"></i> Publik
                </a>
                <a href="{{ route('frontend.downloads', array_merge(request()->query(), ['type' => 'protected'])) }}" 
                   class="px-4 py-2 rounded-full text-sm font-medium transition-colors {{ request('type') == 'protected' ? 'bg-orange-600 text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200' }}">
                    <i class="fas fa-lock mr-1"></i> Terlindungi
                </a>
            </div>
        </div>
        
        {{-- Stats --}}
        <div class="mt-4 text-sm text-gray-600">
            Menampilkan {{ $downloads->count() }} dari {{ $downloads->total() }} file
            @if(request('search'))
                untuk pencarian "{{ request('search') }}"
            @endif
        </div>
    </div>
</div>

{{-- Main Content --}}
<main class="container mx-auto px-4 py-12">
    @if($downloads->count() > 0)
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-12">
            @foreach($downloads as $download)
                <div class="bg-white rounded-lg shadow-md hover:shadow-lg transition-shadow border">
                    {{-- Header --}}
                    <div class="p-6 border-b">
                        <div class="flex items-start justify-between mb-3">
                            <div class="flex-1">
                                <h3 class="text-lg font-bold text-gray-900 mb-2 line-clamp-2">
                                    {{ $download->title }}
                                </h3>
                                @if($download->category)
                                    <span class="inline-block bg-blue-100 text-blue-800 text-xs font-semibold px-2 py-1 rounded-full">
                                        {{ $download->category }}
                                    </span>
                                @endif
                            </div>
                            <div class="ml-3 flex flex-col items-center">
                                @if($download->is_protected)
                                    <i class="fas fa-lock text-orange-500 text-lg mb-1"></i>
                                    <span class="text-xs text-orange-600 font-medium">Terlindungi</span>
                                @else
                                    <i class="fas fa-unlock text-green-500 text-lg mb-1"></i>
                                    <span class="text-xs text-green-600 font-medium">Publik</span>
                                @endif
                            </div>
                        </div>
                        
                        @if($download->description)
                            <p class="text-gray-600 text-sm line-clamp-3 mb-4">
                                {{ $download->description }}
                            </p>
                        @endif
                    </div>
                    
                    {{-- File Info --}}
                    <div class="p-6">
                        <div class="flex items-center justify-between text-sm text-gray-500 mb-4">
                            <div class="flex items-center">
                                <i class="fas fa-file text-gray-400 mr-2"></i>
                                <span>{{ $download->file_name }}</span>
                            </div>
                            <span class="font-medium">{{ $download->formatted_file_size }}</span>
                        </div>
                        
                        <div class="flex items-center justify-between text-sm text-gray-500 mb-6">
                            <div class="flex items-center">
                                <i class="fas fa-download text-gray-400 mr-2"></i>
                                <span>{{ $download->download_count }} unduhan</span>
                            </div>
                            <div class="flex items-center">
                                <i class="fas fa-calendar text-gray-400 mr-2"></i>
                                <span>{{ $download->created_at->format('d M Y') }}</span>
                            </div>
                        </div>
                        
                        {{-- Action Buttons --}}
                        <div class="flex space-x-2">
                            <a href="{{ route('frontend.downloads.show', $download) }}" 
                               class="flex-1 bg-blue-600 text-white text-center py-2 px-4 rounded-lg hover:bg-blue-700 transition-colors text-sm font-medium">
                                <i class="fas fa-eye mr-2"></i>Detail
                            </a>
                            
                            @if($download->is_protected)
                                <a href="{{ route('frontend.downloads.password', $download) }}" 
                                   class="flex-1 bg-orange-600 text-white text-center py-2 px-4 rounded-lg hover:bg-orange-700 transition-colors text-sm font-medium">
                                    <i class="fas fa-download mr-2"></i>Unduh
                                </a>
                            @else
                                <form action="{{ route('frontend.downloads.download', $download) }}" method="POST" class="flex-1">
                                    @csrf
                                    <button type="submit" 
                                            class="w-full bg-green-600 text-white py-2 px-4 rounded-lg hover:bg-green-700 transition-colors text-sm font-medium">
                                        <i class="fas fa-download mr-2"></i>Unduh
                                    </button>
                                </form>
                            @endif
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
        
        {{-- Pagination --}}
        <div class="flex justify-center">
            {{ $downloads->appends(request()->query())->links() }}
        </div>
    @else
        {{-- No Downloads Found --}}
        <div class="text-center py-16">
            <svg class="w-24 h-24 mx-auto text-gray-400 mb-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
            </svg>
            <h3 class="text-2xl font-bold text-gray-900 mb-4">Tidak Ada File Ditemukan</h3>
            <p class="text-gray-600 mb-6">
                @if(request('search'))
                    Tidak ada file yang cocok dengan pencarian "{{ request('search') }}".
                @else
                    Belum ada file yang tersedia untuk diunduh.
                @endif
            </p>
            @if(request()->hasAny(['search', 'category', 'type']))
                <a href="{{ route('frontend.downloads') }}" 
                   class="inline-flex items-center px-6 py-3 bg-blue-600 text-white font-medium rounded-lg hover:bg-blue-700 transition-colors">
                    <i class="fas fa-arrow-left mr-2"></i>
                    Lihat Semua File
                </a>
            @endif
        </div>
    @endif
</main>
@endsection

@push('scripts')
<script>
// Auto-submit form on filter change
document.addEventListener('DOMContentLoaded', function() {
    // Add any JavaScript functionality here if needed
});
</script>
@endpush