@extends('layouts.admin')

@section('title', 'Kelola Downloads')

@section('content')
<div class="container-fluid">
    {{-- Header --}}
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">
            <i class="fas fa-download mr-2"></i>
            Kelola Downloads
        </h1>
        <a href="{{ route('admin.downloads.create') }}" class="btn btn-primary btn-sm shadow-sm">
            <i class="fas fa-plus fa-sm text-white-50 mr-1"></i>
            Tambah Download
        </a>
    </div>

    {{-- Alert Messages --}}
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="fas fa-check-circle mr-2"></i>
            {{ session('success') }}
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="fas fa-exclamation-triangle mr-2"></i>
            {{ session('error') }}
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    @endif

    {{-- Statistics Cards --}}
    <div class="row mb-4">
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                Total Downloads
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                {{ $downloads->total() }}
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-download fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-success shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                File Publik
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                {{ $downloads->where('is_public', true)->count() }}
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-unlock fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-warning shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                File Terlindungi
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                {{ $downloads->where('is_public', false)->count() }}
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-lock fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-info shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                                Total Unduhan
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                {{ $downloads->sum('download_count') }}
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-chart-line fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Filters --}}
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">
                <i class="fas fa-filter mr-2"></i>
                Filter & Pencarian
            </h6>
        </div>
        <div class="card-body">
            <form method="GET" action="{{ route('admin.downloads.index') }}" class="row">
                <div class="col-md-3 mb-3">
                    <label for="search" class="form-label">Pencarian</label>
                    <input type="text" 
                           class="form-control" 
                           id="search" 
                           name="search" 
                           value="{{ request('search') }}"
                           placeholder="Cari judul atau deskripsi...">
                </div>
                
                <div class="col-md-2 mb-3">
                    <label for="category" class="form-label">Kategori</label>
                    <select class="form-control" id="category" name="category">
                        <option value="">Semua Kategori</option>
                        @foreach($categories as $category)
                            <option value="{{ $category }}" {{ request('category') == $category ? 'selected' : '' }}>
                                {{ $category }}
                            </option>
                        @endforeach
                    </select>
                </div>
                
                <div class="col-md-2 mb-3">
                    <label for="type" class="form-label">Tipe</label>
                    <select class="form-control" id="type" name="type">
                        <option value="">Semua Tipe</option>
                        <option value="document" {{ request('type') == 'document' ? 'selected' : '' }}>Dokumen</option>
                        <option value="image" {{ request('type') == 'image' ? 'selected' : '' }}>Gambar</option>
                        <option value="video" {{ request('type') == 'video' ? 'selected' : '' }}>Video</option>
                        <option value="audio" {{ request('type') == 'audio' ? 'selected' : '' }}>Audio</option>
                        <option value="archive" {{ request('type') == 'archive' ? 'selected' : '' }}>Arsip</option>
                        <option value="other" {{ request('type') == 'other' ? 'selected' : '' }}>Lainnya</option>
                    </select>
                </div>
                
                <div class="col-md-2 mb-3">
                    <label for="status" class="form-label">Status</label>
                    <select class="form-control" id="status" name="status">
                        <option value="">Semua Status</option>
                        <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Aktif</option>
                        <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>Tidak Aktif</option>
                    </select>
                </div>
                
                <div class="col-md-2 mb-3">
                    <label for="access" class="form-label">Akses</label>
                    <select class="form-control" id="access" name="access">
                        <option value="">Semua Akses</option>
                        <option value="public" {{ request('access') == 'public' ? 'selected' : '' }}>Publik</option>
                        <option value="protected" {{ request('access') == 'protected' ? 'selected' : '' }}>Terlindungi</option>
                    </select>
                </div>
                
                <div class="col-md-1 mb-3 d-flex align-items-end">
                    <button type="submit" class="btn btn-primary btn-block">
                        <i class="fas fa-search"></i>
                    </button>
                </div>
            </form>
        </div>
    </div>

    {{-- Downloads Table --}}
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">
                <i class="fas fa-list mr-2"></i>
                Daftar Downloads
            </h6>
        </div>
        <div class="card-body">
            @if($downloads->count() > 0)
                <div class="table-responsive">
                    <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                        <thead>
                            <tr>
                                <th width="5%">#</th>
                                <th width="25%">Judul</th>
                                <th width="15%">File</th>
                                <th width="10%">Kategori</th>
                                <th width="8%">Tipe</th>
                                <th width="8%">Akses</th>
                                <th width="8%">Status</th>
                                <th width="8%">Unduhan</th>
                                <th width="13%">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($downloads as $download)
                                <tr>
                                    <td>{{ $downloads->firstItem() + $loop->index }}</td>
                                    <td>
                                        <div class="font-weight-bold">{{ Str::limit($download->title, 40) }}</div>
                                        @if($download->description)
                                            <small class="text-muted">{{ Str::limit($download->description, 60) }}</small>
                                        @endif
                                        <div class="small text-muted mt-1">
                                            <i class="fas fa-user mr-1"></i>{{ $download->user->name ?? 'Unknown' }}
                                            <i class="fas fa-calendar ml-2 mr-1"></i>{{ $download->created_at->format('d/m/Y') }}
                                        </div>
                                    </td>
                                    <td>
                                        <div class="font-weight-bold text-truncate" style="max-width: 120px;" title="{{ $download->file_name }}">
                                            {{ $download->file_name }}
                                        </div>
                                        <small class="text-muted">{{ $download->formatted_file_size }}</small>
                                    </td>
                                    <td>
                                        @if($download->category)
                                            <span class="badge badge-secondary">{{ $download->category }}</span>
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                    <td>
                                        @php
                                            $typeColors = [
                                                'document' => 'primary',
                                                'image' => 'success',
                                                'video' => 'danger',
                                                'audio' => 'warning',
                                                'archive' => 'info',
                                                'other' => 'secondary'
                                            ];
                                        @endphp
                                        <span class="badge badge-{{ $typeColors[$download->type] ?? 'secondary' }}">
                                            {{ ucfirst($download->type) }}
                                        </span>
                                    </td>
                                    <td>
                                        @if($download->is_public)
                                            <span class="badge badge-success">
                                                <i class="fas fa-unlock mr-1"></i>Publik
                                            </span>
                                        @else
                                            <span class="badge badge-warning">
                                                <i class="fas fa-lock mr-1"></i>Terlindungi
                                            </span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($download->is_active)
                                            <span class="badge badge-success">Aktif</span>
                                        @else
                                            <span class="badge badge-danger">Tidak Aktif</span>
                                        @endif
                                    </td>
                                    <td>
                                        <span class="badge badge-info">{{ $download->download_count }}</span>
                                    </td>
                                    <td>
                                        <div class="btn-group" role="group">
                                            <a href="{{ route('admin.downloads.edit', $download) }}" 
                                               class="btn btn-sm btn-warning" 
                                               title="Edit">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            
                                            <form action="{{ route('admin.downloads.toggle', $download) }}" 
                                                  method="POST" 
                                                  class="d-inline">
                                                @csrf
                                                @method('PATCH')
                                                <button type="submit" 
                                                        class="btn btn-sm {{ $download->is_active ? 'btn-secondary' : 'btn-success' }}" 
                                                        title="{{ $download->is_active ? 'Nonaktifkan' : 'Aktifkan' }}">
                                                    <i class="fas {{ $download->is_active ? 'fa-eye-slash' : 'fa-eye' }}"></i>
                                                </button>
                                            </form>
                                            
                                            <form action="{{ route('admin.downloads.destroy', $download) }}" 
                                                  method="POST" 
                                                  class="d-inline" 
                                                  onsubmit="return confirm('Apakah Anda yakin ingin menghapus download ini?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" 
                                                        class="btn btn-sm btn-danger" 
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
                <div class="d-flex justify-content-between align-items-center mt-3">
                    <div class="text-muted">
                        Menampilkan {{ $downloads->firstItem() }} sampai {{ $downloads->lastItem() }} 
                        dari {{ $downloads->total() }} hasil
                    </div>
                    {{ $downloads->appends(request()->query())->links() }}
                </div>
            @else
                <div class="text-center py-5">
                    <i class="fas fa-download fa-3x text-gray-300 mb-3"></i>
                    <h5 class="text-gray-600">Tidak ada download ditemukan</h5>
                    <p class="text-gray-500">Belum ada file download yang ditambahkan atau sesuai dengan filter yang dipilih.</p>
                    <a href="{{ route('admin.downloads.create') }}" class="btn btn-primary">
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
$(document).ready(function() {
    // Auto-submit form on filter change
    $('#category, #type, #status, #access').change(function() {
        $(this).closest('form').submit();
    });
    
    // Clear filters
    $('.btn-clear-filters').click(function(e) {
        e.preventDefault();
        window.location.href = '{{ route("admin.downloads.index") }}';
    });
});
</script>
@endpush