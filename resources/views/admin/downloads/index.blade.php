@extends('layouts.admin')

@section('title', 'Kelola Downloads') 
@section('page-title', 'Downloads')
@section('content')
<div class="max-full mx-auto">
    {{-- Header --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between mb-6">
        <h1 class="text-2xl font-bold text-gray-900 mb-4 sm:mb-0">
            <i class="fas fa-download mr-2 text-blue-600"></i>
            Kelola Downloads
        </h1>
        <a href="{{ route('admin.downloads.create') }}" class="inline-flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-lg shadow-sm transition-colors duration-200">
            <i class="fas fa-plus mr-2"></i>
            Tambah Download
        </a>
    </div>

    {{-- Alert Messages --}}
    @if(session('success'))
        <div class="bg-green-50 border border-green-200 text-green-800 px-4 py-3 rounded-lg mb-6 flex items-center justify-between" role="alert">
            <div class="flex items-center">
                <i class="fas fa-check-circle mr-2 text-green-600"></i>
                {{ session('success') }}
            </div>
            <button type="button" class="text-green-600 hover:text-green-800" onclick="this.parentElement.style.display='none'">
                <i class="fas fa-times"></i>
            </button>
        </div>
    @endif

    @if(session('error'))
        <div class="bg-red-50 border border-red-200 text-red-800 px-4 py-3 rounded-lg mb-6 flex items-center justify-between" role="alert">
            <div class="flex items-center">
                <i class="fas fa-exclamation-triangle mr-2 text-red-600"></i>
                {{ session('error') }}
            </div>
            <button type="button" class="text-red-600 hover:text-red-800" onclick="this.parentElement.style.display='none'">
                <i class="fas fa-times"></i>
            </button>
        </div>
    @endif

    {{-- Statistics Cards --}}
    <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-4 gap-6 mb-8">
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs font-semibold text-blue-600 uppercase tracking-wide mb-1">
                        Total Downloads
                    </p>
                    <p class="text-2xl font-bold text-gray-900">
                        {{ $downloads->total() }}
                    </p>
                </div>
                <div class="p-3 bg-blue-100 rounded-full">
                    <i class="fas fa-download text-2xl text-blue-600"></i>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs font-semibold text-green-600 uppercase tracking-wide mb-1">
                        File Publik
                    </p>
                    <p class="text-2xl font-bold text-gray-900">
                        {{ $downloads->where('is_public', true)->count() }}
                    </p>
                </div>
                <div class="p-3 bg-green-100 rounded-full">
                    <i class="fas fa-unlock text-2xl text-green-600"></i>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs font-semibold text-yellow-600 uppercase tracking-wide mb-1">
                        File Terlindungi
                    </p>
                    <p class="text-2xl font-bold text-gray-900">
                        {{ $downloads->where('is_public', false)->count() }}
                    </p>
                </div>
                <div class="p-3 bg-yellow-100 rounded-full">
                    <i class="fas fa-lock text-2xl text-yellow-600"></i>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs font-semibold text-purple-600 uppercase tracking-wide mb-1">
                        Total Unduhan
                    </p>
                    <p class="text-2xl font-bold text-gray-900">
                        {{ $downloads->sum('download_count') }}
                    </p>
                </div>
                <div class="p-3 bg-purple-100 rounded-full">
                    <i class="fas fa-chart-line text-2xl text-purple-600"></i>
                </div>
            </div>
        </div>
    </div>

    {{-- Filters --}}
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 mb-8">
        <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-semibold text-gray-900">
                <i class="fas fa-filter mr-2 text-blue-600"></i>
                Filter & Pencarian
            </h3>
        </div>
        <div class="p-6">
            <form method="GET" action="{{ route('admin.downloads.index') }}" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-6 gap-4">
                <div class="lg:col-span-2">
                    <label for="search" class="block text-sm font-medium text-gray-700 mb-2">Pencarian</label>
                    <input type="text" 
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500" 
                           id="search" 
                           name="search" 
                           value="{{ request('search') }}"
                           placeholder="Cari judul atau deskripsi...">
                </div>
                
                <div>
                    <label for="category" class="block text-sm font-medium text-gray-700 mb-2">Kategori</label>
                    <select class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500" id="category" name="category">
                        <option value="">Semua Kategori</option>
                        @foreach($categories as $category)
                            <option value="{{ $category->slug }}" {{ request('category') == $category->slug ? 'selected' : '' }}>
                                {{ $category->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                
                <div>
                    <label for="type" class="block text-sm font-medium text-gray-700 mb-2">Tipe</label>
                    <select class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500" id="type" name="type">
                        <option value="">Semua Tipe</option>
                        <option value="document" {{ request('type') == 'document' ? 'selected' : '' }}>Dokumen</option>
                        <option value="image" {{ request('type') == 'image' ? 'selected' : '' }}>Gambar</option>
                        <option value="video" {{ request('type') == 'video' ? 'selected' : '' }}>Video</option>
                        <option value="audio" {{ request('type') == 'audio' ? 'selected' : '' }}>Audio</option>
                        <option value="archive" {{ request('type') == 'archive' ? 'selected' : '' }}>Arsip</option>
                        <option value="other" {{ request('type') == 'other' ? 'selected' : '' }}>Lainnya</option>
                    </select>
                </div>
                
                <div>
                    <label for="status" class="block text-sm font-medium text-gray-700 mb-2">Status</label>
                    <select class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500" id="status" name="status">
                        <option value="">Semua Status</option>
                        <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Aktif</option>
                        <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>Tidak Aktif</option>
                    </select>
                </div>
                
                <div>
                    <label for="access" class="block text-sm font-medium text-gray-700 mb-2">Akses</label>
                    <select class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500" id="access" name="access">
                        <option value="">Semua Akses</option>
                        <option value="public" {{ request('access') == 'public' ? 'selected' : '' }}>Publik</option>
                        <option value="protected" {{ request('access') == 'protected' ? 'selected' : '' }}>Terlindungi</option>
                    </select>
                </div>
                
                <div class="flex items-end">
                    <button type="submit" class="w-full px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white font-medium rounded-lg transition-colors duration-200">
                        <i class="fas fa-search"></i>
                    </button>
                </div>
            </form>
        </div>
    </div>

    {{-- Downloads Table --}}
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 mb-8">
        <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-semibold text-gray-900">
                <i class="fas fa-list mr-2 text-blue-600"></i>
                Daftar Downloads
            </h3>
        </div>
        <div class="p-6">
            @if($downloads->count() > 0)
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-16">#</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Judul</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">File</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Kategori</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tipe</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Akses</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Unduhan</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach($downloads as $download)
                                <tr class="hover:bg-gray-50">
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $downloads->firstItem() + $loop->index }}</td>
                                    <td class="px-6 py-4">
                                        <div class="text-sm font-medium text-gray-900">{{ Str::limit($download->title, 40) }}</div>
                                        @if($download->description)
                                            <div class="text-sm text-gray-500">{{ Str::limit($download->description, 60) }}</div>
                                        @endif
                                        <div class="text-xs text-gray-500 mt-1">
                                            <i class="fas fa-user mr-1"></i>{{ $download->user->name ?? 'Unknown' }}
                                            <i class="fas fa-calendar ml-2 mr-1"></i>{{ $download->created_at->format('d/m/Y') }}
                                        </div>
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="text-sm font-medium text-gray-900 truncate" style="max-width: 120px;" title="{{ $download->file_name }}">
                                            {{ $download->file_name }}
                                        </div>
                                        <div class="text-sm text-gray-500">{{ $download->formatted_file_size }}</div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        @if($download->category_name)
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">{{ $download->category_name }}</span>
                                        @else
                                            <span class="text-gray-500">-</span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        @php
                                            $typeColors = [
                                                'document' => 'bg-blue-100 text-blue-800',
                                                'image' => 'bg-green-100 text-green-800',
                                                'video' => 'bg-red-100 text-red-800',
                                                'audio' => 'bg-yellow-100 text-yellow-800',
                                                'archive' => 'bg-indigo-100 text-indigo-800',
                                                'other' => 'bg-gray-100 text-gray-800'
                                            ];
                                        @endphp
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $typeColors[$download->type] ?? 'bg-gray-100 text-gray-800' }}">
                                            {{ ucfirst($download->type) }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        @if($download->is_public)
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                                <i class="fas fa-unlock mr-1"></i>Publik
                                            </span>
                                        @else
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                                <i class="fas fa-lock mr-1"></i>Terlindungi
                                            </span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        @if($download->is_active)
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">Aktif</span>
                                        @else
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">Tidak Aktif</span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">{{ $download->download_count }}</span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                        <div class="flex space-x-2">
                                            <a href="{{ route('admin.downloads.edit', $download) }}" 
                                               class="inline-flex items-center px-3 py-1 border border-transparent text-xs font-medium rounded-md text-yellow-700 bg-yellow-100 hover:bg-yellow-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-yellow-500" 
                                               title="Edit">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            
                                            <form action="{{ route('admin.downloads.toggle', $download) }}" 
                                                  method="POST" 
                                                  class="inline">
                                                @csrf
                                                @method('PATCH')
                                                <button type="submit" 
                                                        class="inline-flex items-center px-3 py-1 border border-transparent text-xs font-medium rounded-md {{ $download->is_active ? 'text-gray-700 bg-gray-100 hover:bg-gray-200 focus:ring-gray-500' : 'text-green-700 bg-green-100 hover:bg-green-200 focus:ring-green-500' }} focus:outline-none focus:ring-2 focus:ring-offset-2" 
                                                        title="{{ $download->is_active ? 'Nonaktifkan' : 'Aktifkan' }}">
                                                    <i class="fas {{ $download->is_active ? 'fa-eye-slash' : 'fa-eye' }}"></i>
                                                </button>
                                            </form>
                                            
                                            <form action="{{ route('admin.downloads.destroy', $download) }}" 
                                                  method="POST" 
                                                  class="inline" 
                                                  onsubmit="return confirm('Apakah Anda yakin ingin menghapus download ini?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" 
                                                        class="inline-flex items-center px-3 py-1 border border-transparent text-xs font-medium rounded-md text-red-700 bg-red-100 hover:bg-red-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500" 
                                                        title="Hapus">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                
                {{-- Pagination --}}
                <div class="flex justify-between items-center mt-6">
                    <div class="text-sm text-gray-700">
                        Menampilkan {{ $downloads->firstItem() }} sampai {{ $downloads->lastItem() }} 
                        dari {{ $downloads->total() }} hasil
                    </div>
                    {{ $downloads->appends(request()->query())->links() }}
                </div>
            @else
                <div class="text-center py-12">
                    <i class="fas fa-download text-6xl text-gray-400 mb-4"></i>
                    <h3 class="text-lg font-medium text-gray-900 mb-2">Tidak ada download ditemukan</h3>
                    <p class="text-gray-500 mb-6">Belum ada file download yang ditambahkan atau sesuai dengan filter yang dipilih.</p>
                    <a href="{{ route('admin.downloads.create') }}" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                        <i class="fas fa-plus mr-2"></i>
                        Tambah Download Pertama
                    </a>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Auto-submit form on filter change
    const filterElements = document.querySelectorAll('#category, #type, #status, #access');
    filterElements.forEach(function(element) {
        element.addEventListener('change', function() {
            this.closest('form').submit();
        });
    });
    
    // Clear filters
    const clearFiltersBtn = document.querySelector('.btn-clear-filters');
    if (clearFiltersBtn) {
        clearFiltersBtn.addEventListener('click', function(e) {
            e.preventDefault();
            window.location.href = '{{ route("admin.downloads.index") }}';
        });
    }
});
</script>
@endpush