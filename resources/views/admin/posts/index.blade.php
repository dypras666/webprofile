@extends('layouts.admin')

@section('title', 'Posts Management')
@section('page-title', 'Posts')

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
            <h2 class="text-lg font-medium text-gray-900">All Posts</h2>
            <div class="flex items-center space-x-3">
                <!-- Search -->
                <div class="relative">
                    <input type="text" placeholder="Search posts..." class="pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <i class="fas fa-search text-gray-400"></i>
                    </div>
                </div>
                
                <!-- Filter -->
                <select class="border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    <option value="">All Status</option>
                    <option value="published">Published</option>
                    <option value="draft">Draft</option>
                </select>
                
                <!-- Add New Button -->
                <a href="{{ route('admin.posts.create') }}" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg flex items-center">
                    <i class="fas fa-plus mr-2"></i>
                    Add New Post
                </a>
            </div>
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
                                <img class="h-10 w-10 rounded object-cover mr-3" src="{{ $post->featured_image_url }}" alt="{{ $post->featured_image_alt_text ?? $post->title }}">
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
                            <a href="{{ route('admin.posts.edit', $post) }}" class="text-indigo-600 hover:text-indigo-900" title="Edit">
                                <i class="fas fa-edit"></i>
                            </a>
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
                    <td colspan="8" class="px-6 py-12 text-center">
                        <div class="text-gray-500">
                            <i class="fas fa-file-alt text-4xl mb-4"></i>
                            <p class="text-lg font-medium">No posts found</p>
                            <p class="text-sm">Get started by creating your first post.</p>
                            <a href="{{ route('admin.posts.create') }}" class="mt-4 inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                                <i class="fas fa-plus mr-2"></i>
                                Create Post
                            </a>
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