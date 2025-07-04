<?php

namespace App\Services;

use App\Models\User;
use App\Repositories\UserRepository;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Illuminate\Http\UploadedFile;

class UserService
{
    protected $userRepository;
    protected $fileUploadService;

    public function __construct(
        UserRepository $userRepository,
        FileUploadService $fileUploadService
    ) {
        $this->userRepository = $userRepository;
        $this->fileUploadService = $fileUploadService;
    }

    /**
     * Get all users with filters and pagination
     */
    public function getAllUsers(array $filters = [], $perPage = 15): LengthAwarePaginator
    {
        return $this->userRepository->getPaginatedWithFilters($filters, $perPage);
    }

    /**
     * Get active users
     */
    public function getActiveUsers($limit = null): Collection
    {
        return $this->userRepository->getActive($limit);
    }

    /**
     * Get users by role
     */
    public function getUsersByRole($role, $limit = null): Collection
    {
        return $this->userRepository->getByRole($role, $limit);
    }

    /**
     * Get admins
     */
    public function getAdmins(): Collection
    {
        return $this->userRepository->getAdmins();
    }

    /**
     * Get authors
     */
    public function getAuthors(): Collection
    {
        return $this->userRepository->getAuthors();
    }

    /**
     * Get users for dropdown
     */
    public function getUsersForDropdown($role = null): array
    {
        return $this->userRepository->getForDropdown($role);
    }

    /**
     * Get top authors
     */
    public function getTopAuthors($limit = 10): Collection
    {
        return $this->userRepository->getTopAuthors($limit);
    }

    /**
     * Get recently active users
     */
    public function getRecentlyActiveUsers($limit = 10): Collection
    {
        return $this->userRepository->getRecentlyActive($limit);
    }

    /**
     * Get new users
     */
    public function getNewUsers($days = 7, $limit = 10): Collection
    {
        return $this->userRepository->getNewUsers($days, $limit);
    }

    /**
     * Find user by ID
     */
    public function findUser($id): ?User
    {
        return $this->userRepository->find($id);
    }

    /**
     * Find user by email
     */
    public function findUserByEmail($email): ?User
    {
        return $this->userRepository->findByEmail($email);
    }

    /**
     * Create new user
     */
    public function createUser(array $data): User
    {
        DB::beginTransaction();
        
        try {
            // Validate email uniqueness
            if (!$this->userRepository->isEmailUnique($data['email'])) {
                throw new \Exception('Email already exists');
            }
            
            // Handle avatar upload
            if (isset($data['avatar']) && $data['avatar'] instanceof UploadedFile) {
                $media = $this->fileUploadService->upload($data['avatar'], 'users/avatars');
                $data['avatar'] = $media->path;
            }
            
            // Set default values
            $data['is_active'] = $data['is_active'] ?? true;
            $data['role'] = $data['role'] ?? 'user';
            $data['email_verified_at'] = $data['email_verified_at'] ?? now();
            
            // Generate random password if not provided
            if (empty($data['password'])) {
                $data['password'] = Str::random(12);
                $data['temp_password'] = true;
            }
            
            $user = $this->userRepository->createWithHashedPassword($data);
            
            // Send welcome email if specified
            if ($data['send_welcome_email'] ?? false) {
                $this->sendWelcomeEmail($user, $data['password'] ?? null);
            }
            
            // Clear related caches
            $this->clearRelatedCaches();
            
            DB::commit();
            
            return $user;
        } catch (\Exception $e) {
            DB::rollback();
            throw $e;
        }
    }

    /**
     * Update user
     */
    public function updateUser($id, array $data): User
    {
        DB::beginTransaction();
        
        try {
            $user = $this->userRepository->find($id);
            
            if (!$user) {
                throw new \Exception('User not found');
            }
            
            // Validate email uniqueness if email is being changed
            if (isset($data['email']) && $data['email'] !== $user->email) {
                if (!$this->userRepository->isEmailUnique($data['email'], $id)) {
                    throw new \Exception('Email already exists');
                }
            }
            
            // Handle avatar upload
            if (isset($data['avatar']) && $data['avatar'] instanceof UploadedFile) {
                // Delete old avatar if exists
                if ($user->avatar) {
                    $this->fileUploadService->delete($user->avatar);
                }
                
                $media = $this->fileUploadService->upload($data['avatar'], 'users/avatars');
                $data['avatar'] = $media->path;
            }
            
            // Handle password update separately if provided
            if (isset($data['password']) && !empty($data['password'])) {
                $this->userRepository->updatePassword($id, $data['password']);
                unset($data['password']); // Remove from data array to avoid double processing
            }
            
            $updatedUser = $this->userRepository->update($id, $data);
            
            // Clear related caches
            $this->clearRelatedCaches();
            
            DB::commit();
            
            return $updatedUser;
        } catch (\Exception $e) {
            DB::rollback();
            throw $e;
        }
    }

    /**
     * Delete user
     */
    public function deleteUser($id): bool
    {
        DB::beginTransaction();
        
        try {
            $user = $this->userRepository->find($id);
            
            if (!$user) {
                return false;
            }
            
            // Prevent deleting current user
            if ($id == Auth::id()) {
                throw new \Exception('Cannot delete your own account');
            }
            
            // Check if user has posts
            if ($user->posts()->count() > 0) {
                throw new \Exception('Cannot delete user that has posts. Please reassign or delete the posts first.');
            }
            
            // Delete avatar if exists
            if ($user->avatar) {
                $this->fileUploadService->delete($user->avatar);
            }
            
            $deleted = $this->userRepository->delete($id);
            
            // Clear related caches
            $this->clearRelatedCaches();
            
            DB::commit();
            
            return $deleted;
        } catch (\Exception $e) {
            DB::rollback();
            throw $e;
        }
    }

    /**
     * Bulk activate users
     */
    public function bulkActivate(array $ids): int
    {
        $updated = $this->userRepository->bulkActivate($ids);
        $this->clearRelatedCaches();
        return $updated;
    }

    /**
     * Bulk deactivate users
     */
    public function bulkDeactivate(array $ids): int
    {
        // Prevent deactivating current user
        if (in_array(Auth::id(), $ids)) {
            throw new \Exception('Cannot deactivate your own account');
        }
        
        $updated = $this->userRepository->bulkDeactivate($ids);
        $this->clearRelatedCaches();
        return $updated;
    }

    /**
     * Bulk change role
     */
    public function bulkChangeRole(array $ids, $role): int
    {
        // Prevent changing own role to non-admin
        if (in_array(Auth::id(), $ids) && $role !== 'admin') {
            throw new \Exception('Cannot change your own role from admin');
        }
        
        $updated = $this->userRepository->bulkChangeRole($ids, $role);
        $this->clearRelatedCaches();
        return $updated;
    }

    /**
     * Bulk delete users
     */
    public function bulkDelete(array $ids): int
    {
        DB::beginTransaction();
        
        try {
            // Prevent deleting current user
            if (in_array(Auth::id(), $ids)) {
                throw new \Exception('Cannot delete your own account');
            }
            
            // Check if any user has posts
            $usersWithPosts = $this->userRepository->whereIn('id', $ids)
                ->has('posts')
                ->pluck('name')
                ->toArray();
            
            if (!empty($usersWithPosts)) {
                throw new \Exception('Cannot delete users that have posts: ' . implode(', ', $usersWithPosts));
            }
            
            // Delete avatars
            $users = $this->userRepository->whereIn('id', $ids);
            foreach ($users as $user) {
                if ($user->avatar) {
                    $this->fileUploadService->delete($user->avatar);
                }
            }
            
            $deleted = $this->userRepository->bulkDelete($ids);
            
            // Clear related caches
            $this->clearRelatedCaches();
            
            DB::commit();
            
            return $deleted;
        } catch (\Exception $e) {
            DB::rollback();
            throw $e;
        }
    }

    /**
     * Search users
     */
    public function searchUsers($query, array $filters = []): Collection
    {
        return $this->userRepository->search($query, $filters);
    }

    /**
     * Update last login
     */
    public function updateLastLogin($id): bool
    {
        return $this->userRepository->updateLastLogin($id);
    }

    /**
     * Change password
     */
    public function changePassword($id, $currentPassword, $newPassword): bool
    {
        $user = $this->userRepository->find($id);
        
        if (!$user) {
            throw new \Exception('User not found');
        }
        
        if (!Hash::check($currentPassword, $user->password)) {
            throw new \Exception('Current password is incorrect');
        }
        
        return $this->userRepository->updatePassword($id, $newPassword);
    }

    /**
     * Reset password
     */
    public function resetPassword($id): string
    {
        $newPassword = Str::random(12);
        
        if ($this->userRepository->updatePassword($id, $newPassword)) {
            $user = $this->userRepository->find($id);
            
            // Send password reset email
            $this->sendPasswordResetEmail($user, $newPassword);
            
            return $newPassword;
        }
        
        throw new \Exception('Failed to reset password');
    }

    /**
     * Get user statistics
     */
    public function getStatistics(): array
    {
        return $this->userRepository->getStatistics();
    }

    /**
     * Get users count by role
     */
    public function getCountByRole(): array
    {
        return $this->userRepository->getCountByRole();
    }

    /**
     * Get users count by month
     */
    public function getCountByMonth($year = null): array
    {
        return $this->userRepository->getCountByMonth($year);
    }

    /**
     * Get user activity summary
     */
    public function getUserActivitySummary($userId): array
    {
        return $this->userRepository->getActivitySummary($userId);
    }

    /**
     * Clear related caches
     */
    protected function clearRelatedCaches(): void
    {
        Cache::tags(['users', 'posts', 'admin'])->flush();
    }

    /**
     * Send welcome email
     */
    protected function sendWelcomeEmail(User $user, $password = null): void
    {
        // Implementation depends on your mail setup
        // This is a placeholder for the actual email sending logic
        
        try {
            // Mail::to($user->email)->send(new WelcomeEmail($user, $password));
        } catch (\Exception $e) {
            // Log email sending error but don't fail the user creation
            \Log::error('Failed to send welcome email: ' . $e->getMessage());
        }
    }

    /**
     * Send password reset email
     */
    protected function sendPasswordResetEmail(User $user, $newPassword): void
    {
        try {
            // Mail::to($user->email)->send(new PasswordResetEmail($user, $newPassword));
        } catch (\Exception $e) {
            \Log::error('Failed to send password reset email: ' . $e->getMessage());
        }
    }

    /**
     * Update user profile
     */
    public function updateProfile($id, array $data): User
    {
        // Filter only allowed profile fields
        $allowedFields = ['name', 'bio', 'website', 'location', 'avatar'];
        $profileData = array_intersect_key($data, array_flip($allowedFields));
        
        return $this->updateUser($id, $profileData);
    }

    /**
     * Verify user email
     */
    public function verifyEmail($id): bool
    {
        return $this->userRepository->update($id, [
            'email_verified_at' => now()
        ]);
    }

    /**
     * Unverify user email
     */
    public function unverifyEmail($id): bool
    {
        return $this->userRepository->update($id, [
            'email_verified_at' => null
        ]);
    }

    /**
     * Get user dashboard data
     */
    public function getUserDashboardData($userId): array
    {
        $user = $this->userRepository->find($userId);
        
        if (!$user) {
            return [];
        }
        
        $posts = $user->posts();
        
        return [
            'user' => $user,
            'total_posts' => $posts->count(),
            'published_posts' => $posts->published()->count(),
            'draft_posts' => $posts->where('is_published', false)->count(),
            'total_views' => $posts->sum('views'),
            'recent_posts' => $posts->latest()->limit(5)->get(),
            'popular_posts' => $posts->orderBy('views', 'desc')->limit(5)->get(),
            'posts_this_month' => $posts->whereMonth('created_at', now()->month)->count(),
            'views_this_month' => $posts->whereMonth('created_at', now()->month)->sum('views'),
        ];
    }

    /**
     * Export users to CSV
     */
    public function exportToCSV(): string
    {
        $users = $this->userRepository->all();
        
        $csvData = "ID,Name,Email,Role,Status,Posts Count,Last Login,Created At\n";
        
        foreach ($users as $user) {
            $csvData .= sprintf(
                "%d,\"%s\",\"%s\",\"%s\",%s,%d,\"%s\",\"%s\"\n",
                $user->id,
                str_replace('"', '""', $user->name),
                $user->email,
                $user->role,
                $user->is_active ? 'Active' : 'Inactive',
                $user->posts_count ?? 0,
                $user->last_login_at ? $user->last_login_at->format('Y-m-d H:i:s') : 'Never',
                $user->created_at->format('Y-m-d H:i:s')
            );
        }
        
        return $csvData;
    }

    /**
     * Get user permissions
     */
    public function getUserPermissions($userId): array
    {
        $user = $this->userRepository->find($userId);
        
        if (!$user) {
            return [];
        }
        
        // Define role-based permissions
        $permissions = [
            'admin' => [
                'manage_users', 'manage_posts', 'manage_categories', 
                'manage_media', 'manage_settings', 'view_analytics'
            ],
            'editor' => [
                'manage_posts', 'manage_categories', 'manage_media'
            ],
            'author' => [
                'create_posts', 'edit_own_posts', 'upload_media'
            ],
            'user' => [
                'view_posts'
            ]
        ];
        
        return $permissions[$user->role] ?? [];
    }

    /**
     * Check if user has permission
     */
    public function hasPermission($userId, $permission): bool
    {
        $userPermissions = $this->getUserPermissions($userId);
        return in_array($permission, $userPermissions);
    }

    /**
     * Get user activity log
     */
    public function getUserActivityLog($userId, $limit = 50): array
    {
        // This would typically come from an activity log table
        // For now, return basic activity based on posts
        
        $user = $this->userRepository->find($userId);
        
        if (!$user) {
            return [];
        }
        
        $activities = [];
        
        // Get recent posts as activities
        $recentPosts = $user->posts()->latest()->limit($limit)->get();
        
        foreach ($recentPosts as $post) {
            $activities[] = [
                'type' => 'post_created',
                'description' => 'Created post: ' . $post->title,
                'timestamp' => $post->created_at,
                'data' => ['post_id' => $post->id]
            ];
        }
        
        // Sort by timestamp
        usort($activities, function ($a, $b) {
            return $b['timestamp'] <=> $a['timestamp'];
        });
        
        return array_slice($activities, 0, $limit);
    }
}