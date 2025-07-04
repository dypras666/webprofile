@extends('layouts.admin')

@section('title', 'Profile Settings')
@section('page-title', 'Profile')

@push('styles')
<style>
    .profile-avatar {
        width: 120px;
        height: 120px;
        border-radius: 50%;
        object-fit: cover;
        border: 4px solid #e5e7eb;
    }
    .avatar-upload {
        position: relative;
        display: inline-block;
    }
    .avatar-overlay {
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: rgba(0, 0, 0, 0.5);
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        opacity: 0;
        transition: opacity 0.2s;
        cursor: pointer;
    }
    .avatar-upload:hover .avatar-overlay {
        opacity: 1;
    }
    .stats-card {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
    }
</style>
@endpush

@section('content')
<div class="space-y-6">
    <!-- Profile Header -->
    <div class="bg-white rounded-lg shadow p-6">
        <div class="flex items-center space-x-6">
            <div class="avatar-upload">
                @if(auth()->user()->avatar)
                    <img src="{{ auth()->user()->avatar }}" alt="Profile" class="profile-avatar">
                @else
                    <div class="profile-avatar bg-gray-300 flex items-center justify-center text-gray-600 text-3xl font-bold">
                        {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                    </div>
                @endif
                <div class="avatar-overlay">
                    <i class="fas fa-camera text-white text-xl"></i>
                </div>
                <input type="file" id="avatar-input" accept="image/*" class="hidden">
            </div>
            
            <div class="flex-1">
                <h1 class="text-2xl font-bold text-gray-900">{{ auth()->user()->name }}</h1>
                <p class="text-gray-600">{{ auth()->user()->email }}</p>
                <div class="flex items-center mt-2">
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                        <i class="fas fa-user-shield mr-1"></i>
                        {{ ucfirst(auth()->user()->role ?? 'user') }}
                    </span>
                    <span class="ml-3 text-sm text-gray-500">
                        Member since {{ auth()->user()->created_at->format('M Y') }}
                    </span>
                </div>
            </div>
            
            <div class="text-right">
                <p class="text-sm text-gray-500">Last login</p>
                <p class="text-sm font-medium text-gray-900">
                    {{ auth()->user()->last_login_at ? auth()->user()->last_login_at->format('M d, Y H:i') : 'Never' }}
                </p>
            </div>
        </div>
    </div>
    
    <!-- Stats Cards -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
        <div class="stats-card rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-white bg-opacity-20">
                    <i class="fas fa-file-alt text-xl"></i>
                </div>
                <div class="ml-4">
                    <p class="text-sm opacity-90">Total Posts</p>
                    <p class="text-2xl font-bold">{{ $userStats['posts'] ?? 0 }}</p>
                </div>
            </div>
        </div>
        
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-green-100 text-green-600">
                    <i class="fas fa-eye text-xl"></i>
                </div>
                <div class="ml-4">
                    <p class="text-sm text-gray-600">Total Views</p>
                    <p class="text-2xl font-bold text-gray-900">{{ number_format($userStats['views'] ?? 0) }}</p>
                </div>
            </div>
        </div>
        
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-yellow-100 text-yellow-600">
                    <i class="fas fa-heart text-xl"></i>
                </div>
                <div class="ml-4">
                    <p class="text-sm text-gray-600">Total Likes</p>
                    <p class="text-2xl font-bold text-gray-900">{{ number_format($userStats['likes'] ?? 0) }}</p>
                </div>
            </div>
        </div>
        
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-purple-100 text-purple-600">
                    <i class="fas fa-comments text-xl"></i>
                </div>
                <div class="ml-4">
                    <p class="text-sm text-gray-600">Comments</p>
                    <p class="text-2xl font-bold text-gray-900">{{ number_format($userStats['comments'] ?? 0) }}</p>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Profile Forms -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Personal Information -->
        <div class="bg-white rounded-lg shadow">
            <div class="px-6 py-4 border-b border-gray-200">
                <h2 class="text-lg font-medium text-gray-900">Personal Information</h2>
            </div>
            
            <form method="POST" action="{{ route('admin.profile.update') }}" class="p-6 space-y-4">
                @csrf
                @method('PUT')
                
                <!-- Name -->
                <div>
                    <label for="name" class="block text-sm font-medium text-gray-700 mb-1">Full Name *</label>
                    <input type="text" id="name" name="name" value="{{ auth()->user()->name }}" required class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                </div>
                
                <!-- Email -->
                <div>
                    <label for="email" class="block text-sm font-medium text-gray-700 mb-1">Email Address *</label>
                    <input type="email" id="email" name="email" value="{{ auth()->user()->email }}" required class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                </div>
                
                <!-- Bio -->
                <div>
                    <label for="bio" class="block text-sm font-medium text-gray-700 mb-1">Bio</label>
                    <textarea id="bio" name="bio" rows="4" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-transparent" placeholder="Tell us about yourself...">{{ auth()->user()->bio }}</textarea>
                </div>
                
                <!-- Website -->
                <div>
                    <label for="website" class="block text-sm font-medium text-gray-700 mb-1">Website</label>
                    <input type="url" id="website" name="website" value="{{ auth()->user()->website }}" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-transparent" placeholder="https://example.com">
                </div>
                
                <!-- Location -->
                <div>
                    <label for="location" class="block text-sm font-medium text-gray-700 mb-1">Location</label>
                    <input type="text" id="location" name="location" value="{{ auth()->user()->location }}" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-transparent" placeholder="City, Country">
                </div>
                
                <div class="pt-4">
                    <button type="submit" class="w-full bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg">
                        <i class="fas fa-save mr-2"></i>
                        Update Profile
                    </button>
                </div>
            </form>
        </div>
        
        <!-- Security Settings -->
        <div class="bg-white rounded-lg shadow">
            <div class="px-6 py-4 border-b border-gray-200">
                <h2 class="text-lg font-medium text-gray-900">Security Settings</h2>
            </div>
            
            <form method="POST" action="{{ route('admin.profile.password') }}" class="p-6 space-y-4">
                @csrf
                @method('PUT')
                
                <!-- Current Password -->
                <div>
                    <label for="current_password" class="block text-sm font-medium text-gray-700 mb-1">Current Password *</label>
                    <input type="password" id="current_password" name="current_password" required class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                </div>
                
                <!-- New Password -->
                <div>
                    <label for="password" class="block text-sm font-medium text-gray-700 mb-1">New Password *</label>
                    <input type="password" id="password" name="password" required class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    <div class="mt-1">
                        <div class="text-xs text-gray-500">Password strength: <span id="password-strength" class="font-medium">Weak</span></div>
                        <div class="w-full bg-gray-200 rounded-full h-1 mt-1">
                            <div id="password-strength-bar" class="bg-red-500 h-1 rounded-full transition-all duration-300" style="width: 0%"></div>
                        </div>
                    </div>
                </div>
                
                <!-- Confirm Password -->
                <div>
                    <label for="password_confirmation" class="block text-sm font-medium text-gray-700 mb-1">Confirm New Password *</label>
                    <input type="password" id="password_confirmation" name="password_confirmation" required class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                </div>
                
                <div class="pt-4">
                    <button type="submit" class="w-full bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-lg">
                        <i class="fas fa-key mr-2"></i>
                        Change Password
                    </button>
                </div>
            </form>
            
            <!-- Two-Factor Authentication -->
            <div class="px-6 pb-6">
                <div class="border-t border-gray-200 pt-6">
                    <h3 class="text-md font-medium text-gray-900 mb-3">Two-Factor Authentication</h3>
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm text-gray-600">Add an extra layer of security to your account</p>
                        </div>
                        <button class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg text-sm">
                            Enable 2FA
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Recent Activity -->
    <div class="bg-white rounded-lg shadow">
        <div class="px-6 py-4 border-b border-gray-200">
            <h2 class="text-lg font-medium text-gray-900">Recent Activity</h2>
        </div>
        
        <div class="p-6">
            <div class="space-y-4">
                @forelse($recentActivity ?? [] as $activity)
                <div class="flex items-center space-x-4">
                    <div class="p-2 rounded-full bg-blue-100 text-blue-600">
                        <i class="fas fa-{{ $activity['icon'] }} text-sm"></i>
                    </div>
                    <div class="flex-1">
                        <p class="text-sm text-gray-900">{{ $activity['description'] }}</p>
                        <p class="text-xs text-gray-500">{{ $activity['time'] }}</p>
                    </div>
                </div>
                @empty
                <div class="text-center py-8">
                    <i class="fas fa-history text-4xl text-gray-400 mb-4"></i>
                    <p class="text-gray-500">No recent activity</p>
                </div>
                @endforelse
            </div>
        </div>
    </div>
    
    <!-- Danger Zone -->
    <div class="bg-white rounded-lg shadow border border-red-200">
        <div class="px-6 py-4 border-b border-red-200 bg-red-50">
            <h2 class="text-lg font-medium text-red-900">Danger Zone</h2>
        </div>
        
        <div class="p-6">
            <div class="space-y-4">
                <!-- Export Data -->
                <div class="flex items-center justify-between">
                    <div>
                        <h3 class="text-sm font-medium text-gray-900">Export Account Data</h3>
                        <p class="text-sm text-gray-600">Download a copy of all your account data</p>
                    </div>
                    <button class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg text-sm">
                        Export Data
                    </button>
                </div>
                
                <!-- Delete Account -->
                <div class="flex items-center justify-between pt-4 border-t border-gray-200">
                    <div>
                        <h3 class="text-sm font-medium text-red-900">Delete Account</h3>
                        <p class="text-sm text-red-600">Permanently delete your account and all associated data</p>
                    </div>
                    <button onclick="confirmDeleteAccount()" class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-lg text-sm">
                        Delete Account
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
// Avatar upload
document.querySelector('.avatar-upload').addEventListener('click', function() {
    document.getElementById('avatar-input').click();
});

document.getElementById('avatar-input').addEventListener('change', function(e) {
    const file = e.target.files[0];
    if (file) {
        const formData = new FormData();
        formData.append('avatar', file);
        
        fetch('{{ route("admin.profile.avatar") }}', {
            method: 'POST',
            body: formData,
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                location.reload();
            } else {
                alert('Error uploading avatar');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Error uploading avatar');
        });
    }
});

// Password strength checker
document.getElementById('password').addEventListener('input', function() {
    const password = this.value;
    const strength = checkPasswordStrength(password);
    const strengthBar = document.getElementById('password-strength-bar');
    const strengthText = document.getElementById('password-strength');
    
    let width, color, text;
    
    if (strength < 2) {
        width = '25%';
        color = '#ef4444';
        text = 'Weak';
    } else if (strength < 3) {
        width = '50%';
        color = '#f59e0b';
        text = 'Fair';
    } else if (strength < 4) {
        width = '75%';
        color = '#10b981';
        text = 'Good';
    } else {
        width = '100%';
        color = '#059669';
        text = 'Strong';
    }
    
    strengthBar.style.width = width;
    strengthBar.style.backgroundColor = color;
    strengthText.textContent = text;
    strengthText.style.color = color;
});

function checkPasswordStrength(password) {
    let strength = 0;
    
    if (password.length >= 8) strength++;
    if (/[a-z]/.test(password)) strength++;
    if (/[A-Z]/.test(password)) strength++;
    if (/[0-9]/.test(password)) strength++;
    if (/[^A-Za-z0-9]/.test(password)) strength++;
    
    return strength;
}

function confirmDeleteAccount() {
    if (confirm('Are you sure you want to delete your account? This action cannot be undone.')) {
        if (confirm('This will permanently delete all your data. Are you absolutely sure?')) {
            // Implement account deletion
            console.log('Delete account confirmed');
        }
    }
}

// Auto-save profile changes
let autoSaveTimer;
const profileInputs = document.querySelectorAll('#name, #email, #bio, #website, #location');

profileInputs.forEach(input => {
    input.addEventListener('input', function() {
        clearTimeout(autoSaveTimer);
        autoSaveTimer = setTimeout(() => {
            // Auto-save logic here
            console.log('Auto-saving profile changes...');
        }, 2000);
    });
});
</script>
@endpush