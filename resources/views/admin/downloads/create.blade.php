@extends('layouts.admin')

@section('title', 'Tambah Download')
@section('page-title', 'Tambah Download')

@section('content')
<div class="max-full mx-auto">
    {{-- Header --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between mb-6">
        <h1 class="text-2xl font-bold text-gray-900 mb-4 sm:mb-0">
            <i class="fas fa-plus mr-2 text-blue-600"></i>
            Tambah Download
        </h1>
        <a href="{{ route('admin.downloads.index') }}" class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
            <i class="fas fa-arrow-left mr-2"></i>
            Kembali
        </a>
    </div>

    {{-- Form Card --}}
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 mb-8">
        <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-semibold text-gray-900">
                <i class="fas fa-file-upload mr-2 text-blue-600"></i>
                Form Tambah Download
            </h3>
        </div>
        <div class="p-6">
            <form action="{{ route('admin.downloads.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                
                <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                    {{-- Left Column --}}
                    <div class="lg:col-span-2">
                        {{-- Title --}}
                        <div class="mb-6">
                            <label for="title" class="block text-sm font-medium text-gray-700 mb-2">
                                Judul <span class="text-red-500">*</span>
                            </label>
                            <input type="text" 
                                   class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 @error('title') border-red-300 @enderror" 
                                   id="title" 
                                   name="title" 
                                   value="{{ old('title') }}"
                                   placeholder="Masukkan judul download"
                                   required>
                            @error('title')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                        
                        {{-- Description --}}
                        <div class="mb-6">
                            <label for="description" class="block text-sm font-medium text-gray-700 mb-2">Deskripsi</label>
                            <textarea class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 @error('description') border-red-300 @enderror" 
                                      id="description" 
                                      name="description" 
                                      rows="4"
                                      placeholder="Masukkan deskripsi download (opsional)">{{ old('description') }}</textarea>
                            @error('description')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                        
                        {{-- File Upload --}}
                        <div class="mb-6">
                            <label for="file" class="block text-sm font-medium text-gray-700 mb-2">
                                File <span class="text-red-500">*</span>
                            </label>
                            <div class="relative">
                                <input type="file" 
                                       class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 @error('file') border-red-300 @enderror" 
                                       id="file" 
                                       name="file" 
                                       required>
                            </div>
                            @error('file')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                            <p class="mt-1 text-sm text-gray-500">
                                Maksimal ukuran file: 50MB. Format yang didukung: PDF, DOC, DOCX, XLS, XLSX, PPT, PPTX, ZIP, RAR, JPG, PNG, MP4, MP3, dll.
                            </p>
                        </div>
                        
                        {{-- Category --}}
                        <div class="mb-6">
                            <label for="category" class="block text-sm font-medium text-gray-700 mb-2">Kategori</label>
                            <input type="text" 
                                   class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 @error('category') border-red-300 @enderror" 
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
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                    
                    {{-- Right Column --}}
                    <div class="lg:col-span-1">
                        {{-- Access Settings --}}
                        <div class="bg-white rounded-lg border border-blue-200 border-l-4 border-l-blue-500 mb-6">
                            <div class="px-4 py-3 border-b border-gray-200">
                                <h4 class="text-sm font-semibold text-blue-700">
                                    <i class="fas fa-shield-alt mr-2"></i>
                                    Pengaturan Akses
                                </h4>
                            </div>
                            <div class="p-4">
                                {{-- Public Access --}}
                                <div class="mb-4">
                                    <div class="flex items-center">
                                        <input type="checkbox" 
                                               class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded" 
                                               id="is_public" 
                                               name="is_public" 
                                               value="1"
                                               {{ old('is_public', true) ? 'checked' : '' }}>
                                        <label class="ml-2 block text-sm font-medium text-gray-900" for="is_public">
                                            Akses Publik
                                        </label>
                                    </div>
                                    <p class="mt-1 text-xs text-gray-500">
                                        Jika diaktifkan, file dapat diunduh tanpa password.
                                    </p>
                                </div>
                                
                                {{-- Password --}}
                                <div class="mb-4 hidden" id="password-group">
                                    <label for="password" class="block text-sm font-medium text-gray-700 mb-2">Password</label>
                                    <input type="password" 
                                           class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 @error('password') border-red-300 @enderror" 
                                           id="password" 
                                           name="password" 
                                           value="{{ old('password') }}"
                                           placeholder="Masukkan password">
                                    @error('password')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                    <p class="mt-1 text-xs text-gray-500">
                                        Password diperlukan untuk file yang tidak publik.
                                    </p>
                                </div>
                            </div>
                        </div>
                        
                        {{-- Status Settings --}}
                        <div class="bg-white rounded-lg border border-green-200 border-l-4 border-l-green-500 mb-6">
                            <div class="px-4 py-3 border-b border-gray-200">
                                <h4 class="text-sm font-semibold text-green-700">
                                    <i class="fas fa-cog mr-2"></i>
                                    Pengaturan Status
                                </h4>
                            </div>
                            <div class="p-4">
                                {{-- Active Status --}}
                                <div class="mb-4">
                                    <div class="flex items-center">
                                        <input type="checkbox" 
                                               class="h-4 w-4 text-green-600 focus:ring-green-500 border-gray-300 rounded" 
                                               id="is_active" 
                                               name="is_active" 
                                               value="1"
                                               {{ old('is_active', true) ? 'checked' : '' }}>
                                        <label class="ml-2 block text-sm font-medium text-gray-900" for="is_active">
                                            Status Aktif
                                        </label>
                                    </div>
                                    <p class="mt-1 text-xs text-gray-500">
                                        File hanya dapat diunduh jika status aktif.
                                    </p>
                                </div>
                                
                                {{-- Sort Order --}}
                                <div class="mb-4">
                                    <label for="sort_order" class="block text-sm font-medium text-gray-700 mb-2">Urutan Tampil</label>
                                    <input type="number" 
                                           class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-green-500 focus:border-green-500 @error('sort_order') border-red-300 @enderror" 
                                           id="sort_order" 
                                           name="sort_order" 
                                           value="{{ old('sort_order', 0) }}"
                                           min="0">
                                    @error('sort_order')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                    <p class="mt-1 text-xs text-gray-500">
                                        Angka lebih kecil akan ditampilkan lebih dulu.
                                    </p>
                                </div>
                            </div>
                        </div>
                        
                        {{-- File Type Info --}}
                        <div class="bg-white rounded-lg border border-blue-200 border-l-4 border-l-blue-500">
                            <div class="px-4 py-3 border-b border-gray-200">
                                <h4 class="text-sm font-semibold text-blue-700">
                                    <i class="fas fa-info-circle mr-2"></i>
                                    Informasi File
                                </h4>
                            </div>
                            <div class="p-4">
                                <div class="text-xs text-gray-600">
                                    <p class="mb-2 font-medium">Tipe file yang didukung:</p>
                                    <ul class="mb-2 pl-4 space-y-1">
                                        <li><span class="font-medium">Dokumen:</span> PDF, DOC, DOCX, XLS, XLSX, PPT, PPTX</li>
                                        <li><span class="font-medium">Gambar:</span> JPG, JPEG, PNG, GIF, SVG</li>
                                        <li><span class="font-medium">Video:</span> MP4, AVI, MOV, WMV</li>
                                        <li><span class="font-medium">Audio:</span> MP3, WAV, OGG</li>
                                        <li><span class="font-medium">Arsip:</span> ZIP, RAR, 7Z</li>
                                    </ul>
                                    <p class="mb-0">Tipe file akan terdeteksi otomatis berdasarkan ekstensi file.</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                {{-- Submit Buttons --}}
                <div class="mt-6">
                    <div class="border-t border-gray-200 pt-6">
                        <div class="flex justify-between">
                            <a href="{{ route('admin.downloads.index') }}" class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                                <i class="fas fa-times mr-2"></i>
                                Batal
                            </a>
                            <button type="submit" class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
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
document.addEventListener('DOMContentLoaded', function() {
    // Handle file input change
    const fileInput = document.getElementById('file');
    
    fileInput.addEventListener('change', function() {
        const file = this.files[0];
        if (file) {
            const fileName = file.name;
            const fileSize = (file.size / 1024 / 1024).toFixed(2); // Convert to MB
            
            // Create file info display if it doesn't exist
            let fileInfo = document.getElementById('file-info');
            if (!fileInfo) {
                fileInfo = document.createElement('div');
                fileInfo.id = 'file-info';
                fileInfo.className = 'mt-2';
                this.parentNode.appendChild(fileInfo);
            }
            
            // Update file info display
            fileInfo.innerHTML = `
                <div class="bg-blue-50 border border-blue-200 rounded-md p-3 text-sm">
                    <strong class="text-blue-800">File dipilih:</strong> <span class="text-blue-700">${fileName}</span><br>
                    <strong class="text-blue-800">Ukuran:</strong> <span class="text-blue-700">${fileSize} MB</span>
                </div>
            `;
        }
    });
    
    // Handle public access toggle
    const isPublicCheckbox = document.getElementById('is_public');
    const passwordGroup = document.getElementById('password-group');
    const passwordInput = document.getElementById('password');
    
    isPublicCheckbox.addEventListener('change', function() {
        if (this.checked) {
            passwordGroup.classList.add('hidden');
            passwordInput.removeAttribute('required');
        } else {
            passwordGroup.classList.remove('hidden');
            passwordInput.setAttribute('required', 'required');
        }
    });
    
    // Initialize password group visibility
    if (!isPublicCheckbox.checked) {
        passwordGroup.classList.remove('hidden');
        passwordInput.setAttribute('required', 'required');
    }
    
    // Form validation
    const form = document.querySelector('form');
    form.addEventListener('submit', function(e) {
        const title = document.getElementById('title').value.trim();
        const file = fileInput.files[0];
        const isPublic = isPublicCheckbox.checked;
        const password = passwordInput.value;
        
        if (!title) {
            e.preventDefault();
            alert('Judul harus diisi!');
            document.getElementById('title').focus();
            return false;
        }
        
        if (!file) {
            e.preventDefault();
            alert('File harus dipilih!');
            fileInput.focus();
            return false;
        }
        
        // Check file size (max 50MB)
        if (file.size > 50 * 1024 * 1024) {
            e.preventDefault();
            alert('Ukuran file tidak boleh lebih dari 50MB!');
            fileInput.focus();
            return false;
        }
        
        if (!isPublic && !password) {
            e.preventDefault();
            alert('Password diperlukan untuk file yang tidak publik!');
            passwordInput.focus();
            return false;
        }
    });
});
</script>
@endpush