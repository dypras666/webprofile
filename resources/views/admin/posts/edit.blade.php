@extends('layouts.admin')

@section('title', 'Edit Post')



@section('content')
<div class="w-full">
    <div class="bg-white">
        <form action="{{ route('admin.posts.update', $post) }}" method="POST" enctype="multipart/form-data" class="">
            @csrf
            @method('PUT')
            
            <!-- Header -->
            <div class="bg-gray-50 px-6 py-4 border-b border-gray-200">
                <h1 class="text-2xl font-bold text-gray-900">Edit Post</h1>
                <p class="text-sm text-gray-600 mt-1">Update the post information below.</p>
            </div>
            
            <!-- Form Content -->
             <div class="p-6">
                 <div class="grid grid-cols-1 xl:grid-cols-3 gap-8 min-h-screen">
                <!-- Left Column - Main Content -->
                <div class="xl:col-span-2 p-6 border-r border-gray-200">
                    <div class="space-y-6">
                        <!-- Title -->

                        <div>
                            <label for="title" class="block text-sm font-medium text-gray-700 mb-2">Title *</label>
                            <input type="text" id="title" name="title" value="{{ old('title', $post->title) }}" 
                                   class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-colors text-lg" 
                                   required>
                            @error('title')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>



                        <!-- Content -->
                        <div class="flex-1">
                            <label for="content" class="block text-sm font-medium text-gray-700 mb-2">Content</label>
                            <textarea id="content" name="content" 
                                      class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-colors" 
                                      style="min-height: 500px;"
                                      required>{{ old('content', $post->content) }}</textarea>
                            @error('content')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Excerpt -->
                        <div>
                            <label for="excerpt" class="block text-sm font-medium text-gray-700 mb-2">Excerpt</label>
                            <textarea id="excerpt" name="excerpt" rows="3" 
                                      placeholder="Brief description of the post..."
                                      class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-colors">{{ old('excerpt', $post->excerpt) }}</textarea>
                            @error('excerpt')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- Right Column - Settings & Metadata -->
                <div class="xl:col-span-1 bg-gray-50 p-6">
                    <div class="space-y-6 sticky top-6">
                        <!-- Publish Settings -->
                        <div class="bg-white rounded-lg p-4 shadow-sm">
                            <h3 class="text-lg font-medium text-gray-900 mb-4 flex items-center">
                                <i class="fas fa-cog mr-2 text-blue-600"></i>
                                Publish Settings
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
                                        <label for="is_featured" class="text-sm font-medium text-gray-700">Featured</label>
                                        <input type="checkbox" id="is_featured" name="is_featured" value="1" 
                                               {{ old('is_featured', $post->is_featured) ? 'checked' : '' }}
                                               class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                                    </div>

                                    <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                                        <label for="is_slider" class="text-sm font-medium text-gray-700">Show in Slider</label>
                                        <input type="checkbox" id="is_slider" name="is_slider" value="1" 
                                               {{ old('is_slider', $post->is_slider) ? 'checked' : '' }}
                                               class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                                    </div>
                                </div>

                                <!-- Publish Date -->
                                <div>
                                    <label for="published_at" class="block text-sm font-medium text-gray-700 mb-2">Tanggal Publish</label>
                                    <input type="datetime-local" id="published_at" name="published_at" 
                                           value="{{ old('published_at', $post->published_at ? $post->published_at->format('Y-m-d\TH:i') : now()->format('Y-m-d\TH:i')) }}"
                                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-colors">
                                    @error('published_at')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <!-- Category & Type -->
                        <div class="bg-white rounded-lg p-4 shadow-sm">
                            <h3 class="text-lg font-medium text-gray-900 mb-4 flex items-center">
                                <i class="fas fa-tags mr-2 text-green-600"></i>
                                Classification
                            </h3>
                            
                            <div class="space-y-4">
                                @if($post->type === 'berita')
                                <!-- Category -->
                                <div>
                                    <label for="category_id" class="block text-sm font-medium text-gray-700 mb-2">Category</label>
                                    <select id="category_id" name="category_id" 
                                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-colors" 
                                            required>
                                        @foreach($categories as $category)
                                            <option value="{{ $category->id }}" {{ old('category_id', $post->category_id) == $category->id ? 'selected' : '' }}>
                                                {{ $category->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('category_id')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>
                                @endif

                                <!-- Type Display -->
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Type</label>
                                    <div class="w-full px-3 py-2 bg-gray-100 border border-gray-300 rounded-lg text-gray-700">
                                        @if($post->type === 'gallery')
                                            <i class="fas fa-images mr-2 text-purple-600"></i>Gallery
                                        @elseif($post->type === 'video')
                                            <i class="fas fa-video mr-2 text-red-600"></i>Video
                                        @elseif($post->type === 'page')
                                            <i class="fas fa-file-alt mr-2 text-green-600"></i>Page
                                        @else
                                            <i class="fas fa-newspaper mr-2 text-blue-600"></i>Berita
                                        @endif
                                    </div>
                                    <input type="hidden" name="type" value="{{ $post->type }}">
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
                                :value="$post->resolved_featured_image_id" 
                                label="" 
                                accept="image/*" 
                                :multiple="false" 
                            />
                            @error('featured_image')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        @if($post->type === 'video')
                        <!-- Video URL -->
                        <div class="bg-white rounded-lg p-4 shadow-sm">
                            <h3 class="text-lg font-medium text-gray-900 mb-4 flex items-center">
                                <i class="fas fa-video mr-2 text-red-600"></i>
                                Video URL
                            </h3>
                            
                            <div class="space-y-3">
                                <div>
                                    <label for="video_url" class="block text-sm font-medium text-gray-700 mb-2">Video URL (YouTube, Vimeo, etc.)</label>
                                    <input type="url" id="video_url" name="video_url" 
                                           value="{{ old('video_url', $post->video_url) }}"
                                           placeholder="https://www.youtube.com/watch?v=..."
                                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-colors">
                                    @error('video_url')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>
                                <p class="text-sm text-gray-500">
                                    <i class="fas fa-info-circle mr-1"></i>
                                    Supported: YouTube, Vimeo, and direct video file URLs
                                </p>
                            </div>
                        </div>
                        @endif

                        @if($post->type === 'gallery')
                        <!-- Gallery Images -->
                        <div class="bg-white rounded-lg p-4 shadow-sm">
                            <h3 class="text-lg font-medium text-gray-900 mb-4 flex items-center">
                                <i class="fas fa-images mr-2 text-purple-600"></i>
                                Gallery Images
                            </h3>
                            
                            <x-media-picker 
                                name="gallery_images" 
                                :value="$post->gallery_images" 
                                label="" 
                                accept="image/*" 
                                :multiple="true" 
                            />
                            @error('gallery_images')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                            <p class="text-sm text-gray-500 mt-2">
                                <i class="fas fa-info-circle mr-1"></i>
                                Select multiple images for your gallery
                            </p>
                        </div>
                        @endif

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
                                    Update Post
                                </button>
                                <a href="{{ route('admin.posts.index', ['type' => $post->type]) }}" 
                                   class="w-full px-4 py-3 bg-gray-600 text-white font-medium rounded-lg hover:bg-gray-700 focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 transition-colors text-center block">
                                    <i class="fas fa-arrow-left mr-2"></i>
                                    Back to Posts
                                </a>
                            </div>
                        </div>

                        <!-- Post Info -->
                        <div class="bg-white rounded-lg p-4 shadow-sm">
                            <h3 class="text-lg font-medium text-gray-900 mb-4 flex items-center">
                                <i class="fas fa-info-circle mr-2 text-indigo-600"></i>
                                Post Info
                            </h3>
                            
                            <div class="space-y-2 text-sm text-gray-600">
                                <div class="flex justify-between">
                                    <span>Date:</span>
                                    <span>{{ ($post->published_at ?? $post->created_at)->format('M d, Y') }}</span>
                                </div>
                                <div class="flex justify-between">
                                    <span>Updated:</span>
                                    <span>{{ $post->updated_at->format('M d, Y') }}</span>
                                </div>
                                <div class="flex justify-between">
                                    <span>Status:</span>
                                    <span class="px-2 py-1 rounded-full text-xs {{ $post->is_published ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800' }}">
                                        {{ $post->is_published ? 'Published' : 'Draft' }}
                                    </span>
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
    $(document).ready(function() {
        // Defined custom button for Media Picker
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
            placeholder: 'Write your content here...',
            tabsize: 2,
            height: 450,
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