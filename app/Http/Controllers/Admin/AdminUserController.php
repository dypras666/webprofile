<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Admin\BaseAdminController;
use App\Http\Requests\UserRequest;
use App\Services\UserService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class AdminUserController extends BaseAdminController
{
    protected $userService;

    public function __construct(UserService $userService)
    {
        parent::__construct();
        $this->userService = $userService;
        
        // Set permissions
        $this->middleware('permission:manage_users');
    }

    /**
     * Display a listing of users
     */
    public function index(Request $request): View
    {
        $filters = $this->getFilters($request, [
            'search', 'role', 'status', 'email_verified', 
            'date_from', 'date_to', 'last_login_from', 'last_login_to'
        ]);
        
        $users = $this->userService->getAllUsers($filters, $request->get('per_page', 15));
        $statistics = $this->userService->getStatistics();
        $roleStats = $this->userService->getCountByRole();
        
        return view('admin.users.index', compact('users', 'statistics', 'roleStats', 'filters'));
    }

    /**
     * Show the form for creating a new user
     */
    public function create(): View
    {
        $roles = $this->getUserRoles();
        
        return view('admin.users.create', compact('roles'));
    }

    /**
     * Store a newly created user
     */
    public function store(UserRequest $request): RedirectResponse
    {
        try {
            $data = $request->validated();
            $data['send_welcome_email'] = $request->boolean('send_welcome_email');
            
            $user = $this->userService->createUser($data);
            
            $this->logActivity('user_created', 'Created user: ' . $user->name, $user->id);
            
            return redirect()->route('admin.users.index')
                ->with('success', 'User created successfully.');
        } catch (\Exception $e) {
            return back()->withInput()
                ->with('error', 'Failed to create user: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified user
     */
    public function show($id): View
    {
        $user = $this->userService->findUser($id);
        
        if (!$user) {
            abort(404, 'User not found');
        }
        
        $dashboardData = $this->userService->getUserDashboardData($id);
        $activityLog = $this->userService->getUserActivityLog($id, 20);
        $permissions = $this->userService->getUserPermissions($id);
        
        return view('admin.users.show', compact('user', 'dashboardData', 'activityLog', 'permissions'));
    }

    /**
     * Show the form for editing the specified user
     */
    public function edit($id): View
    {
        $user = $this->userService->findUser($id);
        
        if (!$user) {
            abort(404, 'User not found');
        }
        
        $roles = $this->getUserRoles();
        
        return view('admin.users.edit', compact('user', 'roles'));
    }

    /**
     * Update the specified user
     */
    public function update(UserRequest $request, $id): RedirectResponse
    {
        try {
            $user = $this->userService->updateUser($id, $request->validated());
            
            if (!$user) {
                return back()->with('error', 'User not found.');
            }
            
            $this->logActivity('user_updated', 'Updated user: ' . $user->name, $user->id);
            
            return redirect()->route('admin.users.index')
                ->with('success', 'User updated successfully.');
        } catch (\Exception $e) {
            return back()->withInput()
                ->with('error', 'Failed to update user: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified user
     */
    public function destroy($id): RedirectResponse
    {
        try {
            $user = $this->userService->findUser($id);
            
            if (!$user) {
                return back()->with('error', 'User not found.');
            }
            
            $name = $user->name;
            $deleted = $this->userService->deleteUser($id);
            
            if ($deleted) {
                $this->logActivity('user_deleted', 'Deleted user: ' . $name, $id);
                return back()->with('success', 'User deleted successfully.');
            }
            
            return back()->with('error', 'Failed to delete user.');
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to delete user: ' . $e->getMessage());
        }
    }

    /**
     * Bulk actions for users
     */
    public function bulkAction(Request $request): JsonResponse
    {
        $request->validate([
            'action' => 'required|in:activate,deactivate,delete,change_role,verify_email,unverify_email',
            'ids' => 'required|array|min:1',
            'ids.*' => 'integer|exists:users,id',
            'role' => 'required_if:action,change_role|in:admin,editor,author,user'
        ]);
        
        try {
            $action = $request->action;
            $ids = $request->ids;
            $count = 0;
            
            switch ($action) {
                case 'activate':
                    $count = $this->userService->bulkActivate($ids);
                    $message = "Activated {$count} users";
                    break;
                    
                case 'deactivate':
                    $count = $this->userService->bulkDeactivate($ids);
                    $message = "Deactivated {$count} users";
                    break;
                    
                case 'delete':
                    $count = $this->userService->bulkDelete($ids);
                    $message = "Deleted {$count} users";
                    break;
                    
                case 'change_role':
                    $count = $this->userService->bulkChangeRole($ids, $request->role);
                    $message = "Changed role for {$count} users to {$request->role}";
                    break;
                    
                case 'verify_email':
                    $count = 0;
                    foreach ($ids as $id) {
                        if ($this->userService->verifyEmail($id)) {
                            $count++;
                        }
                    }
                    $message = "Verified email for {$count} users";
                    break;
                    
                case 'unverify_email':
                    $count = 0;
                    foreach ($ids as $id) {
                        if ($this->userService->unverifyEmail($id)) {
                            $count++;
                        }
                    }
                    $message = "Unverified email for {$count} users";
                    break;
                    
                default:
                    return $this->errorResponse('Invalid action');
            }
            
            $this->logActivity('users_bulk_' . $action, $message, null, ['ids' => $ids]);
            
            return $this->successResponse($message, ['count' => $count]);
        } catch (\Exception $e) {
            return $this->errorResponse('Bulk action failed: ' . $e->getMessage());
        }
    }

    /**
     * Reset user password
     */
    public function resetPassword($id): RedirectResponse
    {
        try {
            $user = $this->userService->findUser($id);
            
            if (!$user) {
                return back()->with('error', 'User not found.');
            }
            
            $newPassword = $this->userService->resetPassword($id);
            
            $this->logActivity('user_password_reset', 'Reset password for user: ' . $user->name, $user->id);
            
            return back()->with('success', 'Password reset successfully. New password has been sent to the user.');
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to reset password: ' . $e->getMessage());
        }
    }

    /**
     * Change user password
     */
    public function changePassword(Request $request, $id): JsonResponse
    {
        $request->validate([
            'current_password' => 'required_if:user_id,' . Auth::id(),
            'new_password' => 'required|min:8|confirmed'
        ]);
        
        try {
            $user = $this->userService->findUser($id);
            
            if (!$user) {
                return $this->errorResponse('User not found');
            }
            
            // If changing own password, require current password
            if ($id == Auth::id()) {
                $success = $this->userService->changePassword(
                    $id, 
                    $request->current_password, 
                    $request->new_password
                );
            } else {
                // Admin changing another user's password
                $success = $this->userService->resetPassword($id);
            }
            
            if ($success) {
                $this->logActivity('user_password_changed', 'Changed password for user: ' . $user->name, $user->id);
                return $this->successResponse('Password changed successfully');
            }
            
            return $this->errorResponse('Failed to change password');
        } catch (\Exception $e) {
            return $this->errorResponse('Failed to change password: ' . $e->getMessage());
        }
    }

    /**
     * Get user statistics
     */
    public function statistics(): JsonResponse
    {
        try {
            $stats = $this->userService->getStatistics();
            $monthlyStats = $this->userService->getCountByMonth();
            $roleStats = $this->userService->getCountByRole();
            
            return $this->successResponse('Statistics retrieved', [
                'general' => $stats,
                'monthly' => $monthlyStats,
                'by_role' => $roleStats
            ]);
        } catch (\Exception $e) {
            return $this->errorResponse('Failed to get statistics: ' . $e->getMessage());
        }
    }

    /**
     * Export users
     */
    public function export(Request $request)
    {
        try {
            $format = $request->get('format', 'csv');
            
            if ($format === 'csv') {
                $csvData = $this->userService->exportToCSV();
                
                return response($csvData)
                    ->header('Content-Type', 'text/csv')
                    ->header('Content-Disposition', 'attachment; filename="users_' . date('Y-m-d') . '.csv"');
            }
            
            return back()->with('error', 'Unsupported export format.');
        } catch (\Exception $e) {
            return back()->with('error', 'Export failed: ' . $e->getMessage());
        }
    }

    /**
     * Search users (AJAX)
     */
    public function search(Request $request): JsonResponse
    {
        $request->validate([
            'query' => 'required|string|min:2',
            'limit' => 'integer|min:1|max:50'
        ]);
        
        try {
            $users = $this->userService->searchUsers(
                $request->query,
                [],
                $request->get('limit', 10)
            );
            
            $results = $users->map(function ($user) {
                return [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'role' => $user->role,
                    'is_active' => $user->is_active,
                    'email_verified' => $user->email_verified_at ? true : false,
                    'created_at' => $user->created_at->format('M d, Y'),
                    'url' => route('admin.users.edit', $user->id)
                ];
            });
            
            return $this->successResponse('Search completed', $results);
        } catch (\Exception $e) {
            return $this->errorResponse('Search failed: ' . $e->getMessage());
        }
    }

    /**
     * Get top authors
     */
    public function topAuthors(): JsonResponse
    {
        try {
            $topAuthors = $this->userService->getTopAuthors(10);
            
            $results = $topAuthors->map(function ($user) {
                return [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'posts_count' => $user->posts_count ?? 0,
                    'role' => $user->role,
                    'created_at' => $user->created_at->format('M d, Y'),
                    'url' => route('admin.users.show', $user->id)
                ];
            });
            
            return $this->successResponse('Top authors retrieved', $results);
        } catch (\Exception $e) {
            return $this->errorResponse('Failed to get top authors: ' . $e->getMessage());
        }
    }

    /**
     * Get recently active users
     */
    public function recentlyActive(): JsonResponse
    {
        try {
            $recentUsers = $this->userService->getRecentlyActiveUsers(10);
            
            $results = $recentUsers->map(function ($user) {
                return [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'role' => $user->role,
                    'last_login_at' => $user->last_login_at ? $user->last_login_at->format('M d, Y H:i') : 'Never',
                    'url' => route('admin.users.show', $user->id)
                ];
            });
            
            return $this->successResponse('Recently active users retrieved', $results);
        } catch (\Exception $e) {
            return $this->errorResponse('Failed to get recently active users: ' . $e->getMessage());
        }
    }

    /**
     * Get new users
     */
    public function newUsers(Request $request): JsonResponse
    {
        try {
            $days = $request->get('days', 7);
            $newUsers = $this->userService->getNewUsers($days, 10);
            
            $results = $newUsers->map(function ($user) {
                return [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'role' => $user->role,
                    'created_at' => $user->created_at->format('M d, Y H:i'),
                    'url' => route('admin.users.show', $user->id)
                ];
            });
            
            return $this->successResponse('New users retrieved', $results);
        } catch (\Exception $e) {
            return $this->errorResponse('Failed to get new users: ' . $e->getMessage());
        }
    }

    /**
     * Check email availability
     */
    public function checkEmail(Request $request): JsonResponse
    {
        $request->validate([
            'email' => 'required|email',
            'id' => 'nullable|integer|exists:users,id'
        ]);
        
        try {
            $user = $this->userService->findUserByEmail($request->email);
            $isAvailable = !$user || ($request->id && $user->id == $request->id);
            
            return $this->successResponse('Email checked', [
                'available' => $isAvailable,
                'email' => $request->email
            ]);
        } catch (\Exception $e) {
            return $this->errorResponse('Failed to check email: ' . $e->getMessage());
        }
    }

    /**
     * Get user permissions
     */
    public function permissions($id): JsonResponse
    {
        try {
            $permissions = $this->userService->getUserPermissions($id);
            
            return $this->successResponse('Permissions retrieved', $permissions);
        } catch (\Exception $e) {
            return $this->errorResponse('Failed to get permissions: ' . $e->getMessage());
        }
    }

    /**
     * Get user activity summary
     */
    public function activitySummary($id): JsonResponse
    {
        try {
            $summary = $this->userService->getUserActivitySummary($id);
            
            return $this->successResponse('Activity summary retrieved', $summary);
        } catch (\Exception $e) {
            return $this->errorResponse('Failed to get activity summary: ' . $e->getMessage());
        }
    }

    /**
     * Get user dashboard data
     */
    public function dashboardData($id): JsonResponse
    {
        try {
            $data = $this->userService->getUserDashboardData($id);
            
            return $this->successResponse('Dashboard data retrieved', $data);
        } catch (\Exception $e) {
            return $this->errorResponse('Failed to get dashboard data: ' . $e->getMessage());
        }
    }

    /**
     * Update user profile
     */
    public function updateProfile(Request $request, $id): JsonResponse
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'bio' => 'nullable|string|max:1000',
            'website' => 'nullable|url|max:255',
            'location' => 'nullable|string|max:255',
            'avatar' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048'
        ]);
        
        try {
            $user = $this->userService->updateProfile($id, $request->all());
            
            $this->logActivity('user_profile_updated', 'Updated profile for user: ' . $user->name, $user->id);
            
            return $this->successResponse('Profile updated successfully', [
                'user' => [
                    'id' => $user->id,
                    'name' => $user->name,
                    'bio' => $user->bio,
                    'website' => $user->website,
                    'location' => $user->location,
                    'avatar' => $user->avatar
                ]
            ]);
        } catch (\Exception $e) {
            return $this->errorResponse('Failed to update profile: ' . $e->getMessage());
        }
    }

    /**
     * Get available user roles
     */
    protected function getUserRoles(): array
    {
        return [
            'admin' => 'Administrator',
            'editor' => 'Editor',
            'author' => 'Author',
            'user' => 'User'
        ];
    }

    /**
     * Impersonate user (for testing/support)
     */
    public function impersonate($id): RedirectResponse
    {
        try {
            $user = $this->userService->findUser($id);
            
            if (!$user) {
                return back()->with('error', 'User not found.');
            }
            
            // Store original user ID in session
            session(['impersonate_original_user' => Auth::id()]);
            
            // Login as the target user
            Auth::login($user);
            
            $this->logActivity('user_impersonated', 'Impersonated user: ' . $user->name, $user->id);
            
            return redirect()->route('dashboard')
                ->with('info', 'You are now impersonating ' . $user->name . '. Click "Stop Impersonating" to return to your account.');
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to impersonate user: ' . $e->getMessage());
        }
    }

    /**
     * Stop impersonating
     */
    public function stopImpersonating(): RedirectResponse
    {
        try {
            $originalUserId = session('impersonate_original_user');
            
            if (!$originalUserId) {
                return redirect()->route('admin.users.index')
                    ->with('error', 'No impersonation session found.');
            }
            
            $originalUser = $this->userService->findUser($originalUserId);
            
            if (!$originalUser) {
                return redirect()->route('admin.users.index')
                    ->with('error', 'Original user not found.');
            }
            
            // Clear impersonation session
            session()->forget('impersonate_original_user');
            
            // Login back as original user
            Auth::login($originalUser);
            
            return redirect()->route('admin.users.index')
                ->with('success', 'Stopped impersonating. You are now back to your original account.');
        } catch (\Exception $e) {
            return redirect()->route('admin.users.index')
                ->with('error', 'Failed to stop impersonating: ' . $e->getMessage());
        }
    }
}