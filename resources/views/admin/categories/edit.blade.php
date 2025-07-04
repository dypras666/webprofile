@extends('layouts.admin')

@section('title', 'Edit Category - ' . $category->name)
@section('page-title', 'Edit Category')

@push('styles')
<style>
    .color-preview {
        width: 40px;
        height: 40px;
        border-radius: 8px;
        border: 2px solid #e5e7eb;
        cursor: pointer;
        transition: all 0.2s;
    }
    .color-preview:hover {
        transform: scale(1.05);
        border-color: #3b82f6;
    }
    .form-section {
        background: white;
        border-radius: 8px;
        padding: 1.5rem;
        box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.1);
        margin-bottom: 1.5rem;
    }
</style>
@endpush

@section('content')
<div class="max-w-4xl mx-auto">
    <!-- Header -->
    <div class="flex items-center justify-between mb-6">
        <div class="flex items-center space-x-4">
            <a href="{{ route('admin.categories.index') }}" class="text-gray-500 hover:text-gray-700">
                <i class="fas fa-arrow-left"></i>
            </a>
            <h1 class="text-2xl font-bold text-gray-900">Edit Category</h1>
        </div>
        <div class="flex items-center space-x-3">
            <a href="{{ route('admin.categories.show', $category->id) }}" class="bg-gray-600 text-white px-4 py-2 rounded-lg hover:bg-gray-700 transition-colors">
                <i class="fas fa-eye mr-2"></i>View Details
            </a>
        </div>
    </div>

    <form action="{{ route('admin.categories.update', $category->id) }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')
        
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Main Form -->
            <div class="lg:col-span-2 space-y-6">
                <!-- Basic Information -->
                <div class="form-section">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Basic Information</h3>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <!-- Name -->
                        <div class="md:col-span-2">
                            <label for="name" class="block text-sm font-medium text-gray-700 mb-2">Category Name *</label>
                            <input type="text" id="name" name="name" value="{{ old('name', $category->name) }}" 
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('name') border-red-500 @enderror" 
                                   placeholder="Enter category name" required>
                            @error('name')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                        
                        <!-- Slug -->
                        <div class="md:col-span-2">
                            <label for="slug" class="block text-sm font-medium text-gray-700 mb-2">Slug</label>
                            <input type="text" id="slug" name="slug" value="{{ old('slug', $category->slug) }}" 
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('slug') border-red-500 @enderror" 
                                   placeholder="category-slug">
                            <p class="mt-1 text-xs text-gray-500">Leave empty to auto-generate from name</p>
                            @error('slug')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                        
                        <!-- Description -->
                        <div class="md:col-span-2">
                            <label for="description" class="block text-sm font-medium text-gray-700 mb-2">Description</label>
                            <textarea id="description" name="description" rows="3" 
                                      class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('description') border-red-500 @enderror" 
                                      placeholder="Enter category description">{{ old('description', $category->description) }}</textarea>
                            @error('description')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                        
                        <!-- Color -->
                        <div>
                            <label for="color" class="block text-sm font-medium text-gray-700 mb-2">Category Color</label>
                            <div class="flex items-center space-x-3">
                                <div class="color-preview" id="color-preview" style="background-color: {{ $category->color ?? '#6b7280' }}"></div>
                                <input type="color" id="color" name="color" value="{{ old('color', $category->color ?? '#6b7280') }}" 
                                       class="w-16 h-10 border border-gray-300 rounded cursor-pointer">
                                <input type="text" id="color-text" value="{{ old('color', $category->color ?? '#6b7280') }}" 
                                       class="flex-1 px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent" 
                                       placeholder="#6b7280">
                            </div>
                            @error('color')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                        
                        <!-- Sort Order -->
                        <div>
                            <label for="sort_order" class="block text-sm font-medium text-gray-700 mb-2">Sort Order</label>
                            <input type="number" id="sort_order" name="sort_order" value="{{ old('sort_order', $category->sort_order ?? 0) }}" 
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('sort_order') border-red-500 @enderror" 
                                   min="0" placeholder="0">
                            @error('sort_order')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>
                
                <!-- SEO Settings -->
                <div class="form-section">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">SEO Settings</h3>
                    
                    <div class="space-y-4">
                        <!-- Meta Title -->
                        <div>
                            <label for="meta_title" class="block text-sm font-medium text-gray-700 mb-2">Meta Title</label>
                            <input type="text" id="meta_title" name="meta_title" value="{{ old('meta_title', $category->meta_title) }}" 
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('meta_title') border-red-500 @enderror" 
                                   placeholder="SEO title for this category">
                            @error('meta_title')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                        
                        <!-- Meta Description -->
                        <div>
                            <label for="meta_description" class="block text-sm font-medium text-gray-700 mb-2">Meta Description</label>
                            <textarea id="meta_description" name="meta_description" rows="3" 
                                      class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('meta_description') border-red-500 @enderror" 
                                      placeholder="SEO description for this category">{{ old('meta_description', $category->meta_description) }}</textarea>
                            @error('meta_description')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Sidebar -->
            <div class="space-y-6">
                <!-- Status -->
                <div class="form-section">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Status</h3>
                    
                    <div class="space-y-4">
                        <!-- Active Status -->
                        <div class="flex items-center">
                            <input type="checkbox" id="is_active" name="is_active" value="1" 
                                   {{ old('is_active', $category->is_active) ? 'checked' : '' }}
                                   class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                            <label for="is_active" class="ml-2 block text-sm text-gray-900">
                                Active Category
                            </label>
                        </div>
                        <p class="text-xs text-gray-500">Inactive categories won't be visible on the frontend</p>
                    </div>
                </div>
                
                <!-- Category Image -->
                <div class="form-section">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Category Image</h3>
                    
                    @if($category->image)
                        <div class="mb-4">
                            <img src="{{ $category->image }}" alt="{{ $category->name }}" class="w-full h-32 object-cover rounded-lg">
                            <p class="text-xs text-gray-500 mt-2">Current image</p>
                        </div>
                    @endif
                    
                    <div>
                        <label for="image" class="block text-sm font-medium text-gray-700 mb-2">Upload New Image</label>
                        <input type="file" id="image" name="image" accept="image/*" 
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('image') border-red-500 @enderror">
                        <p class="mt-1 text-xs text-gray-500">Max size: 2MB. Formats: JPEG, PNG, JPG, GIF, WebP</p>
                        @error('image')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
                
                <!-- Actions -->
                <div class="form-section">
                    <div class="space-y-3">
                        <button type="submit" class="w-full bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition-colors">
                            <i class="fas fa-save mr-2"></i>Update Category
                        </button>
                        
                        <a href="{{ route('admin.categories.index') }}" class="w-full bg-gray-600 text-white px-4 py-2 rounded-lg hover:bg-gray-700 transition-colors text-center block">
                            <i class="fas fa-times mr-2"></i>Cancel
                        </a>
                        
                        <button type="button" onclick="confirmDelete()" class="w-full bg-red-600 text-white px-4 py-2 rounded-lg hover:bg-red-700 transition-colors">
                            <i class="fas fa-trash mr-2"></i>Delete Category
                        </button>
                    </div>
                </div>
                
                <!-- Category Info -->
                <div class="form-section">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Category Info</h3>
                    
                    <dl class="space-y-2 text-sm">
                        <div>
                            <dt class="font-medium text-gray-500">Created:</dt>
                            <dd class="text-gray-900">{{ $category->created_at->format('M d, Y H:i') }}</dd>
                        </div>
                        <div>
                            <dt class="font-medium text-gray-500">Last Updated:</dt>
                            <dd class="text-gray-900">{{ $category->updated_at->format('M d, Y H:i') }}</dd>
                        </div>
                        <div>
                            <dt class="font-medium text-gray-500">Posts Count:</dt>
                            <dd class="text-gray-900">{{ $category->posts_count ?? 0 }} posts</dd>
                        </div>
                    </dl>
                </div>
            </div>
        </div>
    </form>
    
    <!-- Delete Form (Hidden) -->
    <form id="delete-form" action="{{ route('admin.categories.destroy', $category->id) }}" method="POST" class="hidden">
        @csrf
        @method('DELETE')
    </form>
</div>
@endsection

@push('scripts')
<script>
// Auto-generate slug from name
document.getElementById('name').addEventListener('input', function() {
    const name = this.value;
    const slug = name.toLowerCase()
        .replace(/[^a-z0-9\s-]/g, '')
        .replace(/\s+/g, '-')
        .replace(/-+/g, '-')
        .replace(/^-+|-+$/g, ''); // Remove leading and trailing dashes
    document.getElementById('slug').value = slug;
});

// Color picker synchronization
const colorInput = document.getElementById('color');
const colorText = document.getElementById('color-text');
const colorPreview = document.getElementById('color-preview');

colorInput.addEventListener('change', function() {
    const color = this.value;
    colorText.value = color;
    colorPreview.style.backgroundColor = color;
});

colorText.addEventListener('input', function() {
    const color = this.value;
    if (/^#([A-Fa-f0-9]{6}|[A-Fa-f0-9]{3})$/.test(color)) {
        colorInput.value = color;
        colorPreview.style.backgroundColor = color;
    }
});

// Delete confirmation
function confirmDelete() {
    if (confirm('Are you sure you want to delete this category? This action cannot be undone.')) {
        document.getElementById('delete-form').submit();
    }
}

// Form validation
document.querySelector('form').addEventListener('submit', function(e) {
    const name = document.getElementById('name').value.trim();
    if (!name) {
        e.preventDefault();
        alert('Category name is required.');
        document.getElementById('name').focus();
        return false;
    }
});
</script>
@endpush