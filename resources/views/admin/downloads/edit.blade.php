@extends('layouts.admin')

@section('title', 'Edit Download - ' . $download->title)
@section('page-title', 'Edit Download' . $download->title)
@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
    {{-- Header --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between mb-6">
        <h1 class="text-2xl font-bold text-gray-900 mb-4 sm:mb-0">
            <i class="fas fa-edit mr-2 text-blue-600"></i>
            Edit Download
        </h1>
        <div class="flex space-x-2">
            <a href="{{ route('admin.downloads.index') }}" class="inline-flex items-center px-3 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                <i class="fas fa-arrow-left mr-1"></i>
                Kembali
            </a>
            <a href="{{ $download->file_url }}" target="_blank" class="inline-flex items-center px-3 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                <i class="fas fa-download mr-1"></i>
                Unduh File
            </a>
        </div>
    </div>

    {{-- Alert Messages --}}
    @if(session('success'))
        <div class="bg-green-50 border border-green-200 rounded-md p-4 mb-6" role="alert">
            <div class="flex">
                <div class="flex-shrink-0">
                    <i class="fas fa-check-circle text-green-400"></i>
                </div>
                <div class="ml-3">
                    <p class="text-sm font-medium text-green-800">
                        {{ session('success') }}
                    </p>
                </div>
                <div class="ml-auto pl-3">
                    <div class="-mx-1.5 -my-1.5">
                        <button type="button" class="inline-flex bg-green-50 rounded-md p-1.5 text-green-500 hover:bg-green-100 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-offset-green-50 focus:ring-green-600" onclick="this.parentElement.parentElement.parentElement.parentElement.remove()">
                            <span class="sr-only">Dismiss</span>
                            <i class="fas fa-times text-sm"></i>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endif

    @if(session('error'))
        <div class="bg-red-50 border border-red-200 rounded-md p-4 mb-6" role="alert">
            <div class="flex">
                <div class="flex-shrink-0">
                    <i class="fas fa-exclamation-triangle text-red-400"></i>
                </div>
                <div class="ml-3">
                    <p class="text-sm font-medium text-red-800">
                        {{ session('error') }}
                    </p>
                </div>
                <div class="ml-auto pl-3">
                    <div class="-mx-1.5 -my-1.5">
                        <button type="button" class="inline-flex bg-red-50 rounded-md p-1.5 text-red-500 hover:bg-red-100 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-offset-red-50 focus:ring-red-600" onclick="this.parentElement.parentElement.parentElement.parentElement.remove()">
                            <span class="sr-only">Dismiss</span>
                            <i class="fas fa-times text-sm"></i>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endif

    {{-- Form Card --}}
    <div class="bg-white shadow rounded-lg mb-6">
        <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-medium text-gray-900">
                <i class="fas fa-file-edit mr-2 text-blue-600"></i>
                Form Edit Download
            </h3>
        </div>
        <div class="p-6">
            <form action="{{ route('admin.downloads.update', $download) }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                
                <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                    {{-- Left Column --}}
                    <div class="lg:col-span-2 space-y-6">
                        {{-- Title --}}
                        <div>
                            <label for="title" class="block text-sm font-medium text-gray-700 mb-2">
                                Judul <span class="text-red-500">*</span>
                            </label>
                            <input type="text" 
                                   class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-blue-500 focus:border-blue-500 @error('title') border-red-300 focus:ring-red-500 focus:border-red-500 @enderror" 
                                   id="title" 
                                   name="title" 
                                   value="{{ old('title', $download->title) }}"
                                   placeholder="Masukkan judul download"
                                   required>
                            @error('title')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                        
                        {{-- Description --}}
                        <div>
                            <label for="description" class="block text-sm font-medium text-gray-700 mb-2">Deskripsi</label>
                            <textarea class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-blue-500 focus:border-blue-500 @error('description') border-red-300 focus:ring-red-500 focus:border-red-500 @enderror" 
                                      id="description" 
                                      name="description" 
                                      rows="4"
                                      placeholder="Masukkan deskripsi download (opsional)">{{ old('description', $download->description) }}</textarea>
                            @error('description')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                        
                        {{-- Current File Info --}}
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">File Saat Ini</label>
                            <div class="bg-white rounded-lg border border-blue-200 border-l-4 border-l-blue-500">
                                <div class="p-4">
                                    <div class="flex items-center justify-between">
                                        <div class="flex-1">
                                            <h4 class="text-sm font-semibold text-gray-900 mb-2">{{ $download->file_name }}</h4>
                                            <div class="flex items-center space-x-3 text-xs text-gray-600">
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $download->type == 'document' ? 'bg-blue-100 text-blue-800' : ($download->type == 'image' ? 'bg-green-100 text-green-800' : ($download->type == 'video' ? 'bg-red-100 text-red-800' : ($download->type == 'audio' ? 'bg-yellow-100 text-yellow-800' : 'bg-gray-100 text-gray-800'))) }}">
                                                    {{ ucfirst($download->type) }}
                                                </span>
                                                <span>{{ $download->formatted_file_size }}</span>
                                                <span>{{ $download->download_count }} unduhan</span>
                                            </div>
                                        </div>
                                        <div class="ml-4">
                                            <a href="{{ $download->file_url }}" target="_blank" class="inline-flex items-center px-3 py-1.5 border border-blue-300 rounded-md text-xs font-medium text-blue-700 bg-white hover:bg-blue-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                                                <i class="fas fa-download mr-1"></i>
                                                Unduh
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        {{-- File Upload (Replace) --}}
                        <div>
                            <label for="file" class="block text-sm font-medium text-gray-700 mb-2">
                                Ganti File <small class="text-gray-500">(opsional)</small>
                            </label>
                            <input type="file" 
                                   class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-medium file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('file') border-red-300 focus:ring-red-500 focus:border-red-500 @enderror" 
                                   id="file" 
                                   name="file">
                            @error('file')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                            <p class="mt-1 text-xs text-gray-500">
                                Biarkan kosong jika tidak ingin mengganti file. Maksimal ukuran file: 50MB.
                            </p>
                            <div id="file-info" class="mt-2"></div>
                        </div>
                        
                        {{-- Category --}}
                        <div>
                            <label for="category_id" class="block text-sm font-medium text-gray-700 mb-2">Kategori</label>
                            <select class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 @error('category_id') border-red-300 @enderror" 
                                    id="category_id" 
                                    name="category_id">
                                <option value="">Pilih Kategori (Opsional)</option>
                                @foreach($categories as $category)
                                    <option value="{{ $category->id }}" {{ old('category_id', $download->category_id) == $category->id ? 'selected' : '' }}>
                                        {{ $category->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('category_id')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                    
                    {{-- Right Column --}}
                    <div class="lg:col-span-1 space-y-6">
                        {{-- Download Statistics --}}
                        <div class="bg-white rounded-lg border border-blue-200 border-l-4 border-l-blue-500">
                            <div class="px-4 py-3 border-b border-gray-200">
                                <h4 class="text-sm font-semibold text-blue-700">
                                    <i class="fas fa-chart-line mr-2"></i>
                                    Statistik Download
                                </h4>
                            </div>
                            <div class="p-4">
                                <div class="grid grid-cols-2 gap-4 text-center">
                                    <div>
                                        <div class="text-2xl font-bold text-blue-600">{{ $download->download_count }}</div>
                                        <div class="text-xs text-gray-500">Total Unduhan</div>
                                    </div>
                                    <div>
                                        <div class="text-lg font-bold text-green-600">{{ $download->created_at->diffForHumans() }}</div>
                                        <div class="text-xs text-gray-500">Dibuat</div>
                                    </div>
                                </div>
                                <hr class="my-4 border-gray-200">
                                <div class="text-xs text-gray-600 space-y-2">
                                    <div class="flex justify-between">
                                        <span>Dibuat oleh:</span>
                                        <span class="font-medium">{{ $download->user->name ?? 'Unknown' }}</span>
                                    </div>
                                    <div class="flex justify-between">
                                        <span>Terakhir diupdate:</span>
                                        <span class="font-medium">{{ $download->updated_at->format('d/m/Y H:i') }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        {{-- Access Settings --}}
                        <div class="bg-white rounded-lg border border-blue-200 border-l-4 border-l-blue-500">
                            <div class="px-4 py-3 border-b border-gray-200">
                                <h4 class="text-sm font-semibold text-blue-700">
                                    <i class="fas fa-shield-alt mr-2"></i>
                                    Pengaturan Akses
                                </h4>
                            </div>
                            <div class="p-4 space-y-4">
                                {{-- Public Access --}}
                                <div>
                                    <div class="flex items-center">
                                        <input type="checkbox" 
                                               class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded" 
                                               id="is_public" 
                                               name="is_public" 
                                               value="1"
                                               {{ old('is_public', $download->is_public) ? 'checked' : '' }}>
                                        <label class="ml-2 block text-sm font-medium text-gray-900" for="is_public">
                                            Akses Publik
                                        </label>
                                    </div>
                                    <p class="mt-1 text-xs text-gray-500">
                                        Jika diaktifkan, file dapat diunduh tanpa password.
                                    </p>
                                </div>
                                
                                {{-- Password --}}
                                <div id="password-group" class="{{ old('is_public', $download->is_public) ? 'hidden' : '' }}">
                                    <label for="password" class="block text-sm font-medium text-gray-700 mb-2">Password</label>
                                    <input type="password" 
                                           class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-blue-500 focus:border-blue-500 @error('password') border-red-300 focus:ring-red-500 focus:border-red-500 @enderror" 
                                           id="password" 
                                           name="password" 
                                           placeholder="Masukkan password baru (kosongkan jika tidak ingin mengubah)">
                                    @error('password')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                    <p class="mt-1 text-xs text-gray-500">
                                        @if($download->password)
                                            Password saat ini sudah diatur. Kosongkan jika tidak ingin mengubah.
                                        @else
                                            Password diperlukan untuk file yang tidak publik.
                                        @endif
                                    </p>
                                </div>
                            </div>
                        </div>
                        
                        {{-- Status Settings --}}
                        <div class="bg-white rounded-lg border border-green-200 border-l-4 border-l-green-500">
                            <div class="px-4 py-3 border-b border-gray-200">
                                <h4 class="text-sm font-semibold text-green-700">
                                    <i class="fas fa-cog mr-2"></i>
                                    Pengaturan Status
                                </h4>
                            </div>
                            <div class="p-4 space-y-4">
                                {{-- Active Status --}}
                                <div>
                                    <div class="flex items-center">
                                        <input type="checkbox" 
                                               class="h-4 w-4 text-green-600 focus:ring-green-500 border-gray-300 rounded" 
                                               id="is_active" 
                                               name="is_active" 
                                               value="1"
                                               {{ old('is_active', $download->is_active) ? 'checked' : '' }}>
                                        <label class="ml-2 block text-sm font-medium text-gray-900" for="is_active">
                                            Status Aktif
                                        </label>
                                    </div>
                                    <p class="mt-1 text-xs text-gray-500">
                                        File hanya dapat diunduh jika status aktif.
                                    </p>
                                </div>
                                
                                {{-- Sort Order --}}
                                <div>
                                    <label for="sort_order" class="block text-sm font-medium text-gray-700 mb-2">Urutan Tampil</label>
                                    <input type="number" 
                                           class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-blue-500 focus:border-blue-500 @error('sort_order') border-red-300 focus:ring-red-500 focus:border-red-500 @enderror" 
                                           id="sort_order" 
                                           name="sort_order" 
                                           value="{{ old('sort_order', $download->sort_order) }}"
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
document.addEventListener('DOMContentLoaded', function() {
    // Handle file input change
    const fileInput = document.getElementById('file');
    const fileInfo = document.getElementById('file-info');
    
    fileInput.addEventListener('change', function() {
        const file = this.files[0];
        if (file) {
            const fileName = file.name;
            const fileSize = (file.size / 1024 / 1024).toFixed(2); // Convert to MB
            
            // Update file info display
            fileInfo.innerHTML = `
                <div class="bg-blue-50 border border-blue-200 rounded-md p-3 text-sm">
                    <strong class="text-blue-800">File dipilih:</strong> <span class="text-blue-700">${fileName}</span><br>
                    <strong class="text-blue-800">Ukuran:</strong> <span class="text-blue-700">${fileSize} MB</span>
                </div>
            `;
        } else {
            fileInfo.innerHTML = '';
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
            // Only require password if current download doesn't have one
            @if(!$download->password)
                passwordInput.setAttribute('required', 'required');
            @endif
        }
    });
    
    // Form validation
    const form = document.querySelector('form');
    form.addEventListener('submit', function(e) {
        const isPublic = isPublicCheckbox.checked;
        const password = passwordInput.value;
        const hasCurrentPassword = {{ $download->password ? 'true' : 'false' }};
        
        if (!isPublic && !password && !hasCurrentPassword) {
            e.preventDefault();
            alert('Password diperlukan untuk file yang tidak publik!');
            passwordInput.focus();
            return false;
        }
    });
});
</script>
@endpush