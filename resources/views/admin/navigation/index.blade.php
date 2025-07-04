@extends('layouts.admin')

@section('title', 'Navigation Management')
@section('page-title', 'Navigation Management')

@push('styles')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sortablejs@1.15.0/Sortable.min.css">
<style>
    .sortable-ghost {
        opacity: 0.4;
    }
    .sortable-chosen {
        background-color: #f3f4f6;
    }
    .menu-item {
        transition: all 0.2s ease;
    }
    .menu-item:hover {
        background-color: #f9fafb;
    }
    .nested-menu {
        margin-left: 2rem;
        border-left: 2px solid #e5e7eb;
        padding-left: 1rem;
    }
    .drag-handle {
        cursor: grab;
    }
    .drag-handle:active {
        cursor: grabbing;
    }
</style>
@endpush

@section('content')
<div class="bg-white rounded-lg shadow-sm">
    <!-- Header -->
    <div class="p-6 border-b border-gray-200">
        <div class="flex justify-between items-center">
            <div>
                <h2 class="text-xl font-semibold text-gray-800">Navigation Menu</h2>
                <p class="text-sm text-gray-600 mt-1">Kelola menu navigasi website dengan drag & drop</p>
            </div>
            <button id="add-menu-btn" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg flex items-center">
                <i class="fas fa-plus mr-2"></i>
                Tambah Menu
            </button>
        </div>
    </div>

    <!-- Menu List -->
    <div class="p-6">
        <div id="menu-container">
            @if($menus->count() > 0)
                <div id="sortable-menu" class="space-y-2">
                    @foreach($menus as $menu)
                        @include('admin.navigation.partials.menu-item', ['menu' => $menu])
                    @endforeach
                </div>
            @else
                <div class="text-center py-12">
                    <i class="fas fa-bars text-4xl text-gray-300 mb-4"></i>
                    <h3 class="text-lg font-medium text-gray-900 mb-2">Belum ada menu</h3>
                    <p class="text-gray-500 mb-4">Mulai dengan menambahkan item menu pertama</p>
                    <button class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg" onclick="openAddModal()">
                        <i class="fas fa-plus mr-2"></i>
                        Tambah Menu
                    </button>
                </div>
            @endif
        </div>
    </div>
</div>

<!-- Add/Edit Menu Modal -->
<div id="menu-modal" class="fixed inset-0 bg-black bg-opacity-50 hidden z-50">
    <div class="flex items-center justify-center min-h-screen p-4">
        <div class="bg-white rounded-lg shadow-xl w-full max-w-md">
            <div class="p-6">
                <div class="flex justify-between items-center mb-4">
                    <h3 id="modal-title" class="text-lg font-semibold text-gray-900">Tambah Menu</h3>
                    <button onclick="closeModal()" class="text-gray-400 hover:text-gray-600">
                        <i class="fas fa-times"></i>
                    </button>
                </div>

                <form id="menu-form" class="space-y-4">
                    <input type="hidden" id="menu-id" name="id">
                    
                    <!-- Title -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Judul Menu</label>
                        <input type="text" id="menu-title" name="title" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500" required>
                    </div>

                    <!-- Type -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Tipe Menu</label>
                        <select id="menu-type" name="type" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                            <option value="custom">Custom Link</option>
                            <option value="post">Post/Berita</option>
                            <option value="page">Halaman</option>
                            <option value="category">Kategori</option>
                        </select>
                    </div>

                    <!-- Custom URL -->
                    <div id="custom-url-field">
                        <label class="block text-sm font-medium text-gray-700 mb-1">URL</label>
                        <input type="url" id="menu-url" name="url" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500" placeholder="https://example.com">
                    </div>

                    <!-- Reference Selection -->
                    <div id="reference-field" class="hidden">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Pilih Item</label>
                        <select id="menu-reference" name="reference_id" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                            <option value="">Pilih...</option>
                        </select>
                    </div>

                    <!-- Target -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Target</label>
                        <select id="menu-target" name="target" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                            <option value="_self">Same Window</option>
                            <option value="_blank">New Window</option>
                        </select>
                    </div>

                    <!-- Icon -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Icon (Font Awesome)</label>
                        <div class="relative">
                            <div class="flex">
                                <div class="flex-1">
                                    <input type="text" id="menu-icon" name="icon" class="w-full px-3 py-2 pr-10 border border-gray-300 rounded-l-md focus:outline-none focus:ring-2 focus:ring-blue-500" placeholder="fas fa-home">
                                </div>
                                <button type="button" id="icon-picker-btn" class="px-3 py-2 bg-gray-100 border border-l-0 border-gray-300 rounded-r-md hover:bg-gray-200 focus:outline-none focus:ring-2 focus:ring-blue-500">
                                    <i id="icon-preview" class="fas fa-icons text-gray-600"></i>
                                </button>
                            </div>
                        </div>
                        
                        <!-- Icon Picker Modal -->
                        <div id="icon-picker-modal" class="hidden absolute z-50 mt-1 w-full bg-white border border-gray-300 rounded-md shadow-lg max-h-64 overflow-y-auto">
                            <div class="p-3">
                                <!-- Search Input -->
                                <div class="mb-3">
                                    <input type="text" id="icon-search" placeholder="Cari icon..." class="w-full px-3 py-2 text-sm border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                                </div>
                                <div class="grid grid-cols-6 gap-2" id="icon-grid">
                                    <!-- Popular Icons -->
                                    <button type="button" class="icon-option p-2 text-center hover:bg-gray-100 rounded" data-icon="fas fa-home" title="Home">
                                        <i class="fas fa-home text-lg"></i>
                                    </button>
                                    <button type="button" class="icon-option p-2 text-center hover:bg-gray-100 rounded" data-icon="fas fa-user" title="User">
                                        <i class="fas fa-user text-lg"></i>
                                    </button>
                                    <button type="button" class="icon-option p-2 text-center hover:bg-gray-100 rounded" data-icon="fas fa-envelope" title="Email">
                                        <i class="fas fa-envelope text-lg"></i>
                                    </button>
                                    <button type="button" class="icon-option p-2 text-center hover:bg-gray-100 rounded" data-icon="fas fa-phone" title="Phone">
                                        <i class="fas fa-phone text-lg"></i>
                                    </button>
                                    <button type="button" class="icon-option p-2 text-center hover:bg-gray-100 rounded" data-icon="fas fa-info-circle" title="Info">
                                        <i class="fas fa-info-circle text-lg"></i>
                                    </button>
                                    <button type="button" class="icon-option p-2 text-center hover:bg-gray-100 rounded" data-icon="fas fa-cog" title="Settings">
                                        <i class="fas fa-cog text-lg"></i>
                                    </button>
                                    <button type="button" class="icon-option p-2 text-center hover:bg-gray-100 rounded" data-icon="fas fa-newspaper" title="News">
                                        <i class="fas fa-newspaper text-lg"></i>
                                    </button>
                                    <button type="button" class="icon-option p-2 text-center hover:bg-gray-100 rounded" data-icon="fas fa-images" title="Gallery">
                                        <i class="fas fa-images text-lg"></i>
                                    </button>
                                    <button type="button" class="icon-option p-2 text-center hover:bg-gray-100 rounded" data-icon="fas fa-video" title="Video">
                                        <i class="fas fa-video text-lg"></i>
                                    </button>
                                    <button type="button" class="icon-option p-2 text-center hover:bg-gray-100 rounded" data-icon="fas fa-calendar" title="Calendar">
                                        <i class="fas fa-calendar text-lg"></i>
                                    </button>
                                    <button type="button" class="icon-option p-2 text-center hover:bg-gray-100 rounded" data-icon="fas fa-map-marker-alt" title="Location">
                                        <i class="fas fa-map-marker-alt text-lg"></i>
                                    </button>
                                    <button type="button" class="icon-option p-2 text-center hover:bg-gray-100 rounded" data-icon="fas fa-shopping-cart" title="Cart">
                                        <i class="fas fa-shopping-cart text-lg"></i>
                                    </button>
                                    <button type="button" class="icon-option p-2 text-center hover:bg-gray-100 rounded" data-icon="fas fa-heart" title="Heart">
                                        <i class="fas fa-heart text-lg"></i>
                                    </button>
                                    <button type="button" class="icon-option p-2 text-center hover:bg-gray-100 rounded" data-icon="fas fa-star" title="Star">
                                        <i class="fas fa-star text-lg"></i>
                                    </button>
                                    <button type="button" class="icon-option p-2 text-center hover:bg-gray-100 rounded" data-icon="fas fa-search" title="Search">
                                        <i class="fas fa-search text-lg"></i>
                                    </button>
                                    <button type="button" class="icon-option p-2 text-center hover:bg-gray-100 rounded" data-icon="fas fa-download" title="Download">
                                        <i class="fas fa-download text-lg"></i>
                                    </button>
                                    <button type="button" class="icon-option p-2 text-center hover:bg-gray-100 rounded" data-icon="fas fa-upload" title="Upload">
                                        <i class="fas fa-upload text-lg"></i>
                                    </button>
                                    <button type="button" class="icon-option p-2 text-center hover:bg-gray-100 rounded" data-icon="fas fa-share" title="Share">
                                        <i class="fas fa-share text-lg"></i>
                                    </button>
                                    <button type="button" class="icon-option p-2 text-center hover:bg-gray-100 rounded" data-icon="fas fa-link" title="Link">
                                        <i class="fas fa-link text-lg"></i>
                                    </button>
                                    <button type="button" class="icon-option p-2 text-center hover:bg-gray-100 rounded" data-icon="fas fa-file" title="File">
                                        <i class="fas fa-file text-lg"></i>
                                    </button>
                                    <button type="button" class="icon-option p-2 text-center hover:bg-gray-100 rounded" data-icon="fas fa-folder" title="Folder">
                                        <i class="fas fa-folder text-lg"></i>
                                    </button>
                                    <button type="button" class="icon-option p-2 text-center hover:bg-gray-100 rounded" data-icon="fas fa-tag" title="Tag">
                                        <i class="fas fa-tag text-lg"></i>
                                    </button>
                                    <button type="button" class="icon-option p-2 text-center hover:bg-gray-100 rounded" data-icon="fas fa-tags" title="Tags">
                                        <i class="fas fa-tags text-lg"></i>
                                    </button>
                                    <button type="button" class="icon-option p-2 text-center hover:bg-gray-100 rounded" data-icon="fas fa-book" title="Book">
                                        <i class="fas fa-book text-lg"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- CSS Class -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">CSS Class</label>
                        <input type="text" id="menu-css-class" name="css_class" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                    </div>

                    <!-- Active Status -->
                    <div class="flex items-center">
                        <input type="checkbox" id="menu-active" name="is_active" class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded" checked>
                        <label for="menu-active" class="ml-2 block text-sm text-gray-900">Aktif</label>
                    </div>

                    <!-- Buttons -->
                    <div class="flex justify-end space-x-3 pt-4">
                        <button type="button" onclick="closeModal()" class="px-4 py-2 text-gray-700 bg-gray-200 rounded-md hover:bg-gray-300">
                            Batal
                        </button>
                        <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700">
                            <span id="submit-text">Simpan</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
let sortable;
let isEditing = false;
let currentMenuId = null;

// Initialize sortable
document.addEventListener('DOMContentLoaded', function() {
    initializeSortable();
    setupEventListeners();
});

function initializeSortable() {
    const menuContainer = document.getElementById('sortable-menu');
    if (!menuContainer) return;

    sortable = new Sortable(menuContainer, {
        handle: '.drag-handle',
        animation: 150,
        ghostClass: 'sortable-ghost',
        chosenClass: 'sortable-chosen',
        onEnd: function(evt) {
            updateMenuOrder();
        }
    });
}

function setupEventListeners() {
    // Add menu button
    document.getElementById('add-menu-btn').addEventListener('click', openAddModal);
    
    // Menu type change
    document.getElementById('menu-type').addEventListener('change', handleTypeChange);
    
    // Form submit
    document.getElementById('menu-form').addEventListener('submit', handleFormSubmit);
    
    // Icon picker
    setupIconPicker();
}

function openAddModal() {
    isEditing = false;
    currentMenuId = null;
    document.getElementById('modal-title').textContent = 'Tambah Menu';
    document.getElementById('submit-text').textContent = 'Simpan';
    document.getElementById('menu-form').reset();
    document.getElementById('menu-active').checked = true;
    updateIconPreview(''); // Reset icon preview
    handleTypeChange();
    document.getElementById('menu-modal').classList.remove('hidden');
}

function openEditModal(menuId) {
    isEditing = true;
    currentMenuId = menuId;
    document.getElementById('modal-title').textContent = 'Edit Menu';
    document.getElementById('submit-text').textContent = 'Update';
    
    // Load menu data (you would fetch this from the server)
    // For now, we'll get it from the DOM
    const menuItem = document.querySelector(`[data-menu-id="${menuId}"]`);
    if (menuItem) {
        document.getElementById('menu-id').value = menuId;
        document.getElementById('menu-title').value = menuItem.dataset.title || '';
        document.getElementById('menu-type').value = menuItem.dataset.type || 'custom';
        document.getElementById('menu-url').value = menuItem.dataset.url || '';
        document.getElementById('menu-target').value = menuItem.dataset.target || '_self';
        const iconValue = menuItem.dataset.icon || '';
        document.getElementById('menu-icon').value = iconValue;
        updateIconPreview(iconValue);
        document.getElementById('menu-css-class').value = menuItem.dataset.cssClass || '';
        document.getElementById('menu-active').checked = menuItem.dataset.active === '1';
        
        handleTypeChange();
    }
    
    document.getElementById('menu-modal').classList.remove('hidden');
}

function closeModal() {
    document.getElementById('menu-modal').classList.add('hidden');
}

function handleTypeChange() {
    const type = document.getElementById('menu-type').value;
    const customField = document.getElementById('custom-url-field');
    const referenceField = document.getElementById('reference-field');
    const referenceSelect = document.getElementById('menu-reference');
    
    if (type === 'custom') {
        customField.classList.remove('hidden');
        referenceField.classList.add('hidden');
    } else {
        customField.classList.add('hidden');
        referenceField.classList.remove('hidden');
        loadReferenceOptions(type);
    }
}

function loadReferenceOptions(type) {
    const select = document.getElementById('menu-reference');
    select.innerHTML = '<option value="">Loading...</option>';
    
    let url = '';
    if (type === 'post' || type === 'page') {
        url = `/admin/navigation/posts?type=${type}`;
    } else if (type === 'category') {
        url = '/admin/navigation/categories';
    }
    
    if (url) {
        fetch(url)
            .then(response => response.json())
            .then(data => {
                select.innerHTML = '<option value="">Pilih...</option>';
                data.forEach(item => {
                    const option = document.createElement('option');
                    option.value = item.id;
                    option.textContent = item.title || item.name;
                    select.appendChild(option);
                });
            })
            .catch(error => {
                console.error('Error loading options:', error);
                select.innerHTML = '<option value="">Error loading options</option>';
            });
    }
}

function handleFormSubmit(e) {
    e.preventDefault();
    
    const formData = new FormData(e.target);
    const data = Object.fromEntries(formData.entries());
    data.is_active = document.getElementById('menu-active').checked;
    
    const url = isEditing ? `/admin/navigation/${currentMenuId}` : '/admin/navigation';
    const method = isEditing ? 'PUT' : 'POST';
    
    fetch(url, {
        method: method,
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: JSON.stringify(data)
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            closeModal();
            location.reload(); // Reload to show updated menu
        } else {
            alert('Error: ' + (data.message || 'Something went wrong'));
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Error saving menu item');
    });
}

function deleteMenu(menuId) {
    if (confirm('Apakah Anda yakin ingin menghapus menu ini?')) {
        fetch(`/admin/navigation/${menuId}`, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                location.reload();
            } else {
                alert('Error: ' + (data.message || 'Something went wrong'));
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Error deleting menu item');
        });
    }
}

function toggleActive(menuId) {
    fetch(`/admin/navigation/${menuId}/toggle-active`, {
        method: 'PATCH',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            location.reload();
        } else {
            alert('Error: ' + (data.message || 'Something went wrong'));
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Error toggling menu status');
    });
}

function updateMenuOrder() {
    const menuItems = document.querySelectorAll('#sortable-menu > .menu-item');
    const menus = Array.from(menuItems).map((item, index) => ({
        id: parseInt(item.dataset.menuId),
        sort_order: index + 1
    }));
    
    fetch('/admin/navigation/update-order', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: JSON.stringify({ menus: menus })
    })
    .then(response => response.json())
    .then(data => {
        if (!data.success) {
            alert('Error updating menu order: ' + (data.message || 'Something went wrong'));
            location.reload();
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Error updating menu order');
        location.reload();
    });
}

function setupIconPicker() {
    const iconInput = document.getElementById('menu-icon');
    const iconPickerBtn = document.getElementById('icon-picker-btn');
    const iconPickerModal = document.getElementById('icon-picker-modal');
    const iconPreview = document.getElementById('icon-preview');
    const iconOptions = document.querySelectorAll('.icon-option');
    const iconSearch = document.getElementById('icon-search');
    
    // Toggle icon picker modal
    iconPickerBtn.addEventListener('click', function(e) {
        e.preventDefault();
        iconPickerModal.classList.toggle('hidden');
        if (!iconPickerModal.classList.contains('hidden')) {
            iconSearch.focus();
        }
    });
    
    // Handle icon selection
    iconOptions.forEach(option => {
        option.addEventListener('click', function(e) {
            e.preventDefault();
            const iconClass = this.dataset.icon;
            iconInput.value = iconClass;
            updateIconPreview(iconClass);
            iconPickerModal.classList.add('hidden');
            iconSearch.value = ''; // Reset search
            filterIcons(''); // Show all icons
        });
    });
    
    // Icon search functionality
    iconSearch.addEventListener('input', function() {
        const searchTerm = this.value.toLowerCase();
        filterIcons(searchTerm);
    });
    
    // Update icon preview when typing
    iconInput.addEventListener('input', function() {
        updateIconPreview(this.value);
    });
    
    // Close icon picker when clicking outside
    document.addEventListener('click', function(e) {
        if (!iconPickerBtn.contains(e.target) && !iconPickerModal.contains(e.target)) {
            iconPickerModal.classList.add('hidden');
            iconSearch.value = ''; // Reset search
            filterIcons(''); // Show all icons
        }
    });
}

function updateIconPreview(iconClass) {
    const iconPreview = document.getElementById('icon-preview');
    if (iconClass && iconClass.trim()) {
        iconPreview.className = iconClass + ' text-gray-600';
    } else {
        iconPreview.className = 'fas fa-icons text-gray-600';
    }
}

function filterIcons(searchTerm) {
    const iconOptions = document.querySelectorAll('.icon-option');
    
    iconOptions.forEach(option => {
        const iconClass = option.dataset.icon.toLowerCase();
        const iconTitle = option.title.toLowerCase();
        
        if (iconClass.includes(searchTerm) || iconTitle.includes(searchTerm)) {
            option.style.display = 'block';
        } else {
            option.style.display = 'none';
        }
    });
}
</script>
@endpush