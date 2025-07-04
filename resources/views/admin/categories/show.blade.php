@extends('layouts.admin')

@section('title', 'Category Details - ' . $category->name)
@section('page-title', 'Category Details')

@push('styles')
<style>
    .category-color {
        width: 30px;
        height: 30px;
        border-radius: 50%;
        border: 2px solid #e5e7eb;
    }
    .stat-card {
        transition: transform 0.2s;
    }
    .stat-card:hover {
        transform: translateY(-2px);
    }
</style>
@endpush

@section('content')
<div class="space-y-6">
    <!-- Category Header -->
    <div class="bg-white rounded-lg shadow p-6">
        <div class="flex items-center justify-between mb-6">
            <div class="flex items-center space-x-4">
                <a href="{{ route('admin.categories.index') }}" class="text-gray-500 hover:text-gray-700">
                    <i class="fas fa-arrow-left"></i>
                </a>
                <h1 class="text-2xl font-bold text-gray-900">{{ $category->name }}</h1>
                @if($category->color)
                    <div class="category-color" style="background-color: {{ $category->color }}"></div>
                @endif
            </div>
            <div class="flex items-center space-x-3">
                <a href="{{ route('admin.categories.edit', $category->id) }}" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition-colors">
                    <i class="fas fa-edit mr-2"></i>Edit Category
                </a>
            </div>
        </div>
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <h3 class="text-lg font-medium text-gray-900 mb-3">Category Information</h3>
                <dl class="space-y-3">
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Name</dt>
                        <dd class="text-sm text-gray-900">{{ $category->name }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Slug</dt>
                        <dd class="text-sm text-gray-900">{{ $category->slug }}</dd>
                    </div>
                    @if($category->description)
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Description</dt>
                        <dd class="text-sm text-gray-900">{{ $category->description }}</dd>
                    </div>
                    @endif
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Status</dt>
                        <dd class="text-sm">
                            @if($category->is_active)
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                    <i class="fas fa-check-circle mr-1"></i>Active
                                </span>
                            @else
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                    <i class="fas fa-times-circle mr-1"></i>Inactive
                                </span>
                            @endif
                        </dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Created</dt>
                        <dd class="text-sm text-gray-900">{{ $category->created_at->format('M d, Y H:i') }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Last Updated</dt>
                        <dd class="text-sm text-gray-900">{{ $category->updated_at->format('M d, Y H:i') }}</dd>
                    </div>
                </dl>
            </div>
            
            <!-- Analytics -->
            @if(isset($analytics))
            <div>
                <h3 class="text-lg font-medium text-gray-900 mb-3">Analytics</h3>
                <div class="grid grid-cols-2 gap-4">
                    <div class="stat-card bg-blue-50 p-4 rounded-lg">
                        <div class="flex items-center">
                            <div class="flex-shrink-0">
                                <i class="fas fa-file-alt text-blue-600 text-xl"></i>
                            </div>
                            <div class="ml-3">
                                <p class="text-sm font-medium text-blue-600">Total Posts</p>
                                <p class="text-2xl font-bold text-blue-900">{{ $analytics['total_posts'] ?? 0 }}</p>
                            </div>
                        </div>
                    </div>
                    <div class="stat-card bg-green-50 p-4 rounded-lg">
                        <div class="flex items-center">
                            <div class="flex-shrink-0">
                                <i class="fas fa-eye text-green-600 text-xl"></i>
                            </div>
                            <div class="ml-3">
                                <p class="text-sm font-medium text-green-600">Published Posts</p>
                                <p class="text-2xl font-bold text-green-900">{{ $analytics['published_posts'] ?? 0 }}</p>
                            </div>
                        </div>
                    </div>
                    <div class="stat-card bg-yellow-50 p-4 rounded-lg">
                        <div class="flex items-center">
                            <div class="flex-shrink-0">
                                <i class="fas fa-clock text-yellow-600 text-xl"></i>
                            </div>
                            <div class="ml-3">
                                <p class="text-sm font-medium text-yellow-600">Draft Posts</p>
                                <p class="text-2xl font-bold text-yellow-900">{{ $analytics['draft_posts'] ?? 0 }}</p>
                            </div>
                        </div>
                    </div>
                    <div class="stat-card bg-purple-50 p-4 rounded-lg">
                        <div class="flex items-center">
                            <div class="flex-shrink-0">
                                <i class="fas fa-star text-purple-600 text-xl"></i>
                            </div>
                            <div class="ml-3">
                                <p class="text-sm font-medium text-purple-600">Featured Posts</p>
                                <p class="text-2xl font-bold text-purple-900">{{ $analytics['featured_posts'] ?? 0 }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            @endif
        </div>
    </div>
    
    <!-- Recent Posts -->
    @if(isset($recentPosts) && $recentPosts->posts->count() > 0)
    <div class="bg-white rounded-lg shadow">
        <div class="px-6 py-4 border-b border-gray-200">
            <h2 class="text-lg font-medium text-gray-900">Recent Posts in this Category</h2>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Title</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Author</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Published</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @foreach($recentPosts->posts as $post)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4">
                            <div class="flex items-center">
                                @if($post->featured_image)
                                    <img class="h-10 w-10 rounded-lg object-cover mr-3" src="{{ $post->featured_image }}" alt="{{ $post->title }}">
                                @else
                                    <div class="h-10 w-10 rounded-lg bg-gray-200 flex items-center justify-center mr-3">
                                        <i class="fas fa-image text-gray-400"></i>
                                    </div>
                                @endif
                                <div>
                                    <div class="text-sm font-medium text-gray-900">{{ Str::limit($post->title, 50) }}</div>
                                    @if($post->is_featured)
                                        <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-yellow-100 text-yellow-800 mt-1">
                                            <i class="fas fa-star mr-1"></i>Featured
                                        </span>
                                    @endif
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm text-gray-900">{{ $post->author->name ?? 'Unknown' }}</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            @if($post->is_published)
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                    <i class="fas fa-check-circle mr-1"></i>Published
                                </span>
                            @else
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                    <i class="fas fa-clock mr-1"></i>Draft
                                </span>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            {{ $post->published_at ? $post->published_at->format('M d, Y') : 'Not published' }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                            <div class="flex items-center space-x-2">
                                <a href="{{ route('admin.posts.show', $post->id) }}" class="text-blue-600 hover:text-blue-900">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <a href="{{ route('admin.posts.edit', $post->id) }}" class="text-indigo-600 hover:text-indigo-900">
                                    <i class="fas fa-edit"></i>
                                </a>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        
        @if($recentPosts->posts->count() >= 10)
        <div class="px-6 py-4 border-t border-gray-200">
            <a href="{{ route('admin.posts.index', ['category' => $category->id]) }}" class="text-blue-600 hover:text-blue-800 text-sm font-medium">
                View all posts in this category <i class="fas fa-arrow-right ml-1"></i>
            </a>
        </div>
        @endif
    </div>
    @else
    <div class="bg-white rounded-lg shadow p-6">
        <div class="text-center">
            <i class="fas fa-file-alt text-gray-400 text-4xl mb-4"></i>
            <h3 class="text-lg font-medium text-gray-900 mb-2">No Posts Yet</h3>
            <p class="text-gray-500 mb-4">This category doesn't have any posts yet.</p>
            <a href="{{ route('admin.posts.create', ['category' => $category->id]) }}" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition-colors">
                <i class="fas fa-plus mr-2"></i>Create First Post
            </a>
        </div>
    </div>
    @endif
</div>
@endsection

@push('scripts')
<script>
    // Add any JavaScript functionality here if needed
</script>
@endpush