@extends('layouts.admin')

@section('title', 'Media Library')
@section('page-title', 'Media')

@push('styles')
<style>
    .media-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
        gap: 1rem;
    }
    .media-item {
        position: relative;
        border: 2px solid transparent;
        border-radius: 0.5rem;
        overflow: hidden;
        cursor: pointer;
        transition: all 0.2s;
    }
    .media-item:hover {
        border-color: #3b82f6;
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
    }
    .media-item.selected {
        border-color: #3b82f6;
        background-color: #eff6ff;
    }
    .media-preview {
        width: 100%;
        height: 150px;
        object-fit: cover;
        background-color: #f3f4f6;
    }
    .media-info {
        padding: 0.75rem;
        background: white;
    }
    .media-actions {
        position: absolute;
        top: 0.5rem;
        right: 0.5rem;
        opacity: 0;
        transition: opacity 0.2s;
    }
    .media-item:hover .media-actions {
        opacity: 1;
    }
    .file-icon {
        display: flex;
        align-items: center;
        justify-content: center;
        height: 150px;
        background-color: #f3f4f6;
        color: #6b7280;
        font-size: 3rem;
    }
    .upload-area {
        border: 2px dashed #d1d5db;
        border-radius: 0.5rem;
        padding: 2rem;
        text-align: center;
        transition: all 0.2s;
    }
    .upload-area.dragover {
        border-color: #3b82f6;
        background-color: #eff6ff;
    }
</style>
@endpush

@section('content')
<div class="space-y-6">
    <!-- Upload Area -->
    <div class="bg-white rounded-lg shadow">
        <div class="px-6 py-4 border-b border-gray-200">
            <h2 class="text-lg font-medium text-gray-900">Upload Media</h2>
        </div>
        <div class="p-6">
            <div class="upload-area" id="upload-area">
                <div class="mb-4">
                    <i class="fas fa-cloud-upload-alt text-4xl text-gray-400 mb-4"></i>
                    <p class="text-lg font-medium text-gray-900 mb-2">Drop files here or click to upload</p>
                    <p class="text-sm text-gray-500">Support for images, videos, documents (Max: 10MB per file)</p>
                </div>
                <input type="file" id="file-input" multiple accept="image/*,video/*,.pdf,.doc,.docx,.txt" class="hidden">
                <button onclick="document.getElementById('file-input').click()" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-lg">
                    <i class="fas fa-plus mr-2"></i>
                    Select Files
                </button>
            </div>
            
            <!-- Upload Progress -->
            <div id="upload-progress" class="mt-4 hidden">
                <div class="bg-gray-200 rounded-full h-2">
                    <div id="progress-bar" class="bg-blue-600 h-2 rounded-full transition-all duration-300" style="width: 0%"></div>
                </div>
                <p id="upload-status" class="text-sm text-gray-600 mt-2">Uploading...</p>
            </div>
        </div>
    </div>
    
    <!-- Media Library -->
    <div class="bg-white rounded-lg shadow">
        <!-- Header -->
        <div class="px-6 py-4 border-b border-gray-200">
            <div class="flex items-center justify-between">
                <h2 class="text-lg font-medium text-gray-900">Media Library</h2>
                <div class="flex items-center space-x-3">
                    <!-- Search -->
                    <div class="relative">
                        <input type="text" id="search-input" placeholder="Search media..." class="pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <i class="fas fa-search text-gray-400"></i>
                        </div>
                    </div>
                    
                    <!-- Filter -->
                    <select id="type-filter" class="border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        <option value="">All Types</option>
                        <option value="image">Images</option>
                        <option value="video">Videos</option>
                        <option value="document">Documents</option>
                    </select>
                    
                    <!-- View Toggle -->
                    <div class="flex border border-gray-300 rounded-lg">
                        <button id="grid-view" class="px-3 py-2 text-sm bg-blue-600 text-white rounded-l-lg">
                            <i class="fas fa-th"></i>
                        </button>
                        <button id="list-view" class="px-3 py-2 text-sm bg-white text-gray-700 rounded-r-lg border-l">
                            <i class="fas fa-list"></i>
                        </button>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Media Grid -->
        <div class="p-6">
            <div id="media-container" class="media-grid">
                @forelse($media ?? [] as $item)
                <div class="media-item" data-id="{{ $item->id }}" data-type="{{ $item->type }}">
                    <div class="media-actions">
                        <div class="flex space-x-1">
                            <button onclick="viewMedia({{ $item->id }})" class="bg-blue-600 text-white p-1 rounded text-xs hover:bg-blue-700">
                                <i class="fas fa-eye"></i>
                            </button>
                            <button onclick="editMedia({{ $item->id }})" class="bg-green-600 text-white p-1 rounded text-xs hover:bg-green-700">
                                <i class="fas fa-edit"></i>
                            </button>
                            <button onclick="deleteMedia({{ $item->id }})" class="bg-red-600 text-white p-1 rounded text-xs hover:bg-red-700">
                                <i class="fas fa-trash"></i>
                            </button>
                        </div>
                    </div>
                    
                    @if(in_array($item->type, ['jpg', 'jpeg', 'png', 'gif', 'webp']))
                        <img src="{{ $item->url }}" alt="{{ $item->filename }}" class="media-preview">
                    @elseif(in_array($item->type, ['mp4', 'webm', 'ogg']))
                        <video class="media-preview" preload="metadata">
                            <source src="{{ $item->url }}" type="video/{{ $item->type }}">
                        </video>
                    @else
                        <div class="file-icon">
                            <i class="fas fa-file-{{ $item->type === 'pdf' ? 'pdf' : 'alt' }}"></i>
                        </div>
                    @endif
                    
                    <div class="media-info">
                        <p class="text-sm font-medium text-gray-900 truncate">{{ $item->filename }}</p>
                        <p class="text-xs text-gray-500">{{ strtoupper($item->type) }} • {{ $item->size_formatted }}</p>
                        <p class="text-xs text-gray-400">{{ $item->created_at->format('M d, Y') }}</p>
                    </div>
                </div>
                @empty
                <div class="col-span-full text-center py-12">
                    <i class="fas fa-images text-4xl text-gray-400 mb-4"></i>
                    <p class="text-lg font-medium text-gray-900">No media files found</p>
                    <p class="text-sm text-gray-500">Upload your first media file to get started.</p>
                </div>
                @endforelse
            </div>
        </div>
        
        <!-- Pagination -->
        @if(isset($media) && $media->hasPages())
        <div class="px-6 py-4 border-t border-gray-200">
            {{ $media->links() }}
        </div>
        @endif
    </div>
    
    <!-- Selected Media Actions -->
    <div id="bulk-actions" class="bg-white rounded-lg shadow p-6 hidden">
        <div class="flex items-center justify-between">
            <div>
                <h3 class="text-lg font-medium text-gray-900">Bulk Actions</h3>
                <p class="text-sm text-gray-500"><span id="selected-count">0</span> items selected</p>
            </div>
            <div class="flex items-center space-x-3">
                <button onclick="downloadSelected()" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg">
                    <i class="fas fa-download mr-2"></i>
                    Download
                </button>
                <button onclick="deleteSelected()" class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-lg">
                    <i class="fas fa-trash mr-2"></i>
                    Delete
                </button>
                <button onclick="clearSelection()" class="bg-gray-300 hover:bg-gray-400 text-gray-700 px-4 py-2 rounded-lg">
                    Clear
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Media Modal -->
<div id="media-modal" class="fixed inset-0 bg-black bg-opacity-50 hidden z-50">
    <div class="flex items-center justify-center min-h-screen p-4">
        <div class="bg-white rounded-lg max-w-4xl w-full max-h-full overflow-auto">
            <div class="flex items-center justify-between p-6 border-b">
                <h3 class="text-lg font-medium text-gray-900">Media Details</h3>
                <button onclick="closeModal()" class="text-gray-400 hover:text-gray-600">
                    <i class="fas fa-times text-xl"></i>
                </button>
            </div>
            <div id="modal-content" class="p-6">
                <!-- Content will be loaded here -->
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
let selectedMedia = new Set();

// File upload handling
document.getElementById('file-input').addEventListener('change', handleFileUpload);

// Drag and drop
const uploadArea = document.getElementById('upload-area');
uploadArea.addEventListener('dragover', (e) => {
    e.preventDefault();
    uploadArea.classList.add('dragover');
});

uploadArea.addEventListener('dragleave', () => {
    uploadArea.classList.remove('dragover');
});

uploadArea.addEventListener('drop', (e) => {
    e.preventDefault();
    uploadArea.classList.remove('dragover');
    const files = e.dataTransfer.files;
    handleFileUpload({ target: { files } });
});

function handleFileUpload(event) {
    const files = Array.from(event.target.files);
    if (files.length === 0) return;
    
    const formData = new FormData();
    files.forEach(file => formData.append('files[]', file));
    
    const progressContainer = document.getElementById('upload-progress');
    const progressBar = document.getElementById('progress-bar');
    const statusText = document.getElementById('upload-status');
    
    progressContainer.classList.remove('hidden');
    
    fetch('/admin/media/upload', {
        method: 'POST',
        body: formData,
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        }
    })
    .then(response => response.json())
    .then(data => {
        progressBar.style.width = '100%';
        statusText.textContent = 'Upload complete!';
        setTimeout(() => {
            progressContainer.classList.add('hidden');
            location.reload();
        }, 1000);
    })
    .catch(error => {
        console.error('Error:', error);
        statusText.textContent = 'Upload failed!';
        statusText.classList.add('text-red-600');
    });
}

// Media selection
document.addEventListener('click', (e) => {
    const mediaItem = e.target.closest('.media-item');
    if (mediaItem && !e.target.closest('.media-actions')) {
        const mediaId = mediaItem.dataset.id;
        
        if (e.ctrlKey || e.metaKey) {
            // Multi-select
            if (selectedMedia.has(mediaId)) {
                selectedMedia.delete(mediaId);
                mediaItem.classList.remove('selected');
            } else {
                selectedMedia.add(mediaId);
                mediaItem.classList.add('selected');
            }
        } else {
            // Single select
            document.querySelectorAll('.media-item').forEach(item => {
                item.classList.remove('selected');
            });
            selectedMedia.clear();
            selectedMedia.add(mediaId);
            mediaItem.classList.add('selected');
        }
        
        updateBulkActions();
    }
});

function updateBulkActions() {
    const bulkActions = document.getElementById('bulk-actions');
    const selectedCount = document.getElementById('selected-count');
    
    selectedCount.textContent = selectedMedia.size;
    
    if (selectedMedia.size > 0) {
        bulkActions.classList.remove('hidden');
    } else {
        bulkActions.classList.add('hidden');
    }
}

// View toggle
document.getElementById('grid-view').addEventListener('click', () => {
    document.getElementById('media-container').className = 'media-grid';
    document.getElementById('grid-view').classList.add('bg-blue-600', 'text-white');
    document.getElementById('grid-view').classList.remove('bg-white', 'text-gray-700');
    document.getElementById('list-view').classList.remove('bg-blue-600', 'text-white');
    document.getElementById('list-view').classList.add('bg-white', 'text-gray-700');
});

document.getElementById('list-view').addEventListener('click', () => {
    document.getElementById('media-container').className = 'space-y-2';
    document.getElementById('list-view').classList.add('bg-blue-600', 'text-white');
    document.getElementById('list-view').classList.remove('bg-white', 'text-gray-700');
    document.getElementById('grid-view').classList.remove('bg-blue-600', 'text-white');
    document.getElementById('grid-view').classList.add('bg-white', 'text-gray-700');
});

// Media actions
function viewMedia(mediaId) {
    fetch(`/admin/media/${mediaId}`)
        .then(response => response.json())
        .then(media => {
            const modalContent = document.getElementById('modal-content');
            modalContent.innerHTML = `
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                    <div>
                        ${media.type.includes('image') ? 
                            `<img src="${media.url}" alt="${media.filename}" class="w-full rounded-lg">` :
                            `<div class="bg-gray-100 rounded-lg p-8 text-center">
                                <i class="fas fa-file-${media.type === 'pdf' ? 'pdf' : 'alt'} text-6xl text-gray-400 mb-4"></i>
                                <p class="text-lg font-medium">${media.filename}</p>
                            </div>`
                        }
                    </div>
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Filename</label>
                            <p class="text-sm text-gray-900">${media.filename}</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">File Type</label>
                            <p class="text-sm text-gray-900">${media.type.toUpperCase()}</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">File Size</label>
                            <p class="text-sm text-gray-900">${media.size_formatted}</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Upload Date</label>
                            <p class="text-sm text-gray-900">${new Date(media.created_at).toLocaleDateString()}</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">URL</label>
                            <div class="flex items-center space-x-2">
                                <input type="text" value="${media.url}" readonly class="flex-1 text-sm border border-gray-300 rounded px-3 py-2">
                                <button onclick="copyToClipboard('${media.url}')" class="bg-blue-600 text-white px-3 py-2 rounded text-sm">
                                    <i class="fas fa-copy"></i>
                                </button>
                            </div>
                        </div>
                        <div class="flex space-x-3 pt-4">
                            <a href="${media.url}" download class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg">
                                <i class="fas fa-download mr-2"></i>Download
                            </a>
                            <button onclick="deleteMedia(${media.id})" class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-lg">
                                <i class="fas fa-trash mr-2"></i>Delete
                            </button>
                        </div>
                    </div>
                </div>
            `;
            document.getElementById('media-modal').classList.remove('hidden');
        });
}

function editMedia(mediaId) {
    // Implement edit functionality
    console.log('Edit media:', mediaId);
}

function deleteMedia(mediaId) {
    if (confirm('Are you sure you want to delete this media file?')) {
        fetch(`/admin/media/${mediaId}`, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                location.reload();
            }
        });
    }
}

function closeModal() {
    document.getElementById('media-modal').classList.add('hidden');
}

function copyToClipboard(text) {
    navigator.clipboard.writeText(text).then(() => {
        // Show success message
        alert('URL copied to clipboard!');
    });
}

function clearSelection() {
    selectedMedia.clear();
    document.querySelectorAll('.media-item').forEach(item => {
        item.classList.remove('selected');
    });
    updateBulkActions();
}

function deleteSelected() {
    if (selectedMedia.size === 0) return;
    
    if (confirm(`Are you sure you want to delete ${selectedMedia.size} selected media files?`)) {
        const mediaIds = Array.from(selectedMedia);
        
        fetch('/admin/media/bulk-delete', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify({ media_ids: mediaIds })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                location.reload();
            }
        });
    }
}

function downloadSelected() {
    if (selectedMedia.size === 0) return;
    
    const mediaIds = Array.from(selectedMedia);
    
    // Create download links for each selected media
    mediaIds.forEach(mediaId => {
        const mediaItem = document.querySelector(`[data-id="${mediaId}"]`);
        if (mediaItem) {
            // This would need to be implemented based on your media URL structure
            console.log('Download media:', mediaId);
        }
    });
}
</script>
@endpush