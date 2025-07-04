<div class="menu-item bg-white border border-gray-200 rounded-lg p-4 mb-2" 
     data-menu-id="{{ $menu->id }}"
     data-title="{{ $menu->title }}"
     data-type="{{ $menu->type }}"
     data-url="{{ $menu->url }}"
     data-target="{{ $menu->target }}"
     data-icon="{{ $menu->icon }}"
     data-css-class="{{ $menu->css_class }}"
     data-active="{{ $menu->is_active ? '1' : '0' }}"
     data-parent-id="{{ $menu->parent_id }}">
    
    <div class="flex items-center justify-between">
        <!-- Left side: Drag handle and menu info -->
        <div class="flex items-center space-x-3">
            <!-- Drag Handle -->
            <div class="drag-handle text-gray-400 hover:text-gray-600">
                <i class="fas fa-grip-vertical"></i>
            </div>
            
            <!-- Menu Icon -->
            @if($menu->icon)
                <div class="text-gray-600">
                    <i class="{{ $menu->icon }}"></i>
                </div>
            @endif
            
            <!-- Menu Info -->
            <div class="flex-1">
                <div class="flex items-center space-x-2">
                    @if($menu->parent_id)
                        <span class="menu-level-indicator"></span>
                    @endif
                    <h4 class="font-medium text-gray-900">{{ $menu->title }}</h4>
                    
                    <!-- Type Badge -->
                    <span class="px-2 py-1 text-xs rounded-full 
                        @if($menu->type === 'custom') bg-gray-100 text-gray-800
                        @elseif($menu->type === 'post') bg-blue-100 text-blue-800
                        @elseif($menu->type === 'page') bg-green-100 text-green-800
                        @elseif($menu->type === 'category') bg-purple-100 text-purple-800
                        @endif">
                        @if($menu->type === 'custom') Custom
                        @elseif($menu->type === 'post') Post
                        @elseif($menu->type === 'page') Page
                        @elseif($menu->type === 'category') Category
                        @endif
                    </span>
                    
                    <!-- Status Badge -->
                    <span class="px-2 py-1 text-xs rounded-full {{ $menu->is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                        {{ $menu->is_active ? 'Active' : 'Inactive' }}
                    </span>
                </div>
                
                <!-- URL/Reference Info -->
                <div class="text-sm text-gray-500 mt-1">
                    @if($menu->type === 'custom')
                        <i class="fas fa-link mr-1"></i>
                        {{ $menu->url ?: '#' }}
                    @elseif($menu->type === 'post' && $menu->referencedPost)
                        <i class="fas fa-newspaper mr-1"></i>
                        {{ $menu->referencedPost->title }}
                    @elseif($menu->type === 'page' && $menu->referencedPost)
                        <i class="fas fa-file-alt mr-1"></i>
                        {{ $menu->referencedPost->title }}
                    @elseif($menu->type === 'category' && $menu->referencedCategory)
                        <i class="fas fa-tags mr-1"></i>
                        {{ $menu->referencedCategory->name }}
                    @else
                        <i class="fas fa-exclamation-triangle mr-1 text-yellow-500"></i>
                        <span class="text-yellow-600">Reference not found</span>
                    @endif
                </div>
            </div>
        </div>
        
        <!-- Right side: Actions -->
        <div class="flex items-center space-x-2">
            <!-- Add Sub Menu -->
            <button onclick="openAddSubMenuModal({{ $menu->id }}, '{{ $menu->title }}')" 
                    class="add-submenu-btn text-gray-400 hover:text-green-600" title="Tambah Sub Menu">
                <i class="fas fa-plus"></i>
            </button>
            
            <!-- Preview Link -->
            @if($menu->final_url && $menu->final_url !== '#')
                <a href="{{ $menu->final_url }}" target="_blank" 
                   class="text-gray-400 hover:text-blue-600" title="Preview">
                    <i class="fas fa-external-link-alt"></i>
                </a>
            @endif
            
            <!-- Toggle Active -->
            <button onclick="toggleActive({{ $menu->id }})" 
                    class="text-gray-400 hover:text-{{ $menu->is_active ? 'red' : 'green' }}-600" 
                    title="{{ $menu->is_active ? 'Deactivate' : 'Activate' }}">
                <i class="fas fa-{{ $menu->is_active ? 'eye-slash' : 'eye' }}"></i>
            </button>
            
            <!-- Edit -->
            <button onclick="openEditModal({{ $menu->id }})" 
                    class="text-gray-400 hover:text-blue-600" title="Edit">
                <i class="fas fa-edit"></i>
            </button>
            
            <!-- Delete -->
            <button onclick="deleteMenu({{ $menu->id }})" 
                    class="text-gray-400 hover:text-red-600" title="Delete">
                <i class="fas fa-trash"></i>
            </button>
        </div>
    </div>
    
    <!-- Children (Sub-menus) -->
    @if($menu->children && $menu->children->count() > 0)
        <div class="nested-menu mt-4">
            @foreach($menu->children as $child)
                @include('admin.navigation.partials.menu-item', ['menu' => $child])
            @endforeach
        </div>
    @endif
</div>