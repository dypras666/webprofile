@extends('layouts.admin')

@section('title', 'Dashboard Admin')
@section('page-title', 'Dashboard')

@push('styles')
<style>
    .stat-card {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        border-radius: 12px;
        padding: 1.5rem;
        color: white;
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
        transition: transform 0.2s;
    }
    .stat-card:hover {
        transform: translateY(-2px);
    }
    .stat-number {
        font-size: 2.5rem;
        font-weight: bold;
        margin-bottom: 0.5rem;
    }
    .stat-label {
        font-size: 0.875rem;
        opacity: 0.9;
    }
    .recent-item {
        padding: 1rem;
        border-bottom: 1px solid #e5e7eb;
        transition: background-color 0.2s;
    }
    .recent-item:hover {
        background-color: #f9fafb;
    }
    .recent-item:last-child {
        border-bottom: none;
    }
</style>
@endpush

@section('content')
<div class="min-h-screen bg-gray-50 py-8">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="mb-8">
            <h1 class="text-3xl font-bold text-gray-900">Dashboard Admin</h1>
            <p class="text-gray-600 mt-2">Selamat datang kembali, {{ auth()->user()->name }}!</p>
        </div>

        <!-- Statistics Cards -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
            <div class="stat-card">
                <div class="stat-number">{{ $stats['total_posts'] }}</div>
                <div class="stat-label">Total Posts</div>
            </div>
            <div class="stat-card" style="background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);">
                <div class="stat-number">{{ $stats['published_posts'] }}</div>
                <div class="stat-label">Published Posts</div>
            </div>
            <div class="stat-card" style="background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);">
                <div class="stat-number">{{ $stats['total_categories'] }}</div>
                <div class="stat-label">Categories</div>
            </div>
            <div class="stat-card" style="background: linear-gradient(135deg, #43e97b 0%, #38f9d7 100%);">
                <div class="stat-number">{{ $stats['total_users'] }}</div>
                <div class="stat-label">Users</div>
            </div>
        </div>

        <!-- Content Grid -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
            <!-- Recent Posts -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h2 class="text-lg font-semibold text-gray-900">Recent Posts</h2>
                </div>
                <div class="divide-y divide-gray-200">
                    @forelse($recentPosts as $post)
                    <div class="recent-item">
                        <div class="flex items-center justify-between">
                            <div class="flex-1">
                                <h3 class="text-sm font-medium text-gray-900 truncate">{{ $post->title }}</h3>
                                <p class="text-xs text-gray-500 mt-1">
                                    {{ $post->category->name ?? 'No Category' }} • 
                                    {{ $post->created_at->diffForHumans() }}
                                </p>
                            </div>
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                {{ $post->is_published ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800' }}">
                                {{ $post->is_published ? 'Published' : 'Draft' }}
                            </span>
                        </div>
                    </div>
                    @empty
                    <div class="recent-item text-center text-gray-500">
                        No posts found
                    </div>
                    @endforelse
                </div>
            </div>

            <!-- Popular Posts -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h2 class="text-lg font-semibold text-gray-900">Popular Posts</h2>
                </div>
                <div class="divide-y divide-gray-200">
                    @forelse($popularPosts as $post)
                    <div class="recent-item">
                        <div class="flex items-center justify-between">
                            <div class="flex-1">
                                <h3 class="text-sm font-medium text-gray-900 truncate">{{ $post->title }}</h3>
                                <p class="text-xs text-gray-500 mt-1">
                                    {{ $post->views }} views • 
                                    {{ $post->created_at->diffForHumans() }}
                                </p>
                            </div>
                            <div class="text-right">
                                <div class="text-sm font-medium text-blue-600">{{ $post->views }}</div>
                                <div class="text-xs text-gray-500">views</div>
                            </div>
                        </div>
                    </div>
                    @empty
                    <div class="recent-item text-center text-gray-500">
                        No posts found
                    </div>
                    @endforelse
                </div>
            </div>
        </div>

        <!-- Quick Actions -->
        <div class="mt-8">
            <h2 class="text-lg font-semibold text-gray-900 mb-4">Quick Actions</h2>
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                <a href="{{ route('admin.posts.create') }}" class="bg-white p-4 rounded-lg border border-gray-200 hover:border-blue-300 transition-colors">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <i class="fas fa-plus-circle text-blue-500 text-xl"></i>
                        </div>
                        <div class="ml-3">
                            <p class="text-sm font-medium text-gray-900">New Post</p>
                            <p class="text-xs text-gray-500">Create new article</p>
                        </div>
                    </div>
                </a>
                
                <a href="{{ route('admin.categories.index') }}" class="bg-white p-4 rounded-lg border border-gray-200 hover:border-green-300 transition-colors">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <i class="fas fa-tags text-green-500 text-xl"></i>
                        </div>
                        <div class="ml-3">
                            <p class="text-sm font-medium text-gray-900">Categories</p>
                            <p class="text-xs text-gray-500">Manage categories</p>
                        </div>
                    </div>
                </a>
                
                <a href="{{ route('admin.media.index') }}" class="bg-white p-4 rounded-lg border border-gray-200 hover:border-purple-300 transition-colors">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <i class="fas fa-images text-purple-500 text-xl"></i>
                        </div>
                        <div class="ml-3">
                            <p class="text-sm font-medium text-gray-900">Media</p>
                            <p class="text-xs text-gray-500">Upload files</p>
                        </div>
                    </div>
                </a>
                
                <a href="{{ route('admin.settings.index') }}" class="bg-white p-4 rounded-lg border border-gray-200 hover:border-orange-300 transition-colors">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <i class="fas fa-cog text-orange-500 text-xl"></i>
                        </div>
                        <div class="ml-3">
                            <p class="text-sm font-medium text-gray-900">Settings</p>
                            <p class="text-xs text-gray-500">Site configuration</p>
                        </div>
                    </div>
                </a>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    // Add any dashboard-specific JavaScript here
    console.log('Dashboard loaded successfully');
</script>
@endpush