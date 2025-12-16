@extends('layouts.admin')

@php
    $currentType = $currentType ?? $type ?? 'berita';
    $pageTitle = match($currentType) {
        'gallery' => 'Create Gallery',
        'video' => 'Create Video',
        'partner' => 'Tambah Partner (Kerja Sama)',
        'event' => 'Create Event',
        'testimonial' => 'Create Testimonial',
        default => 'Create Post'
    };
@endphp

@section('title', $pageTitle)



@section('content')
<div class="w-full">
                <div class="bg-white">
                     <form action="{{ route('admin.posts.store') }}" method="POST" enctype="multipart/form-data" class="">
                         @csrf
                         <input type="hidden" name="type" value="{{ $currentType }}">
                         
                         <!-- Header -->
                         <div class="bg-gray-50 px-6 py-4 border-b border-gray-200">
                             <h1 class="text-2xl font-bold text-gray-900">{{ $pageTitle }}</h1>
                             <p class="text-sm text-gray-600 mt-1">
                                 @if($currentType === 'gallery')
                                     Create a new gallery with multiple images.
                                 @elseif($currentType === 'video')
                                     Create a new video post with video content.
                                 @elseif($currentType === 'partner')
                                     Tambahkan partner kerja sama baru (Logo dan Link).
                                 @elseif($currentType === 'event')
                                     Create a new event or agenda item.
                                 @else
                                     Fill in the post information below.
                                 @endif
                             </p>
                         </div>
                         
                         <!-- Form Content -->
                         <div class="p-6">
                             <div class="grid grid-cols-1 xl:grid-cols-3 gap-8 min-h-screen">
                            <!-- Left Column - Main Content -->
                            <div class="xl:col-span-2 p-6 border-r border-gray-200">
                                <div class="space-y-6">
                                    <!-- Title -->
                                    <div>
                                        <label for="title" class="block text-sm font-medium text-gray-700 mb-2">{{ $currentType === 'partner' ? 'Nama Partner *' : 'Title *' }}</label>
                                        <input type="text" id="title" name="title" value="{{ old('title') }}" 
                                               class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-colors text-lg" 
                                               required>
                                        @error('title')
                                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                        @enderror
                                    </div>

                                    <!-- Content/Link -->
                                    <div class="flex-1">
                                        @if($currentType === 'partner')
                                            <label for="content" class="block text-sm font-medium text-gray-700 mb-2">Link Website (URL)</label>
                                            <input type="url" id="content" name="content" value="{{ old('content') }}" 
                                                   placeholder="https://example.com"
                                                   class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-colors"
                                                   required>
                                            <p class="text-xs text-gray-500 mt-1">Masukkan URL website partner (contoh: https://google.com)</p>
                                        @else
                                            <label for="content" class="block text-sm font-medium text-gray-700 mb-2">Content</label>
                                            <textarea id="content" name="content" 
                                                      class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-colors" 
                                                      style="min-height: 500px;"
                                                      required>{{ old('content') }}</textarea>
                                        @endif
                                        @error('content')
                                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                        @enderror
                                    </div>

                                    @if($currentType !== 'partner')
                                    <!-- Excerpt / Rating -->
                                    <div>
                                        @if($currentType === 'testimonial')
                                            <label for="excerpt" class="block text-sm font-medium text-gray-700 mb-2">Rating (Bintang)</label>
                                            <select id="excerpt" name="excerpt" 
                                                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-colors text-lg text-yellow-500">
                                                <option value="5" {{ old('excerpt') == '5' ? 'selected' : '' }}>⭐⭐⭐⭐⭐ (5/5)</option>
                                                <option value="4" {{ old('excerpt') == '4' ? 'selected' : '' }}>⭐⭐⭐⭐ (4/5)</option>
                                                <option value="3" {{ old('excerpt') == '3' ? 'selected' : '' }}>⭐⭐⭐ (3/5)</option>
                                                <option value="2" {{ old('excerpt') == '2' ? 'selected' : '' }}>⭐⭐ (2/5)</option>
                                                <option value="1" {{ old('excerpt') == '1' ? 'selected' : '' }}>⭐ (1/5)</option>
                                            </select>
                                            <p class="text-xs text-gray-500 mt-1">Pilih jumlah bintang untuk testimoni ini.</p>
                                        @else
                                            <label for="excerpt" class="block text-sm font-medium text-gray-700 mb-2">Excerpt</label>
                                            <textarea id="excerpt" name="excerpt" rows="3" 
                                                      class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-colors">{{ old('excerpt') }}</textarea>
                                        @endif
                                        @error('excerpt')
                                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                        @enderror
                                    </div>
                                    @endif
                                </div>
                            </div>

                            <!-- Right Column - Settings & Metadata -->
                            <div class="xl:col-span-1 bg-gray-50 p-6">
                                <div class="space-y-6 sticky top-6">
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
                                                           {{ old('is_published', true) ? 'checked' : '' }}
                                                           class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                                                </div>

                                                @if($currentType !== 'partner')
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
                                                    <p class="mt-1 text-xs text-gray-500">Set when this post should be published. Leave as current time for immediate publishing.</p>
                                                    @error('published_at')
                                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                                    @enderror
                                                </div>

                                                <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                                                    <label for="is_featured" class="text-sm font-medium text-gray-700">Featured</label>
                                                    <input type="checkbox" id="is_featured" name="is_featured" value="1" 
                                                           {{ old('is_featured') ? 'checked' : '' }}
                                                           class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                                                </div>

                                                <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                                                    <label for="is_slider" class="text-sm font-medium text-gray-700">Show in Slider</label>
                                                    <input type="checkbox" id="is_slider" name="is_slider" value="1" 
                                                           {{ old('is_slider') ? 'checked' : '' }}
                                                           class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                                                </div>
                                                @endif
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Category & Type -->
                                    @if($currentType !== 'partner')
                                    <div class="bg-white rounded-lg p-4 shadow-sm">
                                        <h3 class="text-lg font-medium text-gray-900 mb-4 flex items-center">
                                            <i class="fas fa-tags mr-2 text-green-600"></i>
                                            Classification
                                        </h3>
                                        
                                        <div class="space-y-4">
                                            @if($currentType === 'berita')
                                            <!-- Category -->
                                            <div>
                                                <label for="category_id" class="block text-sm font-medium text-gray-700 mb-2">Category</label>
                                                <select id="category_id" name="category_id" 
                                                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-colors" 
                                                        required>
                                                    <option value="">Select Category</option>
                                                    @foreach($categories as $category)
                                                        <option value="{{ $category->id }}" {{ old('category_id') == $category->id ? 'selected' : '' }}>
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
                                                    @if($currentType === 'gallery')
                                                        <i class="fas fa-images mr-2 text-purple-600"></i>Gallery
                                                    @elseif($currentType === 'video')
                                                        <i class="fas fa-video mr-2 text-red-600"></i>Video
                                                    @elseif($currentType === 'fasilitas')
                                                        <i class="fas fa-building mr-2 text-indigo-600"></i>Fasilitas
                                                    @elseif($currentType === 'event')
                                                        <i class="fas fa-calendar-alt mr-2 text-pink-600"></i>Event
                                                    @else
                                                        <i class="fas fa-newspaper mr-2 text-blue-600"></i>Berita
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
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
                                                Create Post
                                            </button>
                                            <a href="{{ route('admin.posts.index', ['type' => $currentType]) }}" 
                                               class="px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 flex items-center">
                                                <i class="fas fa-arrow-left mr-2"></i>
                                                Back to Posts
                                            </a>
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
                                        @error('featured_image')
                                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                        @enderror
                                    </div>

                                    @if($currentType === 'video')
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
                                                       value="{{ old('video_url') }}"
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

                                    @if($currentType === 'gallery')
                                    <!-- Gallery Images -->
                                    <div class="bg-white rounded-lg p-4 shadow-sm">
                                        <h3 class="text-lg font-medium text-gray-900 mb-4 flex items-center">
                                            <i class="fas fa-images mr-2 text-purple-600"></i>
                                            Gallery Images
                                        </h3>
                                        
                                        <x-media-picker 
                                            name="gallery_images" 
                                            :value="old('gallery_images')" 
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

        @if($currentType !== 'partner')
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
        @endif
        $('form').on('submit', function(e) {
            @if($currentType !== 'partner')
            var content = $('#content').summernote('isEmpty');
            if(content) {
                e.preventDefault();
                alert('Konten tidak boleh kosong! Harap isi konten sebelum menyimpan.');
                return false;
            }
            @endif
        });
    });
</script>
@endpush