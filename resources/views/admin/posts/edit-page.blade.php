@extends('layouts.admin')

@section('title', 'Edit Page')

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
        <form action="{{ route('admin.pages.update', $post) }}" method="POST" enctype="multipart/form-data" class="">
            @csrf
            @method('PUT')
            
            <!-- Hidden field for type -->
            <input type="hidden" name="type" value="page">
            
            <!-- Header -->
            <div class="bg-gray-50 px-6 py-4 border-b border-gray-200">
                <h1 class="text-2xl font-bold text-gray-900">Edit Page</h1>
                <p class="text-sm text-gray-600 mt-1">Update your static page content.</p>
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
                                <input type="text" id="title" name="title" value="{{ old('title', $post->title) }}" 
                                       class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-colors text-lg" 
                                       placeholder="Enter page title..."
                                       required>
                                @error('title')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Slug -->
                            <div>
                                <label for="slug" class="block text-sm font-medium text-gray-700 mb-2">Page Slug</label>
                                <div class="flex items-center">
                                    <span class="text-sm text-gray-500 mr-2">{{ url('/') }}/</span>
                                    <input type="text" id="slug" name="slug" value="{{ old('slug', $post->slug) }}" 
                                           class="flex-1 px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-colors font-mono text-sm" 
                                           placeholder="page-url">
                                </div>
                                <p class="mt-1 text-sm text-gray-500">Leave empty to auto-generate from title.</p>
                                @error('slug')
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
                                          required>{{ old('content', $post->content) }}</textarea>
                                @error('content')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Excerpt -->
                            <div>
                                <label for="excerpt" class="block text-sm font-medium text-gray-700 mb-2">Page Description</label>
                                <textarea id="excerpt" name="excerpt" rows="3" 
                                          class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-colors"
                                          placeholder="Brief description of this page (optional)">{{ old('excerpt', $post->excerpt) }}</textarea>
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
                                                   {{ old('is_published', $post->is_published) ? 'checked' : '' }}
                                                   class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                                        </div>

                                        <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                                            <label for="is_featured" class="text-sm font-medium text-gray-700">Featured Page</label>
                                            <input type="checkbox" id="is_featured" name="is_featured" value="1" 
                                                   {{ old('is_featured', $post->is_featured) ? 'checked' : '' }}
                                                   class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Page Statistics -->
                            <div class="bg-white rounded-lg p-4 shadow-sm">
                                <h3 class="text-lg font-medium text-gray-900 mb-4 flex items-center">
                                    <i class="fas fa-chart-bar mr-2 text-green-600"></i>
                                    Page Statistics
                                </h3>
                                
                                <div class="space-y-3 text-sm">
                                    <div class="flex justify-between items-center p-2 bg-blue-50 rounded">
                                        <span class="text-gray-600">Views:</span>
                                        <span class="font-semibold text-blue-600">{{ number_format($post->views) }}</span>
                                    </div>
                                    <div class="flex justify-between items-center p-2 bg-green-50 rounded">
                                        <span class="text-gray-600">Created:</span>
                                        <span class="font-semibold text-green-600">{{ $post->created_at->format('M d, Y') }}</span>
                                    </div>
                                    <div class="flex justify-between items-center p-2 bg-yellow-50 rounded">
                                        <span class="text-gray-600">Updated:</span>
                                        <span class="font-semibold text-yellow-600">{{ $post->updated_at->format('M d, Y') }}</span>
                                    </div>
                                </div>
                            </div>

                            <!-- Page Info -->
                            <div class="bg-white rounded-lg p-4 shadow-sm">
                                <h3 class="text-lg font-medium text-gray-900 mb-4 flex items-center">
                                    <i class="fas fa-info-circle mr-2 text-purple-600"></i>
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
                                    :value="old('featured_image', $post->featured_image_id)" 
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
                                        Update Page
                                    </button>
                                    <a href="{{ route('admin.posts.show', $post->id) }}" 
                                       class="w-full px-4 py-3 bg-green-600 text-white font-medium rounded-lg hover:bg-green-700 focus:ring-2 focus:ring-green-500 focus:ring-offset-2 transition-colors text-center block">
                                        <i class="fas fa-eye mr-2"></i>
                                        View Page
                                    </a>
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
        plugins: 'anchor autolink charmap codesample emoticons image link lists media searchreplace table visualblocks wordcount checklist mediaembed casechange export formatpainter pageembed linkchecker a11ychecker tinymcespellchecker permanentpen powerpaste advtable advcode editimage advtemplate ai mentions tinycomments tableofcontents footnotes mergetags autocorrect typography inlinecss',
        toolbar: 'undo redo | blocks fontfamily fontsize | bold italic underline strikethrough | link image media table mergetags | addcomment showcomments | spellcheckdialog a11ycheck typography | align lineheight | checklist numlist bullist indent outdent | emoticons charmap | removeformat',
        tinycomments_mode: 'embedded',
        tinycomments_author: 'Author name',
        mergetags_list: [
            { value: 'First.Name', title: 'First Name' },
            { value: 'Email', title: 'Email' },
        ],
        ai_request: (request, respondWith) => respondWith.string(() => Promise.reject("See docs to implement AI Assistant")),
        height: 450,
        menubar: false,
        branding: false,
        promotion: false,
        file_picker_callback: function(callback, value, meta) {
            // Custom media picker integration
            window.mediaPicker({
                multiple: false,
                onSelect: function(media) {
                    if (media && media.length > 0) {
                        const selectedMedia = media[0];
                        callback(selectedMedia.url, {
                            alt: selectedMedia.alt_text || selectedMedia.title,
                            title: selectedMedia.title
                        });
                    }
                }
            }).open();
        }
    });
</script>
@endpush