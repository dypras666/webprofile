<?php

namespace App\Repositories;

use App\Models\Post;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class PostRepository extends BaseRepository
{
    public function __construct(Post $model)
    {
        parent::__construct($model);
    }

    /**
     * Get published posts
     */
    public function getPublished($limit = null): Collection
    {
        $cacheKey = $this->getCacheKey('published', [$limit]);
        
        return Cache::remember($cacheKey, $this->cacheTime, function () use ($limit) {
            $query = $this->model->published()->with(['category', 'user', 'featuredImage']);
            
            if ($limit) {
                $query->limit($limit);
            }
            
            return $query->get();
        });
    }

    /**
     * Get draft posts
     */
    public function getDrafts($limit = null): Collection
    {
        $cacheKey = $this->getCacheKey('drafts', [$limit]);
        
        return Cache::remember($cacheKey, $this->cacheTime, function () use ($limit) {
            $query = $this->model->where('is_published', false)->with(['category', 'user', 'featuredImage']);
            
            if ($limit) {
                $query->limit($limit);
            }
            
            return $query->get();
        });
    }

    /**
     * Get posts by category
     */
    public function getByCategory($categoryId, $limit = null): Collection
    {
        $cacheKey = $this->getCacheKey('by_category', [$categoryId, $limit]);
        
        return Cache::remember($cacheKey, $this->cacheTime, function () use ($categoryId, $limit) {
            $query = $this->model->where('category_id', $categoryId)
                ->published()
                ->with(['category', 'user', 'featuredImage'])
                ->latest();
            
            if ($limit) {
                $query->limit($limit);
            }
            
            return $query->get();
        });
    }

    /**
     * Get posts by type
     */
    public function getByType($type, $limit = null): Collection
    {
        $cacheKey = $this->getCacheKey('by_type', [$type, $limit]);
        
        return Cache::remember($cacheKey, $this->cacheTime, function () use ($type, $limit) {
            $query = $this->model->byType($type)
                ->published()
                ->with(['category', 'user', 'featuredImage'])
                ->latest();
            
            if ($limit) {
                $query->limit($limit);
            }
            
            return $query->get();
        });
    }

    /**
     * Get featured posts
     */
    public function getFeatured($limit = 5): Collection
    {
        $cacheKey = $this->getCacheKey('featured', [$limit]);
        
        return Cache::remember($cacheKey, $this->cacheTime, function () use ($limit) {
            return $this->model->where('is_featured', true)
                ->published()
                ->with(['category', 'user', 'featuredImage'])
                ->latest()
                ->limit($limit)
                ->get();
        });
    }

    /**
     * Get slider posts
     */
    public function getSlider($limit = 5): Collection
    {
        $cacheKey = $this->getCacheKey('slider', [$limit]);
        
        return Cache::remember($cacheKey, $this->cacheTime, function () use ($limit) {
            return $this->model->where('is_slider', true)
                ->published()
                ->with(['category', 'user', 'featuredImage'])
                ->orderBy('sort_order')
                ->limit($limit)
                ->get();
        });
    }

    /**
     * Get popular posts
     */
    public function getPopular($limit = 10): Collection
    {
        $cacheKey = $this->getCacheKey('popular', [$limit]);
        
        return Cache::remember($cacheKey, $this->cacheTime, function () use ($limit) {
            return $this->model->published()
                ->with(['category', 'user', 'featuredImage'])
                ->orderBy('views', 'desc')
                ->limit($limit)
                ->get();
        });
    }

    /**
     * Get recent posts
     */
    public function getRecent($limit = 10): Collection
    {
        $cacheKey = $this->getCacheKey('recent', [$limit]);
        
        return Cache::remember($cacheKey, $this->cacheTime, function () use ($limit) {
            return $this->model->published()
                ->with(['category', 'user', 'featuredImage'])
                ->latest()
                ->limit($limit)
                ->get();
        });
    }

    /**
     * Search posts
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
            // Default search in title, content, and excerpt
            $modelQuery->where(function ($q) use ($query) {
                $q->where('title', 'LIKE', "%{$query}%")
                  ->orWhere('content', 'LIKE', "%{$query}%")
                  ->orWhere('excerpt', 'LIKE', "%{$query}%");
            });
        }
        
        // Apply limit if provided
        if ($limit) {
            $modelQuery->limit($limit);
        }
        
        return $modelQuery->with(['category', 'user', 'featuredImage'])
            ->latest()
            ->get();
    }

    /**
     * Get posts with pagination and filters
     */
    public function getPaginatedWithFilters(array $filters = [], $perPage = 15): LengthAwarePaginator
    {
        $query = $this->model->with(['category', 'user', 'featuredImage']);
        
        // Apply search
        if (!empty($filters['search'])) {
            $search = $filters['search'];
            $query->where(function ($q) use ($search) {
                $q->where('title', 'LIKE', "%{$search}%")
                  ->orWhere('content', 'LIKE', "%{$search}%")
                  ->orWhere('excerpt', 'LIKE', "%{$search}%");
            });
        }
        
        // Filter by category
        if (!empty($filters['category_id'])) {
            $query->where('category_id', $filters['category_id']);
        }
        
        // Filter by type
        if (!empty($filters['type'])) {
            $query->byType($filters['type']);
        }
        
        // Filter by status
        if (!empty($filters['status'])) {
            if ($filters['status'] === 'published') {
                $query->published();
            } elseif ($filters['status'] === 'draft') {
                $query->where('is_published', false);
            }
        }
        
        // Filter by user
        if (!empty($filters['user_id'])) {
            $query->where('user_id', $filters['user_id']);
        }
        
        // Filter by featured
        if (isset($filters['is_featured'])) {
            $query->where('is_featured', $filters['is_featured']);
        }
        
        // Filter by slider
        if (isset($filters['is_slider'])) {
            $query->where('is_slider', $filters['is_slider']);
        }
        
        // Date range filter
        if (!empty($filters['date_from'])) {
            $query->whereDate('created_at', '>=', $filters['date_from']);
        }
        
        if (!empty($filters['date_to'])) {
            $query->whereDate('created_at', '<=', $filters['date_to']);
        }
        
        // Sorting
        $sortBy = $filters['sort_by'] ?? 'created_at';
        $sortOrder = $filters['sort_order'] ?? 'desc';
        $query->orderBy($sortBy, $sortOrder);
        
        return $query->paginate($perPage);
    }

    /**
     * Get post by slug
     */
    public function findBySlug($slug): ?Post
    {
        $cacheKey = $this->getCacheKey('by_slug', [$slug]);
        
        return Cache::remember($cacheKey, $this->cacheTime, function () use ($slug) {
            return $this->model->where('slug', $slug)
                ->with(['category', 'user', 'featuredImage'])
                ->first();
        });
    }

    /**
     * Increment views
     */
    public function incrementViews($id): bool
    {
        $result = $this->model->where('id', $id)->increment('views');
        
        // Clear cache after updating views
        $this->clearCache();
        
        return $result > 0;
    }

    /**
     * Get posts count by type
     */
    public function getCountByType(): array
    {
        $cacheKey = $this->getCacheKey('count_by_type');
        
        return Cache::remember($cacheKey, $this->cacheTime, function () {
            return $this->model->select('type', DB::raw('count(*) as count'))
                ->groupBy('type')
                ->pluck('count', 'type')
                ->toArray();
        });
    }

    /**
     * Get posts count by month
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
     * Get related posts
     */
    public function getRelated($postId, $categoryId, $limit = 5): Collection
    {
        $cacheKey = $this->getCacheKey('related', [$postId, $categoryId, $limit]);
        
        return Cache::remember($cacheKey, $this->cacheTime, function () use ($postId, $categoryId, $limit) {
            return $this->model->where('id', '!=', $postId)
                ->where('category_id', $categoryId)
                ->published()
                ->with(['category', 'user', 'featuredImage'])
                ->latest()
                ->limit($limit)
                ->get();
        });
    }

    /**
     * Bulk publish posts
     */
    public function bulkPublish(array $ids): int
    {
        DB::beginTransaction();
        
        try {
            $updated = $this->model->whereIn('id', $ids)
                ->update([
                    'is_published' => true,
                    'published_at' => now()
                ]);
            
            $this->clearCache();
            
            DB::commit();
            
            return $updated;
        } catch (\Exception $e) {
            DB::rollback();
            throw $e;
        }
    }

    /**
     * Bulk unpublish posts
     */
    public function bulkUnpublish(array $ids): int
    {
        DB::beginTransaction();
        
        try {
            $updated = $this->model->whereIn('id', $ids)
                ->update([
                    'is_published' => false,
                    'published_at' => null
                ]);
            
            $this->clearCache();
            
            DB::commit();
            
            return $updated;
        } catch (\Exception $e) {
            DB::rollback();
            throw $e;
        }
    }

    /**
     * Get posts statistics
     */
    public function getStatistics(): array
    {
        $cacheKey = $this->getCacheKey('statistics');
        
        return Cache::remember($cacheKey, $this->cacheTime, function () {
            $base = parent::getStatistics();
            
            return array_merge($base, [
                'published' => $this->model->published()->count(),
                'drafts' => $this->model->where('is_published', false)->count(),
                'featured' => $this->model->where('is_featured', true)->count(),
                'slider' => $this->model->where('is_slider', true)->count(),
                'total_views' => $this->model->sum('views'),
                'by_type' => $this->getCountByType(),
            ]);
        });
    }
}