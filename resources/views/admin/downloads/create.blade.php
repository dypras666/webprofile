@extends('layouts.admin')

@section('title', 'Tambah Download')

@section('content')
<div class="container-fluid">
    {{-- Header --}}
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">
            <i class="fas fa-plus mr-2"></i>
            Tambah Download
        </h1>
        <a href="{{ route('admin.downloads.index') }}" class="btn btn-secondary btn-sm shadow-sm">
            <i class="fas fa-arrow-left fa-sm text-white-50 mr-1"></i>
            Kembali
        </a>
    </div>

    {{-- Form Card --}}
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">
                <i class="fas fa-file-upload mr-2"></i>
                Form Tambah Download
            </h6>
        </div>
        <div class="card-body">
            <form action="{{ route('admin.downloads.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                
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
                                   value="{{ old('title') }}"
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
                                      placeholder="Masukkan deskripsi download (opsional)">{{ old('description') }}</textarea>
                            @error('description')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        {{-- File Upload --}}
                        <div class="form-group">
                            <label for="file" class="form-label">
                                File <span class="text-danger">*</span>
                            </label>
                            <div class="custom-file">
                                <input type="file" 
                                       class="custom-file-input @error('file') is-invalid @enderror" 
                                       id="file" 
                                       name="file" 
                                       required>
                                <label class="custom-file-label" for="file">Pilih file...</label>
                            </div>
                            @error('file')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                            <small class="form-text text-muted">
                                Maksimal ukuran file: 50MB. Format yang didukung: PDF, DOC, DOCX, XLS, XLSX, PPT, PPTX, ZIP, RAR, JPG, PNG, MP4, MP3, dll.
                            </small>
                        </div>
                        
                        {{-- Category --}}
                        <div class="form-group">
                            <label for="category" class="form-label">Kategori</label>
                            <input type="text" 
                                   class="form-control @error('category') is-invalid @enderror" 
                                   id="category" 
                                   name="category" 
                                   value="{{ old('category') }}"
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
                                               {{ old('is_public', true) ? 'checked' : '' }}>
                                        <label class="custom-control-label" for="is_public">
                                            <strong>Akses Publik</strong>
                                        </label>
                                    </div>
                                    <small class="form-text text-muted">
                                        Jika diaktifkan, file dapat diunduh tanpa password.
                                    </small>
                                </div>
                                
                                {{-- Password --}}
                                <div class="form-group" id="password-group" style="display: none;">
                                    <label for="password" class="form-label">Password</label>
                                    <input type="password" 
                                           class="form-control @error('password') is-invalid @enderror" 
                                           id="password" 
                                           name="password" 
                                           value="{{ old('password') }}"
                                           placeholder="Masukkan password">
                                    @error('password')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <small class="form-text text-muted">
                                        Password diperlukan untuk file yang tidak publik.
                                    </small>
                                </div>
                            </div>
                        </div>
                        
                        {{-- Status Settings --}}
                        <div class="card border-left-success mb-4">
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
                                               {{ old('is_active', true) ? 'checked' : '' }}>
                                        <label class="custom-control-label" for="is_active">
                                            <strong>Status Aktif</strong>
                                        </label>
                                    </div>
                                    <small class="form-text text-muted">
                                        File hanya dapat diunduh jika status aktif.
                                    </small>
                                </div>
                                
                                {{-- Sort Order --}}
                                <div class="form-group">
                                    <label for="sort_order" class="form-label">Urutan Tampil</label>
                                    <input type="number" 
                                           class="form-control @error('sort_order') is-invalid @enderror" 
                                           id="sort_order" 
                                           name="sort_order" 
                                           value="{{ old('sort_order', 0) }}"
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
                        
                        {{-- File Type Info --}}
                        <div class="card border-left-info">
                            <div class="card-header py-2">
                                <h6 class="m-0 font-weight-bold text-info">
                                    <i class="fas fa-info-circle mr-2"></i>
                                    Informasi File
                                </h6>
                            </div>
                            <div class="card-body">
                                <div class="small text-muted">
                                    <p class="mb-2"><strong>Tipe file yang didukung:</strong></p>
                                    <ul class="mb-2 pl-3">
                                        <li><strong>Dokumen:</strong> PDF, DOC, DOCX, XLS, XLSX, PPT, PPTX</li>
                                        <li><strong>Gambar:</strong> JPG, JPEG, PNG, GIF, SVG</li>
                                        <li><strong>Video:</strong> MP4, AVI, MOV, WMV</li>
                                        <li><strong>Audio:</strong> MP3, WAV, OGG</li>
                                        <li><strong>Arsip:</strong> ZIP, RAR, 7Z</li>
                                    </ul>
                                    <p class="mb-0">Tipe file akan terdeteksi otomatis berdasarkan ekstensi file.</p>
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
                                Simpan Download
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
            $('#password').attr('required', 'required');
        }
    });
    
    // Initialize password group visibility
    if (!$('#is_public').is(':checked')) {
        $('#password-group').show();
        $('#password').attr('required', 'required');
    }
    
    // Form validation
    $('form').on('submit', function(e) {
        let isPublic = $('#is_public').is(':checked');
        let password = $('#password').val();
        
        if (!isPublic && !password) {
            e.preventDefault();
            alert('Password diperlukan untuk file yang tidak publik!');
            $('#password').focus();
            return false;
        }
    });
});
</script>
@endpush