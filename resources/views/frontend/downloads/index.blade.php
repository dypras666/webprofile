@extends('layouts.app')

@section('title', 'Download Area - ' . config('app.name'))

@section('meta')
<meta name="description" content="Download berbagai dokumen, panduan, dan materi dari LPM Institut Islam Al-Mujaddid Sabak">
<meta name="keywords" content="download, dokumen, panduan, LPM, IIMS, mutu, ISO">
@endsection

@push('styles')
<link rel="stylesheet" href="{{ asset('datatables/css/dataTables.dataTables.min.css') }}">
<style>
    .line-clamp-2 {
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden;
    }
    .line-clamp-3 {
        display: -webkit-box;
        -webkit-line-clamp: 3;
        -webkit-box-orient: vertical;
        overflow: hidden;
    }
    
    /* DataTable Custom Styles */
    .dataTables_wrapper .dataTables_length select,
    .dataTables_wrapper .dataTables_filter input {
        @apply border border-gray-300 rounded-lg px-3 py-2 text-sm;
    }
    
    .dataTables_wrapper .dataTables_paginate .paginate_button {
        @apply px-3 py-2 mx-1 border border-gray-300 rounded text-sm hover:bg-gray-50;
    }
    
    .dataTables_wrapper .dataTables_paginate .paginate_button.current {
        @apply bg-blue-600 text-white border-blue-600;
    }
</style>
@endpush

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
                    @if(request('view'))
                        <input type="hidden" name="view" value="{{ request('view') }}">
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

{{-- Filters & Controls --}}
<div class="bg-white border-b">
    <div class="container mx-auto px-4 py-6">
        <div class="flex flex-col lg:flex-row justify-between items-center space-y-4 lg:space-y-0">
            {{-- Left Side: Filters --}}
            <div class="flex flex-col sm:flex-row items-center space-y-4 sm:space-y-0 sm:space-x-6">
                {{-- Category Filter --}}
                <div class="flex flex-wrap items-center space-x-2">
                    <span class="text-gray-600 font-medium mr-4">Kategori:</span>
                    <a href="{{ route('frontend.downloads', array_merge(request()->except('category'), request('view') ? ['view' => request('view')] : [])) }}" 
                       class="px-4 py-2 rounded-full text-sm font-medium transition-colors {{ !request('category') ? 'bg-blue-600 text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200' }}">
                        Semua
                    </a>
                    @foreach($categories as $category)
                        <a href="{{ route('frontend.downloads', array_merge(request()->query(), ['category' => $category])) }}" 
                           class="px-4 py-2 rounded-full text-sm font-medium transition-colors {{ request('category') == $category ? 'bg-blue-600 text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200' }}">
                            {{ $category }}
                        </a>
                    @endforeach
                </div>
                
                {{-- Type Filter --}}
                <div class="flex items-center space-x-2">
                    <span class="text-gray-600 font-medium mr-4">Tipe:</span>
                    <a href="{{ route('frontend.downloads', array_merge(request()->except('type'), request('view') ? ['view' => request('view')] : [])) }}" 
                       class="px-4 py-2 rounded-full text-sm font-medium transition-colors {{ !request('type') ? 'bg-gray-600 text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200' }}">
                        Semua
                    </a>
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
            
            {{-- Right Side: View Toggle --}}
            <div class="flex items-center space-x-4">
                {{-- Stats --}}
                <div class="text-sm text-gray-600">
                    {{ $downloads->count() }} dari {{ $downloads->total() }} file
                </div>
                
                {{-- View Toggle --}}
                <div class="flex items-center bg-gray-100 rounded-lg p-1">
                    <a href="{{ route('frontend.downloads', array_merge(request()->except('view'))) }}" 
                       class="px-3 py-2 rounded-md text-sm font-medium transition-colors {{ !request('view') || request('view') == 'cards' ? 'bg-white text-gray-900 shadow-sm' : 'text-gray-600 hover:text-gray-900' }}">
                        <i class="fas fa-th-large mr-2"></i>Cards
                    </a>
                    <a href="{{ route('frontend.downloads', array_merge(request()->query(), ['view' => 'table'])) }}" 
                       class="px-3 py-2 rounded-md text-sm font-medium transition-colors {{ request('view') == 'table' ? 'bg-white text-gray-900 shadow-sm' : 'text-gray-600 hover:text-gray-900' }}">
                        <i class="fas fa-table mr-2"></i>Table
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Main Content --}}
<main class="container mx-auto px-4 py-12">
    @if($downloads->count() > 0)
        {{-- Card View --}}
        @if(!request('view') || request('view') == 'cards')
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
                                    <span class="truncate">{{ $download->file_name }}</span>
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
                                    <span>{{ ($download->published_at ?? $download->created_at)->format('d M Y') }}</span>
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
            
            {{-- Pagination for Cards --}}
            <div class="flex justify-center">
                {{ $downloads->appends(request()->query())->links() }}
            </div>
        @endif
        
        {{-- Table View --}}
        @if(request('view') == 'table')
            <div class="bg-white rounded-lg shadow-md overflow-hidden">
                {{-- Advanced Filters for Table --}}
                <div class="p-6 border-b bg-gray-50">
                    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Cari File</label>
                            <input type="text" id="table-search" placeholder="Cari judul, deskripsi, atau nama file..." 
                                   class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Kategori</label>
                            <select id="category-filter" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                                <option value="">Semua Kategori</option>
                                @foreach($categories as $category)
                                    <option value="{{ $category }}">{{ $category }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Tipe Akses</label>
                            <select id="type-filter" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                                <option value="">Semua Tipe</option>
                                <option value="public">Publik</option>
                                <option value="protected">Terlindungi</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Urutkan</label>
                            <select id="sort-filter" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                                <option value="newest">Terbaru</option>
                                <option value="oldest">Terlama</option>
                                <option value="title">Judul A-Z</option>
                                <option value="downloads">Paling Banyak Diunduh</option>
                                <option value="size">Ukuran File</option>
                            </select>
                        </div>
                    </div>
                </div>
                
                {{-- DataTable --}}
                <div class="overflow-x-auto">
                    <table id="downloads-table" class="w-full">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">File</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Kategori</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tipe</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Ukuran</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Unduhan</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tanggal</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach($downloads as $download)
                                <tr class="hover:bg-gray-50">
                                    <td class="px-6 py-4">
                                        <div class="flex items-start">
                                            <div class="flex-shrink-0 mr-3">
                                                <i class="fas fa-file-alt text-2xl text-gray-400"></i>
                                            </div>
                                            <div class="min-w-0 flex-1">
                                                <h4 class="text-sm font-medium text-gray-900 line-clamp-2">{{ $download->title }}</h4>
                                                @if($download->description)
                                                    <p class="text-sm text-gray-500 line-clamp-2 mt-1">{{ $download->description }}</p>
                                                @endif
                                                <p class="text-xs text-gray-400 mt-1">{{ $download->file_name }}</p>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        @if($download->category)
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                                {{ $download->category }}
                                            </span>
                                        @else
                                            <span class="text-gray-400">-</span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        @if($download->is_protected)
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-orange-100 text-orange-800">
                                                <i class="fas fa-lock mr-1"></i> Terlindungi
                                            </span>
                                        @else
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                                <i class="fas fa-unlock mr-1"></i> Publik
                                            </span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        {{ $download->formatted_file_size }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        <div class="flex items-center">
                                            <i class="fas fa-download text-gray-400 mr-2"></i>
                                            {{ $download->download_count }}
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        {{ ($download->published_at ?? $download->created_at)->format('d M Y') }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                        <div class="flex space-x-2">
                                            <a href="{{ route('frontend.downloads.show', $download) }}" 
                                               class="text-blue-600 hover:text-blue-900" title="Lihat Detail">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            
                                            @if($download->is_protected)
                                                <a href="{{ route('frontend.downloads.password', $download) }}" 
                                                   class="text-orange-600 hover:text-orange-900" title="Unduh (Perlu Password)">
                                                    <i class="fas fa-download"></i>
                                                </a>
                                            @else
                                                <form action="{{ route('frontend.downloads.download', $download) }}" method="POST" class="inline">
                                                    @csrf
                                                    <button type="submit" 
                                                            class="text-green-600 hover:text-green-900" title="Unduh">
                                                        <i class="fas fa-download"></i>
                                                    </button>
                                                </form>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        @endif
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
<script src="{{ asset('jquery/jquery.min.js') }}"></script>
<script src="{{ asset('datatables/js/dataTables.min.js') }}"></script>

<script>
$(document).ready(function() {
    // Initialize DataTable only if table view is active
    @if(request('view') == 'table')
    var table = $('#downloads-table').DataTable({
        "pageLength": 25,
        "lengthMenu": [[10, 25, 50, 100], [10, 25, 50, 100]],
        "order": [[5, "desc"]], // Sort by date column
        "columnDefs": [
            { "orderable": false, "targets": [6] }, // Disable sorting for action column
            { "searchable": false, "targets": [6] }  // Disable search for action column
        ],
        "language": {
            "lengthMenu": "Tampilkan _MENU_ file per halaman",
            "zeroRecords": "Tidak ada file yang ditemukan",
            "info": "Menampilkan _START_ sampai _END_ dari _TOTAL_ file",
            "infoEmpty": "Menampilkan 0 sampai 0 dari 0 file",
            "infoFiltered": "(difilter dari _MAX_ total file)",
            "search": "Cari:",
            "paginate": {
                "first": "Pertama",
                "last": "Terakhir",
                "next": "Selanjutnya",
                "previous": "Sebelumnya"
            }
        },
        "dom": '<"flex flex-col sm:flex-row justify-between items-center mb-4"<"mb-2 sm:mb-0"l><"mb-2 sm:mb-0"f>>rtip'
    });
    
    // Custom search
    $('#table-search').on('keyup', function() {
        table.search(this.value).draw();
    });
    
    // Category filter
    $('#category-filter').on('change', function() {
        var val = this.value;
        table.column(1).search(val ? '^' + val + '$' : '', true, false).draw();
    });
    
    // Type filter
    $('#type-filter').on('change', function() {
        var val = this.value;
        if (val === 'public') {
            table.column(2).search('Publik').draw();
        } else if (val === 'protected') {
            table.column(2).search('Terlindungi').draw();
        } else {
            table.column(2).search('').draw();
        }
    });
    
    // Sort filter
    $('#sort-filter').on('change', function() {
        var val = this.value;
        switch(val) {
            case 'newest':
                table.order([5, 'desc']).draw();
                break;
            case 'oldest':
                table.order([5, 'asc']).draw();
                break;
            case 'title':
                table.order([0, 'asc']).draw();
                break;
            case 'downloads':
                table.order([4, 'desc']).draw();
                break;
            case 'size':
                table.order([3, 'desc']).draw();
                break;
        }
    });
    @endif
});
</script>
@endpush