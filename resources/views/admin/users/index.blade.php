@extends('layouts.admin')

@section('title', 'Users Management')
@section('page-title', 'Users')

@push('styles')
<style>
    .user-avatar {
        width: 40px;
        height: 40px;
        border-radius: 50%;
        object-fit: cover;
    }
    .role-badge {
        display: inline-flex;
        align-items: center;
        padding: 0.25rem 0.75rem;
        border-radius: 9999px;
        font-size: 0.75rem;
        font-weight: 500;
    }
    .role-admin {
        background-color: #fef3c7;
        color: #92400e;
    }
    .role-editor {
        background-color: #dbeafe;
        color: #1e40af;
    }
    .role-user {
        background-color: #f3f4f6;
        color: #374151;
    }
    .status-active {
        background-color: #dcfce7;
        color: #166534;
    }
    .status-inactive {
        background-color: #fee2e2;
        color: #dc2626;
    }
    .table-actions {
        white-space: nowrap;
    }
</style>
@endpush

@section('content')
<div class="space-y-6">
    <!-- Stats Cards -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-blue-100 text-blue-600">
                    <i class="fas fa-users text-xl"></i>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600">Total Users</p>
                    <p class="text-2xl font-bold text-gray-900">{{ $totalUsers ?? 0 }}</p>
                </div>
            </div>
        </div>
        
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-green-100 text-green-600">
                    <i class="fas fa-user-check text-xl"></i>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600">Active Users</p>
                    <p class="text-2xl font-bold text-gray-900">{{ $activeUsers ?? 0 }}</p>
                </div>
            </div>
        </div>
        
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-yellow-100 text-yellow-600">
                    <i class="fas fa-user-shield text-xl"></i>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600">Admins</p>
                    <p class="text-2xl font-bold text-gray-900">{{ $adminUsers ?? 0 }}</p>
                </div>
            </div>
        </div>
        
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-purple-100 text-purple-600">
                    <i class="fas fa-user-plus text-xl"></i>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600">New This Month</p>
                    <p class="text-2xl font-bold text-gray-900">{{ $newUsersThisMonth ?? 0 }}</p>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Users Table -->
    <div class="bg-white rounded-lg shadow">
        <!-- Header -->
        <div class="px-6 py-4 border-b border-gray-200">
            <div class="flex items-center justify-between">
                <h2 class="text-lg font-medium text-gray-900">All Users</h2>
                <div class="flex items-center space-x-3">
                    <!-- Search -->
                    <div class="relative">
                        <input type="text" placeholder="Search users..." class="pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <i class="fas fa-search text-gray-400"></i>
                        </div>
                    </div>
                    
                    <!-- Role Filter -->
                    <select class="border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        <option value="">All Roles</option>
                        <option value="admin">Admin</option>
                        <option value="editor">Editor</option>
                        <option value="user">User</option>
                    </select>
                    
                    <!-- Status Filter -->
                    <select class="border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        <option value="">All Status</option>
                        <option value="active">Active</option>
                        <option value="inactive">Inactive</option>
                    </select>
                    
                    <!-- Add New Button -->
                    <button onclick="openUserModal()" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg flex items-center">
                        <i class="fas fa-plus mr-2"></i>
                        Add User
                    </button>
                </div>
            </div>
        </div>
        
        <!-- Table -->
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            <input type="checkbox" class="rounded border-gray-300">
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            User
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Role
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Status
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Posts
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Last Login
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Actions
                        </th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($users ?? [] as $user)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 whitespace-nowrap">
                            <input type="checkbox" class="rounded border-gray-300" value="{{ $user->id }}">
                        </td>
                        <td class="px-6 py-4">
                            <div class="flex items-center">
                                @if($user->avatar)
                                    <img class="user-avatar mr-3" src="{{ $user->avatar }}" alt="{{ $user->name }}">
                                @else
                                    <div class="user-avatar mr-3 bg-gray-300 flex items-center justify-center text-gray-600 font-medium">
                                        {{ strtoupper(substr($user->name, 0, 1)) }}
                                    </div>
                                @endif
                                <div>
                                    <div class="text-sm font-medium text-gray-900">{{ $user->name }}</div>
                                    <div class="text-sm text-gray-500">{{ $user->email }}</div>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="role-badge role-{{ $user->role ?? 'user' }}">
                                <i class="fas fa-{{ $user->role === 'admin' ? 'user-shield' : ($user->role === 'editor' ? 'user-edit' : 'user') }} text-xs mr-1"></i>
                                {{ ucfirst($user->role ?? 'user') }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="role-badge {{ $user->is_active ? 'status-active' : 'status-inactive' }}">
                                <i class="fas fa-circle text-xs mr-1"></i>
                                {{ $user->is_active ? 'Active' : 'Inactive' }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            {{ $user->posts_count ?? 0 }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            {{ $user->last_login_at ? $user->last_login_at->format('M d, Y H:i') : 'Never' }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap table-actions">
                            <div class="flex items-center space-x-2">
                                <button onclick="viewUser({{ $user->id }})" class="text-blue-600 hover:text-blue-900" title="View">
                                    <i class="fas fa-eye"></i>
                                </button>
                                <button onclick="editUser({{ $user->id }})" class="text-indigo-600 hover:text-indigo-900" title="Edit">
                                    <i class="fas fa-edit"></i>
                                </button>
                                <button onclick="toggleUserStatus({{ $user->id }})" class="text-{{ $user->is_active ? 'orange' : 'green' }}-600 hover:text-{{ $user->is_active ? 'orange' : 'green' }}-900" title="{{ $user->is_active ? 'Deactivate' : 'Activate' }}">
                                    <i class="fas fa-{{ $user->is_active ? 'user-slash' : 'user-check' }}"></i>
                                </button>
                                @if($user->id !== auth()->id())
                                <form method="POST" action="{{ route('admin.users.destroy', $user) }}" class="inline" onsubmit="return confirm('Are you sure you want to delete this user?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-red-600 hover:text-red-900" title="Delete">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="px-6 py-12 text-center">
                            <div class="text-gray-500">
                                <i class="fas fa-users text-4xl mb-4"></i>
                                <p class="text-lg font-medium">No users found</p>
                                <p class="text-sm">Get started by adding your first user.</p>
                                <button onclick="openUserModal()" class="mt-4 inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                                    <i class="fas fa-plus mr-2"></i>
                                    Add User
                                </button>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        <!-- Pagination -->
        @if(isset($users) && $users->hasPages())
        <div class="px-6 py-4 border-t border-gray-200">
            {{ $users->links() }}
        </div>
        @endif
    </div>
    
    <!-- Bulk Actions -->
    <div class="bg-white rounded-lg shadow p-6">
        <h3 class="text-lg font-medium text-gray-900 mb-4">Bulk Actions</h3>
        <div class="flex items-center space-x-4">
            <select id="bulk-action" class="border border-gray-300 rounded-lg px-3 py-2">
                <option value="">Select Action</option>
                <option value="activate">Activate</option>
                <option value="deactivate">Deactivate</option>
                <option value="change-role">Change Role</option>
                <option value="delete">Delete</option>
            </select>
            <select id="bulk-role" class="border border-gray-300 rounded-lg px-3 py-2 hidden">
                <option value="user">User</option>
                <option value="editor">Editor</option>
                <option value="admin">Admin</option>
            </select>
            <button onclick="executeBulkAction()" class="bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded-lg">
                Apply
            </button>
        </div>
    </div>
</div>

<!-- User Modal -->
<div id="user-modal" class="fixed inset-0 bg-black bg-opacity-50 hidden z-50">
    <div class="flex items-center justify-center min-h-screen p-4">
        <div class="bg-white rounded-lg max-w-2xl w-full">
            <div class="flex items-center justify-between p-6 border-b">
                <h3 id="modal-title" class="text-lg font-medium text-gray-900">Add New User</h3>
                <button onclick="closeUserModal()" class="text-gray-400 hover:text-gray-600">
                    <i class="fas fa-times text-xl"></i>
                </button>
            </div>
            
            <form id="user-form" method="POST" action="{{ route('admin.users.store') }}" class="p-6 space-y-4">
                @csrf
                <input type="hidden" id="user-id" name="_method" value="">
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <!-- Name -->
                    <div>
                        <label for="modal-name" class="block text-sm font-medium text-gray-700 mb-1">Name *</label>
                        <input type="text" id="modal-name" name="name" required class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    </div>
                    
                    <!-- Email -->
                    <div>
                        <label for="modal-email" class="block text-sm font-medium text-gray-700 mb-1">Email *</label>
                        <input type="email" id="modal-email" name="email" required class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    </div>
                </div>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <!-- Password -->
                    <div>
                        <label for="modal-password" class="block text-sm font-medium text-gray-700 mb-1">Password</label>
                        <input type="password" id="modal-password" name="password" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        <p class="text-xs text-gray-500 mt-1">Leave empty to keep current password</p>
                    </div>
                    
                    <!-- Confirm Password -->
                    <div>
                        <label for="modal-password-confirmation" class="block text-sm font-medium text-gray-700 mb-1">Confirm Password</label>
                        <input type="password" id="modal-password-confirmation" name="password_confirmation" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    </div>
                </div>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <!-- Role -->
                    <div>
                        <label for="modal-role" class="block text-sm font-medium text-gray-700 mb-1">Role *</label>
                        <select id="modal-role" name="role" required class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                            <option value="user">User</option>
                            <option value="editor">Editor</option>
                            <option value="admin">Admin</option>
                        </select>
                    </div>
                    
                    <!-- Status -->
                    <div>
                        <label for="modal-status" class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                        <select id="modal-status" name="is_active" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                            <option value="1">Active</option>
                            <option value="0">Inactive</option>
                        </select>
                    </div>
                </div>
                
                <!-- Bio -->
                <div>
                    <label for="modal-bio" class="block text-sm font-medium text-gray-700 mb-1">Bio</label>
                    <textarea id="modal-bio" name="bio" rows="3" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-transparent"></textarea>
                </div>
                
                <!-- Submit Buttons -->
                <div class="flex items-center justify-end space-x-3 pt-4">
                    <button type="button" onclick="closeUserModal()" class="bg-gray-300 hover:bg-gray-400 text-gray-700 px-4 py-2 rounded-lg">
                        Cancel
                    </button>
                    <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg">
                        <span id="submit-text">Create User</span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function openUserModal(userId = null) {
    const modal = document.getElementById('user-modal');
    const form = document.getElementById('user-form');
    const title = document.getElementById('modal-title');
    const submitText = document.getElementById('submit-text');
    
    if (userId) {
        // Edit mode
        title.textContent = 'Edit User';
        submitText.textContent = 'Update User';
        form.action = `/admin/users/${userId}`;
        document.getElementById('user-id').value = 'PUT';
        
        // Load user data
        fetch(`/admin/users/${userId}`)
            .then(response => response.json())
            .then(user => {
                document.getElementById('modal-name').value = user.name;
                document.getElementById('modal-email').value = user.email;
                document.getElementById('modal-role').value = user.role;
                document.getElementById('modal-status').value = user.is_active ? '1' : '0';
                document.getElementById('modal-bio').value = user.bio || '';
            });
    } else {
        // Create mode
        title.textContent = 'Add New User';
        submitText.textContent = 'Create User';
        form.action = '{{ route("admin.users.store") }}';
        document.getElementById('user-id').value = '';
        form.reset();
    }
    
    modal.classList.remove('hidden');
}

function closeUserModal() {
    document.getElementById('user-modal').classList.add('hidden');
}

function viewUser(userId) {
    // Implement view user functionality
    console.log('View user:', userId);
}

function editUser(userId) {
    openUserModal(userId);
}

function toggleUserStatus(userId) {
    fetch(`/admin/users/${userId}/toggle-status`, {
        method: 'PATCH',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            'Content-Type': 'application/json',
        },
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            location.reload();
        } else {
            alert('Error updating user status');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Error updating user status');
    });
}

// Bulk actions
document.getElementById('bulk-action').addEventListener('change', function() {
    const roleSelect = document.getElementById('bulk-role');
    if (this.value === 'change-role') {
        roleSelect.classList.remove('hidden');
    } else {
        roleSelect.classList.add('hidden');
    }
});

function executeBulkAction() {
    const action = document.getElementById('bulk-action').value;
    const checkboxes = document.querySelectorAll('tbody input[type="checkbox"]:checked');
    const userIds = Array.from(checkboxes).map(cb => cb.value);
    
    if (!action) {
        alert('Please select an action');
        return;
    }
    
    if (userIds.length === 0) {
        alert('Please select at least one user');
        return;
    }
    
    if (action === 'delete' && !confirm('Are you sure you want to delete the selected users?')) {
        return;
    }
    
    const data = { user_ids: userIds };
    if (action === 'change-role') {
        data.role = document.getElementById('bulk-role').value;
    }
    
    fetch(`/admin/users/bulk-${action}`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: JSON.stringify(data)
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            location.reload();
        } else {
            alert('Error executing bulk action');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Error executing bulk action');
    });
}

// Select all checkbox functionality
document.querySelector('thead input[type="checkbox"]').addEventListener('change', function() {
    const checkboxes = document.querySelectorAll('tbody input[type="checkbox"]');
    checkboxes.forEach(cb => cb.checked = this.checked);
});
</script>
@endpush