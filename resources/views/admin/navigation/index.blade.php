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
            margin-top: 0.5rem;
        }

        .nested-menu .menu-item {
            background-color: #f8fafc;
            border-left: 3px solid #3b82f6;
            margin-bottom: 0.5rem;
        }

        .nested-menu .menu-item:hover {
            background-color: #f1f5f9;
        }

        .menu-level-indicator {
            display: inline-block;
            width: 12px;
            height: 12px;
            background-color: #3b82f6;
            border-radius: 50%;
            margin-right: 8px;
            opacity: 0.7;
        }

        .add-submenu-btn {
            transition: all 0.2s ease;
        }

        .add-submenu-btn:hover {
            transform: scale(1.1);
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
                <button id="add-menu-btn"
                    class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg flex items-center">
                    <i class="fas fa-plus mr-2"></i>
                    Tambah Menu
                </button>
            </div>
        </div>

        <!-- Menu List -->
        <div class="p-6" x-data="{ activeTab: 'top' }">
            <input type="hidden" id="active-tab-input" x-model="activeTab">
            <!-- Tabs -->
            <div class="flex border-b border-gray-200 mb-6">
                <button @click="activeTab = 'top'"
                    :class="{ 'border-blue-500 text-blue-600': activeTab === 'top', 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300': activeTab !== 'top' }"
                    class="py-2 px-4 border-b-2 font-medium text-sm focus:outline-none">
                    Top Navigation
                </button>
                <button @click="activeTab = 'bottom'"
                    :class="{ 'border-blue-500 text-blue-600': activeTab === 'bottom', 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300': activeTab !== 'bottom' }"
                    class="py-2 px-4 border-b-2 font-medium text-sm focus:outline-none">
                    Bottom Navigation
                </button>
                <button @click="activeTab = 'quicklink'"
                    :class="{ 'border-blue-500 text-blue-600': activeTab === 'quicklink', 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300': activeTab !== 'quicklink' }"
                    class="py-2 px-4 border-b-2 font-medium text-sm focus:outline-none">
                    Quick Links
                </button>
                <button @click="activeTab = 'footer_2'"
                    :class="{ 'border-blue-500 text-blue-600': activeTab === 'footer_2', 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300': activeTab !== 'footer_2' }"
                    class="py-2 px-4 border-b-2 font-medium text-sm focus:outline-none">
                    Footer Link
                </button>
            </div>

            <!-- Top Navigation -->
            <div x-show="activeTab === 'top'" class="menu-container-tab" data-position="top">
                <div id="sortable-menu-top" class="space-y-2 sortable-root">
                    @foreach($menus->where('position', 'top') as $menu)
                        @include('admin.navigation.partials.menu-item', ['menu' => $menu])
                    @endforeach
                    @if($menus->where('position', 'top')->count() === 0)
                        <p class="text-gray-500 text-center py-4">Belum ada menu di posisi ini.</p>
                    @endif
                </div>
            </div>

            <!-- Bottom Navigation -->
            <div x-show="activeTab === 'bottom'" class="menu-container-tab" data-position="bottom" style="display: none;">
                <div id="sortable-menu-bottom" class="space-y-2 sortable-root">
                    @foreach($menus->where('position', 'bottom') as $menu)
                        @include('admin.navigation.partials.menu-item', ['menu' => $menu])
                    @endforeach
                    @if($menus->where('position', 'bottom')->count() === 0)
                        <p class="text-gray-500 text-center py-4">Belum ada menu di posisi ini.</p>
                    @endif
                </div>
            </div>

            <!-- Quick Links -->
            <div x-show="activeTab === 'quicklink'" class="menu-container-tab" data-position="quicklink"
                style="display: none;">
                <div id="sortable-menu-quicklink" class="space-y-2 sortable-root">
                    <div class="p-2 bg-yellow-100 text-yellow-800 text-xs rounded mb-2">
                        Debug: Total Quicklinks filtered in View: {{ $menus->where('position', 'quicklink')->count() }}
                        <br>
                        Total Menus: {{ $menus->count() }}
                    </div>
                    @foreach($menus->where('position', 'quicklink') as $menu)
                        @include('admin.navigation.partials.menu-item', ['menu' => $menu])
                    @endforeach
                    @if($menus->where('position', 'quicklink')->count() === 0)
                        <p class="text-gray-500 text-center py-4">Belum ada menu di posisi ini.</p>
                    @endif
                </div>
            </div>

            <!-- Footer Link (Footer 2) -->
            <div x-show="activeTab === 'footer_2'" class="menu-container-tab" data-position="footer_2"
                style="display: none;">
                <div id="sortable-menu-footer_2" class="space-y-2 sortable-root">
                    @foreach($menus->where('position', 'footer_2') as $menu)
                        @include('admin.navigation.partials.menu-item', ['menu' => $menu])
                    @endforeach
                    @if($menus->where('position', 'footer_2')->count() === 0)
                        <p class="text-gray-500 text-center py-4">Belum ada menu di posisi ini.</p>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Add/Edit Menu Modal -->
    <div id="menu-modal" class="fixed inset-0 z-50 overflow-y-auto hidden" aria-labelledby="modal-title" role="dialog"
        aria-modal="true">
        <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" aria-hidden="true"
                onclick="closeModal()"></div>

            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

            <div
                class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-md sm:w-full">
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
                            <input type="text" id="menu-title" name="title"
                                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                                required>
                        </div>

                        <!-- Position -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Posisi</label>
                            <select id="menu-position" name="position"
                                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                                onchange="filterParentOptions()">
                                <option value="top">Top Navigation</option>
                                <option value="bottom">Bottom Navigation</option>
                                <option value="quicklink">Quick Links</option>
                                <option value="footer_2">Footer Link</option>
                            </select>
                        </div>

                        <!-- Type -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Tipe Menu</label>
                            <select id="menu-type" name="type"
                                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                                <option value="custom">Custom Link</option>
                                <option value="post">Post/Berita</option>
                                <option value="page">Halaman</option>
                                <option value="category">Kategori</option>
                            </select>
                        </div>

                        <!-- Custom URL -->
                        <div id="custom-url-field">
                            <label class="block text-sm font-medium text-gray-700 mb-1">URL</label>
                            <input type="text" id="menu-url" name="url"
                                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                                placeholder="https://example.com or #">
                        </div>

                        <!-- Reference Selection -->
                        <div id="reference-field" class="hidden">
                            <label class="block text-sm font-medium text-gray-700 mb-1">Pilih Item</label>
                            <select id="menu-reference" name="reference_id"
                                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                                <option value="">Pilih...</option>
                            </select>
                        </div>

                        <!-- Parent Menu -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Parent Menu</label>
                            <select id="menu-parent" name="parent_id"
                                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                                <option value="">-- Root Menu --</option>
                                @foreach($menus as $parentMenu)
                                    <option value="{{ $parentMenu->id }}" data-position="{{ $parentMenu->position }}">
                                        {{ $parentMenu->title }} ({{ ucfirst($parentMenu->position) }})
                                    </option>
                                    @if($parentMenu->children && $parentMenu->children->count() > 0)
                                        @foreach($parentMenu->children as $childMenu)
                                            <option value="{{ $childMenu->id }}">-- {{ $childMenu->title }}</option>
                                        @endforeach
                                    @endif
                                @endforeach
                            </select>
                            <p class="text-xs text-gray-500 mt-1">Pilih parent menu untuk membuat sub menu. Kosongkan untuk
                                menu utama.</p>
                        </div>

                        <!-- Target -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Target</label>
                            <select id="menu-target" name="target"
                                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
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
                                        <input type="text" id="menu-icon" name="icon"
                                            class="w-full px-3 py-2 pr-10 border border-gray-300 rounded-l-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                                            placeholder="fas fa-home">
                                    </div>
                                    <button type="button" id="icon-picker-btn"
                                        class="px-3 py-2 bg-gray-100 border border-l-0 border-gray-300 rounded-r-md hover:bg-gray-200 focus:outline-none focus:ring-2 focus:ring-blue-500">
                                        <i id="icon-preview" class="fas fa-icons text-gray-600"></i>
                                    </button>
                                </div>
                            </div>

                            <!-- Icon Picker Modal -->
                            <div id="icon-picker-modal"
                                class="hidden absolute z-50 mt-1 w-full bg-white border border-gray-300 rounded-md shadow-lg max-h-64 overflow-y-auto">
                                <div class="p-3">
                                    <!-- Search Input -->
                                    <div class="mb-3">
                                        <input type="text" id="icon-search" placeholder="Cari icon..."
                                            class="w-full px-3 py-2 text-sm border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                                    </div>
                                    <div class="grid grid-cols-6 gap-2" id="icon-grid">
                                        <!-- Popular Icons -->
                                        <button type="button" class="icon-option p-2 text-center hover:bg-gray-100 rounded"
                                            data-icon="fas fa-home" title="Home">
                                            <i class="fas fa-home text-lg"></i>
                                        </button>
                                        <button type="button" class="icon-option p-2 text-center hover:bg-gray-100 rounded"
                                            data-icon="fas fa-user" title="User">
                                            <i class="fas fa-user text-lg"></i>
                                        </button>
                                        <button type="button" class="icon-option p-2 text-center hover:bg-gray-100 rounded"
                                            data-icon="fas fa-envelope" title="Email">
                                            <i class="fas fa-envelope text-lg"></i>
                                        </button>
                                        <button type="button" class="icon-option p-2 text-center hover:bg-gray-100 rounded"
                                            data-icon="fas fa-phone" title="Phone">
                                            <i class="fas fa-phone text-lg"></i>
                                        </button>
                                        <button type="button" class="icon-option p-2 text-center hover:bg-gray-100 rounded"
                                            data-icon="fas fa-info-circle" title="Info">
                                            <i class="fas fa-info-circle text-lg"></i>
                                        </button>
                                        <button type="button" class="icon-option p-2 text-center hover:bg-gray-100 rounded"
                                            data-icon="fas fa-cog" title="Settings">
                                            <i class="fas fa-cog text-lg"></i>
                                        </button>
                                        <button type="button" class="icon-option p-2 text-center hover:bg-gray-100 rounded"
                                            data-icon="fas fa-newspaper" title="News">
                                            <i class="fas fa-newspaper text-lg"></i>
                                        </button>
                                        <button type="button" class="icon-option p-2 text-center hover:bg-gray-100 rounded"
                                            data-icon="fas fa-images" title="Gallery">
                                            <i class="fas fa-images text-lg"></i>
                                        </button>
                                        <button type="button" class="icon-option p-2 text-center hover:bg-gray-100 rounded"
                                            data-icon="fas fa-video" title="Video">
                                            <i class="fas fa-video text-lg"></i>
                                        </button>
                                        <button type="button" class="icon-option p-2 text-center hover:bg-gray-100 rounded"
                                            data-icon="fas fa-calendar" title="Calendar">
                                            <i class="fas fa-calendar text-lg"></i>
                                        </button>
                                        <button type="button" class="icon-option p-2 text-center hover:bg-gray-100 rounded"
                                            data-icon="fas fa-map-marker-alt" title="Location">
                                            <i class="fas fa-map-marker-alt text-lg"></i>
                                        </button>
                                        <button type="button" class="icon-option p-2 text-center hover:bg-gray-100 rounded"
                                            data-icon="fas fa-shopping-cart" title="Cart">
                                            <i class="fas fa-shopping-cart text-lg"></i>
                                        </button>
                                        <button type="button" class="icon-option p-2 text-center hover:bg-gray-100 rounded"
                                            data-icon="fas fa-heart" title="Heart">
                                            <i class="fas fa-heart text-lg"></i>
                                        </button>
                                        <button type="button" class="icon-option p-2 text-center hover:bg-gray-100 rounded"
                                            data-icon="fas fa-star" title="Star">
                                            <i class="fas fa-star text-lg"></i>
                                        </button>
                                        <button type="button" class="icon-option p-2 text-center hover:bg-gray-100 rounded"
                                            data-icon="fas fa-search" title="Search">
                                            <i class="fas fa-search text-lg"></i>
                                        </button>
                                        <button type="button" class="icon-option p-2 text-center hover:bg-gray-100 rounded"
                                            data-icon="fas fa-download" title="Download">
                                            <i class="fas fa-download text-lg"></i>
                                        </button>
                                        <button type="button" class="icon-option p-2 text-center hover:bg-gray-100 rounded"
                                            data-icon="fas fa-upload" title="Upload">
                                            <i class="fas fa-upload text-lg"></i>
                                        </button>
                                        <button type="button" class="icon-option p-2 text-center hover:bg-gray-100 rounded"
                                            data-icon="fas fa-share" title="Share">
                                            <i class="fas fa-share text-lg"></i>
                                        </button>
                                        <button type="button" class="icon-option p-2 text-center hover:bg-gray-100 rounded"
                                            data-icon="fas fa-link" title="Link">
                                            <i class="fas fa-link text-lg"></i>
                                        </button>
                                        <button type="button" class="icon-option p-2 text-center hover:bg-gray-100 rounded"
                                            data-icon="fas fa-file" title="File">
                                            <i class="fas fa-file text-lg"></i>
                                        </button>
                                        <button type="button" class="icon-option p-2 text-center hover:bg-gray-100 rounded"
                                            data-icon="fas fa-folder" title="Folder">
                                            <i class="fas fa-folder text-lg"></i>
                                        </button>
                                        <button type="button" class="icon-option p-2 text-center hover:bg-gray-100 rounded"
                                            data-icon="fas fa-tag" title="Tag">
                                            <i class="fas fa-tag text-lg"></i>
                                        </button>
                                        <button type="button" class="icon-option p-2 text-center hover:bg-gray-100 rounded"
                                            data-icon="fas fa-tags" title="Tags">
                                            <i class="fas fa-tags text-lg"></i>
                                        </button>
                                        <button type="button" class="icon-option p-2 text-center hover:bg-gray-100 rounded"
                                            data-icon="fas fa-book" title="Book">
                                            <i class="fas fa-book text-lg"></i>
                                        </button>
                                        <!-- Business & Office -->
                                        <button type="button" class="icon-option p-2 text-center hover:bg-gray-100 rounded"
                                            data-icon="fas fa-briefcase" title="Briefcase">
                                            <i class="fas fa-briefcase text-lg"></i>
                                        </button>
                                        <button type="button" class="icon-option p-2 text-center hover:bg-gray-100 rounded"
                                            data-icon="fas fa-building" title="Building">
                                            <i class="fas fa-building text-lg"></i>
                                        </button>
                                        <button type="button" class="icon-option p-2 text-center hover:bg-gray-100 rounded"
                                            data-icon="fas fa-chart-bar" title="Chart">
                                            <i class="fas fa-chart-bar text-lg"></i>
                                        </button>
                                        <button type="button" class="icon-option p-2 text-center hover:bg-gray-100 rounded"
                                            data-icon="fas fa-handshake" title="Partnership">
                                            <i class="fas fa-handshake text-lg"></i>
                                        </button>
                                        <button type="button" class="icon-option p-2 text-center hover:bg-gray-100 rounded"
                                            data-icon="fas fa-trophy" title="Achievement">
                                            <i class="fas fa-trophy text-lg"></i>
                                        </button>
                                        <!-- Education -->
                                        <button type="button" class="icon-option p-2 text-center hover:bg-gray-100 rounded"
                                            data-icon="fas fa-graduation-cap" title="Education">
                                            <i class="fas fa-graduation-cap text-lg"></i>
                                        </button>
                                        <button type="button" class="icon-option p-2 text-center hover:bg-gray-100 rounded"
                                            data-icon="fas fa-university" title="University">
                                            <i class="fas fa-university text-lg"></i>
                                        </button>
                                        <button type="button" class="icon-option p-2 text-center hover:bg-gray-100 rounded"
                                            data-icon="fas fa-chalkboard-teacher" title="Teaching">
                                            <i class="fas fa-chalkboard-teacher text-lg"></i>
                                        </button>
                                        <button type="button" class="icon-option p-2 text-center hover:bg-gray-100 rounded"
                                            data-icon="fas fa-certificate" title="Certificate">
                                            <i class="fas fa-certificate text-lg"></i>
                                        </button>
                                        <!-- Social Media -->
                                        <button type="button" class="icon-option p-2 text-center hover:bg-gray-100 rounded"
                                            data-icon="fab fa-facebook" title="Facebook">
                                            <i class="fab fa-facebook text-lg"></i>
                                        </button>
                                        <button type="button" class="icon-option p-2 text-center hover:bg-gray-100 rounded"
                                            data-icon="fab fa-twitter" title="Twitter">
                                            <i class="fab fa-twitter text-lg"></i>
                                        </button>
                                        <button type="button" class="icon-option p-2 text-center hover:bg-gray-100 rounded"
                                            data-icon="fab fa-instagram" title="Instagram">
                                            <i class="fab fa-instagram text-lg"></i>
                                        </button>
                                        <button type="button" class="icon-option p-2 text-center hover:bg-gray-100 rounded"
                                            data-icon="fab fa-youtube" title="YouTube">
                                            <i class="fab fa-youtube text-lg"></i>
                                        </button>
                                        <button type="button" class="icon-option p-2 text-center hover:bg-gray-100 rounded"
                                            data-icon="fab fa-linkedin" title="LinkedIn">
                                            <i class="fab fa-linkedin text-lg"></i>
                                        </button>
                                        <button type="button" class="icon-option p-2 text-center hover:bg-gray-100 rounded"
                                            data-icon="fab fa-whatsapp" title="WhatsApp">
                                            <i class="fab fa-whatsapp text-lg"></i>
                                        </button>
                                        <!-- Technology -->
                                        <button type="button" class="icon-option p-2 text-center hover:bg-gray-100 rounded"
                                            data-icon="fas fa-laptop" title="Laptop">
                                            <i class="fas fa-laptop text-lg"></i>
                                        </button>
                                        <button type="button" class="icon-option p-2 text-center hover:bg-gray-100 rounded"
                                            data-icon="fas fa-mobile-alt" title="Mobile">
                                            <i class="fas fa-mobile-alt text-lg"></i>
                                        </button>
                                        <button type="button" class="icon-option p-2 text-center hover:bg-gray-100 rounded"
                                            data-icon="fas fa-code" title="Code">
                                            <i class="fas fa-code text-lg"></i>
                                        </button>
                                        <button type="button" class="icon-option p-2 text-center hover:bg-gray-100 rounded"
                                            data-icon="fas fa-database" title="Database">
                                            <i class="fas fa-database text-lg"></i>
                                        </button>
                                        <button type="button" class="icon-option p-2 text-center hover:bg-gray-100 rounded"
                                            data-icon="fas fa-server" title="Server">
                                            <i class="fas fa-server text-lg"></i>
                                        </button>
                                        <button type="button" class="icon-option p-2 text-center hover:bg-gray-100 rounded"
                                            data-icon="fas fa-wifi" title="WiFi">
                                            <i class="fas fa-wifi text-lg"></i>
                                        </button>
                                        <!-- Health & Medical -->
                                        <button type="button" class="icon-option p-2 text-center hover:bg-gray-100 rounded"
                                            data-icon="fas fa-hospital" title="Hospital">
                                            <i class="fas fa-hospital text-lg"></i>
                                        </button>
                                        <button type="button" class="icon-option p-2 text-center hover:bg-gray-100 rounded"
                                            data-icon="fas fa-user-md" title="Doctor">
                                            <i class="fas fa-user-md text-lg"></i>
                                        </button>
                                        <button type="button" class="icon-option p-2 text-center hover:bg-gray-100 rounded"
                                            data-icon="fas fa-stethoscope" title="Medical">
                                            <i class="fas fa-stethoscope text-lg"></i>
                                        </button>
                                        <button type="button" class="icon-option p-2 text-center hover:bg-gray-100 rounded"
                                            data-icon="fas fa-pills" title="Medicine">
                                            <i class="fas fa-pills text-lg"></i>
                                        </button>
                                        <!-- Transportation -->
                                        <button type="button" class="icon-option p-2 text-center hover:bg-gray-100 rounded"
                                            data-icon="fas fa-car" title="Car">
                                            <i class="fas fa-car text-lg"></i>
                                        </button>
                                        <button type="button" class="icon-option p-2 text-center hover:bg-gray-100 rounded"
                                            data-icon="fas fa-plane" title="Plane">
                                            <i class="fas fa-plane text-lg"></i>
                                        </button>
                                        <button type="button" class="icon-option p-2 text-center hover:bg-gray-100 rounded"
                                            data-icon="fas fa-ship" title="Ship">
                                            <i class="fas fa-ship text-lg"></i>
                                        </button>
                                        <button type="button" class="icon-option p-2 text-center hover:bg-gray-100 rounded"
                                            data-icon="fas fa-bus" title="Bus">
                                            <i class="fas fa-bus text-lg"></i>
                                        </button>
                                        <!-- Food & Restaurant -->
                                        <button type="button" class="icon-option p-2 text-center hover:bg-gray-100 rounded"
                                            data-icon="fas fa-utensils" title="Restaurant">
                                            <i class="fas fa-utensils text-lg"></i>
                                        </button>
                                        <button type="button" class="icon-option p-2 text-center hover:bg-gray-100 rounded"
                                            data-icon="fas fa-coffee" title="Coffee">
                                            <i class="fas fa-coffee text-lg"></i>
                                        </button>
                                        <button type="button" class="icon-option p-2 text-center hover:bg-gray-100 rounded"
                                            data-icon="fas fa-pizza-slice" title="Food">
                                            <i class="fas fa-pizza-slice text-lg"></i>
                                        </button>
                                        <button type="button" class="icon-option p-2 text-center hover:bg-gray-100 rounded"
                                            data-icon="fas fa-wine-glass" title="Beverage">
                                            <i class="fas fa-wine-glass text-lg"></i>
                                        </button>
                                        <!-- Sports & Recreation -->
                                        <button type="button" class="icon-option p-2 text-center hover:bg-gray-100 rounded"
                                            data-icon="fas fa-football-ball" title="Sports">
                                            <i class="fas fa-football-ball text-lg"></i>
                                        </button>
                                        <button type="button" class="icon-option p-2 text-center hover:bg-gray-100 rounded"
                                            data-icon="fas fa-dumbbell" title="Fitness">
                                            <i class="fas fa-dumbbell text-lg"></i>
                                        </button>
                                        <button type="button" class="icon-option p-2 text-center hover:bg-gray-100 rounded"
                                            data-icon="fas fa-swimming-pool" title="Swimming">
                                            <i class="fas fa-swimming-pool text-lg"></i>
                                        </button>
                                        <button type="button" class="icon-option p-2 text-center hover:bg-gray-100 rounded"
                                            data-icon="fas fa-gamepad" title="Gaming">
                                            <i class="fas fa-gamepad text-lg"></i>
                                        </button>
                                        <!-- Finance -->
                                        <button type="button" class="icon-option p-2 text-center hover:bg-gray-100 rounded"
                                            data-icon="fas fa-dollar-sign" title="Money">
                                            <i class="fas fa-dollar-sign text-lg"></i>
                                        </button>
                                        <button type="button" class="icon-option p-2 text-center hover:bg-gray-100 rounded"
                                            data-icon="fas fa-credit-card" title="Payment">
                                            <i class="fas fa-credit-card text-lg"></i>
                                        </button>
                                        <button type="button" class="icon-option p-2 text-center hover:bg-gray-100 rounded"
                                            data-icon="fas fa-piggy-bank" title="Savings">
                                            <i class="fas fa-piggy-bank text-lg"></i>
                                        </button>
                                        <button type="button" class="icon-option p-2 text-center hover:bg-gray-100 rounded"
                                            data-icon="fas fa-coins" title="Coins">
                                            <i class="fas fa-coins text-lg"></i>
                                        </button>
                                        <!-- Weather -->
                                        <button type="button" class="icon-option p-2 text-center hover:bg-gray-100 rounded"
                                            data-icon="fas fa-sun" title="Sunny">
                                            <i class="fas fa-sun text-lg"></i>
                                        </button>
                                        <button type="button" class="icon-option p-2 text-center hover:bg-gray-100 rounded"
                                            data-icon="fas fa-cloud" title="Cloudy">
                                            <i class="fas fa-cloud text-lg"></i>
                                        </button>
                                        <button type="button" class="icon-option p-2 text-center hover:bg-gray-100 rounded"
                                            data-icon="fas fa-snowflake" title="Snow">
                                            <i class="fas fa-snowflake text-lg"></i>
                                        </button>
                                        <button type="button" class="icon-option p-2 text-center hover:bg-gray-100 rounded"
                                            data-icon="fas fa-bolt" title="Thunder">
                                            <i class="fas fa-bolt text-lg"></i>
                                        </button>
                                        <!-- Miscellaneous -->
                                        <button type="button" class="icon-option p-2 text-center hover:bg-gray-100 rounded"
                                            data-icon="fas fa-gift" title="Gift">
                                            <i class="fas fa-gift text-lg"></i>
                                        </button>
                                        <button type="button" class="icon-option p-2 text-center hover:bg-gray-100 rounded"
                                            data-icon="fas fa-bell" title="Notification">
                                            <i class="fas fa-bell text-lg"></i>
                                        </button>
                                        <button type="button" class="icon-option p-2 text-center hover:bg-gray-100 rounded"
                                            data-icon="fas fa-lock" title="Security">
                                            <i class="fas fa-lock text-lg"></i>
                                        </button>
                                        <button type="button" class="icon-option p-2 text-center hover:bg-gray-100 rounded"
                                            data-icon="fas fa-key" title="Key">
                                            <i class="fas fa-key text-lg"></i>
                                        </button>
                                        <button type="button" class="icon-option p-2 text-center hover:bg-gray-100 rounded"
                                            data-icon="fas fa-shield-alt" title="Protection">
                                            <i class="fas fa-shield-alt text-lg"></i>
                                        </button>
                                        <button type="button" class="icon-option p-2 text-center hover:bg-gray-100 rounded"
                                            data-icon="fas fa-globe" title="Global">
                                            <i class="fas fa-globe text-lg"></i>
                                        </button>
                                        <button type="button" class="icon-option p-2 text-center hover:bg-gray-100 rounded"
                                            data-icon="fas fa-flag" title="Flag">
                                            <i class="fas fa-flag text-lg"></i>
                                        </button>
                                        <button type="button" class="icon-option p-2 text-center hover:bg-gray-100 rounded"
                                            data-icon="fas fa-clock" title="Time">
                                            <i class="fas fa-clock text-lg"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- CSS Class -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">CSS Class</label>
                            <input type="text" id="menu-css-class" name="css_class"
                                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                        </div>

                        <!-- Active Status -->
                        <div class="flex items-center">
                            <input type="checkbox" id="menu-active" name="is_active"
                                class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded" checked>
                            <label for="menu-active" class="ml-2 block text-sm text-gray-900">Aktif</label>
                        </div>

                        <!-- Buttons -->
                        <div class="flex justify-end space-x-3 pt-4">
                            <button type="button" onclick="closeModal()"
                                class="px-4 py-2 text-gray-700 bg-gray-200 rounded-md hover:bg-gray-300">
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
        document.addEventListener('DOMContentLoaded', function () {
            initializeSortable();
            setupEventListeners();
        });

        function initializeSortable() {
            const menuContainers = document.querySelectorAll('.sortable-root');
            menuContainers.forEach(container => {
                new Sortable(container, {
                    handle: '.drag-handle',
                    animation: 150,
                    ghostClass: 'sortable-ghost',
                    chosenClass: 'sortable-chosen',
                    group: 'nested',
                    fallbackOnBody: true,
                    swapThreshold: 0.65,
                    onEnd: function (evt) {
                        updateMenuOrder();
                    }
                });
            });

            // Initialize sortable for nested menus
            initializeNestedSortable();
        }

        function initializeNestedSortable() {
            const nestedContainers = document.querySelectorAll('.nested-menu');
            nestedContainers.forEach(container => {
                new Sortable(container, {
                    handle: '.drag-handle',
                    animation: 150,
                    ghostClass: 'sortable-ghost',
                    chosenClass: 'sortable-chosen',
                    group: 'nested',
                    fallbackOnBody: true,
                    swapThreshold: 0.65,
                    onEnd: function (evt) {
                        updateMenuOrder();
                    }
                });
            });
        }

        function refreshSortable() {
            // Re-initialize sortable
            initializeSortable();
            // Re-initialize nested sortables
            initializeNestedSortable();
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
            document.getElementById('menu-parent').value = ''; // Reset parent selection
            updateIconPreview(''); // Reset icon preview

            // Set position based on active tab
            const activeTab = document.getElementById('active-tab-input').value || 'top';
            document.getElementById('menu-position').value = activeTab;
            filterParentOptions();

            handleTypeChange();
            document.getElementById('menu-modal').classList.remove('hidden');
        }

        function openAddSubMenuModal(parentId, parentTitle) {
            isEditing = false;
            currentMenuId = null;
            document.getElementById('modal-title').textContent = `Tambah Sub Menu - ${parentTitle}`;
            document.getElementById('submit-text').textContent = 'Simpan';
            document.getElementById('menu-form').reset();
            document.getElementById('menu-active').checked = true;

            // Find parent element to get its position
            const parentItem = document.querySelector(`[data-menu-id="${parentId}"]`);
            if (parentItem) {
                // Traverse up to find .menu-container-tab
                const container = parentItem.closest('.menu-container-tab');
                if (container) {
                    const position = container.dataset.position;
                    document.getElementById('menu-position').value = position;
                }
            }
            filterParentOptions();
            document.getElementById('menu-parent').value = parentId; // Set parent

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
                document.getElementById('menu-position').value = menuItem.dataset.position || 'top'; // Set position
                filterParentOptions(); // Filter parents

                document.getElementById('menu-url').value = menuItem.dataset.url || '';
                document.getElementById('menu-target').value = menuItem.dataset.target || '_self';
                document.getElementById('menu-parent').value = menuItem.dataset.parentId || '';
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
            const urlInput = document.getElementById('menu-url');
            const referenceField = document.getElementById('reference-field');
            const referenceSelect = document.getElementById('menu-reference');

            if (type === 'custom') {
                customField.classList.remove('hidden');
                urlInput.disabled = false; // Enable input for custom links
                urlInput.required = true; // Make it required
                referenceField.classList.add('hidden');
            } else {
                customField.classList.add('hidden');
                urlInput.disabled = true; // Disable input for other types to prevent validation error
                urlInput.required = false;
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
                        location.reload(); // Reload to show updated menu and refresh sortable
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
            const menus = [];

            // Process root level menus from ALL containers
            const rootMenus = document.querySelectorAll('.sortable-root > .menu-item');
            rootMenus.forEach((item, index) => {
                // Calculate index relative to its container to reset order for different lists
                // Actually, we can just trust the sort_order to be sequential global or per parent.
                // But since we split lists, let's just use index.
                const menuData = {
                    id: parseInt(item.dataset.menuId),
                    parent_id: null,
                    sort_order: index + 1
                };
                menus.push(menuData);

                // Process children of this menu
                const nestedContainer = item.querySelector('.nested-menu');
                if (nestedContainer) {
                    const childMenus = nestedContainer.querySelectorAll('.menu-item');
                    childMenus.forEach((childItem, childIndex) => {
                        const childData = {
                            id: parseInt(childItem.dataset.menuId),
                            parent_id: parseInt(item.dataset.menuId),
                            sort_order: childIndex + 1
                        };
                        menus.push(childData);
                    });
                }
            });

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
            // Toggle icon picker modal
            iconPickerBtn.addEventListener('click', function (e) {
                e.preventDefault();
                iconPickerModal.classList.toggle('hidden');
                if (!iconPickerModal.classList.contains('hidden')) {
                    iconSearch.focus();
                }
            });

            // Handle icon selection
            iconOptions.forEach(option => {
                option.addEventListener('click', function (e) {
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
            iconSearch.addEventListener('input', function () {
                const searchTerm = this.value.toLowerCase();
                filterIcons(searchTerm);
            });

            // Update icon preview when typing
            iconInput.addEventListener('input', function () {
                updateIconPreview(this.value);
            });

            // Close icon picker when clicking outside
            document.addEventListener('click', function (e) {
                if (!iconPickerBtn.contains(e.target) && !iconPickerModal.contains(e.target)) {
                    iconPickerModal.classList.add('hidden');
                    iconSearch.value = ''; // Reset search
                    filterIcons(''); // Show all icons
                }
            });
        }


        function filterParentOptions() {
            const position = document.getElementById('menu-position').value;
            const parentSelect = document.getElementById('menu-parent');
            const options = parentSelect.querySelectorAll('option');

            // Reset selection if hidden
            // Use simpler logic: if current value is hidden, reset it.
            // But for now just filter.

            options.forEach(option => {
                if (!option.value) return; // Skip label

                const optionPosition = option.dataset.position;
                if (optionPosition === position) {
                    option.style.display = 'block';
                } else {
                    option.style.display = 'none';
                }
            });

            // If selected option is now hidden, reset to empty
            const selected = parentSelect.options[parentSelect.selectedIndex];
            if (selected && selected.value && selected.style.display === 'none') {
                parentSelect.value = '';
            }
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