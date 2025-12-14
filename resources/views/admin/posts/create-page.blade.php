@extends('layouts.admin')

@section('title', 'Create Page')



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
<script type="module">
    $(document).ready(function() {            // Defined custom button for Media Picker
            var MediaPickerButton = function (context) {
                var ui = $.summernote.ui;
                var button = ui.button({
                    contents: '<i class="nav-icon fas fa-image"></i>',
                    tooltip: 'Insert Image from Media Library',
                    click: function () {
                        if (window.mainMediaPicker) {
                            window.mainMediaPicker.openMediaPicker(function(selectedMedia) {
                                if (selectedMedia && selectedMedia.length > 0) {
                                    selectedMedia.forEach(media => {
                                        if (media.type === 'image') {
                                            context.invoke('editor.insertImage', media.url);
                                        }
                                    });
                                }
                            });
                        } else {
                            console.error('Media Picker instance not found');
                            // Fallback to default if needed, or show alert
                            alert('Media Picker not available');
                        }
                    }
                });

                return button.render();
            }

            $('#content').summernote({
                placeholder: 'Write your page content here...',
                tabsize: 2,
                height: 500,
                toolbar: [
                    ['style', ['style']],
                    ['font', ['bold', 'underline', 'clear']],
                    ['color', ['color']],
                    ['para', ['ul', 'ol', 'paragraph']],
                    ['table', ['table']],
                    ['insert', ['link', 'mediaPicker', 'video']], // Replaced 'picture' with 'mediaPicker'
                    ['view', ['fullscreen', 'codeview', 'help']]
                ],
                buttons: {
                    mediaPicker: MediaPickerButton
                },
                callbacks: {
                    onImageUpload: function(files) {
                        // Logic for image upload if needed, for now we use base64 default
                    }
                }
            });
    });
</script>
@endpush