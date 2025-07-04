<?php

namespace App\Repositories;

use App\Models\User;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UserRepository extends BaseRepository
{
    public function __construct(User $model)
    {
        parent::__construct($model);
    }

    /**
     * Get active users
     */
    public function getActive($limit = null): Collection
    {
        $cacheKey = $this->getCacheKey('active', [$limit]);
        
        return Cache::remember($cacheKey, $this->cacheTime, function () use ($limit) {
            $query = $this->model->where('is_active', true)
                ->withCount('posts')
                ->orderBy('name');
            
            if ($limit) {
                $query->limit($limit);
            }
            
            return $query->get();
        });
    }

    /**
     * Get users by role
     */
    public function getByRole($role, $limit = null): Collection
    {
        $cacheKey = $this->getCacheKey('by_role', [$role, $limit]);
        
        return Cache::remember($cacheKey, $this->cacheTime, function () use ($role, $limit) {
            $query = $this->model->where('role', $role)
                ->withCount('posts')
                ->orderBy('name');
            
            if ($limit) {
                $query->limit($limit);
            }
            
            return $query->get();
        });
    }

    /**
     * Get admins
     */
    public function getAdmins(): Collection
    {
        return $this->getByRole('admin');
    }

    /**
     * Get authors
     */
    public function getAuthors(): Collection
    {
        return $this->getByRole('author');
    }

    /**
     * Get users with post count
     */
    public function getWithPostCount(): Collection
    {
        $cacheKey = $this->getCacheKey('with_post_count');
        
        return Cache::remember($cacheKey, $this->cacheTime, function () {
            return $this->model->withCount('posts')
                ->orderBy('name')
                ->get();
        });
    }

    /**
     * Get users for dropdown
     */
    public function getForDropdown($role = null): array
    {
        $cacheKey = $this->getCacheKey('for_dropdown', [$role]);
        
        return Cache::remember($cacheKey, $this->cacheTime, function () use ($role) {
            $query = $this->model->where('is_active', true);
            
            if ($role) {
                $query->where('role', $role);
            }
            
            return $query->orderBy('name')
                ->pluck('name', 'id')
                ->toArray();
        });
    }

    /**
     * Find user by email
     */
    public function findByEmail($email): ?User
    {
        $cacheKey = $this->getCacheKey('by_email', [$email]);
        
        return Cache::remember($cacheKey, $this->cacheTime, function () use ($email) {
            return $this->model->where('email', $email)->first();
        });
    }

    /**
     * Get users with pagination and filters
     */
    public function getPaginatedWithFilters(array $filters = [], $perPage = 15): LengthAwarePaginator
    {
        $query = $this->model->withCount('posts');
        
        // Apply search
        if (!empty($filters['search'])) {
            $search = $filters['search'];
            $query->where(function ($q) use ($search) {
                $q->where('name', 'LIKE', "%{$search}%")
                  ->orWhere('email', 'LIKE', "%{$search}%")
                  ->orWhere('bio', 'LIKE', "%{$search}%");
            });
        }
        
        // Filter by role
        if (!empty($filters['role'])) {
            $query->where('role', $filters['role']);
        }
        
        // Filter by status
        if (isset($filters['is_active'])) {
            $query->where('is_active', $filters['is_active']);
        }
        
        // Filter by email verification
        if (isset($filters['email_verified'])) {
            if ($filters['email_verified']) {
                $query->whereNotNull('email_verified_at');
            } else {
                $query->whereNull('email_verified_at');
            }
        }
        
        // Date range filter
        if (!empty($filters['date_from'])) {
            $query->whereDate('created_at', '>=', $filters['date_from']);
        }
        
        if (!empty($filters['date_to'])) {
            $query->whereDate('created_at', '<=', $filters['date_to']);
        }
        
        // Last login filter
        if (!empty($filters['last_login_from'])) {
            $query->whereDate('last_login_at', '>=', $filters['last_login_from']);
        }
        
        if (!empty($filters['last_login_to'])) {
            $query->whereDate('last_login_at', '<=', $filters['last_login_to']);
        }
        
        // Sorting
        $sortBy = $filters['sort_by'] ?? 'created_at';
        $sortOrder = $filters['sort_order'] ?? 'desc';
        $query->orderBy($sortBy, $sortOrder);
        
        return $query->paginate($perPage);
    }

    /**
     * Search users
     */
    public function search($query, $fields = [], $limit = null): Collection
    {
        $modelQuery = $this->model->newQuery();
        
        // If specific fields are provided, search in those fields
        if (!empty($fields)) {
            $modelQuery->where(function ($q) use ($query, $fields) {
                foreach ($fields as $field) {
                    $q->orWhere($field, 'LIKE', "%{$query}%");
                }
            });
        } else {
            // Default search in name, email, and bio
            $modelQuery->where(function ($q) use ($query) {
                $q->where('name', 'LIKE', "%{$query}%")
                  ->orWhere('email', 'LIKE', "%{$query}%")
                  ->orWhere('bio', 'LIKE', "%{$query}%");
            });
        }
        
        // Apply limit if provided
        if ($limit) {
            $modelQuery->limit($limit);
        }
        
        return $modelQuery->withCount('posts')
            ->orderBy('name')
            ->get();
    }

    /**
     * Get top authors by post count
     */
    public function getTopAuthors($limit = 10): Collection
    {
        $cacheKey = $this->getCacheKey('top_authors', [$limit]);
        
        return Cache::remember($cacheKey, $this->cacheTime, function () use ($limit) {
            return $this->model->where('is_active', true)
                ->withCount('posts')
                ->orderBy('posts_count', 'desc')
                ->limit($limit)
                ->get();
        });
    }

    /**
     * Get recently active users
     */
    public function getRecentlyActive($limit = 10): Collection
    {
        $cacheKey = $this->getCacheKey('recently_active', [$limit]);
        
        return Cache::remember($cacheKey, $this->cacheTime, function () use ($limit) {
            return $this->model->where('is_active', true)
                ->whereNotNull('last_login_at')
                ->orderBy('last_login_at', 'desc')
                ->limit($limit)
                ->get();
        });
    }

    /**
     * Get new users (registered recently)
     */
    public function getNewUsers($days = 7, $limit = 10): Collection
    {
        $cacheKey = $this->getCacheKey('new_users', [$days, $limit]);
        
        return Cache::remember($cacheKey, $this->cacheTime, function () use ($days, $limit) {
            return $this->model->where('created_at', '>=', now()->subDays($days))
                ->orderBy('created_at', 'desc')
                ->limit($limit)
                ->get();
        });
    }

    /**
     * Create user with hashed password
     */
    public function createWithHashedPassword(array $data): User
    {
        if (isset($data['password'])) {
            $data['password'] = Hash::make($data['password']);
        }
        
        DB::beginTransaction();
        
        try {
            $user = $this->create($data);
            
            $this->clearCache();
            
            DB::commit();
            
            return $user;
        } catch (\Exception $e) {
            DB::rollback();
            throw $e;
        }
    }

    /**
     * Update user password
     */
    public function updatePassword($id, $password): bool
    {
        DB::beginTransaction();
        
        try {
            $updated = $this->model->where('id', $id)
                ->update(['password' => Hash::make($password)]);
            
            $this->clearCache();
            
            DB::commit();
            
            return $updated > 0;
        } catch (\Exception $e) {
            DB::rollback();
            throw $e;
        }
    }

    /**
     * Update last login
     */
    public function updateLastLogin($id): bool
    {
        return $this->model->where('id', $id)
            ->update(['last_login_at' => now()]);
    }

    /**
     * Bulk activate users
     */
    public function bulkActivate(array $ids): int
    {
        DB::beginTransaction();
        
        try {
            $updated = $this->model->whereIn('id', $ids)
                ->update(['is_active' => true]);
            
            $this->clearCache();
            
            DB::commit();
            
            return $updated;
        } catch (\Exception $e) {
            DB::rollback();
            throw $e;
        }
    }

    /**
     * Bulk deactivate users
     */
    public function bulkDeactivate(array $ids): int
    {
        DB::beginTransaction();
        
        try {
            $updated = $this->model->whereIn('id', $ids)
                ->update(['is_active' => false]);
            
            $this->clearCache();
            
            DB::commit();
            
            return $updated;
        } catch (\Exception $e) {
            DB::rollback();
            throw $e;
        }
    }

    /**
     * Bulk change role
     */
    public function bulkChangeRole(array $ids, $role): int
    {
        DB::beginTransaction();
        
        try {
            $updated = $this->model->whereIn('id', $ids)
                ->update(['role' => $role]);
            
            $this->clearCache();
            
            DB::commit();
            
            return $updated;
        } catch (\Exception $e) {
            DB::rollback();
            throw $e;
        }
    }

    /**
     * Get users count by role
     */
    public function getCountByRole(): array
    {
        $cacheKey = $this->getCacheKey('count_by_role');
        
        return Cache::remember($cacheKey, $this->cacheTime, function () {
            return $this->model->select('role', DB::raw('count(*) as count'))
                ->groupBy('role')
                ->pluck('count', 'role')
                ->toArray();
        });
    }

    /**
     * Get users count by month
     */
    public function getCountByMonth($year = null): array
    {
        $year = $year ?? now()->year;
        $cacheKey = $this->getCacheKey('count_by_month', [$year]);
        
        return Cache::remember($cacheKey, $this->cacheTime, function () use ($year) {
            return $this->model->selectRaw('MONTH(created_at) as month, COUNT(*) as count')
                ->whereYear('created_at', $year)
                ->groupBy('month')
                ->orderBy('month')
                ->pluck('count', 'month')
                ->toArray();
        });
    }

    /**
     * Check if email is unique
     */
    public function isEmailUnique($email, $excludeId = null): bool
    {
        $query = $this->model->where('email', $email);
        
        if ($excludeId) {
            $query->where('id', '!=', $excludeId);
        }
        
        return !$query->exists();
    }

    /**
     * Get users statistics
     */
    public function getStatistics(): array
    {
        $cacheKey = $this->getCacheKey('statistics');
        
        return Cache::remember($cacheKey, $this->cacheTime, function () {
            $base = parent::getStatistics();
            
            return array_merge($base, [
                'active' => $this->model->where('is_active', true)->count(),
                'inactive' => $this->model->where('is_active', false)->count(),
                'verified' => $this->model->whereNotNull('email_verified_at')->count(),
                'unverified' => $this->model->whereNull('email_verified_at')->count(),
                'by_role' => $this->getCountByRole(),
                'with_posts' => $this->model->has('posts')->count(),
                'without_posts' => $this->model->doesntHave('posts')->count(),
                'avg_posts_per_user' => round($this->model->withCount('posts')->avg('posts_count'), 2),
                'top_author' => $this->model->withCount('posts')
                    ->orderBy('posts_count', 'desc')
                    ->first(['name', 'posts_count']),
                'recent_registrations' => $this->getNewUsers(30)->count(),
                'recent_logins' => $this->model->where('last_login_at', '>=', now()->subDays(7))->count(),
            ]);
        });
    }

    /**
     * Get user activity summary
     */
    public function getActivitySummary($userId): array
    {
        $cacheKey = $this->getCacheKey('activity_summary', [$userId]);
        
        return Cache::remember($cacheKey, $this->cacheTime, function () use ($userId) {
            $user = $this->find($userId);
            
            if (!$user) {
                return [];
            }
            
            return [
                'total_posts' => $user->posts()->count(),
                'published_posts' => $user->posts()->published()->count(),
                'draft_posts' => $user->posts()->where('is_published', false)->count(),
                'total_views' => $user->posts()->sum('views'),
                'avg_views_per_post' => round($user->posts()->avg('views'), 2),
                'most_viewed_post' => $user->posts()->orderBy('views', 'desc')->first(['title', 'views']),
                'recent_posts' => $user->posts()->latest()->limit(5)->get(['title', 'created_at', 'is_published']),
                'posts_by_month' => $user->posts()
                    ->selectRaw('MONTH(created_at) as month, COUNT(*) as count')
                    ->whereYear('created_at', now()->year)
                    ->groupBy('month')
                    ->orderBy('month')
                    ->pluck('count', 'month')
                    ->toArray(),
            ];
        });
    }
}