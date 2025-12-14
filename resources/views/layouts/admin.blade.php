<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', 'Admin Panel') - {{ config('app.name', 'Laravel') }}</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700&display=swap" rel="stylesheet" />

    <!-- Font Awesome -->
    <link rel="stylesheet" href="{{ asset('fontawesome-free/css/all.min.css') }}">

    <!-- Styles -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @stack('styles')

    <style>
        .sidebar {
            transition: transform 0.3s ease-in-out;
        }

        /* Mobile: sidebar hidden by default */
        @media (max-width: 767px) {
            .sidebar {
                transform: translateX(-100%);
            }

            .sidebar.sidebar-open {
                transform: translateX(0);
            }
        }

        /* Desktop: sidebar visible by default */
        @media (min-width: 768px) {
            .sidebar {
                transform: translateX(0);
                transition: width 0.3s ease-in-out;
            }

            .sidebar.sidebar-collapsed {
                width: 5rem;
            }

            .sidebar.sidebar-collapsed .sidebar-text {
                display: none;
            }

            .sidebar.sidebar-collapsed a {
                justify-content: center;
                padding-left: 0;
                padding-right: 0;
            }

            .sidebar.sidebar-collapsed .sidebar-icon {
                margin: 0;
                font-size: 1.25rem;
            }

            .sidebar.sidebar-collapsed .sidebar-header {
                justify-content: center;
                padding: 0;
            }
        }

        .main-content {
            transition: margin-left 0.3s ease-in-out;
        }

        .main-content-expanded {
            margin-left: 5rem;
        }
    </style>
</head>

<body class="font-sans antialiased bg-gray-100">
    <div class="flex h-screen">
        <!-- Sidebar -->
        <div id="sidebar"
            class="sidebar fixed inset-y-0 left-0 z-50 w-64 bg-gray-900 text-white transform md:relative md:translate-x-0 flex flex-col">
            <!-- Sidebar Header -->
            <div class="sidebar-header flex items-center justify-between h-16 px-4 bg-gray-800 flex-shrink-0">
                <div class="flex items-center overflow-hidden whitespace-nowrap">
                    <i class="fas fa-cog text-2xl text-blue-400 flex-shrink-0"></i>
                    <span class="sidebar-text ml-2 text-xl font-bold">Admin Panel</span>
                </div>
                <button id="sidebar-toggle" class="md:hidden text-gray-400 hover:text-white">
                    <i class="fas fa-times"></i>
                </button>
            </div>

            <!-- Navigation -->
            <nav class="mt-8 flex-1 overflow-y-auto custom-scrollbar">
                <div class="px-2 space-y-2">
                    <!-- Dashboard -->
                    <a href="{{ route('admin.dashboard') }}"
                        class="flex items-center px-4 py-3 text-sm rounded-lg hover:bg-gray-700 transition-colors duration-200 {{ request()->routeIs('admin.dashboard') ? 'bg-gray-700 text-blue-400' : '' }}"
                        title="Dashboard">
                        <i class="sidebar-icon fas fa-tachometer-alt w-5 text-center"></i>
                        <span class="sidebar-text ml-3 font-medium">Dashboard</span>
                    </a>

                    <!-- Content Management -->
                    <div class="space-y-1">
                        <!-- Posts (Berita) -->
                        <a href="{{ route('admin.posts.index', ['type' => 'berita']) }}"
                            class="flex items-center px-4 py-3 text-sm rounded-lg hover:bg-gray-700 transition-colors duration-200 {{ request()->routeIs('admin.posts.*') && request()->get('type') == 'berita' ? 'bg-gray-700 text-blue-400' : '' }}"
                            title="Berita">
                            <i class="sidebar-icon fas fa-newspaper w-5 text-center"></i>
                            <span class="sidebar-text ml-3 font-medium">Berita</span>
                        </a>

                        <!-- Partners (Kerja Sama) -->
                        <a href="{{ route('admin.posts.index', ['type' => 'partner']) }}"
                            class="flex items-center px-4 py-3 text-sm rounded-lg hover:bg-gray-700 transition-colors duration-200 {{ request()->routeIs('admin.posts.*') && request()->get('type') == 'partner' ? 'bg-gray-700 text-blue-400' : '' }}"
                            title="Kerja Sama">
                            <i class="sidebar-icon fas fa-handshake w-5 text-center"></i>
                            <span class="sidebar-text ml-3 font-medium">Kerja Sama</span>
                        </a>

                        <!-- Pages (Halaman) -->
                        <a href="{{ route('admin.posts.index', ['type' => 'page']) }}"
                            class="flex items-center px-4 py-3 text-sm rounded-lg hover:bg-gray-700 transition-colors duration-200 {{ request()->routeIs('admin.posts.*') && request()->get('type') == 'page' ? 'bg-gray-700 text-blue-400' : '' }}"
                            title="Halaman">
                            <i class="sidebar-icon fas fa-file-alt w-5 text-center"></i>
                            <span class="sidebar-text ml-3 font-medium">Halaman</span>
                        </a>

                        <!-- Gallery -->
                        <a href="{{ route('admin.posts.index', ['type' => 'gallery']) }}"
                            class="flex items-center px-4 py-3 text-sm rounded-lg hover:bg-gray-700 transition-colors duration-200 {{ request()->routeIs('admin.posts.*') && request()->get('type') == 'gallery' ? 'bg-gray-700 text-blue-400' : '' }}"
                            title="Gallery">
                            <i class="sidebar-icon fas fa-images w-5 text-center"></i>
                            <span class="sidebar-text ml-3 font-medium">Gallery</span>
                        </a>

                        <!-- Video -->
                        <a href="{{ route('admin.posts.index', ['type' => 'video']) }}"
                            class="flex items-center px-4 py-3 text-sm rounded-lg hover:bg-gray-700 transition-colors duration-200 {{ request()->routeIs('admin.posts.*') && request()->get('type') == 'video' ? 'bg-gray-700 text-blue-400' : '' }}"
                            title="Video">
                            <i class="sidebar-icon fas fa-video w-5 text-center"></i>
                            <span class="sidebar-text ml-3 font-medium">Video</span>
                        </a>
                    </div>

                    <!-- Categories -->
                    <a href="{{ route('admin.categories.index') }}"
                        class="flex items-center px-4 py-3 text-sm rounded-lg hover:bg-gray-700 transition-colors duration-200 {{ request()->routeIs('admin.categories.*') ? 'bg-gray-700 text-blue-400' : '' }}"
                        title="Categories">
                        <i class="sidebar-icon fas fa-tags w-5 text-center"></i>
                        <span class="sidebar-text ml-3 font-medium">Categories</span>
                    </a>

                    <!-- Navigation -->
                    <a href="{{ route('admin.navigation.index') }}"
                        class="flex items-center px-4 py-3 text-sm rounded-lg hover:bg-gray-700 transition-colors duration-200 {{ request()->routeIs('admin.navigation.*') ? 'bg-gray-700 text-blue-400' : '' }}"
                        title="Navigation">
                        <i class="sidebar-icon fas fa-bars w-5 text-center"></i>
                        <span class="sidebar-text ml-3 font-medium">Navigation</span>
                    </a>

                    <!-- Media -->
                    <a href="{{ route('admin.media.index') }}"
                        class="flex items-center px-4 py-3 text-sm rounded-lg hover:bg-gray-700 transition-colors duration-200 {{ request()->routeIs('admin.media.*') ? 'bg-gray-700 text-blue-400' : '' }}"
                        title="Media">
                        <i class="sidebar-icon fas fa-images w-5 text-center"></i>
                        <span class="sidebar-text ml-3 font-medium">Media</span>
                    </a>

                    <!-- Downloads -->
                    <a href="{{ route('admin.downloads.index') }}"
                        class="flex items-center px-4 py-3 text-sm rounded-lg hover:bg-gray-700 transition-colors duration-200 {{ request()->routeIs('admin.downloads.*') ? 'bg-gray-700 text-blue-400' : '' }}"
                        title="Downloads">
                        <i class="sidebar-icon fas fa-download w-5 text-center"></i>
                        <span class="sidebar-text ml-3 font-medium">Downloads</span>
                    </a>

                    <!-- Users -->
                    <a href="{{ route('admin.users.index') }}"
                        class="flex items-center px-4 py-3 text-sm rounded-lg hover:bg-gray-700 transition-colors duration-200 {{ request()->routeIs('admin.users.*') ? 'bg-gray-700 text-blue-400' : '' }}"
                        title="Users">
                        <i class="sidebar-icon fas fa-users w-5 text-center"></i>
                        <span class="sidebar-text ml-3 font-medium">Users</span>
                    </a>

                    <!-- Settings -->
                    <a href="{{ route('admin.settings.index') }}"
                        class="flex items-center px-4 py-3 text-sm rounded-lg hover:bg-gray-700 transition-colors duration-200 {{ request()->routeIs('admin.settings.*') ? 'bg-gray-700 text-blue-400' : '' }}"
                        title="Settings">
                        <i class="sidebar-icon fas fa-cog w-5 text-center"></i>
                        <span class="sidebar-text ml-3 font-medium">Settings</span>
                    </a>
                </div>

                <!-- User Menu -->
                <div class="mt-auto pt-4 pb-6 border-t border-gray-700">
                    <div class="px-2 space-y-2">
                        <a href="{{ route('admin.profile') }}"
                            class="flex items-center px-4 py-3 text-sm rounded-lg hover:bg-gray-700 transition-colors duration-200 {{ request()->routeIs('admin.profile') ? 'bg-gray-700 text-blue-400' : '' }}"
                            title="Profile">
                            <i class="sidebar-icon fas fa-user w-5 text-center"></i>
                            <span class="sidebar-text ml-3 font-medium">Profile</span>
                        </a>

                        <a href="{{ route('frontend.index') }}"
                            class="flex items-center px-4 py-3 text-sm rounded-lg hover:bg-gray-700 transition-colors duration-200"
                            target="_blank" title="View Site">
                            <i class="sidebar-icon fas fa-external-link-alt w-5 text-center"></i>
                            <span class="sidebar-text ml-3 font-medium">View Site</span>
                        </a>

                        <form method="POST" action="{{ route('logout') }}" class="inline-block w-full">
                            @csrf
                            <button type="submit"
                                class="flex items-center w-full px-4 py-3 text-sm rounded-lg hover:bg-gray-700 text-left transition-colors duration-200"
                                title="Logout">
                                <i class="sidebar-icon fas fa-sign-out-alt w-5 text-center"></i>
                                <span class="sidebar-text ml-3 font-medium">Logout</span>
                            </button>
                        </form>
                    </div>
                </div>
            </nav>
        </div>

        <!-- Main Content -->
        <div id="main-content" class="main-content flex-1 flex flex-col overflow-hidden">
            <!-- Top Bar -->
            <header class="bg-white shadow-sm border-b border-gray-200">
                <div class="flex items-center justify-between h-16 px-4">
                    <div class="flex items-center">
                        <button id="sidebar-toggle-desktop"
                            class="text-gray-500 hover:text-gray-700 mr-4 focus:outline-none">
                            <i class="fas fa-bars text-lg"></i>
                        </button>
                        <h1 class="ml-4 text-xl font-semibold text-gray-800">@yield('page-title', 'Dashboard')</h1>
                    </div>

                    <div class="flex items-center space-x-4">
                        <!-- Notifications -->
                        <button class="text-gray-500 hover:text-gray-700 focus:outline-none">
                            <i class="fas fa-bell"></i>
                        </button>

                        <!-- User Menu -->
                        <div class="relative" x-data="{ open: false }" @click.away="open = false">
                            <button @click="open = !open"
                                class="flex items-center text-sm text-gray-700 hover:text-gray-900 focus:outline-none">
                                <img class="w-8 h-8 rounded-full border border-gray-200"
                                    src="{{ auth()->user()->profile_photo ? asset('storage/' . auth()->user()->profile_photo) : 'https://ui-avatars.com/api/?name=' . urlencode(auth()->user()->name) . '&background=3b82f6&color=fff' }}"
                                    alt="{{ auth()->user()->name }}">
                                <span class="ml-2 hidden md:block font-medium">{{ auth()->user()->name }}</span>
                                <i class="fas fa-chevron-down ml-1 text-xs transition-transform duration-200"
                                    :class="{ 'transform rotate-180': open }"></i>
                            </button>

                            <!-- Dropdown -->
                            <div x-show="open" x-transition:enter="transition ease-out duration-100"
                                x-transition:enter-start="transform opacity-0 scale-95"
                                x-transition:enter-end="transform opacity-100 scale-100"
                                x-transition:leave="transition ease-in duration-75"
                                x-transition:leave-start="transform opacity-100 scale-100"
                                x-transition:leave-end="transform opacity-0 scale-95"
                                class="absolute right-0 mt-2 w-48 bg-white rounded-md shadow-lg py-1 z-50 ring-1 ring-black ring-opacity-5"
                                style="display: none;">

                                <a href="{{ route('admin.profile') }}"
                                    class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                    <i class="fas fa-user-circle mr-2 w-4 text-gray-500"></i> Edit Profile
                                </a>

                                <a href="{{ route('frontend.index') }}" target="_blank"
                                    class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                    <i class="fas fa-external-link-alt mr-2 w-4 text-gray-500"></i> View Site
                                </a>

                                <div class="border-t border-gray-100 my-1"></div>

                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <button type="submit"
                                        class="block w-full text-left px-4 py-2 text-sm text-red-600 hover:bg-red-50">
                                        <i class="fas fa-sign-out-alt mr-2 w-4"></i> Logout
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </header>

            <!-- Page Content -->
            <main class="flex-1 overflow-x-hidden overflow-y-auto bg-gray-100">
                <div class="container mx-auto px-1 py-8">
                    <!-- Flash Messages -->
                    @if(session('success'))
                        <div class="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative"
                            role="alert">
                            <span class="block sm:inline">{{ session('success') }}</span>
                            <span class="absolute top-0 bottom-0 right-0 px-4 py-3">
                                <i class="fas fa-times cursor-pointer"
                                    onclick="this.parentElement.parentElement.style.display='none'"></i>
                            </span>
                        </div>
                    @endif

                    @if(session('error'))
                        <div class="mb-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative"
                            role="alert">
                            <span class="block sm:inline">{{ session('error') }}</span>
                            <span class="absolute top-0 bottom-0 right-0 px-4 py-3">
                                <i class="fas fa-times cursor-pointer"
                                    onclick="this.parentElement.parentElement.style.display='none'"></i>
                            </span>
                        </div>
                    @endif

                    @yield('content')
                </div>
            </main>
        </div>
    </div>

    <!-- Sidebar Overlay for Mobile -->
    <div id="sidebar-overlay" class="fixed inset-0 z-40 bg-black bg-opacity-50 hidden md:hidden"></div>

    @stack('scripts')

    <!-- Alpine.js -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

    <script>
        // Sidebar Toggle Functionality
        const sidebar = document.getElementById('sidebar');
        const sidebarOverlay = document.getElementById('sidebar-overlay');
        const sidebarToggle = document.getElementById('sidebar-toggle');
        const sidebarToggleDesktop = document.getElementById('sidebar-toggle-desktop');
        const mainContent = document.getElementById('main-content');

        // Define localStorage key
        const SIDEBAR_STATE_KEY = 'sidebar-collapsed';

        function toggleSidebar() {
            if (window.innerWidth < 768) {
                // Mobile behavior: toggle sidebar visibility
                sidebar.classList.toggle('sidebar-open');
                sidebarOverlay.classList.toggle('hidden');
            } else {
                // Desktop behavior: toggle sidebar collapse
                sidebar.classList.toggle('sidebar-collapsed');

                // Save state
                const isCollapsed = sidebar.classList.contains('sidebar-collapsed');
                localStorage.setItem(SIDEBAR_STATE_KEY, isCollapsed);
            }
        }

        function closeMobileSidebar() {
            if (window.innerWidth < 768) {
                sidebar.classList.remove('sidebar-open');
                sidebarOverlay.classList.add('hidden');
            }
        }

        sidebarToggle?.addEventListener('click', toggleSidebar);
        sidebarToggleDesktop?.addEventListener('click', toggleSidebar);
        sidebarOverlay?.addEventListener('click', closeMobileSidebar);

        // Close sidebar on window resize and ensure proper state
        window.addEventListener('resize', function () {
            if (window.innerWidth >= 768) {
                // Desktop: hide overlay and remove mobile classes
                sidebarOverlay.classList.add('hidden');
                sidebar.classList.remove('sidebar-open');

                // Restore logic relies on localStorage, but we respect current manual toggle for now unless reload
            } else {
                // Mobile: ensure sidebar is hidden and remove desktop classes
                sidebar.classList.remove('sidebar-collapsed');
                mainContent.classList.remove('main-content-expanded');
            }
        });

        // Initialize proper state on page load
        document.addEventListener('DOMContentLoaded', function () {
            if (window.innerWidth < 768) {
                sidebar.classList.remove('sidebar-open');
                sidebarOverlay.classList.add('hidden');
            } else {
                // Check local storage for desktop
                const savedState = localStorage.getItem(SIDEBAR_STATE_KEY);
                if (savedState === 'true') {
                    sidebar.classList.add('sidebar-collapsed');
                }
            }
        });
    </script>
</body>

</html>