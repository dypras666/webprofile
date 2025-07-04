@extends('layouts.admin')

@section('title', 'Categories Management')
@section('page-title', 'Categories')

@push('styles')
<style>
    .category-color {
        width: 20px;
        height: 20px;
        border-radius: 50%;
        border: 2px solid #e5e7eb;
    }
    .table-actions {
        white-space: nowrap;
    }
</style>
@endpush

@section('content')
<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    <!-- Categories List -->
    <div class="lg:col-span-2">
        <div class="bg-white rounded-lg shadow">
            <!-- Header -->
            <div class="px-6 py-4 border-b border-gray-200">
                <div class="flex items-center justify-between">
                    <h2 class="text-lg font-medium text-gray-900">All Categories</h2>
                    <div class="flex items-center space-x-3">
                        <!-- Search -->
                        <div class="relative">
                            <input type="text" placeholder="Search categories..." class="pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <i class="fas fa-search text-gray-400"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Table -->
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Name
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Description
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Posts
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Actions
                            </th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse($categories ?? [] as $category)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4">
                                <div class="flex items-center">
                                    <div class="category-color mr-3" style="background-color: {{ $category->color ?? '#6b7280' }}"></div>
                                    <div>
                                        <div class="text-sm font-medium text-gray-900">{{ $category->name }}</div>
                                        <div class="text-sm text-gray-500">{{ $category->slug }}</div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="text-sm text-gray-900">{{ Str::limit($category->description, 50) }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                    {{ $category->posts_count ?? 0 }} posts
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap table-actions">
                                <div class="flex items-center space-x-2">
                                    <button onclick="editCategory({{ $category->id }})" class="text-indigo-600 hover:text-indigo-900" title="Edit">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <a href="{{ route('frontend.category', $category->slug) }}" target="_blank" class="text-blue-600 hover:text-blue-900" title="View">
                                        <i class="fas fa-external-link-alt"></i>
                                    </a>
                                    <form method="POST" action="{{ route('admin.categories.destroy', $category) }}" class="inline" onsubmit="return confirm('Are you sure you want to delete this category?')">
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
                            <td colspan="4" class="px-6 py-12 text-center">
                                <div class="text-gray-500">
                                    <i class="fas fa-folder text-4xl mb-4"></i>
                                    <p class="text-lg font-medium">No categories found</p>
                                    <p class="text-sm">Get started by creating your first category.</p>
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    
    <!-- Add/Edit Category Form -->
    <div class="lg:col-span-1">
        <div class="bg-white rounded-lg shadow">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-medium text-gray-900" id="form-title">Add New Category</h3>
            </div>
            
            <form id="category-form" method="POST" action="{{ route('admin.categories.store') }}" class="p-6 space-y-4">
                @csrf
                <input type="hidden" id="category-id" name="_method" value="">
                
                <!-- Name -->
                <div>
                    <label for="name" class="block text-sm font-medium text-gray-700 mb-1">Name *</label>
                    <input type="text" id="name" name="name" required class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                </div>
                
                <!-- Slug -->
                <div>
                    <label for="slug" class="block text-sm font-medium text-gray-700 mb-1">Slug</label>
                    <input type="text" id="slug" name="slug" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    <p class="text-xs text-gray-500 mt-1">Leave empty to auto-generate from name</p>
                </div>
                
                <!-- Description -->
                <div>
                    <label for="description" class="block text-sm font-medium text-gray-700 mb-1">Description</label>
                    <textarea id="description" name="description" rows="3" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-transparent"></textarea>
                </div>
                
                <!-- Color -->
                <div>
                    <label for="color" class="block text-sm font-medium text-gray-700 mb-1">Color</label>
                    <div class="flex items-center space-x-3">
                        <input type="color" id="color" name="color" value="#6b7280" class="h-10 w-16 border border-gray-300 rounded cursor-pointer">
                        <input type="text" id="color-text" class="flex-1 border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-transparent" placeholder="#6b7280">
                    </div>
                </div>
                
                <!-- Meta Title -->
                <div>
                    <label for="meta_title" class="block text-sm font-medium text-gray-700 mb-1">Meta Title</label>
                    <input type="text" id="meta_title" name="meta_title" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                </div>
                
                <!-- Meta Description -->
                <div>
                    <label for="meta_description" class="block text-sm font-medium text-gray-700 mb-1">Meta Description</label>
                    <textarea id="meta_description" name="meta_description" rows="2" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-transparent"></textarea>
                </div>
                
                <!-- Submit Buttons -->
                <div class="flex items-center space-x-3 pt-4">
                    <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg flex-1">
                        <span id="submit-text">Create Category</span>
                    </button>
                    <button type="button" onclick="resetForm()" class="bg-gray-300 hover:bg-gray-400 text-gray-700 px-4 py-2 rounded-lg">
                        Reset
                    </button>
                </div>
            </form>
        </div>
        
        <!-- Category Stats -->
        <div class="mt-6 bg-white rounded-lg shadow p-6">
            <h3 class="text-lg font-medium text-gray-900 mb-4">Statistics</h3>
            <div class="space-y-3">
                <div class="flex justify-between">
                    <span class="text-sm text-gray-600">Total Categories</span>
                    <span class="text-sm font-medium text-gray-900">{{ $totalCategories ?? 0 }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-sm text-gray-600">Categories with Posts</span>
                    <span class="text-sm font-medium text-gray-900">{{ $categoriesWithPosts ?? 0 }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-sm text-gray-600">Empty Categories</span>
                    <span class="text-sm font-medium text-gray-900">{{ $emptyCategories ?? 0 }}</span>
                </div>
            </div>
        </div>
    </div>
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

// Color picker sync
document.getElementById('color').addEventListener('change', function() {
    document.getElementById('color-text').value = this.value;
});

document.getElementById('color-text').addEventListener('input', function() {
    const color = this.value;
    if (/^#[0-9A-F]{6}$/i.test(color)) {
        document.getElementById('color').value = color;
    }
});

// Edit category function
function editCategory(categoryId) {
    console.log('Editing category ID:', categoryId);
    
    fetch(`/admin/categories/${categoryId}`, {
        method: 'GET',
        headers: {
            'Accept': 'application/json',
            'Content-Type': 'application/json',
            'X-Requested-With': 'XMLHttpRequest'
        }
    })
        .then(response => {
            console.log('Response status:', response.status);
            console.log('Response headers:', response.headers.get('content-type'));
            
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            
            return response.json();
        })
        .then(category => {
            console.log('Category data received:', category);
            
            document.getElementById('form-title').textContent = 'Edit Category';
            document.getElementById('submit-text').textContent = 'Update Category';
            document.getElementById('category-form').action = `/admin/categories/${categoryId}`;
            document.getElementById('category-id').value = 'PUT';
            
            document.getElementById('name').value = category.name || '';
            document.getElementById('slug').value = category.slug || '';
            document.getElementById('description').value = category.description || '';
            document.getElementById('color').value = category.color || '#6b7280';
            document.getElementById('color-text').value = category.color || '#6b7280';
            document.getElementById('meta_title').value = category.meta_title || '';
            document.getElementById('meta_description').value = category.meta_description || '';
            
            // Update color preview
            updateColorPreview(category.color || '#6b7280');
        })
        .catch(error => {
            console.error('Error details:', error);
            alert(`Error loading category data: ${error.message}`);
        });
}

// Update color preview function
function updateColorPreview(color) {
    const colorInput = document.getElementById('color');
    const colorText = document.getElementById('color-text');
    
    if (colorInput && colorText) {
        colorInput.value = color;
        colorText.value = color;
    }
}

// Reset form function
function resetForm() {
    document.getElementById('form-title').textContent = 'Add New Category';
    document.getElementById('submit-text').textContent = 'Create Category';
    document.getElementById('category-form').action = '{{ route("admin.categories.store") }}';
    document.getElementById('category-id').value = '';
    document.getElementById('category-form').reset();
    document.getElementById('color').value = '#6b7280';
    document.getElementById('color-text').value = '#6b7280';
    updateColorPreview('#6b7280');
}
</script>
@endpush