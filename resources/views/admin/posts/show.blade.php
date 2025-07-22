@extends('layouts.admin')

@section('title', 'Post Details - ' . $post->title)
@section('page-title', 'Post Details')

@push('styles')
<style>
    .post-content {
        line-height: 1.8;
    }
    .post-content img {
        max-width: 100%;
        height: auto;
        border-radius: 0.5rem;
        margin: 1rem 0;
    }
    .post-meta {
        background: #f8fafc;
        border-left: 4px solid #3b82f6;
    }
    .status-badge {
        display: inline-flex;
        align-items: center;
        padding: 0.25rem 0.75rem;
        border-radius: 9999px;
        font-size: 0.75rem;
        font-weight: 500;
    }
    .status-published {
        background-color: #dcfce7;
        color: #166534;
    }
    .status-draft {
        background-color: #fef3c7;
        color: #92400e;
    }
</style>
@endpush

@section('content')
<div class="bg-white rounded-lg shadow">
    <!-- Header -->
    <div class="px-6 py-4 border-b border-gray-200">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">{{ $post->title }}</h1>
                <p class="text-sm text-gray-500 mt-1">Created {{ $post->created_at->format('M d, Y \\a\\t g:i A') }}</p>
            </div>
            <div class="flex items-center space-x-3">
                <a href="{{ route('admin.posts.edit', $post) }}" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg flex items-center">
                    <i class="fas fa-edit mr-2"></i>
                    Edit Post
                </a>
                <a href="{{ route('admin.posts.index') }}" class="bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded-lg flex items-center">
                    <i class="fas fa-arrow-left mr-2"></i>
                    Back to Posts
                </a>
            </div>
        </div>
    </div>

    <div class="p-6">
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Main Content -->
            <div class="lg:col-span-2">
                <!-- Featured Image -->
                @if($post->featured_image_url)
                <div class="mb-6">
                    <img src="{{ $post->featured_image_url }}" alt="{{ $post->featured_image_alt_text ?? $post->title }}" class="w-full h-64 object-cover rounded-lg">
                </div>
                @endif

                <!-- Excerpt -->
                @if($post->excerpt)
                <div class="mb-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-2">Excerpt</h3>
                    <p class="text-gray-700 italic bg-gray-50 p-4 rounded-lg">{{ $post->excerpt }}</p>
                </div>
                @endif

                <!-- Content -->
                <div class="mb-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Content</h3>
                    <div class="post-content prose max-w-none">
                        {!! $post->content !!}
                    </div>
                </div>

                <!-- Gallery Images -->
                @if($post->gallery_images && count($post->gallery_images) > 0)
                <div class="mb-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Gallery</h3>
                    <div class="grid grid-cols-2 md:grid-cols-3 gap-4">
                        @foreach($post->gallery_images as $image)
                        <img src="{{ asset('storage/' . $image) }}" alt="Gallery image" class="w-full h-32 object-cover rounded-lg">
                        @endforeach
                    </div>
                </div>
                @endif

                <!-- Video URL -->
                @if($post->video_url)
                <div class="mb-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Video</h3>
                    <div class="bg-gray-100 p-4 rounded-lg">
                        <a href="{{ $post->video_url }}" target="_blank" class="text-blue-600 hover:text-blue-800 flex items-center">
                            <i class="fas fa-play-circle mr-2"></i>
                            {{ $post->video_url }}
                        </a>
                    </div>
                </div>
                @endif
            </div>

            <!-- Sidebar -->
            <div class="lg:col-span-1">
                <!-- Post Meta -->
                <div class="post-meta p-4 rounded-lg mb-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Post Information</h3>
                    
                    <div class="space-y-3">
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Status</label>
                            <span class="status-badge {{ $post->is_published ? 'status-published' : 'status-draft' }}">
                                <i class="fas fa-circle text-xs mr-1"></i>
                                {{ $post->is_published ? 'Published' : 'Draft' }}
                            </span>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700">Category</label>
                            <p class="text-sm text-gray-900">{{ $post->category->name ?? 'Uncategorized' }}</p>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700">Author</label>
                            <p class="text-sm text-gray-900">{{ $post->user->name ?? 'Unknown' }}</p>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700">Type</label>
                            <p class="text-sm text-gray-900">{{ ucfirst($post->type ?? 'article') }}</p>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700">Views</label>
                            <p class="text-sm text-gray-900">{{ number_format($post->views) }}</p>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700">Slug</label>
                            <p class="text-sm text-gray-900 font-mono bg-gray-100 px-2 py-1 rounded">{{ $post->slug }}</p>
                        </div>

                        @if($post->featured_image_id)
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Featured Image ID</label>
                            <p class="text-sm text-gray-900">{{ $post->featured_image_id }}</p>
                        </div>
                        @endif

                        <div>
                            <label class="block text-sm font-medium text-gray-700">Date</label>
                            <p class="text-sm text-gray-900">{{ ($post->published_at ?? $post->created_at)->format('M d, Y g:i A') }}</p>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700">Updated</label>
                            <p class="text-sm text-gray-900">{{ $post->updated_at->format('M d, Y g:i A') }}</p>
                        </div>

                        @if($post->published_at)
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Published</label>
                            <p class="text-sm text-gray-900">{{ $post->published_at->format('M d, Y g:i A') }}</p>
                        </div>
                        @endif
                    </div>
                </div>

                <!-- Special Flags -->
                <div class="bg-white border border-gray-200 p-4 rounded-lg mb-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Special Flags</h3>
                    
                    <div class="space-y-2">
                        <div class="flex items-center justify-between">
                            <span class="text-sm text-gray-700">Featured</span>
                            <span class="text-sm {{ $post->is_featured ? 'text-green-600' : 'text-gray-400' }}">
                                <i class="fas fa-{{ $post->is_featured ? 'check-circle' : 'times-circle' }}"></i>
                                {{ $post->is_featured ? 'Yes' : 'No' }}
                            </span>
                        </div>
                        
                        <div class="flex items-center justify-between">
                            <span class="text-sm text-gray-700">Slider</span>
                            <span class="text-sm {{ $post->is_slider ? 'text-green-600' : 'text-gray-400' }}">
                                <i class="fas fa-{{ $post->is_slider ? 'check-circle' : 'times-circle' }}"></i>
                                {{ $post->is_slider ? 'Yes' : 'No' }}
                            </span>
                        </div>
                    </div>
                </div>

                <!-- SEO Information -->
                @if($post->meta_title || $post->meta_description || $post->meta_keywords)
                <div class="bg-white border border-gray-200 p-4 rounded-lg">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">SEO Information</h3>
                    
                    <div class="space-y-3">
                        @if($post->meta_title)
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Meta Title</label>
                            <p class="text-sm text-gray-900">{{ $post->meta_title }}</p>
                        </div>
                        @endif

                        @if($post->meta_description)
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Meta Description</label>
                            <p class="text-sm text-gray-900">{{ $post->meta_description }}</p>
                        </div>
                        @endif

                        @if($post->meta_keywords)
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Meta Keywords</label>
                            <p class="text-sm text-gray-900">{{ $post->meta_keywords }}</p>
                        </div>
                        @endif
                    </div>
                </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Actions Footer -->
    <div class="px-6 py-4 border-t border-gray-200 bg-gray-50">
        <div class="flex items-center justify-between">
            <div class="flex items-center space-x-3">
                <button onclick="togglePublish({{ $post->id }})" class="{{ $post->is_published ? 'bg-orange-600 hover:bg-orange-700' : 'bg-green-600 hover:bg-green-700' }} text-white px-4 py-2 rounded-lg flex items-center">
                    <i class="fas fa-{{ $post->is_published ? 'eye-slash' : 'eye' }} mr-2"></i>
                    {{ $post->is_published ? 'Unpublish' : 'Publish' }}
                </button>
                
                @if($post->is_published)
                <a href="{{ route('frontend.post', $post->slug) }}" target="_blank" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg flex items-center">
                    <i class="fas fa-external-link-alt mr-2"></i>
                    View on Site
                </a>
                @endif
            </div>
            
            <form method="POST" action="{{ route('admin.posts.destroy', $post) }}" class="inline" onsubmit="return confirm('Are you sure you want to delete this post?')">
                @csrf
                @method('DELETE')
                <button type="submit" class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-lg flex items-center">
                    <i class="fas fa-trash mr-2"></i>
                    Delete Post
                </button>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function togglePublish(postId) {
    fetch(`/admin/posts/${postId}/toggle-publish`, {
        method: 'PATCH',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            'Content-Type': 'application/json',
        },
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            location.reload();
        } else {
            alert('Error updating post status');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Error updating post status');
    });
}
</script>
@endpush