@extends('layouts.admin')

@php
    $currentType = request()->get('type', 'berita');
    $pageTitle = match($currentType) {
        'page' => 'Pages Management',
        'gallery' => 'Gallery Management', 
        'video' => 'Video Management',
        default => 'Posts Management'
    };
    $sectionTitle = match($currentType) {
        'page' => 'Pages',
        'gallery' => 'Gallery',
        'video' => 'Video', 
        default => 'Posts'
    };
@endphp

@section('title', $pageTitle)
@section('page-title', $sectionTitle)

@push('styles')
<style>
    .table-actions {
        white-space: nowrap;
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
            <h2 class="text-lg font-medium text-gray-900">All {{ $sectionTitle }}</h2>
            <form method="GET" action="{{ route('admin.posts.index') }}" class="flex items-center space-x-3">
                <input type="hidden" name="type" value="{{ $currentType }}">
                
                <!-- Search -->
                <div class="relative">
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Search {{ strtolower($sectionTitle) }}..." class="pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <i class="fas fa-search text-gray-400"></i>
                    </div>
                </div>
                
                <!-- Filter -->
                <select name="status" class="border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-transparent" onchange="this.form.submit()">
                    <option value="">All Status</option>
                    <option value="published" {{ request('status') === 'published' ? 'selected' : '' }}>Published</option>
                    <option value="draft" {{ request('status') === 'draft' ? 'selected' : '' }}>Draft</option>
                </select>
                
                @if($currentType !== 'page')
                <!-- Category Filter -->
                <select name="category_id" class="border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-transparent" onchange="this.form.submit()">
                    <option value="">All Categories</option>
                    @foreach($categories as $category)
                        <option value="{{ $category->id }}" {{ request('category_id') == $category->id ? 'selected' : '' }}>{{ $category->name }}</option>
                    @endforeach
                </select>
                @endif
                
                <button type="submit" class="bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded-lg">
                    <i class="fas fa-search"></i>
                </button>
                
                <!-- Add New Button -->
                @if($currentType === 'page')
                    <a href="{{ route('admin.pages.create') }}" class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg flex items-center">
                        <i class="fas fa-file-alt mr-2"></i>
                        Add New Page
                    </a>
                @elseif($currentType === 'gallery')
                    <a href="{{ route('admin.posts.create', ['type' => 'gallery']) }}" class="bg-purple-600 hover:bg-purple-700 text-white px-4 py-2 rounded-lg flex items-center">
                        <i class="fas fa-images mr-2"></i>
                        Add New Gallery
                    </a>
                @elseif($currentType === 'video')
                    <a href="{{ route('admin.posts.create', ['type' => 'video']) }}" class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-lg flex items-center">
                        <i class="fas fa-video mr-2"></i>
                        Add New Video
                    </a>
                @else
                    <a href="{{ route('admin.posts.create') }}" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg flex items-center">
                        <i class="fas fa-plus mr-2"></i>
                        Add New Post
                    </a>
                @endif
            </form>
        </div>
    </div>
    
    <!-- Table -->
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        <input type="checkbox" class="rounded border-gray-300">
                    </th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Title
                    </th>
                   
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Category
                    </th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Type
                    </th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Status
                    </th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Views
                    </th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Date
                    </th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Actions
                    </th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse($posts ?? [] as $post)
                <tr class="hover:bg-gray-50">
                    <td class="px-6 py-4 whitespace-nowrap">
                        <input type="checkbox" class="rounded border-gray-300" value="{{ $post->id }}">
                    </td>
                    <td class="px-6 py-4">
                        <div class="flex items-center">
                            @if($post->featured_image_url)
                                <img class="h-10 w-10 rounded object-cover mr-3" 
                                    src="{{ $post->featured_image_url }}" 
                                    alt="{{ $post->featured_image_alt_text ?? $post->title }}">
                            @else
                                <div class="h-10 w-10 rounded bg-gray-200 flex items-center justify-center mr-3">
                                    <i class="fas fa-image text-gray-400"></i>
                                </div>
                            @endif
                            
                            <div>
                                <div class="text-sm font-medium text-gray-900">{{ $post->title }}</div>
                                <div class="text-sm text-gray-500">{{ Str::limit($post->excerpt, 50) }}</div>
                            </div>
                        </div>
                    </td>

                    
                    <td class="px-6 py-4 whitespace-nowrap">
                        <span class="text-sm text-gray-900">{{ $post->category->name ?? 'Uncategorized' }}</span>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium 
                            {{ $post->type === 'page' ? 'bg-green-100 text-green-800' : 
                               ($post->type === 'berita' ? 'bg-blue-100 text-blue-800' : 
                               ($post->type === 'gallery' ? 'bg-purple-100 text-purple-800' : 
                               ($post->type === 'video' ? 'bg-red-100 text-red-800' : 'bg-gray-100 text-gray-800'))) }}">
                            @if($post->type === 'page')
                                <i class="fas fa-file-alt mr-1"></i>
                            @elseif($post->type === 'berita')
                                <i class="fas fa-newspaper mr-1"></i>
                            @elseif($post->type === 'gallery')
                                <i class="fas fa-images mr-1"></i>
                            @elseif($post->type === 'video')
                                <i class="fas fa-video mr-1"></i>
                            @else
                                <i class="fas fa-file mr-1"></i>
                            @endif
                            {{ ucfirst($post->type) }}
                        </span>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <span class="status-badge {{ $post->is_published ? 'status-published' : 'status-draft' }}">
                            <i class="fas fa-circle text-xs mr-1"></i>
                            {{ $post->is_published ? 'Published' : 'Draft' }}
                        </span>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                        {{ number_format($post->views) }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                        {{ $post->created_at->format('M d, Y') }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap table-actions">
                        <div class="flex items-center space-x-2">
                            <a href="{{ route('admin.posts.show', $post) }}" class="text-blue-600 hover:text-blue-900" title="View">
                                <i class="fas fa-eye"></i>
                            </a>
                            @if($post->type === 'page')
                                <a href="{{ route('admin.pages.edit', $post) }}" class="text-green-600 hover:text-green-900" title="Edit Page">
                                    <i class="fas fa-file-alt"></i>
                                </a>
                            @else
                                <a href="{{ route('admin.posts.edit', $post) }}" class="text-indigo-600 hover:text-indigo-900" title="Edit Post">
                                    <i class="fas fa-edit"></i>
                                </a>
                            @endif
                            <button onclick="togglePublish({{ $post->id }})" class="{{ $post->is_published ? 'text-orange-600 hover:text-orange-900' : 'text-green-600 hover:text-green-900' }}" title="{{ $post->is_published ? 'Unpublish' : 'Publish' }}">
                                <i class="fas fa-{{ $post->is_published ? 'eye-slash' : 'eye' }}"></i>
                            </button>
                            <form method="POST" action="{{ route('admin.posts.destroy', $post) }}" class="inline" onsubmit="return confirm('Are you sure you want to delete this post?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-red-600 hover:text-red-900" title="Delete">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="9" class="px-6 py-12 text-center">
                        <div class="text-gray-500">
                            @if($currentType === 'page')
                                <i class="fas fa-file-alt text-4xl mb-4"></i>
                                <p class="text-lg font-medium">No pages found</p>
                                <p class="text-sm">Get started by creating your first page.</p>
                                <a href="{{ route('admin.pages.create') }}" class="mt-4 inline-flex items-center px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700">
                                    <i class="fas fa-file-alt mr-2"></i>
                                    Create Page
                                </a>
                            @elseif($currentType === 'gallery')
                                <i class="fas fa-images text-4xl mb-4"></i>
                                <p class="text-lg font-medium">No galleries found</p>
                                <p class="text-sm">Get started by creating your first gallery.</p>
                                <a href="{{ route('admin.posts.create', ['type' => 'gallery']) }}" class="mt-4 inline-flex items-center px-4 py-2 bg-purple-600 text-white rounded-lg hover:bg-purple-700">
                                    <i class="fas fa-images mr-2"></i>
                                    Create Gallery
                                </a>
                            @elseif($currentType === 'video')
                                <i class="fas fa-video text-4xl mb-4"></i>
                                <p class="text-lg font-medium">No videos found</p>
                                <p class="text-sm">Get started by creating your first video.</p>
                                <a href="{{ route('admin.posts.create', ['type' => 'video']) }}" class="mt-4 inline-flex items-center px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700">
                                    <i class="fas fa-video mr-2"></i>
                                    Create Video
                                </a>
                            @else
                                <i class="fas fa-newspaper text-4xl mb-4"></i>
                                <p class="text-lg font-medium">No posts found</p>
                                <p class="text-sm">Get started by creating your first post.</p>
                                <a href="{{ route('admin.posts.create') }}" class="mt-4 inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                                    <i class="fas fa-plus mr-2"></i>
                                    Create Post
                                </a>
                            @endif
                        </div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    
    <!-- Pagination -->
    @if(isset($posts) && $posts->hasPages())
    <div class="px-6 py-4 border-t border-gray-200">
        {{ $posts->links() }}
    </div>
    @endif
</div>

<!-- Bulk Actions -->
<div class="mt-6 bg-white rounded-lg shadow p-6">
    <h3 class="text-lg font-medium text-gray-900 mb-4">Bulk Actions</h3>
    <div class="flex items-center space-x-4">
        <select id="bulk-action" class="border border-gray-300 rounded-lg px-3 py-2">
            <option value="">Select Action</option>
            <option value="publish">Publish</option>
            <option value="unpublish">Unpublish</option>
            <option value="delete">Delete</option>
        </select>
        <button onclick="executeBulkAction()" class="bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded-lg">
            Apply
        </button>
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

function executeBulkAction() {
    const action = document.getElementById('bulk-action').value;
    const checkboxes = document.querySelectorAll('tbody input[type="checkbox"]:checked');
    const postIds = Array.from(checkboxes).map(cb => cb.value);
    
    if (!action) {
        alert('Please select an action');
        return;
    }
    
    if (postIds.length === 0) {
        alert('Please select at least one post');
        return;
    }
    
    if (action === 'delete' && !confirm('Are you sure you want to delete the selected posts?')) {
        return;
    }
    
    // Implement bulk action logic here
    console.log('Bulk action:', action, 'Post IDs:', postIds);
}

// Select all checkbox functionality
document.querySelector('thead input[type="checkbox"]').addEventListener('change', function() {
    const checkboxes = document.querySelectorAll('tbody input[type="checkbox"]');
    checkboxes.forEach(cb => cb.checked = this.checked);
});
</script>
@endpush