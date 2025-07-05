@extends('layouts.admin')

@section('title', 'Edit Download - ' . $download->title)

@section('content')
<div class="container-fluid">
    {{-- Header --}}
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">
            <i class="fas fa-edit mr-2"></i>
            Edit Download
        </h1>
        <div>
            <a href="{{ route('admin.downloads.index') }}" class="btn btn-secondary btn-sm shadow-sm mr-2">
                <i class="fas fa-arrow-left fa-sm text-white-50 mr-1"></i>
                Kembali
            </a>
            <a href="{{ $download->file_url }}" target="_blank" class="btn btn-info btn-sm shadow-sm">
                <i class="fas fa-download fa-sm text-white-50 mr-1"></i>
                Unduh File
            </a>
        </div>
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

    {{-- Form Card --}}
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">
                <i class="fas fa-file-edit mr-2"></i>
                Form Edit Download
            </h6>
        </div>
        <div class="card-body">
            <form action="{{ route('admin.downloads.update', $download) }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                
                <div class="row">
                    {{-- Left Column --}}
                    <div class="col-md-8">
                        {{-- Title --}}
                        <div class="form-group">
                            <label for="title" class="form-label">
                                Judul <span class="text-danger">*</span>
                            </label>
                            <input type="text" 
                                   class="form-control @error('title') is-invalid @enderror" 
                                   id="title" 
                                   name="title" 
                                   value="{{ old('title', $download->title) }}"
                                   placeholder="Masukkan judul download"
                                   required>
                            @error('title')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        {{-- Description --}}
                        <div class="form-group">
                            <label for="description" class="form-label">Deskripsi</label>
                            <textarea class="form-control @error('description') is-invalid @enderror" 
                                      id="description" 
                                      name="description" 
                                      rows="4"
                                      placeholder="Masukkan deskripsi download (opsional)">{{ old('description', $download->description) }}</textarea>
                            @error('description')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        {{-- Current File Info --}}
                        <div class="form-group">
                            <label class="form-label">File Saat Ini</label>
                            <div class="card border-left-info">
                                <div class="card-body py-3">
                                    <div class="row align-items-center">
                                        <div class="col-md-8">
                                            <h6 class="mb-1 font-weight-bold">{{ $download->file_name }}</h6>
                                            <div class="small text-muted">
                                                <span class="badge badge-{{ $download->type == 'document' ? 'primary' : ($download->type == 'image' ? 'success' : ($download->type == 'video' ? 'danger' : ($download->type == 'audio' ? 'warning' : 'secondary'))) }}">
                                                    {{ ucfirst($download->type) }}
                                                </span>
                                                <span class="ml-2">{{ $download->formatted_file_size }}</span>
                                                <span class="ml-2">{{ $download->download_count }} unduhan</span>
                                            </div>
                                        </div>
                                        <div class="col-md-4 text-right">
                                            <a href="{{ $download->file_url }}" target="_blank" class="btn btn-sm btn-outline-primary">
                                                <i class="fas fa-download mr-1"></i>
                                                Unduh
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        {{-- File Upload (Replace) --}}
                        <div class="form-group">
                            <label for="file" class="form-label">
                                Ganti File <small class="text-muted">(opsional)</small>
                            </label>
                            <div class="custom-file">
                                <input type="file" 
                                       class="custom-file-input @error('file') is-invalid @enderror" 
                                       id="file" 
                                       name="file">
                                <label class="custom-file-label" for="file">Pilih file baru...</label>
                            </div>
                            @error('file')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                            <small class="form-text text-muted">
                                Biarkan kosong jika tidak ingin mengganti file. Maksimal ukuran file: 50MB.
                            </small>
                        </div>
                        
                        {{-- Category --}}
                        <div class="form-group">
                            <label for="category" class="form-label">Kategori</label>
                            <input type="text" 
                                   class="form-control @error('category') is-invalid @enderror" 
                                   id="category" 
                                   name="category" 
                                   value="{{ old('category', $download->category) }}"
                                   placeholder="Masukkan kategori (opsional)"
                                   list="category-suggestions">
                            <datalist id="category-suggestions">
                                @foreach($categories as $category)
                                    <option value="{{ $category }}">{{ $category }}</option>
                                @endforeach
                            </datalist>
                            @error('category')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    
                    {{-- Right Column --}}
                    <div class="col-md-4">
                        {{-- Download Statistics --}}
                        <div class="card border-left-info mb-4">
                            <div class="card-header py-2">
                                <h6 class="m-0 font-weight-bold text-info">
                                    <i class="fas fa-chart-line mr-2"></i>
                                    Statistik Download
                                </h6>
                            </div>
                            <div class="card-body">
                                <div class="row text-center">
                                    <div class="col-6">
                                        <div class="h4 font-weight-bold text-primary">{{ $download->download_count }}</div>
                                        <div class="small text-muted">Total Unduhan</div>
                                    </div>
                                    <div class="col-6">
                                        <div class="h4 font-weight-bold text-success">{{ $download->created_at->diffForHumans() }}</div>
                                        <div class="small text-muted">Dibuat</div>
                                    </div>
                                </div>
                                <hr class="my-3">
                                <div class="small text-muted">
                                    <div class="d-flex justify-content-between mb-1">
                                        <span>Dibuat oleh:</span>
                                        <span class="font-weight-bold">{{ $download->user->name ?? 'Unknown' }}</span>
                                    </div>
                                    <div class="d-flex justify-content-between">
                                        <span>Terakhir diupdate:</span>
                                        <span class="font-weight-bold">{{ $download->updated_at->format('d/m/Y H:i') }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        {{-- Access Settings --}}
                        <div class="card border-left-primary mb-4">
                            <div class="card-header py-2">
                                <h6 class="m-0 font-weight-bold text-primary">
                                    <i class="fas fa-shield-alt mr-2"></i>
                                    Pengaturan Akses
                                </h6>
                            </div>
                            <div class="card-body">
                                {{-- Public Access --}}
                                <div class="form-group">
                                    <div class="custom-control custom-switch">
                                        <input type="checkbox" 
                                               class="custom-control-input" 
                                               id="is_public" 
                                               name="is_public" 
                                               value="1"
                                               {{ old('is_public', $download->is_public) ? 'checked' : '' }}>
                                        <label class="custom-control-label" for="is_public">
                                            <strong>Akses Publik</strong>
                                        </label>
                                    </div>
                                    <small class="form-text text-muted">
                                        Jika diaktifkan, file dapat diunduh tanpa password.
                                    </small>
                                </div>
                                
                                {{-- Password --}}
                                <div class="form-group" id="password-group" style="{{ old('is_public', $download->is_public) ? 'display: none;' : '' }}">
                                    <label for="password" class="form-label">Password</label>
                                    <input type="password" 
                                           class="form-control @error('password') is-invalid @enderror" 
                                           id="password" 
                                           name="password" 
                                           placeholder="Masukkan password baru (kosongkan jika tidak ingin mengubah)">
                                    @error('password')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <small class="form-text text-muted">
                                        @if($download->password)
                                            Password saat ini sudah diatur. Kosongkan jika tidak ingin mengubah.
                                        @else
                                            Password diperlukan untuk file yang tidak publik.
                                        @endif
                                    </small>
                                </div>
                            </div>
                        </div>
                        
                        {{-- Status Settings --}}
                        <div class="card border-left-success">
                            <div class="card-header py-2">
                                <h6 class="m-0 font-weight-bold text-success">
                                    <i class="fas fa-cog mr-2"></i>
                                    Pengaturan Status
                                </h6>
                            </div>
                            <div class="card-body">
                                {{-- Active Status --}}
                                <div class="form-group">
                                    <div class="custom-control custom-switch">
                                        <input type="checkbox" 
                                               class="custom-control-input" 
                                               id="is_active" 
                                               name="is_active" 
                                               value="1"
                                               {{ old('is_active', $download->is_active) ? 'checked' : '' }}>
                                        <label class="custom-control-label" for="is_active">
                                            <strong>Status Aktif</strong>
                                        </label>
                                    </div>
                                    <small class="form-text text-muted">
                                        File hanya dapat diunduh jika status aktif.
                                    </small>
                                </div>
                                
                                {{-- Sort Order --}}
                                <div class="form-group mb-0">
                                    <label for="sort_order" class="form-label">Urutan Tampil</label>
                                    <input type="number" 
                                           class="form-control @error('sort_order') is-invalid @enderror" 
                                           id="sort_order" 
                                           name="sort_order" 
                                           value="{{ old('sort_order', $download->sort_order) }}"
                                           min="0">
                                    @error('sort_order')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <small class="form-text text-muted">
                                        Angka lebih kecil akan ditampilkan lebih dulu.
                                    </small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                {{-- Submit Buttons --}}
                <div class="row">
                    <div class="col-12">
                        <hr>
                        <div class="d-flex justify-content-between">
                            <a href="{{ route('admin.downloads.index') }}" class="btn btn-secondary">
                                <i class="fas fa-times mr-2"></i>
                                Batal
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save mr-2"></i>
                                Update Download
                            </button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
$(document).ready(function() {
    // Handle file input change
    $('.custom-file-input').on('change', function() {
        let fileName = $(this).val().split('\\').pop();
        $(this).next('.custom-file-label').addClass('selected').html(fileName);
    });
    
    // Handle public access toggle
    $('#is_public').change(function() {
        if ($(this).is(':checked')) {
            $('#password-group').hide();
            $('#password').removeAttr('required');
        } else {
            $('#password-group').show();
            // Only require password if current download doesn't have one
            @if(!$download->password)
                $('#password').attr('required', 'required');
            @endif
        }
    });
    
    // Form validation
    $('form').on('submit', function(e) {
        let isPublic = $('#is_public').is(':checked');
        let password = $('#password').val();
        let hasCurrentPassword = {{ $download->password ? 'true' : 'false' }};
        
        if (!isPublic && !password && !hasCurrentPassword) {
            e.preventDefault();
            alert('Password diperlukan untuk file yang tidak publik!');
            $('#password').focus();
            return false;
        }
    });
});
</script>
@endpush