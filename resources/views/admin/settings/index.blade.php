@extends('layouts.admin')

@section('title', 'Site Settings')
@section('page-title', 'Settings')

@push('styles')
    <style>
        .settings-nav {
            border-right: 1px solid #e5e7eb;
        }

        .settings-nav .nav-item {
            display: block;
            padding: 0.75rem 1rem;
            color: #6b7280;
            text-decoration: none;
            border-radius: 0.375rem;
            margin-bottom: 0.25rem;
            transition: all 0.2s;
        }

        .settings-nav .nav-item:hover {
            background-color: #f3f4f6;
            color: #374151;
        }

        .settings-nav .nav-item.active {
            background-color: #3b82f6;
            color: white;
        }

        .settings-section {
            display: none;
        }

        .settings-section.active {
            display: block;
        }

        .logo-preview {
            max-width: 200px;
            max-height: 100px;
            object-fit: contain;
        }
    </style>
@endpush

@section('content')
    <div class="bg-white rounded-lg shadow">
        <div class="grid grid-cols-1 lg:grid-cols-4">
            <!-- Settings Navigation -->
            <div class="lg:col-span-1 settings-nav p-6">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Settings</h3>
                <nav class="space-y-1">
                    <a href="#general" class="nav-item active" onclick="showSection('general', this)">
                        <i class="fas fa-cog mr-2"></i>
                        General
                    </a>
                    <a href="#welcome" class="nav-item" onclick="showSection('welcome', this)">
                        <i class="fas fa-home mr-2"></i>
                        Welcome Section
                    </a>
                    <a href="#appearance" class="nav-item" onclick="showSection('appearance', this)">
                        <i class="fas fa-palette mr-2"></i>
                        Appearance
                    </a>
                    <a href="#seo" class="nav-item" onclick="showSection('seo', this)">
                        <i class="fas fa-search mr-2"></i>
                        SEO
                    </a>
                    <a href="#social" class="nav-item" onclick="showSection('social', this)">
                        <i class="fas fa-share-alt mr-2"></i>
                        Social Media
                    </a>
                    <a href="#email" class="nav-item" onclick="showSection('email', this)">
                        <i class="fas fa-envelope mr-2"></i>
                        Email
                    </a>
                    <a href="#security" class="nav-item" onclick="showSection('security', this)">
                        <i class="fas fa-shield-alt mr-2"></i>
                        Security
                    </a>
                    <a href="#advanced" class="nav-item" onclick="showSection('advanced', this)">
                        <i class="fas fa-cogs mr-2"></i>
                        Advanced
                    </a>
                </nav>

                <div class="mt-8 pt-6 border-t border-gray-200">
                    <h4 class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-3">System</h4>
                    <form action="{{ route('admin.settings.clear.cache') }}" method="POST"
                        onsubmit="return confirm('Are you sure you want to clear the system cache? This might slow down the site temporarily.');">
                        @csrf
                        <button type="submit"
                            class="w-full flex items-center px-3 py-2 text-sm font-medium text-red-700 bg-red-50 hover:bg-red-100 rounded-md transition-colors group">
                            <i class="fas fa-trash-alt w-5 mr-2 group-hover:text-red-800"></i>
                            Clear System Cache
                        </button>
                    </form>
                </div>
            </div>

            <!-- Settings Content -->
            <div class="lg:col-span-3 p-6">
                <form method="POST" action="{{ route('admin.settings.update') }}" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')
                    <!-- General Settings -->
                    <input type="hidden" name="_active_tab" id="active_tab_input" value="general">
                    <div id="general-section" class="settings-section active">
                        <h2 class="text-xl font-semibold text-gray-900 mb-6">General Settings</h2>

                        <div class="space-y-6">
                            <!-- Site Name -->
                            <div>
                                <label for="site_name" class="block text-sm font-medium text-gray-700 mb-2">Site Name
                                    *</label>
                                <input type="text" id="site_name" name="site_name"
                                    value="{{ $settings['site_name'] ?? '' }}" required
                                    class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                            </div>

                            <!-- Site Description -->
                            <div>
                                <label for="site_description" class="block text-sm font-medium text-gray-700 mb-2">Site
                                    Description</label>
                                <textarea id="site_description" name="site_description" rows="3"
                                    class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-transparent">{{ $settings['site_description'] ?? '' }}</textarea>
                            </div>

                            <!-- Site URL -->
                            <div>
                                <label for="site_url" class="block text-sm font-medium text-gray-700 mb-2">Site URL</label>
                                <input type="url" id="site_url" name="site_url" value="{{ $settings['site_url'] ?? '' }}"
                                    class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                            </div>

                            <!-- Contact Email -->
                            <div>
                                <label for="contact_email" class="block text-sm font-medium text-gray-700 mb-2">Contact
                                    Email</label>
                                <input type="email" id="contact_email" name="contact_email"
                                    value="{{ $settings['contact_email'] ?? '' }}"
                                    class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                            </div>

                            <!-- Timezone -->
                            <div>
                                <label for="timezone" class="block text-sm font-medium text-gray-700 mb-2">Timezone</label>
                                <select id="timezone" name="timezone"
                                    class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                    <option value="Asia/Jakarta" {{ ($settings['timezone'] ?? '') === 'Asia/Jakarta' ? 'selected' : '' }}>Asia/Jakarta</option>
                                    <option value="UTC" {{ ($settings['timezone'] ?? '') === 'UTC' ? 'selected' : '' }}>UTC
                                    </option>
                                    <option value="America/New_York" {{ ($settings['timezone'] ?? '') === 'America/New_York' ? 'selected' : '' }}>America/New_York</option>
                                    <option value="Europe/London" {{ ($settings['timezone'] ?? '') === 'Europe/London' ? 'selected' : '' }}>Europe/London</option>
                                </select>
                            </div>

                            <!-- Language -->
                            <div>
                                <label for="language" class="block text-sm font-medium text-gray-700 mb-2">Language</label>
                                <select id="language" name="language"
                                    class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                    <option value="en" {{ ($settings['language'] ?? '') === 'en' ? 'selected' : '' }}>English
                                    </option>
                                    <option value="id" {{ ($settings['language'] ?? '') === 'id' ? 'selected' : '' }}>Bahasa
                                        Indonesia</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <!-- Welcome Settings -->
                    <div id="welcome-section" class="settings-section">
                        <h2 class="text-xl font-semibold text-gray-900 mb-6">Welcome Section Settings</h2>

                        <div class="space-y-6">
                            <!-- Enable Welcome Section -->
                            <div class="bg-blue-50 p-4 rounded-lg border border-blue-100">
                                <div class="flex items-start">
                                    <div class="flex h-5 items-center">
                                        <input type="hidden" name="enable_welcome_section" value="0">
                                        <input id="enable_welcome_section" name="enable_welcome_section" type="checkbox"
                                            value="1"
                                            class="h-4 w-4 rounded border-gray-300 text-blue-600 focus:ring-blue-500" {{ ($settings['enable_welcome_section'] ?? '1') == '1' ? 'checked' : '' }}>
                                    </div>
                                    <div class="ml-3 text-sm">
                                        <label for="enable_welcome_section" class="font-medium text-gray-900">Enable Welcome
                                            Section</label>
                                        <p class="text-gray-500">Show or hide the Chairman's welcome section on the
                                            homepage.</p>
                                    </div>
                                </div>
                            </div>

                            <!-- Welcome Label -->
                            <div>
                                <label for="welcome_label" class="block text-sm font-medium text-gray-700 mb-2">Welcome
                                    Label</label>
                                <input type="text" id="welcome_label" name="welcome_label"
                                    value="{{ $settings['welcome_label'] ?? '' }}"
                                    class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                <p class="text-xs text-gray-500 mt-1">Example: SAMBUTAN</p>
                            </div>

                            <!-- Leader Title -->
                            <div>
                                <label for="leader_title" class="block text-sm font-medium text-gray-700 mb-2">Leader Title
                                    / Position</label>
                                <input type="text" id="leader_title" name="leader_title"
                                    value="{{ $settings['leader_title'] ?? '' }}"
                                    class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                <p class="text-xs text-gray-500 mt-1">Example: KETUA</p>
                            </div>

                            <!-- Leader Name -->
                            <div>
                                <label for="leader_name" class="block text-sm font-medium text-gray-700 mb-2">Leader
                                    Name</label>
                                <input type="text" id="leader_name" name="leader_name"
                                    value="{{ $settings['leader_name'] ?? '' }}"
                                    class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                            </div>

                            <!-- Leader Photo -->
                            <div>
                                <label for="leader_photo" class="block text-sm font-medium text-gray-700 mb-2">Leader
                                    Photo</label>
                                @if(isset($settings['leader_photo']) && $settings['leader_photo'])
                                    <div class="mb-3">
                                        <img src="{{ \Illuminate\Support\Str::startsWith($settings['leader_photo'], 'http') ? $settings['leader_photo'] : \Illuminate\Support\Facades\Storage::disk('public')->url($settings['leader_photo']) }}"
                                            alt="Leader Photo" class="logo-preview border border-gray-300 rounded"
                                            style="height: 100px;">
                                    </div>
                                @endif
                                <input type="file" id="leader_photo" name="leader_photo" accept="image/*"
                                    class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                <p class="text-xs text-gray-500 mt-1">Recommended size: 400x500px, PNG or JPG format</p>
                            </div>

                            <!-- Welcome Text -->
                            <div>
                                <label for="welcome_text" class="block text-sm font-medium text-gray-700 mb-2">Welcome
                                    Text</label>
                                <textarea id="welcome_text" name="welcome_text" rows="5"
                                    class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-transparent">{{ $settings['welcome_text'] ?? '' }}</textarea>
                            </div>
                        </div>

                        <hr class="my-8 border-gray-200">

                        <h3 class="text-lg font-medium text-gray-900 mb-4">Homepage Slider Welcome Item</h3>
                        <p class="text-sm text-gray-500 mb-6">Configure a custom "Welcome" slide to appear as the first item
                            in the homepage main slider.</p>

                        <div class="space-y-6">
                            <!-- Enable Welcome Slider -->
                            <div class="bg-blue-50 p-4 rounded-lg border border-blue-100">
                                <div class="flex items-start">
                                    <div class="flex h-5 items-center">
                                        <input type="hidden" name="welcome_slider_enabled" value="0">
                                        <input id="welcome_slider_enabled" name="welcome_slider_enabled" type="checkbox"
                                            value="1"
                                            class="h-4 w-4 rounded border-gray-300 text-blue-600 focus:ring-blue-500" {{ ($settings['welcome_slider_enabled'] ?? '0') == '1' ? 'checked' : '' }}>
                                    </div>
                                    <div class="ml-3 text-sm">
                                        <label for="welcome_slider_enabled" class="font-medium text-gray-900">Enable Welcome
                                            Slide</label>
                                        <p class="text-gray-500">If enabled, this will be the first slide shown on the
                                            homepage.</p>
                                    </div>
                                </div>
                            </div>

                            <!-- Slider Title -->
                            <div>
                                <label for="welcome_slider_title"
                                    class="block text-sm font-medium text-gray-700 mb-2">Slider Title</label>
                                <input type="text" id="welcome_slider_title" name="welcome_slider_title"
                                    value="{{ $settings['welcome_slider_title'] ?? 'Welcome to Our University' }}"
                                    class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                            </div>

                            <!-- Slider Subtitle -->
                            <div>
                                <label for="welcome_slider_subtitle"
                                    class="block text-sm font-medium text-gray-700 mb-2">Slider Subtitle/Description</label>
                                <textarea id="welcome_slider_subtitle" name="welcome_slider_subtitle" rows="3"
                                    class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-transparent">{{ $settings['welcome_slider_subtitle'] ?? '' }}</textarea>
                            </div>

                            <!-- Slider Background -->
                            <div>
                                <label for="welcome_slider_background"
                                    class="block text-sm font-medium text-gray-700 mb-2">Slider Background Image</label>
                                @if(isset($settings['welcome_slider_background']) && $settings['welcome_slider_background'])
                                    <div class="mb-3">
                                        <img src="{{ \Illuminate\Support\Str::startsWith($settings['welcome_slider_background'], 'http') ? $settings['welcome_slider_background'] : \Illuminate\Support\Facades\Storage::disk('public')->url($settings['welcome_slider_background']) }}"
                                            alt="Slider Background" class="logo-preview border border-gray-300 rounded"
                                            style="max-height: 200px;">
                                    </div>
                                @endif
                                <input type="file" id="welcome_slider_background" name="welcome_slider_background"
                                    accept="image/*"
                                    class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                <p class="text-xs text-gray-500 mt-1">Recommended size: 1920x800px, PNG or JPG format</p>
                            </div>

                            <!-- Slider Button Text -->
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div>
                                    <label for="welcome_slider_button_text"
                                        class="block text-sm font-medium text-gray-700 mb-2">Button Text</label>
                                    <input type="text" id="welcome_slider_button_text" name="welcome_slider_button_text"
                                        value="{{ $settings['welcome_slider_button_text'] ?? 'Learn More' }}"
                                        class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                </div>
                                <div>
                                    <label for="welcome_slider_button_link"
                                        class="block text-sm font-medium text-gray-700 mb-2">Button Link</label>
                                    <input type="text" id="welcome_slider_button_link" name="welcome_slider_button_link"
                                        value="{{ $settings['welcome_slider_button_link'] ?? '#' }}"
                                        class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                </div>
                            </div>
                        </div>
                        </div>


                        <!-- Appearance Settings -->
                        <div id="appearance-section" class="settings-section">
                            <h2 class="text-xl font-semibold text-gray-900 mb-6">Appearance Settings</h2>

                            <div class="space-y-6">
                                <!-- Template Selection -->
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Active Template</label>
                                    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-6">
                                        <!-- Default Template -->
                                        <div class="relative group cursor-pointer"
                                            onclick="document.getElementById('template_default').click()">
                                            <div class="absolute top-3 right-3 z-10">
                                                <input type="radio" name="active_template" id="template_default"
                                                    value="default"
                                                    class="w-5 h-5 text-blue-600 border-gray-300 focus:ring-blue-500" {{ ($settings['active_template'] ?? 'default') === 'default' ? 'checked' : '' }}>
                                            </div>
                                            <label for="template_default"
                                                class="block border-4 rounded-xl p-2 group-hover:border-blue-300 peer-checked:border-blue-600 transition-all border-gray-200">
                                                <div
                                                    class="aspect-w-16 aspect-h-9 bg-gray-100 rounded-lg mb-3 overflow-hidden shadow-sm">
                                                    <img src="{{ asset('images/default-template.png') }}" alt="Default"
                                                        class="object-cover w-full h-full">
                                                </div>
                                                <div class="flex items-center justify-between px-1">
                                                    <span class="font-bold text-gray-800">Default Theme</span>
                                                </div>
                                            </label>
                                        </div>

                                        <!-- Available Templates -->
                                        @foreach($templates ?? [] as $template)
                                            <div class="relative group cursor-pointer"
                                                onclick="document.getElementById('template_{{ $template['name'] }}').click()">
                                                <div class="absolute top-3 right-3 z-10">
                                                    <input type="radio" name="active_template"
                                                        id="template_{{ $template['name'] }}" value="{{ $template['name'] }}"
                                                        class="w-5 h-5 text-blue-600 border-gray-300 focus:ring-blue-500" {{ ($settings['active_template'] ?? 'default') === $template['name'] ? 'checked' : '' }}>
                                                </div>
                                                <label for="template_{{ $template['name'] }}"
                                                    class="block border-4 rounded-xl p-2 group-hover:border-blue-300 peer-checked:border-blue-600 transition-all border-gray-200">
                                                    <div
                                                        class="aspect-w-16 aspect-h-9 bg-gray-100 rounded-lg mb-3 overflow-hidden shadow-sm">
                                                        <img src="{{ $template['preview'] }}" alt="{{ $template['label'] }}"
                                                            class="object-cover w-full h-full">
                                                    </div>
                                                    <div class="flex items-center justify-between px-1">
                                                        <span class="font-bold text-gray-800">{{ $template['label'] }}</span>
                                                        @if(($settings['active_template'] ?? 'default') === $template['name'])
                                                            <a href="{{ route('admin.settings.theme') }}"
                                                                class="text-xs bg-indigo-100 text-indigo-700 px-2 py-1 rounded hover:bg-indigo-200 transition-colors">
                                                                <i class="fas fa-magic mr-1"></i> Sesuaikan
                                                            </a>
                                                        @endif
                                                    </div>
                                                </label>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>

                                <hr class="border-gray-200">
                                <!-- Logo -->
                                <div>
                                    <label for="logo" class="block text-sm font-medium text-gray-700 mb-2">Site Logo</label>
                                    @if(isset($settings['logo']) && $settings['logo'])
                                        <div class="mb-3">
                                            <img src="{{ $settings['logo'] }}" alt="Current Logo"
                                                class="logo-preview border border-gray-300 rounded"
                                                style="height: {{ $settings['logo_height'] ?? '50' }}px">
                                        </div>
                                    @endif
                                    <input type="file" id="logo" name="logo" accept="image/*"
                                        class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                    <p class="text-xs text-gray-500 mt-1">Recommended size: 200x100px, PNG or JPG format</p>
                                </div>

                                <!-- Logo Height -->
                                <div>
                                    <label for="logo_height" class="block text-sm font-medium text-gray-700 mb-2">Logo
                                        Height (px)</label>
                                    <input type="number" id="logo_height" name="logo_height"
                                        value="{{ $settings['logo_height'] ?? '50' }}" min="20" max="200"
                                        class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                    <p class="text-xs text-gray-500 mt-1">Adjust the height of the logo in the header.
                                        Default is 50px.</p>
                                </div>

                                <!-- Favicon -->
                                <div>
                                    <label for="favicon"
                                        class="block text-sm font-medium text-gray-700 mb-2">Favicon</label>
                                    @if(isset($settings['favicon']) && $settings['favicon'])
                                        <div class="mb-3">
                                            <img src="{{ $settings['favicon'] }}" alt="Current Logo"
                                                class="logo-preview border border-gray-300 rounded">
                                        </div>
                                    @endif
                                    <input type="file" id="favicon" name="favicon" accept="image/*"
                                        class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                    <p class="text-xs text-gray-500 mt-1">Recommended size: 32x32px, ICO or PNG format</p>
                                </div>

                                <!-- Footer Menu Title -->
                                <div>
                                    <label for="footer_menu_title"
                                        class="block text-sm font-medium text-gray-700 mb-2">Footer
                                        Menu Title</label>
                                    <input type="text" id="footer_menu_title" name="footer_menu_title"
                                        value="{{ $settings['footer_menu_title'] ?? 'Tautan' }}"
                                        class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                    <p class="text-xs text-gray-500 mt-1">Title for the 4th column in footer (default:
                                        Tautan)</p>
                                </div>

                                <!-- Theme Color -->
                                <div>
                                    <label for="theme_color" class="block text-sm font-medium text-gray-700 mb-2">Primary
                                        Theme
                                        Color</label>
                                    <div class="flex items-center space-x-3">
                                        <input type="color" id="theme_color" name="theme_color"
                                            value="{{ $settings['theme_color'] ?? '#3b82f6' }}"
                                            class="h-10 w-16 border border-gray-300 rounded cursor-pointer">
                                        <input type="text" id="theme_color_text"
                                            class="flex-1 border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                            value="{{ $settings['theme_color'] ?? '#3b82f6' }}">
                                    </div>
                                </div>

                                <!-- Posts Per Page -->
                                <div>
                                    <label for="posts_per_page" class="block text-sm font-medium text-gray-700 mb-2">Posts
                                        Per
                                        Page</label>
                                    <input type="number" id="posts_per_page" name="posts_per_page"
                                        value="{{ $settings['posts_per_page'] ?? 10 }}" min="1" max="50"
                                        class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                </div>

                                <!-- Show Excerpts -->
                                <div class="flex items-center">
                                    <input type="hidden" name="show_excerpts" value="0">
                                    <input type="checkbox" id="show_excerpts" name="show_excerpts" value="1" {{ ($settings['show_excerpts'] ?? false) ? 'checked' : '' }}
                                        class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                                    <label for="show_excerpts" class="ml-2 text-sm text-gray-700">Show post excerpts on
                                        homepage</label>
                                </div>
                            </div>
                        </div>

                        <!-- SEO Settings -->
                        <div id="seo-section" class="settings-section">
                            <h2 class="text-xl font-semibold text-gray-900 mb-6">SEO Settings</h2>

                            <div class="space-y-6">
                                <!-- Meta Title -->
                                <div>
                                    <label for="meta_title" class="block text-sm font-medium text-gray-700 mb-2">Default
                                        Meta
                                        Title</label>
                                    <input type="text" id="meta_title" name="meta_title"
                                        value="{{ $settings['meta_title'] ?? '' }}"
                                        class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                </div>

                                <!-- Meta Description -->
                                <div>
                                    <label for="meta_description"
                                        class="block text-sm font-medium text-gray-700 mb-2">Default
                                        Meta Description</label>
                                    <textarea id="meta_description" name="meta_description" rows="3"
                                        class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-transparent">{{ $settings['meta_description'] ?? '' }}</textarea>
                                </div>

                                <!-- Meta Keywords -->
                                <div>
                                    <label for="meta_keywords" class="block text-sm font-medium text-gray-700 mb-2">Default
                                        Meta
                                        Keywords</label>
                                    <input type="text" id="meta_keywords" name="meta_keywords"
                                        value="{{ $settings['meta_keywords'] ?? '' }}"
                                        class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                    <p class="text-xs text-gray-500 mt-1">Separate keywords with commas</p>
                                </div>

                                <!-- Google Analytics -->
                                <div>
                                    <label for="google_analytics"
                                        class="block text-sm font-medium text-gray-700 mb-2">Google
                                        Analytics Tracking ID</label>
                                    <input type="text" id="google_analytics" name="google_analytics"
                                        value="{{ $settings['google_analytics'] ?? '' }}" placeholder="G-XXXXXXXXXX"
                                        class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                </div>

                                <!-- Google Search Console -->
                                <div>
                                    <label for="google_search_console"
                                        class="block text-sm font-medium text-gray-700 mb-2">Google Search Console
                                        Verification</label>
                                    <input type="text" id="google_search_console" name="google_search_console"
                                        value="{{ $settings['google_search_console'] ?? '' }}"
                                        class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                </div>
                            </div>
                        </div>

                        <!-- Social Media Settings -->
                        <div id="social-section" class="settings-section">
                            <h2 class="text-xl font-semibold text-gray-900 mb-6">Social Media Settings</h2>

                            <div class="space-y-6">
                                <!-- Facebook -->
                                <div>
                                    <label for="facebook_url" class="block text-sm font-medium text-gray-700 mb-2">Facebook
                                        URL</label>
                                    <input type="url" id="facebook_url" name="facebook_url"
                                        value="{{ $settings['facebook_url'] ?? '' }}"
                                        class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                </div>

                                <!-- Twitter -->
                                <div>
                                    <label for="twitter_url" class="block text-sm font-medium text-gray-700 mb-2">Twitter
                                        URL</label>
                                    <input type="url" id="twitter_url" name="twitter_url"
                                        value="{{ $settings['twitter_url'] ?? '' }}"
                                        class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                </div>

                                <!-- Instagram -->
                                <div>
                                    <label for="instagram_url"
                                        class="block text-sm font-medium text-gray-700 mb-2">Instagram
                                        URL</label>
                                    <input type="url" id="instagram_url" name="instagram_url"
                                        value="{{ $settings['instagram_url'] ?? '' }}"
                                        class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                </div>

                                <!-- LinkedIn -->
                                <div>
                                    <label for="linkedin_url" class="block text-sm font-medium text-gray-700 mb-2">LinkedIn
                                        URL</label>
                                    <input type="url" id="linkedin_url" name="linkedin_url"
                                        value="{{ $settings['linkedin_url'] ?? '' }}"
                                        class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                </div>

                                <!-- YouTube -->
                                <div>
                                    <label for="youtube_url" class="block text-sm font-medium text-gray-700 mb-2">YouTube
                                        URL</label>
                                    <input type="url" id="youtube_url" name="youtube_url"
                                        value="{{ $settings['youtube_url'] ?? '' }}"
                                        class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                </div>
                            </div>
                        </div>

                        <!-- Email Settings -->
                        <div id="email-section" class="settings-section">
                            <h2 class="text-xl font-semibold text-gray-900 mb-6">Email Settings</h2>

                            <div class="space-y-6">
                                <!-- SMTP Host -->
                                <div>
                                    <label for="smtp_host" class="block text-sm font-medium text-gray-700 mb-2">SMTP
                                        Host</label>
                                    <input type="text" id="smtp_host" name="smtp_host"
                                        value="{{ $settings['smtp_host'] ?? '' }}"
                                        class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                </div>

                                <!-- SMTP Port -->
                                <div>
                                    <label for="smtp_port" class="block text-sm font-medium text-gray-700 mb-2">SMTP
                                        Port</label>
                                    <input type="number" id="smtp_port" name="smtp_port"
                                        value="{{ $settings['smtp_port'] ?? 587 }}"
                                        class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                </div>

                                <!-- SMTP Username -->
                                <div>
                                    <label for="smtp_username" class="block text-sm font-medium text-gray-700 mb-2">SMTP
                                        Username</label>
                                    <input type="text" id="smtp_username" name="smtp_username"
                                        value="{{ $settings['smtp_username'] ?? '' }}"
                                        class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                </div>

                                <!-- SMTP Password -->
                                <div>
                                    <label for="smtp_password" class="block text-sm font-medium text-gray-700 mb-2">SMTP
                                        Password</label>
                                    <input type="password" id="smtp_password" name="smtp_password"
                                        value="{{ $settings['smtp_password'] ?? '' }}"
                                        class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                </div>

                                <!-- From Email -->
                                <div>
                                    <label for="from_email" class="block text-sm font-medium text-gray-700 mb-2">From
                                        Email</label>
                                    <input type="email" id="from_email" name="from_email"
                                        value="{{ $settings['from_email'] ?? '' }}"
                                        class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                </div>

                                <!-- From Name -->
                                <div>
                                    <label for="from_name" class="block text-sm font-medium text-gray-700 mb-2">From
                                        Name</label>
                                    <input type="text" id="from_name" name="from_name"
                                        value="{{ $settings['from_name'] ?? '' }}"
                                        class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                </div>
                            </div>
                        </div>

                        <!-- Security Settings -->
                        <div id="security-section" class="settings-section">
                            <h2 class="text-xl font-semibold text-gray-900 mb-6">Security Settings</h2>

                            <div class="space-y-6">
                                <!-- Enable Registration -->
                                <div class="flex items-center">
                                    <input type="hidden" name="enable_registration" value="0">
                                    <input type="checkbox" id="enable_registration" name="enable_registration" value="1" {{ ($settings['enable_registration'] ?? false) ? 'checked' : '' }}
                                        class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                                    <label for="enable_registration" class="ml-2 text-sm text-gray-700">Allow user
                                        registration</label>
                                </div>

                                <!-- Require Email Verification -->
                                <div class="flex items-center">
                                    <input type="hidden" name="require_email_verification" value="0">
                                    <input type="checkbox" id="require_email_verification" name="require_email_verification"
                                        value="1" {{ ($settings['require_email_verification'] ?? false) ? 'checked' : '' }}
                                        class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                                    <label for="require_email_verification" class="ml-2 text-sm text-gray-700">Require email
                                        verification for new users</label>
                                </div>

                                <!-- Enable Comments -->
                                <div class="flex items-center">
                                    <input type="hidden" name="enable_comments" value="0">
                                    <input type="checkbox" id="enable_comments" name="enable_comments" value="1" {{ ($settings['enable_comments'] ?? false) ? 'checked' : '' }}
                                        class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                                    <label for="enable_comments" class="ml-2 text-sm text-gray-700">Enable comments on
                                        posts</label>
                                </div>

                                <!-- Moderate Comments -->
                                <div class="flex items-center">
                                    <input type="hidden" name="moderate_comments" value="0">
                                    <input type="checkbox" id="moderate_comments" name="moderate_comments" value="1" {{ ($settings['moderate_comments'] ?? false) ? 'checked' : '' }}
                                        class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                                    <label for="moderate_comments" class="ml-2 text-sm text-gray-700">Moderate comments
                                        before
                                        publishing</label>
                                </div>

                                <div class="border-t border-gray-200 pt-6 mt-6">
                                    <h3 class="text-md font-medium text-gray-900 mb-4">Google Recaptcha Settings</h3>

                                    <!-- Recaptcha Site Key -->
                                    <div class="mb-4">
                                        <label for="recaptcha_site_key"
                                            class="block text-sm font-medium text-gray-700 mb-2">Recaptcha Site Key</label>
                                        <input type="text" id="recaptcha_site_key" name="recaptcha_site_key"
                                            value="{{ $settings['recaptcha_site_key'] ?? '' }}"
                                            class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                    </div>

                                    <!-- Recaptcha Secret Key -->
                                    <div>
                                        <label for="recaptcha_secret_key"
                                            class="block text-sm font-medium text-gray-700 mb-2">Recaptcha Secret
                                            Key</label>
                                        <input type="text" id="recaptcha_secret_key" name="recaptcha_secret_key"
                                            value="{{ $settings['recaptcha_secret_key'] ?? '' }}"
                                            class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Advanced Settings -->
                        <div id="advanced-section" class="settings-section">
                            <h2 class="text-xl font-semibold text-gray-900 mb-6">Advanced Settings</h2>

                            <div class="space-y-6">
                                <!-- Maintenance Mode -->
                                <div class="flex items-center">
                                    <input type="hidden" name="maintenance_mode" value="0">
                                    <input type="checkbox" id="maintenance_mode" name="maintenance_mode" value="1" {{ ($settings['maintenance_mode'] ?? false) ? 'checked' : '' }}
                                        class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                                    <label for="maintenance_mode" class="ml-2 text-sm text-gray-700">Enable maintenance
                                        mode</label>
                                </div>

                                <!-- Cache Duration -->
                                <div>
                                    <label for="cache_duration" class="block text-sm font-medium text-gray-700 mb-2">Cache
                                        Duration (minutes)</label>
                                    <input type="number" id="cache_duration" name="cache_duration"
                                        value="{{ $settings['cache_duration'] ?? 60 }}" min="0"
                                        class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                </div>

                                <!-- Custom CSS -->
                                <div>
                                    <label for="custom_css" class="block text-sm font-medium text-gray-700 mb-2">Custom
                                        CSS</label>
                                    <textarea id="custom_css" name="custom_css" rows="6"
                                        class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-transparent font-mono text-sm">{{ $settings['custom_css'] ?? '' }}</textarea>
                                </div>

                                <!-- Custom JavaScript -->
                                <div>
                                    <label for="custom_js" class="block text-sm font-medium text-gray-700 mb-2">Custom
                                        JavaScript</label>
                                    <textarea id="custom_js" name="custom_js" rows="6"
                                        class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-transparent font-mono text-sm">{{ $settings['custom_js'] ?? '' }}</textarea>
                                </div>
                            </div>
                        </div>

                        <!-- Save Button -->
                        <div class="mt-8 pt-6 border-t border-gray-200">
                            <div class="flex items-center justify-end space-x-3">
                                <button type="button" onclick="resetForm()"
                                    class="bg-gray-300 hover:bg-gray-400 text-gray-700 px-6 py-2 rounded-lg">
                                    Reset
                                </button>
                                <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-lg">
                                    <i class="fas fa-save mr-2"></i>
                                    Save Settings
                                </button>
                            </div>
                        </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        function showSection(sectionId, element) {
            // Hide all sections
            document.querySelectorAll('.settings-section').forEach(section => {
                section.classList.remove('active');
            });

            // Remove active class from all nav items
            document.querySelectorAll('.nav-item').forEach(item => {
                item.classList.remove('active');
            });

            // Show selected section
            document.getElementById(sectionId + '-section').classList.add('active');

            // Add active class to clicked nav item
            element.classList.add('active');

            // Update hidden input
            if (document.getElementById('active_tab_input')) {
                document.getElementById('active_tab_input').value = sectionId;
            }

            // Prevent default link behavior
            event.preventDefault();
        }

        // On Load: Check URL hash
        document.addEventListener('DOMContentLoaded', function () {
            if (window.location.hash) {
                const sectionId = window.location.hash.substring(1);
                const navLink = document.querySelector(`a[href="#${sectionId}"]`);
                if (navLink) {
                    navLink.click(); // Trigger click to show section
                }
            }
        });

        // Color picker sync
        document.getElementById('theme_color').addEventListener('change', function () {
            document.getElementById('theme_color_text').value = this.value;
        });

        document.getElementById('theme_color_text').addEventListener('input', function () {
            const color = this.value;
            if (/^#[0-9A-F]{6}$/i.test(color)) {
                document.getElementById('theme_color').value = color;
            }
        });

        function resetForm() {
            if (confirm('Are you sure you want to reset all settings to their default values?')) {
                document.querySelector('form').reset();
            }
        }

        // Auto-save draft functionality
        let autoSaveTimer;
        const formInputs = document.querySelectorAll('input, textarea, select');

        formInputs.forEach(input => {
            input.addEventListener('input', function () {
                clearTimeout(autoSaveTimer);
                autoSaveTimer = setTimeout(() => {
                    // Auto-save draft logic here
                    console.log('Auto-saving draft...');
                }, 2000);
            });
        });
    </script>
@endpush