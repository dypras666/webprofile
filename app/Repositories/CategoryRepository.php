<?php

namespace App\Repositories;

use App\Models\Category;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class CategoryRepository extends BaseRepository
{
    public function __construct(Category $model)
    {
        parent::__construct($model);
    }

    /**
     * Get active categories
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
     * Get categories with post count
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
     * Get categories for dropdown
     */
    public function getForDropdown(): array
    {
        $cacheKey = $this->getCacheKey('for_dropdown');
        
        return Cache::remember($cacheKey, $this->cacheTime, function () {
            return $this->model->where('is_active', true)
                ->orderBy('name')
                ->pluck('name', 'id')
                ->toArray();
        });
    }

    /**
     * Get category by slug
     */
    public function findBySlug($slug): ?Category
    {
        $cacheKey = $this->getCacheKey('by_slug', [$slug]);
        
        return Cache::remember($cacheKey, $this->cacheTime, function () use ($slug) {
            return $this->model->where('slug', $slug)
                ->where('is_active', true)
                ->withCount('posts')
                ->first();
        });
    }

    /**
     * Get categories with pagination and filters
     */
    public function getPaginatedWithFilters(array $filters = [], $perPage = 15): LengthAwarePaginator
    {
        $query = $this->model->withCount('posts');
        
        // Apply search
        if (!empty($filters['search'])) {
            $search = $filters['search'];
            $query->where(function ($q) use ($search) {
                $q->where('name', 'LIKE', "%{$search}%")
                  ->orWhere('description', 'LIKE', "%{$search}%")
                  ->orWhere('slug', 'LIKE', "%{$search}%");
            });
        }
        
        // Filter by status
        if (isset($filters['is_active'])) {
            $query->where('is_active', $filters['is_active']);
        }
        
        // Filter by color
        if (!empty($filters['color'])) {
            $query->where('color', $filters['color']);
        }
        
        // Date range filter
        if (!empty($filters['date_from'])) {
            $query->whereDate('created_at', '>=', $filters['date_from']);
        }
        
        if (!empty($filters['date_to'])) {
            $query->whereDate('created_at', '<=', $filters['date_to']);
        }
        
        // Sorting
        $sortBy = $filters['sort_by'] ?? 'name';
        $sortOrder = $filters['sort_order'] ?? 'asc';
        $query->orderBy($sortBy, $sortOrder);
        
        return $query->paginate($perPage);
    }

    /**
     * Search categories
     */
    public function search($query, $filters = []): Collection
    {
        $modelQuery = $this->model->newQuery();
        
        // Search in name, description, and slug
        $modelQuery->where(function ($q) use ($query) {
            $q->where('name', 'LIKE', "%{$query}%")
              ->orWhere('description', 'LIKE', "%{$query}%")
              ->orWhere('slug', 'LIKE', "%{$query}%");
        });
        
        // Apply filters
        if (isset($filters['is_active'])) {
            $modelQuery->where('is_active', $filters['is_active']);
        }
        
        if (!empty($filters['color'])) {
            $modelQuery->where('color', $filters['color']);
        }
        
        return $modelQuery->withCount('posts')
            ->orderBy('name')
            ->get();
    }

    /**
     * Get popular categories (by post count)
     */
    public function getPopular($limit = 10): Collection
    {
        $cacheKey = $this->getCacheKey('popular', [$limit]);
        
        return Cache::remember($cacheKey, $this->cacheTime, function () use ($limit) {
            return $this->model->where('is_active', true)
                ->withCount('posts')
                ->orderBy('posts_count', 'desc')
                ->limit($limit)
                ->get();
        });
    }

    /**
     * Get categories with recent posts
     */
    public function getWithRecentPosts($limit = 5): Collection
    {
        $cacheKey = $this->getCacheKey('with_recent_posts', [$limit]);
        
        return Cache::remember($cacheKey, $this->cacheTime, function () use ($limit) {
            return $this->model->where('is_active', true)
                ->with(['posts' => function ($query) use ($limit) {
                    $query->published()
                        ->latest()
                        ->limit($limit)
                        ->with('user');
                }])
                ->withCount('posts')
                ->orderBy('name')
                ->get();
        });
    }

    /**
     * Get categories by color
     */
    public function getByColor($color): Collection
    {
        $cacheKey = $this->getCacheKey('by_color', [$color]);
        
        return Cache::remember($cacheKey, $this->cacheTime, function () use ($color) {
            return $this->model->where('color', $color)
                ->where('is_active', true)
                ->withCount('posts')
                ->orderBy('name')
                ->get();
        });
    }

    /**
     * Get all unique colors
     */
    public function getUniqueColors(): array
    {
        $cacheKey = $this->getCacheKey('unique_colors');
        
        return Cache::remember($cacheKey, $this->cacheTime, function () {
            return $this->model->whereNotNull('color')
                ->distinct()
                ->pluck('color')
                ->toArray();
        });
    }

    /**
     * Bulk activate categories
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
     * Bulk deactivate categories
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
     * Update category color
     */
    public function updateColor($id, $color): bool
    {
        DB::beginTransaction();
        
        try {
            $updated = $this->model->where('id', $id)
                ->update(['color' => $color]);
            
            $this->clearCache();
            
            DB::commit();
            
            return $updated > 0;
        } catch (\Exception $e) {
            DB::rollback();
            throw $e;
        }
    }

    /**
     * Get categories statistics
     */
    public function getStatistics(): array
    {
        $cacheKey = $this->getCacheKey('statistics');
        
        return Cache::remember($cacheKey, $this->cacheTime, function () {
            $base = parent::getStatistics();
            
            return array_merge($base, [
                'active' => $this->model->where('is_active', true)->count(),
                'inactive' => $this->model->where('is_active', false)->count(),
                'with_posts' => $this->model->has('posts')->count(),
                'without_posts' => $this->model->doesntHave('posts')->count(),
                'colors' => $this->getUniqueColors(),
                'avg_posts_per_category' => round($this->model->withCount('posts')->avg('posts_count'), 2),
                'most_popular' => $this->model->withCount('posts')
                    ->orderBy('posts_count', 'desc')
                    ->first(['name', 'posts_count']),
            ]);
        });
    }

    /**
     * Get category hierarchy (if using nested categories)
     */
    public function getHierarchy(): Collection
    {
        $cacheKey = $this->getCacheKey('hierarchy');
        
        return Cache::remember($cacheKey, $this->cacheTime, function () {
            return $this->model->where('is_active', true)
                ->withCount('posts')
                ->orderBy('name')
                ->get()
                ->groupBy('parent_id'); // Assuming you have parent_id field
        });
    }

    /**
     * Get breadcrumb for category
     */
    public function getBreadcrumb($categoryId): array
    {
        $cacheKey = $this->getCacheKey('breadcrumb', [$categoryId]);
        
        return Cache::remember($cacheKey, $this->cacheTime, function () use ($categoryId) {
            $breadcrumb = [];
            $category = $this->find($categoryId);
            
            while ($category) {
                array_unshift($breadcrumb, [
                    'id' => $category->id,
                    'name' => $category->name,
                    'slug' => $category->slug
                ]);
                
                // If you have parent relationship
                $category = $category->parent ?? null;
            }
            
            return $breadcrumb;
        });
    }

    /**
     * Check if slug is unique
     */
    public function isSlugUnique($slug, $excludeId = null): bool
    {
        $query = $this->model->where('slug', $slug);
        
        if ($excludeId) {
            $query->where('id', '!=', $excludeId);
        }
        
        return !$query->exists();
    }

    /**
     * Generate unique slug
     */
    public function generateUniqueSlug($name, $excludeId = null): string
    {
        $slug = \Str::slug($name);
        $originalSlug = $slug;
        $counter = 1;
        
        while (!$this->isSlugUnique($slug, $excludeId)) {
            $slug = $originalSlug . '-' . $counter;
            $counter++;
        }
        
        return $slug;
    }
}