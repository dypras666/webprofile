@extends('layouts.admin')

@section('title', 'Create Page')

@push('styles')
<style>
    .tox-tinymce {
        border-radius: 0.5rem !important;
        border: 1px solid #d1d5db !important;
    }
    .tox .tox-editor-header {
        border-radius: 0.5rem 0.5rem 0 0 !important;
    }
</style>
@endpush

@section('content')
<div class="w-full">
    <div class="bg-white">
        <form action="{{ route('admin.pages.store') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
            @csrf
            
            <!-- Hidden field for type -->
            <input type="hidden" name="type" value="page">
            
            <!-- Header -->
            <div class="bg-gray-50 px-6 py-4 border-b border-gray-200">
                <h1 class="text-2xl font-bold text-gray-900">Create New Page</h1>
                <p class="text-sm text-gray-600 mt-1">Create a static page for your website.</p>
            </div>
            
            <!-- Form Content -->
            <div class="p-6">
                <div class="grid grid-cols-1 xl:grid-cols-3 gap-8 min-h-screen">
                    <!-- Left Column - Main Content -->
                    <div class="xl:col-span-2 p-6 border-r border-gray-200">
                        <div class="space-y-6">
                            <!-- Title -->
                            <div>
                                <label for="title" class="block text-sm font-medium text-gray-700 mb-2">Page Title *</label>
                                <input type="text" id="title" name="title" value="{{ old('title') }}" 
                                       class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-colors text-lg" 
                                       placeholder="Enter page title..."
                                       required>
                                @error('title')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Content -->
                            <div class="flex-1">
                                <label for="content" class="block text-sm font-medium text-gray-700 mb-2">Page Content *</label>
                                <textarea id="content" name="content" 
                                          class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-colors" 
                                          style="min-height: 500px;"
                                          placeholder="Write your page content here..."
                                          required>{{ old('content') }}</textarea>
                                @error('content')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Excerpt -->
                            <div>
                                <label for="excerpt" class="block text-sm font-medium text-gray-700 mb-2">Page Description</label>
                                <textarea id="excerpt" name="excerpt" rows="3" 
                                          class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-colors"
                                          placeholder="Brief description of this page (optional)">{{ old('excerpt') }}</textarea>
                                <p class="mt-1 text-sm text-gray-500">This will be used for SEO meta description.</p>
                                @error('excerpt')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <!-- Right Column - Settings & Metadata -->
                    <div class="xl:col-span-1 bg-gray-50 p-6">
                        <div class="space-y-6 sticky top-6">
                            <!-- Page Settings -->
                            <div class="bg-white rounded-lg p-4 shadow-sm">
                                <h3 class="text-lg font-medium text-gray-900 mb-4 flex items-center">
                                    <i class="fas fa-cog mr-2 text-blue-600"></i>
                                    Page Settings
                                </h3>
                                
                                <div class="space-y-4">
                                    <!-- Status Options -->
                                    <div class="space-y-3">
                                        <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                                            <label for="is_published" class="text-sm font-medium text-gray-700">Published</label>
                                            <input type="checkbox" id="is_published" name="is_published" value="1" 
                                                   {{ old('is_published', true) ? 'checked' : '' }}
                                                   class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                                        </div>

                                        <!-- Publish Date -->
                                        <div class="p-3 bg-gray-50 rounded-lg">
                                            <label for="published_at" class="block text-sm font-medium text-gray-700 mb-2">
                                                <i class="fas fa-calendar-alt mr-1 text-blue-600"></i>
                                                Publish Date
                                            </label>
                                            <input type="datetime-local" 
                                                   id="published_at" 
                                                   name="published_at" 
                                                   value="{{ old('published_at', now()->format('Y-m-d\TH:i')) }}"
                                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-colors text-sm">
                                            <p class="mt-1 text-xs text-gray-500">Set when this page should be published. Leave as current time for immediate publishing.</p>
                                            @error('published_at')
                                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                            @enderror
                                        </div>

                                        <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                                            <label for="is_featured" class="text-sm font-medium text-gray-700">Featured Page</label>
                                            <input type="checkbox" id="is_featured" name="is_featured" value="1" 
                                                   {{ old('is_featured') ? 'checked' : '' }}
                                                   class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Page Info -->
                            <div class="bg-white rounded-lg p-4 shadow-sm">
                                <h3 class="text-lg font-medium text-gray-900 mb-4 flex items-center">
                                    <i class="fas fa-info-circle mr-2 text-green-600"></i>
                                    Page Information
                                </h3>
                                
                                <div class="space-y-3 text-sm text-gray-600">
                                    <div class="flex items-center p-2 bg-blue-50 rounded">
                                        <i class="fas fa-file-alt mr-2 text-blue-600"></i>
                                        <span>Type: Static Page</span>
                                    </div>
                                    <div class="flex items-center p-2 bg-green-50 rounded">
                                        <i class="fas fa-eye mr-2 text-green-600"></i>
                                        <span>Visible in navigation</span>
                                    </div>
                                    <div class="flex items-center p-2 bg-yellow-50 rounded">
                                        <i class="fas fa-search mr-2 text-yellow-600"></i>
                                        <span>SEO optimized</span>
                                    </div>
                                </div>
                            </div>

                            <!-- Featured Image -->
                            <div class="bg-white rounded-lg p-4 shadow-sm">
                                <h3 class="text-lg font-medium text-gray-900 mb-4 flex items-center">
                                    <i class="fas fa-image mr-2 text-orange-600"></i>
                                    Featured Image
                                </h3>
                                
                                <x-media-picker 
                                    name="featured_image" 
                                    :value="old('featured_image')" 
                                    label="" 
                                    accept="image/*" 
                                    :multiple="false" 
                                />
                                <p class="mt-2 text-sm text-gray-500">This image will be used as the page header or thumbnail.</p>
                                @error('featured_image')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Action Buttons -->
                            <div class="bg-white rounded-lg p-4 shadow-sm">
                                <h3 class="text-lg font-medium text-gray-900 mb-4 flex items-center">
                                    <i class="fas fa-save mr-2 text-purple-600"></i>
                                    Actions
                                </h3>
                                
                                <div class="space-y-3">
                                    <button type="submit" 
                                            class="w-full px-4 py-3 bg-blue-600 text-white font-medium rounded-lg hover:bg-blue-700 focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition-colors">
                                        <i class="fas fa-save mr-2"></i>
                                        Create Page
                                    </button>
                                    <a href="{{ route('admin.posts.index') }}" 
                                       class="w-full px-4 py-3 bg-gray-600 text-white font-medium rounded-lg hover:bg-gray-700 focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 transition-colors text-center block">
                                        <i class="fas fa-arrow-left mr-2"></i>
                                        Back to Posts
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.tiny.cloud/1/842imvajtzcmmfcf61kux7jyg2lant2sa691sjcoeh8q38cb/tinymce/6/tinymce.min.js" referrerpolicy="origin"></script>
<script>
    tinymce.init({
        selector: '#content',
        height: 450,
        menubar: false,
        plugins: [
            'advlist', 'autolink', 'lists', 'link', 'image', 'charmap', 'preview',
            'anchor', 'searchreplace', 'visualblocks', 'code', 'fullscreen',
            'insertdatetime', 'media', 'table', 'help', 'wordcount', 'emoticons'
        ],
        toolbar: 'undo redo | blocks fontfamily fontsize | ' +
            'bold italic underline strikethrough | forecolor backcolor | ' +
            'alignleft aligncenter alignright alignjustify | ' +
            'bullist numlist outdent indent | ' +
            'table link image media | ' +
            'code preview fullscreen | help',
        content_style: 'body { font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif; font-size: 14px; line-height: 1.6; color: #333; }',
        skin: 'oxide',
        content_css: 'default',
        branding: false,
        promotion: false,
        resize: true,
        statusbar: true,
        elementpath: false,
        image_advtab: true,
        image_caption: true,
        image_title: true,
        automatic_uploads: true,
        file_picker_types: 'image',
        file_picker_callback: function(callback, value, meta) {
            // Create a temporary media picker for TinyMCE
            if (meta.filetype === 'image') {
                // Create modal backdrop
                const backdrop = document.createElement('div');
                backdrop.className = 'fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center';
                backdrop.innerHTML = `
                    <div class="bg-white rounded-lg shadow-xl max-w-4xl w-full mx-4 max-h-[90vh] overflow-hidden">
                        <div class="flex items-center justify-between p-4 border-b">
                            <h3 class="text-lg font-semibold">Select Image from Media Library</h3>
                            <button type="button" class="text-gray-400 hover:text-gray-600" onclick="this.closest('.fixed').remove()">
                                <i class="fas fa-times"></i>
                            </button>
                        </div>
                        <div class="p-4">
                            <div id="tinymce-media-grid" class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-6 gap-4 max-h-96 overflow-y-auto">
                                <div class="text-center py-8 col-span-full">
                                    <i class="fas fa-spinner fa-spin text-2xl text-gray-400"></i>
                                    <p class="text-gray-500 mt-2">Loading media...</p>
                                </div>
                            </div>
                        </div>
                    </div>
                `;
                
                document.body.appendChild(backdrop);
                
                // Load media from API
                fetch('/admin/media/api?type=image&per_page=50')
                    .then(response => response.json())
                    .then(data => {
                        const grid = document.getElementById('tinymce-media-grid');
                        if (data.data && data.data.length > 0) {
                            grid.innerHTML = data.data.map(media => `
                                <div class="relative group cursor-pointer border-2 border-transparent hover:border-blue-500 rounded-lg overflow-hidden" 
                                     onclick="selectTinyMCEMedia('${media.url}', '${media.alt || media.title}')">
                                    <img src="${media.url}" alt="${media.alt || media.title}" 
                                         class="w-full h-24 object-cover">
                                    <div class="absolute inset-0 bg-black bg-opacity-0 group-hover:bg-opacity-30 transition-all duration-200 flex items-center justify-center">
                                        <i class="fas fa-check text-white opacity-0 group-hover:opacity-100 text-xl"></i>
                                    </div>
                                    <div class="absolute bottom-0 left-0 right-0 bg-gradient-to-t from-black to-transparent p-2">
                                        <p class="text-white text-xs truncate">${media.title}</p>
                                    </div>
                                </div>
                            `).join('');
                        } else {
                            grid.innerHTML = `
                                <div class="text-center py-8 col-span-full">
                                    <i class="fas fa-images text-4xl text-gray-400 mb-2"></i>
                                    <p class="text-gray-500">No images found</p>
                                </div>
                            `;
                        }
                    })
                    .catch(error => {
                        console.error('Error loading media:', error);
                        document.getElementById('tinymce-media-grid').innerHTML = `
                            <div class="text-center py-8 col-span-full">
                                <i class="fas fa-exclamation-triangle text-4xl text-red-400 mb-2"></i>
                                <p class="text-red-500">Error loading media</p>
                            </div>
                        `;
                    });
                
                // Global function to select media
                window.selectTinyMCEMedia = function(url, alt) {
                    callback(url, { alt: alt });
                    backdrop.remove();
                    delete window.selectTinyMCEMedia;
                };
            }
        },
        setup: function (editor) {
            editor.on('change', function () {
                editor.save();
            });
            editor.on('init', function () {
                editor.getContainer().style.transition = 'border-color 0.15s ease-in-out, box-shadow 0.15s ease-in-out';
            });
        }
    });
</script>
@endpush