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
        </div>
        
        <!-- Settings Content -->
        <div class="lg:col-span-3 p-6">
            <form method="POST" action="{{ route('admin.settings.update') }}" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                <!-- General Settings -->
                <div id="general-section" class="settings-section active">
                    <h2 class="text-xl font-semibold text-gray-900 mb-6">General Settings</h2>
                    
                    <div class="space-y-6">
                        <!-- Site Name -->
                        <div>
                            <label for="site_name" class="block text-sm font-medium text-gray-700 mb-2">Site Name *</label>
                            <input type="text" id="site_name" name="site_name" value="{{ $settings['site_name'] ?? '' }}" required class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        </div>
                        
                        <!-- Site Description -->
                        <div>
                            <label for="site_description" class="block text-sm font-medium text-gray-700 mb-2">Site Description</label>
                            <textarea id="site_description" name="site_description" rows="3" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-transparent">{{ $settings['site_description'] ?? '' }}</textarea>
                        </div>
                        
                        <!-- Site URL -->
                        <div>
                            <label for="site_url" class="block text-sm font-medium text-gray-700 mb-2">Site URL</label>
                            <input type="url" id="site_url" name="site_url" value="{{ $settings['site_url'] ?? '' }}" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        </div>
                        
                        <!-- Admin Email -->
                        <div>
                            <label for="admin_email" class="block text-sm font-medium text-gray-700 mb-2">Admin Email</label>
                            <input type="email" id="admin_email" name="admin_email" value="{{ $settings['admin_email'] ?? '' }}" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        </div>
                        
                        <!-- Timezone -->
                        <div>
                            <label for="timezone" class="block text-sm font-medium text-gray-700 mb-2">Timezone</label>
                            <select id="timezone" name="timezone" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                <option value="Asia/Jakarta" {{ ($settings['timezone'] ?? '') === 'Asia/Jakarta' ? 'selected' : '' }}>Asia/Jakarta</option>
                                <option value="UTC" {{ ($settings['timezone'] ?? '') === 'UTC' ? 'selected' : '' }}>UTC</option>
                                <option value="America/New_York" {{ ($settings['timezone'] ?? '') === 'America/New_York' ? 'selected' : '' }}>America/New_York</option>
                                <option value="Europe/London" {{ ($settings['timezone'] ?? '') === 'Europe/London' ? 'selected' : '' }}>Europe/London</option>
                            </select>
                        </div>
                        
                        <!-- Language -->
                        <div>
                            <label for="language" class="block text-sm font-medium text-gray-700 mb-2">Language</label>
                            <select id="language" name="language" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                <option value="en" {{ ($settings['language'] ?? '') === 'en' ? 'selected' : '' }}>English</option>
                                <option value="id" {{ ($settings['language'] ?? '') === 'id' ? 'selected' : '' }}>Bahasa Indonesia</option>
                            </select>
                        </div>
                    </div>
                </div>
                
                <!-- Appearance Settings -->
                <div id="appearance-section" class="settings-section">
                    <h2 class="text-xl font-semibold text-gray-900 mb-6">Appearance Settings</h2>
                    
                    <div class="space-y-6">
                        <!-- Logo -->
                        <div>
                            <label for="logo" class="block text-sm font-medium text-gray-700 mb-2">Site Logo</label>
                            @if(isset($settings['logo']) && $settings['logo'])
                                <div class="mb-3">
                                    <img src="{{ $settings['logo'] }}" alt="Current Logo" class="logo-preview border border-gray-300 rounded">
                                </div>
                            @endif
                            <input type="file" id="logo" name="logo" accept="image/*" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                            <p class="text-xs text-gray-500 mt-1">Recommended size: 200x100px, PNG or JPG format</p>
                        </div>
                        
                        <!-- Favicon -->
                        <div>
                            <label for="favicon" class="block text-sm font-medium text-gray-700 mb-2">Favicon</label>
                                @if(isset($settings['favicon']) && $settings['favicon'])
                                <div class="mb-3">
                                    <img src="{{ $settings['favicon'] }}" alt="Current Logo" class="logo-preview border border-gray-300 rounded">
                                </div>
                            @endif
                            <input type="file" id="favicon" name="favicon" accept="image/*" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                            <p class="text-xs text-gray-500 mt-1">Recommended size: 32x32px, ICO or PNG format</p>
                        </div>
                        
                        <!-- Theme Color -->
                        <div>
                            <label for="theme_color" class="block text-sm font-medium text-gray-700 mb-2">Primary Theme Color</label>
                            <div class="flex items-center space-x-3">
                                <input type="color" id="theme_color" name="theme_color" value="{{ $settings['theme_color'] ?? '#3b82f6' }}" class="h-10 w-16 border border-gray-300 rounded cursor-pointer">
                                <input type="text" id="theme_color_text" class="flex-1 border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-transparent" value="{{ $settings['theme_color'] ?? '#3b82f6' }}">
                            </div>
                        </div>
                        
                        <!-- Posts Per Page -->
                        <div>
                            <label for="posts_per_page" class="block text-sm font-medium text-gray-700 mb-2">Posts Per Page</label>
                            <input type="number" id="posts_per_page" name="posts_per_page" value="{{ $settings['posts_per_page'] ?? 10 }}" min="1" max="50" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        </div>
                        
                        <!-- Show Excerpts -->
                        <div class="flex items-center">
                            <input type="checkbox" id="show_excerpts" name="show_excerpts" value="1" {{ ($settings['show_excerpts'] ?? false) ? 'checked' : '' }} class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                            <label for="show_excerpts" class="ml-2 text-sm text-gray-700">Show post excerpts on homepage</label>
                        </div>
                    </div>
                </div>
                
                <!-- SEO Settings -->
                <div id="seo-section" class="settings-section">
                    <h2 class="text-xl font-semibold text-gray-900 mb-6">SEO Settings</h2>
                    
                    <div class="space-y-6">
                        <!-- Meta Title -->
                        <div>
                            <label for="meta_title" class="block text-sm font-medium text-gray-700 mb-2">Default Meta Title</label>
                            <input type="text" id="meta_title" name="meta_title" value="{{ $settings['meta_title'] ?? '' }}" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        </div>
                        
                        <!-- Meta Description -->
                        <div>
                            <label for="meta_description" class="block text-sm font-medium text-gray-700 mb-2">Default Meta Description</label>
                            <textarea id="meta_description" name="meta_description" rows="3" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-transparent">{{ $settings['meta_description'] ?? '' }}</textarea>
                        </div>
                        
                        <!-- Meta Keywords -->
                        <div>
                            <label for="meta_keywords" class="block text-sm font-medium text-gray-700 mb-2">Default Meta Keywords</label>
                            <input type="text" id="meta_keywords" name="meta_keywords" value="{{ $settings['meta_keywords'] ?? '' }}" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                            <p class="text-xs text-gray-500 mt-1">Separate keywords with commas</p>
                        </div>
                        
                        <!-- Google Analytics -->
                        <div>
                            <label for="google_analytics" class="block text-sm font-medium text-gray-700 mb-2">Google Analytics Tracking ID</label>
                            <input type="text" id="google_analytics" name="google_analytics" value="{{ $settings['google_analytics'] ?? '' }}" placeholder="G-XXXXXXXXXX" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        </div>
                        
                        <!-- Google Search Console -->
                        <div>
                            <label for="google_search_console" class="block text-sm font-medium text-gray-700 mb-2">Google Search Console Verification</label>
                            <input type="text" id="google_search_console" name="google_search_console" value="{{ $settings['google_search_console'] ?? '' }}" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        </div>
                    </div>
                </div>
                
                <!-- Social Media Settings -->
                <div id="social-section" class="settings-section">
                    <h2 class="text-xl font-semibold text-gray-900 mb-6">Social Media Settings</h2>
                    
                    <div class="space-y-6">
                        <!-- Facebook -->
                        <div>
                            <label for="facebook_url" class="block text-sm font-medium text-gray-700 mb-2">Facebook URL</label>
                            <input type="url" id="facebook_url" name="facebook_url" value="{{ $settings['facebook_url'] ?? '' }}" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        </div>
                        
                        <!-- Twitter -->
                        <div>
                            <label for="twitter_url" class="block text-sm font-medium text-gray-700 mb-2">Twitter URL</label>
                            <input type="url" id="twitter_url" name="twitter_url" value="{{ $settings['twitter_url'] ?? '' }}" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        </div>
                        
                        <!-- Instagram -->
                        <div>
                            <label for="instagram_url" class="block text-sm font-medium text-gray-700 mb-2">Instagram URL</label>
                            <input type="url" id="instagram_url" name="instagram_url" value="{{ $settings['instagram_url'] ?? '' }}" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        </div>
                        
                        <!-- LinkedIn -->
                        <div>
                            <label for="linkedin_url" class="block text-sm font-medium text-gray-700 mb-2">LinkedIn URL</label>
                            <input type="url" id="linkedin_url" name="linkedin_url" value="{{ $settings['linkedin_url'] ?? '' }}" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        </div>
                        
                        <!-- YouTube -->
                        <div>
                            <label for="youtube_url" class="block text-sm font-medium text-gray-700 mb-2">YouTube URL</label>
                            <input type="url" id="youtube_url" name="youtube_url" value="{{ $settings['youtube_url'] ?? '' }}" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        </div>
                    </div>
                </div>
                
                <!-- Email Settings -->
                <div id="email-section" class="settings-section">
                    <h2 class="text-xl font-semibold text-gray-900 mb-6">Email Settings</h2>
                    
                    <div class="space-y-6">
                        <!-- SMTP Host -->
                        <div>
                            <label for="smtp_host" class="block text-sm font-medium text-gray-700 mb-2">SMTP Host</label>
                            <input type="text" id="smtp_host" name="smtp_host" value="{{ $settings['smtp_host'] ?? '' }}" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        </div>
                        
                        <!-- SMTP Port -->
                        <div>
                            <label for="smtp_port" class="block text-sm font-medium text-gray-700 mb-2">SMTP Port</label>
                            <input type="number" id="smtp_port" name="smtp_port" value="{{ $settings['smtp_port'] ?? 587 }}" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        </div>
                        
                        <!-- SMTP Username -->
                        <div>
                            <label for="smtp_username" class="block text-sm font-medium text-gray-700 mb-2">SMTP Username</label>
                            <input type="text" id="smtp_username" name="smtp_username" value="{{ $settings['smtp_username'] ?? '' }}" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        </div>
                        
                        <!-- SMTP Password -->
                        <div>
                            <label for="smtp_password" class="block text-sm font-medium text-gray-700 mb-2">SMTP Password</label>
                            <input type="password" id="smtp_password" name="smtp_password" value="{{ $settings['smtp_password'] ?? '' }}" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        </div>
                        
                        <!-- From Email -->
                        <div>
                            <label for="from_email" class="block text-sm font-medium text-gray-700 mb-2">From Email</label>
                            <input type="email" id="from_email" name="from_email" value="{{ $settings['from_email'] ?? '' }}" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        </div>
                        
                        <!-- From Name -->
                        <div>
                            <label for="from_name" class="block text-sm font-medium text-gray-700 mb-2">From Name</label>
                            <input type="text" id="from_name" name="from_name" value="{{ $settings['from_name'] ?? '' }}" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        </div>
                    </div>
                </div>
                
                <!-- Security Settings -->
                <div id="security-section" class="settings-section">
                    <h2 class="text-xl font-semibold text-gray-900 mb-6">Security Settings</h2>
                    
                    <div class="space-y-6">
                        <!-- Enable Registration -->
                        <div class="flex items-center">
                            <input type="checkbox" id="enable_registration" name="enable_registration" value="1" {{ ($settings['enable_registration'] ?? false) ? 'checked' : '' }} class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                            <label for="enable_registration" class="ml-2 text-sm text-gray-700">Allow user registration</label>
                        </div>
                        
                        <!-- Require Email Verification -->
                        <div class="flex items-center">
                            <input type="checkbox" id="require_email_verification" name="require_email_verification" value="1" {{ ($settings['require_email_verification'] ?? false) ? 'checked' : '' }} class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                            <label for="require_email_verification" class="ml-2 text-sm text-gray-700">Require email verification for new users</label>
                        </div>
                        
                        <!-- Enable Comments -->
                        <div class="flex items-center">
                            <input type="checkbox" id="enable_comments" name="enable_comments" value="1" {{ ($settings['enable_comments'] ?? false) ? 'checked' : '' }} class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                            <label for="enable_comments" class="ml-2 text-sm text-gray-700">Enable comments on posts</label>
                        </div>
                        
                        <!-- Moderate Comments -->
                        <div class="flex items-center">
                            <input type="checkbox" id="moderate_comments" name="moderate_comments" value="1" {{ ($settings['moderate_comments'] ?? false) ? 'checked' : '' }} class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                            <label for="moderate_comments" class="ml-2 text-sm text-gray-700">Moderate comments before publishing</label>
                        </div>
                    </div>
                </div>
                
                <!-- Advanced Settings -->
                <div id="advanced-section" class="settings-section">
                    <h2 class="text-xl font-semibold text-gray-900 mb-6">Advanced Settings</h2>
                    
                    <div class="space-y-6">
                        <!-- Maintenance Mode -->
                        <div class="flex items-center">
                            <input type="checkbox" id="maintenance_mode" name="maintenance_mode" value="1" {{ ($settings['maintenance_mode'] ?? false) ? 'checked' : '' }} class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                            <label for="maintenance_mode" class="ml-2 text-sm text-gray-700">Enable maintenance mode</label>
                        </div>
                        
                        <!-- Cache Duration -->
                        <div>
                            <label for="cache_duration" class="block text-sm font-medium text-gray-700 mb-2">Cache Duration (minutes)</label>
                            <input type="number" id="cache_duration" name="cache_duration" value="{{ $settings['cache_duration'] ?? 60 }}" min="0" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        </div>
                        
                        <!-- Custom CSS -->
                        <div>
                            <label for="custom_css" class="block text-sm font-medium text-gray-700 mb-2">Custom CSS</label>
                            <textarea id="custom_css" name="custom_css" rows="6" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-transparent font-mono text-sm">{{ $settings['custom_css'] ?? '' }}</textarea>
                        </div>
                        
                        <!-- Custom JavaScript -->
                        <div>
                            <label for="custom_js" class="block text-sm font-medium text-gray-700 mb-2">Custom JavaScript</label>
                            <textarea id="custom_js" name="custom_js" rows="6" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-transparent font-mono text-sm">{{ $settings['custom_js'] ?? '' }}</textarea>
                        </div>
                    </div>
                </div>
                
                <!-- Save Button -->
                <div class="mt-8 pt-6 border-t border-gray-200">
                    <div class="flex items-center justify-end space-x-3">
                        <button type="button" onclick="resetForm()" class="bg-gray-300 hover:bg-gray-400 text-gray-700 px-6 py-2 rounded-lg">
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
    
    // Prevent default link behavior
    event.preventDefault();
}

// Color picker sync
document.getElementById('theme_color').addEventListener('change', function() {
    document.getElementById('theme_color_text').value = this.value;
});

document.getElementById('theme_color_text').addEventListener('input', function() {
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
    input.addEventListener('input', function() {
        clearTimeout(autoSaveTimer);
        autoSaveTimer = setTimeout(() => {
            // Auto-save draft logic here
            console.log('Auto-saving draft...');
        }, 2000);
    });
});
</script>
@endpush