<?php

namespace App\Services;

use App\Models\Category;
use App\Repositories\CategoryRepository;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class CategoryService
{
    protected $categoryRepository;

    public function __construct(CategoryRepository $categoryRepository)
    {
        $this->categoryRepository = $categoryRepository;
    }

    /**
     * Get all categories with filters and pagination
     */
    public function getAllCategories(array $filters = [], $perPage = 15): LengthAwarePaginator
    {
        return $this->categoryRepository->getPaginatedWithFilters($filters, $perPage);
    }

    /**
     * Get active categories
     */
    public function getActiveCategories($limit = null): Collection
    {
        return $this->categoryRepository->getActive($limit);
    }

    /**
     * Get categories with post count
     */
    public function getCategoriesWithPostCount(): Collection
    {
        return $this->categoryRepository->getWithPostCount();
    }

    /**
     * Get categories for dropdown
     */
    public function getCategoriesForDropdown(): array
    {
        return $this->categoryRepository->getForDropdown();
    }

    /**
     * Get popular categories
     */
    public function getPopularCategories($limit = 10): Collection
    {
        return $this->categoryRepository->getPopular($limit);
    }

    /**
     * Find category by ID
     */
    public function findCategory($id): ?Category
    {
        return $this->categoryRepository->find($id);
    }

    /**
     * Find category by slug
     */
    public function findCategoryBySlug($slug): ?Category
    {
        return $this->categoryRepository->findBySlug($slug);
    }

    /**
     * Create new category
     */
    public function createCategory(array $data): Category
    {
        DB::beginTransaction();
        
        try {
            // Generate slug if not provided
            if (empty($data['slug'])) {
                $data['slug'] = $this->categoryRepository->generateUniqueSlug($data['name']);
            } else {
                // Ensure provided slug is unique
                if (!$this->categoryRepository->isSlugUnique($data['slug'])) {
                    $data['slug'] = $this->categoryRepository->generateUniqueSlug($data['slug']);
                }
            }
            
            // Set default values
            $data['is_active'] = $data['is_active'] ?? true;
            $data['color'] = $data['color'] ?? $this->generateRandomColor();
            
            // Generate meta fields if not provided
            if (empty($data['meta_title'])) {
                $data['meta_title'] = $data['name'];
            }
            
            if (empty($data['meta_description']) && !empty($data['description'])) {
                $data['meta_description'] = Str::limit(strip_tags($data['description']), 160);
            }
            
            $category = $this->categoryRepository->create($data);
            
            // Clear related caches
            $this->clearRelatedCaches();
            
            DB::commit();
            
            return $category;
        } catch (\Exception $e) {
            DB::rollback();
            throw $e;
        }
    }

    /**
     * Update category
     */
    public function updateCategory($id, array $data): Category
    {
        DB::beginTransaction();
        
        try {
            $category = $this->categoryRepository->find($id);
            
            if (!$category) {
                throw new \Exception('Category not found');
            }
            
            // Generate slug if name changed
            if (isset($data['name']) && $data['name'] !== $category->name) {
                if (empty($data['slug'])) {
                    $data['slug'] = $this->categoryRepository->generateUniqueSlug($data['name'], $id);
                }
            }
            
            // Ensure slug is unique if provided
            if (isset($data['slug']) && !$this->categoryRepository->isSlugUnique($data['slug'], $id)) {
                $data['slug'] = $this->categoryRepository->generateUniqueSlug($data['slug'], $id);
            }
            
            // Update meta fields if not provided
            if (empty($data['meta_title']) && !empty($data['name'])) {
                $data['meta_title'] = $data['name'];
            }
            
            if (empty($data['meta_description']) && !empty($data['description'])) {
                $data['meta_description'] = Str::limit(strip_tags($data['description']), 160);
            }
            
            $updatedCategory = $this->categoryRepository->update($id, $data);
            
            // Clear related caches
            $this->clearRelatedCaches();
            
            DB::commit();
            
            return $updatedCategory;
        } catch (\Exception $e) {
            DB::rollback();
            throw $e;
        }
    }

    /**
     * Delete category
     */
    public function deleteCategory($id): bool
    {
        DB::beginTransaction();
        
        try {
            $category = $this->categoryRepository->find($id);
            
            if (!$category) {
                return false;
            }
            
            // Check if category has posts
            if ($category->posts()->count() > 0) {
                throw new \Exception('Cannot delete category that has posts. Please move or delete the posts first.');
            }
            
            $deleted = $this->categoryRepository->delete($id);
            
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
     * Bulk activate categories
     */
    public function bulkActivate(array $ids): int
    {
        $updated = $this->categoryRepository->bulkActivate($ids);
        $this->clearRelatedCaches();
        return $updated;
    }

    /**
     * Bulk deactivate categories
     */
    public function bulkDeactivate(array $ids): int
    {
        $updated = $this->categoryRepository->bulkDeactivate($ids);
        $this->clearRelatedCaches();
        return $updated;
    }

    /**
     * Bulk delete categories
     */
    public function bulkDelete(array $ids): int
    {
        DB::beginTransaction();
        
        try {
            // Check if any category has posts
            $categoriesWithPosts = $this->categoryRepository->whereIn('id', $ids)
                ->has('posts')
                ->pluck('name')
                ->toArray();
            
            if (!empty($categoriesWithPosts)) {
                throw new \Exception('Cannot delete categories that have posts: ' . implode(', ', $categoriesWithPosts));
            }
            
            $deleted = $this->categoryRepository->bulkDelete($ids);
            
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
     * Search categories
     */
    public function searchCategories($query, array $filters = []): Collection
    {
        return $this->categoryRepository->search($query, $filters);
    }

    /**
     * Get categories by color
     */
    public function getCategoriesByColor($color): Collection
    {
        return $this->categoryRepository->getByColor($color);
    }

    /**
     * Get unique colors
     */
    public function getUniqueColors(): array
    {
        return $this->categoryRepository->getUniqueColors();
    }

    /**
     * Update category color
     */
    public function updateCategoryColor($id, $color): bool
    {
        $updated = $this->categoryRepository->updateColor($id, $color);
        $this->clearRelatedCaches();
        return $updated;
    }

    /**
     * Get category statistics
     */
    public function getStatistics(): array
    {
        return $this->categoryRepository->getStatistics();
    }

    /**
     * Get categories with recent posts
     */
    public function getCategoriesWithRecentPosts($limit = 5): Collection
    {
        return $this->categoryRepository->getWithRecentPosts($limit);
    }

    /**
     * Get category hierarchy
     */
    public function getCategoryHierarchy(): Collection
    {
        return $this->categoryRepository->getHierarchy();
    }

    /**
     * Get breadcrumb for category
     */
    public function getCategoryBreadcrumb($categoryId): array
    {
        return $this->categoryRepository->getBreadcrumb($categoryId);
    }

    /**
     * Generate random color for category
     */
    protected function generateRandomColor(): string
    {
        $colors = [
            '#FF6B6B', '#4ECDC4', '#45B7D1', '#96CEB4', '#FFEAA7',
            '#DDA0DD', '#98D8C8', '#F7DC6F', '#BB8FCE', '#85C1E9',
            '#F8C471', '#82E0AA', '#F1948A', '#85C1E9', '#D7BDE2'
        ];
        
        return $colors[array_rand($colors)];
    }

    /**
     * Clear related caches
     */
    protected function clearRelatedCaches(): void
    {
        // Check if cache driver supports tagging
        if (method_exists(Cache::getStore(), 'tags')) {
            Cache::tags(['categories', 'posts', 'homepage'])->flush();
        } else {
            // Fallback for cache drivers that don't support tagging (like database)
            Cache::flush();
        }
    }

    /**
     * Merge categories
     */
    public function mergeCategories($sourceId, $targetId): bool
    {
        DB::beginTransaction();
        
        try {
            $sourceCategory = $this->categoryRepository->find($sourceId);
            $targetCategory = $this->categoryRepository->find($targetId);
            
            if (!$sourceCategory || !$targetCategory) {
                throw new \Exception('One or both categories not found');
            }
            
            if ($sourceId === $targetId) {
                throw new \Exception('Cannot merge category with itself');
            }
            
            // Move all posts from source to target category
            $sourceCategory->posts()->update(['category_id' => $targetId]);
            
            // Delete source category
            $this->categoryRepository->delete($sourceId);
            
            // Clear related caches
            $this->clearRelatedCaches();
            
            DB::commit();
            
            return true;
        } catch (\Exception $e) {
            DB::rollback();
            throw $e;
        }
    }

    /**
     * Duplicate category
     */
    public function duplicateCategory($id): Category
    {
        DB::beginTransaction();
        
        try {
            $originalCategory = $this->categoryRepository->find($id);
            
            if (!$originalCategory) {
                throw new \Exception('Category not found');
            }
            
            $data = $originalCategory->toArray();
            
            // Remove ID and timestamps
            unset($data['id'], $data['created_at'], $data['updated_at']);
            
            // Modify name and slug
            $data['name'] = $data['name'] . ' (Copy)';
            $data['slug'] = $this->categoryRepository->generateUniqueSlug($data['name']);
            
            // Generate new color
            $data['color'] = $this->generateRandomColor();
            
            $duplicatedCategory = $this->categoryRepository->create($data);
            
            DB::commit();
            
            return $duplicatedCategory;
        } catch (\Exception $e) {
            DB::rollback();
            throw $e;
        }
    }

    /**
     * Get category analytics
     */
    public function getCategoryAnalytics($id): array
    {
        $category = $this->categoryRepository->find($id);
        
        if (!$category) {
            return [];
        }
        
        $posts = $category->posts();
        
        return [
            'total_posts' => $posts->count(),
            'published_posts' => $posts->published()->count(),
            'draft_posts' => $posts->where('is_published', false)->count(),
            'total_views' => $posts->sum('views'),
            'avg_views_per_post' => round($posts->avg('views'), 2),
            'most_viewed_post' => $posts->orderBy('views', 'desc')->first(['title', 'views']),
            'recent_posts' => $posts->latest()->limit(5)->get(['title', 'created_at', 'is_published']),
            'posts_by_month' => $posts->selectRaw('MONTH(created_at) as month, COUNT(*) as count')
                ->whereYear('created_at', now()->year)
                ->groupBy('month')
                ->orderBy('month')
                ->pluck('count', 'month')
                ->toArray(),
            'top_authors' => $posts->select('user_id', DB::raw('count(*) as posts_count'))
                ->with('user:id,name')
                ->groupBy('user_id')
                ->orderBy('posts_count', 'desc')
                ->limit(5)
                ->get()
                ->mapWithKeys(function ($item) {
                    return [$item->user->name ?? 'Unknown' => $item->posts_count];
                })
                ->toArray(),
        ];
    }

    /**
     * Get trending categories
     */
    public function getTrendingCategories($days = 7, $limit = 10): Collection
    {
        return $this->categoryRepository->whereHas('posts', function ($query) use ($days) {
                $query->where('created_at', '>=', now()->subDays($days));
            })
            ->withCount(['posts' => function ($query) use ($days) {
                $query->where('created_at', '>=', now()->subDays($days));
            }])
            ->orderBy('posts_count', 'desc')
            ->limit($limit)
            ->get();
    }

    /**
     * Get category suggestions based on content
     */
    public function getCategorySuggestions($content, $limit = 5): Collection
    {
        // Simple keyword-based suggestion
        // In a real application, you might use more sophisticated NLP
        $keywords = $this->extractKeywords($content);
        
        return $this->categoryRepository->where(function ($query) use ($keywords) {
                foreach ($keywords as $keyword) {
                    $query->orWhere('name', 'LIKE', "%{$keyword}%")
                          ->orWhere('description', 'LIKE', "%{$keyword}%");
                }
            })
            ->where('is_active', true)
            ->limit($limit)
            ->get();
    }

    /**
     * Extract keywords from content
     */
    protected function extractKeywords($content, $limit = 10): array
    {
        // Remove HTML tags and convert to lowercase
        $text = strtolower(strip_tags($content));
        
        // Remove common stop words
        $stopWords = ['the', 'a', 'an', 'and', 'or', 'but', 'in', 'on', 'at', 'to', 'for', 'of', 'with', 'by', 'is', 'are', 'was', 'were', 'be', 'been', 'have', 'has', 'had', 'do', 'does', 'did', 'will', 'would', 'could', 'should'];
        
        // Extract words
        $words = str_word_count($text, 1);
        
        // Filter out stop words and short words
        $keywords = array_filter($words, function ($word) use ($stopWords) {
            return strlen($word) > 3 && !in_array($word, $stopWords);
        });
        
        // Count word frequency
        $wordCount = array_count_values($keywords);
        
        // Sort by frequency and return top keywords
        arsort($wordCount);
        
        return array_slice(array_keys($wordCount), 0, $limit);
    }

    /**
     * Export categories to CSV
     */
    public function exportToCSV(): string
    {
        $categories = $this->categoryRepository->all();
        
        $csvData = "ID,Name,Slug,Description,Color,Is Active,Posts Count,Created At\n";
        
        foreach ($categories as $category) {
            $csvData .= sprintf(
                "%d,\"%s\",\"%s\",\"%s\",\"%s\",%s,%d,\"%s\"\n",
                $category->id,
                str_replace('"', '""', $category->name),
                $category->slug,
                str_replace('"', '""', $category->description ?? ''),
                $category->color,
                $category->is_active ? 'Yes' : 'No',
                $category->posts_count ?? 0,
                $category->created_at->format('Y-m-d H:i:s')
            );
        }
        
        return $csvData;
    }
}