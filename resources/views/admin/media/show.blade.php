@extends('layouts.admin')

@section('title', 'Media Details - ' . $media->filename)
@section('page-title', 'Media Details')

@push('styles')
<style>
    .media-preview-large {
        max-width: 100%;
        max-height: 500px;
        object-fit: contain;
        border-radius: 0.5rem;
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
    }
    .file-icon-large {
        display: flex;
        align-items: center;
        justify-content: center;
        height: 300px;
        background-color: #f3f4f6;
        color: #6b7280;
        font-size: 6rem;
        border-radius: 0.5rem;
        border: 2px dashed #d1d5db;
    }
    .metadata-card {
        transition: transform 0.2s;
    }
    .metadata-card:hover {
        transform: translateY(-2px);
    }
    .usage-item {
        padding: 0.75rem;
        border: 1px solid #e5e7eb;
        border-radius: 0.5rem;
        transition: all 0.2s;
    }
    .usage-item:hover {
        border-color: #3b82f6;
        background-color: #f8fafc;
    }
</style>
@endpush

@section('content')
<div class="space-y-6">
    <!-- Header with Actions -->
    <div class="bg-white rounded-lg shadow">
        <div class="px-6 py-4 border-b border-gray-200">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-2xl font-bold text-gray-900">{{ $media->filename }}</h1>
                    <p class="text-sm text-gray-500 mt-1">{{ $media->original_name }}</p>
                </div>
                <div class="flex items-center space-x-3">
                    <a href="{{ route('admin.media.edit', $media) }}" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg text-sm">
                        <i class="fas fa-edit mr-2"></i>
                        Edit
                    </a>
                    <a href="{{ $media->url }}" target="_blank" class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg text-sm">
                        <i class="fas fa-external-link-alt mr-2"></i>
                        View Original
                    </a>
                    <a href="{{ $media->url }}" download class="bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded-lg text-sm">
                        <i class="fas fa-download mr-2"></i>
                        Download
                    </a>
                    <a href="{{ route('admin.media.index') }}" class="bg-gray-300 hover:bg-gray-400 text-gray-700 px-4 py-2 rounded-lg text-sm">
                        <i class="fas fa-arrow-left mr-2"></i>
                        Back to Library
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Media Preview -->
        <div class="lg:col-span-2">
            <div class="bg-white rounded-lg shadow p-6">
                <h2 class="text-lg font-medium text-gray-900 mb-4">Preview</h2>
                <div class="text-center">
                    @if($media->type === 'image')
                        <img src="{{ $media->url }}" alt="{{ $media->filename }}" class="media-preview-large mx-auto">
                    @elseif($media->type === 'video')
                        <video controls class="media-preview-large mx-auto">
                            <source src="{{ $media->url }}" type="{{ $media->mime_type }}">
                            Your browser does not support the video tag.
                        </video>
                    @elseif($media->type === 'audio')
                        <div class="file-icon-large mx-auto mb-4">
                            <i class="fas fa-music"></i>
                        </div>
                        <audio controls class="w-full">
                            <source src="{{ $media->url }}" type="{{ $media->mime_type }}">
                            Your browser does not support the audio tag.
                        </audio>
                    @else
                        <div class="file-icon-large mx-auto">
                            @switch($media->extension)
                                @case('pdf')
                                    <i class="fas fa-file-pdf"></i>
                                    @break
                                @case('doc')
                                @case('docx')
                                    <i class="fas fa-file-word"></i>
                                    @break
                                @case('xls')
                                @case('xlsx')
                                    <i class="fas fa-file-excel"></i>
                                    @break
                                @case('ppt')
                                @case('pptx')
                                    <i class="fas fa-file-powerpoint"></i>
                                    @break
                                @case('zip')
                                @case('rar')
                                @case('7z')
                                    <i class="fas fa-file-archive"></i>
                                    @break
                                @default
                                    <i class="fas fa-file-alt"></i>
                            @endswitch
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Media Information -->
        <div class="space-y-6">
            <!-- Basic Information -->
            <div class="bg-white rounded-lg shadow p-6 metadata-card">
                <h3 class="text-lg font-medium text-gray-900 mb-4">File Information</h3>
                <dl class="space-y-3">
                    <div>
                        <dt class="text-sm font-medium text-gray-500">File Name</dt>
                        <dd class="text-sm text-gray-900 break-all">{{ $metadata['filename'] }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Original Name</dt>
                        <dd class="text-sm text-gray-900 break-all">{{ $metadata['original_name'] }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Type</dt>
                        <dd class="text-sm text-gray-900">{{ strtoupper($metadata['type']) }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Extension</dt>
                        <dd class="text-sm text-gray-900">{{ strtoupper($metadata['extension']) }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500">File Size</dt>
                        <dd class="text-sm text-gray-900">{{ $metadata['size'] }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500">MIME Type</dt>
                        <dd class="text-sm text-gray-900">{{ $metadata['mime_type'] }}</dd>
                    </div>
                    @if(isset($metadata['dimensions']))
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Dimensions</dt>
                        <dd class="text-sm text-gray-900">{{ $metadata['dimensions'] }}</dd>
                    </div>
                    @endif
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Uploaded</dt>
                        <dd class="text-sm text-gray-900">{{ $metadata['created_at'] }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Last Modified</dt>
                        <dd class="text-sm text-gray-900">{{ $metadata['updated_at'] }}</dd>
                    </div>
                </dl>
            </div>

            <!-- Usage Information -->
            <div class="bg-white rounded-lg shadow p-6 metadata-card">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Usage Information</h3>
                
                @if($usage['total'] > 0)
                    <div class="mb-4">
                        <div class="flex items-center justify-between mb-2">
                            <span class="text-sm font-medium text-gray-500">Total Usage</span>
                            <span class="text-sm font-bold text-blue-600">{{ $usage['total'] }} times</span>
                        </div>
                    </div>

                    @if(!empty($usage['posts']))
                    <div class="mb-4">
                        <h4 class="text-sm font-medium text-gray-700 mb-2">Used in Posts ({{ count($usage['posts']) }})</h4>
                        <div class="space-y-2">
                            @foreach($usage['posts'] as $post)
                            <div class="usage-item">
                                <div class="flex items-center justify-between">
                                    <div>
                                        <p class="text-sm font-medium text-gray-900">{{ $post->title }}</p>
                                        <p class="text-xs text-gray-500">{{ ($post->published_at ?? $post->created_at)->format('M d, Y') }}</p>
                                    </div>
                                    <a href="{{ route('admin.posts.edit', $post->id) }}" class="text-blue-600 hover:text-blue-800 text-xs">
                                        <i class="fas fa-external-link-alt"></i>
                                    </a>
                                </div>
                            </div>
                            @endforeach
                        </div>
                    </div>
                    @endif

                    @if(!empty($usage['categories']))
                    <div class="mb-4">
                        <h4 class="text-sm font-medium text-gray-700 mb-2">Used in Categories ({{ count($usage['categories']) }})</h4>
                        <div class="space-y-2">
                            @foreach($usage['categories'] as $category)
                            <div class="usage-item">
                                <div class="flex items-center justify-between">
                                    <div>
                                        <p class="text-sm font-medium text-gray-900">{{ $category->name }}</p>
                                        <p class="text-xs text-gray-500">{{ $category->created_at->format('M d, Y') }}</p>
                                    </div>
                                    <a href="{{ route('admin.categories.edit', $category->id) }}" class="text-blue-600 hover:text-blue-800 text-xs">
                                        <i class="fas fa-external-link-alt"></i>
                                    </a>
                                </div>
                            </div>
                            @endforeach
                        </div>
                    </div>
                    @endif
                @else
                    <div class="text-center py-8">
                        <i class="fas fa-info-circle text-3xl text-gray-400 mb-3"></i>
                        <p class="text-sm text-gray-500">This media file is not currently being used.</p>
                        <p class="text-xs text-gray-400 mt-1">It's safe to delete if no longer needed.</p>
                    </div>
                @endif
            </div>

            <!-- Quick Actions -->
            <div class="bg-white rounded-lg shadow p-6 metadata-card">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Quick Actions</h3>
                <div class="space-y-3">
                    @if($media->type === 'image')
                    <button onclick="generateThumbnail({{ $media->id }})" class="w-full bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg text-sm">
                        <i class="fas fa-image mr-2"></i>
                        Generate Thumbnail
                    </button>
                    @endif
                    
                    <button onclick="duplicateMedia({{ $media->id }})" class="w-full bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg text-sm">
                        <i class="fas fa-copy mr-2"></i>
                        Duplicate File
                    </button>
                    
                    @if($usage['total'] === 0)
                    <button onclick="deleteMedia({{ $media->id }})" class="w-full bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-lg text-sm">
                        <i class="fas fa-trash mr-2"></i>
                        Delete File
                    </button>
                    @else
                    <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-3">
                        <p class="text-xs text-yellow-800">
                            <i class="fas fa-exclamation-triangle mr-1"></i>
                            Cannot delete: File is currently in use
                        </p>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
function generateThumbnail(id) {
    if (confirm('Generate a new thumbnail for this image?')) {
        fetch(`/admin/media/${id}/thumbnail`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Accept': 'application/json'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert('Thumbnail generated successfully!');
                location.reload();
            } else {
                alert('Failed to generate thumbnail: ' + data.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('An error occurred while generating thumbnail.');
        });
    }
}

function duplicateMedia(id) {
    if (confirm('Create a duplicate of this media file?')) {
        fetch(`/admin/media/${id}/duplicate`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Accept': 'application/json'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert('Media duplicated successfully!');
                window.location.href = '/admin/media';
            } else {
                alert('Failed to duplicate media: ' + data.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('An error occurred while duplicating media.');
        });
    }
}

function deleteMedia(id) {
    if (confirm('Are you sure you want to delete this media file? This action cannot be undone.')) {
        fetch(`/admin/media/${id}`, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Accept': 'application/json'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert('Media deleted successfully!');
                window.location.href = '/admin/media';
            } else {
                alert('Failed to delete media: ' + data.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('An error occurred while deleting media.');
        });
    }
}
</script>
@endpush
@endsection