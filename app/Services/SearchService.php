<?php

namespace App\Services;

use App\Models\Post;
use App\Models\User;
use App\Models\Category;
use App\Models\Media;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Carbon\Carbon;

class SearchService
{
    /**
     * Perform global search across all content types.
     */
    public function globalSearch(string $query, array $filters = [], int $perPage = 20): array
    {
        $cacheKey = 'global_search_' . md5($query . serialize($filters) . $perPage);
        
        return Cache::remember($cacheKey, 300, function () use ($query, $filters, $perPage) {
            $results = [
                'query' => $query,
                'total_results' => 0,
                'posts' => [],
                'users' => [],
                'categories' => [],
                'media' => [],
                'suggestions' => [],
            ];
            
            // Search posts
            if (!isset($filters['type']) || $filters['type'] === 'posts') {
                $posts = $this->searchPosts($query, $filters, $perPage);
                $results['posts'] = $posts;
                $results['total_results'] += $posts->total();
            }
            
            // Search users
            if (!isset($filters['type']) || $filters['type'] === 'users') {
                $users = $this->searchUsers($query, $filters, $perPage);
                $results['users'] = $users;
                $results['total_results'] += $users->total();
            }
            
            // Search categories
            if (!isset($filters['type']) || $filters['type'] === 'categories') {
                $categories = $this->searchCategories($query, $filters, $perPage);
                $results['categories'] = $categories;
                $results['total_results'] += $categories->total();
            }
            
            // Search media
            if (!isset($filters['type']) || $filters['type'] === 'media') {
                $media = $this->searchMedia($query, $filters, $perPage);
                $results['media'] = $media;
                $results['total_results'] += $media->total();
            }
            
            // Generate search suggestions
            $results['suggestions'] = $this->generateSearchSuggestions($query);
            
            // Store search query for analytics
            $this->storeSearchQuery($query, $results['total_results']);
            
            return $results;
        });
    }

    /**
     * Search posts.
     */
    public function searchPosts(string $query, array $filters = [], int $perPage = 20): LengthAwarePaginator
    {
        $searchQuery = Post::with(['author', 'category', 'tags'])
            ->where('status', 'published')
            ->where(function ($q) use ($query) {
                $q->where('title', 'like', '%' . $query . '%')
                  ->orWhere('excerpt', 'like', '%' . $query . '%')
                  ->orWhere('content', 'like', '%' . $query . '%')
                  ->orWhere('slug', 'like', '%' . $query . '%')
                  ->orWhereHas('tags', function ($tagQuery) use ($query) {
                      $tagQuery->where('name', 'like', '%' . $query . '%');
                  });
            })
            ->when(isset($filters['category_id']), function ($q) use ($filters) {
                $q->where('category_id', $filters['category_id']);
            })
            ->when(isset($filters['author_id']), function ($q) use ($filters) {
                $q->where('user_id', $filters['author_id']);
            })
            ->when(isset($filters['date_from']), function ($q) use ($filters) {
                $q->whereDate('published_at', '>=', $filters['date_from']);
            })
            ->when(isset($filters['date_to']), function ($q) use ($filters) {
                $q->whereDate('published_at', '<=', $filters['date_to']);
            })
            ->when(isset($filters['type']), function ($q) use ($filters) {
                $q->where('type', $filters['type']);
            })
            ->when(isset($filters['featured']), function ($q) use ($filters) {
                $q->where('is_featured', $filters['featured']);
            })
            ->orderByRaw($this->getRelevanceScore($query, 'posts'))
            ->orderBy('published_at', 'desc');

        return $searchQuery->paginate($perPage);
    }

    /**
     * Search users.
     */
    public function searchUsers(string $query, array $filters = [], int $perPage = 20): LengthAwarePaginator
    {
        $searchQuery = User::where('is_active', true)
            ->where(function ($q) use ($query) {
                $q->where('name', 'like', '%' . $query . '%')
                  ->orWhere('username', 'like', '%' . $query . '%')
                  ->orWhere('email', 'like', '%' . $query . '%')
                  ->orWhere('bio', 'like', '%' . $query . '%')
                  ->orWhere('location', 'like', '%' . $query . '%');
            })
            ->when(isset($filters['role']), function ($q) use ($filters) {
                $q->where('role', $filters['role']);
            })
            ->when(isset($filters['verified']), function ($q) use ($filters) {
                if ($filters['verified']) {
                    $q->whereNotNull('email_verified_at');
                } else {
                    $q->whereNull('email_verified_at');
                }
            })
            ->when(isset($filters['location']), function ($q) use ($filters) {
                $q->where('location', 'like', '%' . $filters['location'] . '%');
            })
            ->orderByRaw($this->getRelevanceScore($query, 'users'))
            ->orderBy('created_at', 'desc');

        return $searchQuery->paginate($perPage);
    }

    /**
     * Search categories.
     */
    public function searchCategories(string $query, array $filters = [], int $perPage = 20): LengthAwarePaginator
    {
        $searchQuery = Category::where('is_active', true)
            ->where(function ($q) use ($query) {
                $q->where('name', 'like', '%' . $query . '%')
                  ->orWhere('slug', 'like', '%' . $query . '%')
                  ->orWhere('description', 'like', '%' . $query . '%');
            })
            ->when(isset($filters['parent_id']), function ($q) use ($filters) {
                if ($filters['parent_id'] === 'null') {
                    $q->whereNull('parent_id');
                } else {
                    $q->where('parent_id', $filters['parent_id']);
                }
            })
            ->when(isset($filters['featured']), function ($q) use ($filters) {
                $q->where('is_featured', $filters['featured']);
            })
            ->withCount('posts')
            ->orderByRaw($this->getRelevanceScore($query, 'categories'))
            ->orderBy('posts_count', 'desc');

        return $searchQuery->paginate($perPage);
    }

    /**
     * Search media.
     */
    public function searchMedia(string $query, array $filters = [], int $perPage = 20): LengthAwarePaginator
    {
        $searchQuery = Media::where(function ($q) use ($query) {
                $q->where('name', 'like', '%' . $query . '%')
                  ->orWhere('original_name', 'like', '%' . $query . '%')
                  ->orWhere('description', 'like', '%' . $query . '%')
                  ->orWhere('alt_text', 'like', '%' . $query . '%');
            })
            ->when(isset($filters['type']), function ($q) use ($filters) {
                $q->where('type', $filters['type']);
            })
            ->when(isset($filters['mime_type']), function ($q) use ($filters) {
                $q->where('mime_type', 'like', $filters['mime_type'] . '%');
            })
            ->when(isset($filters['user_id']), function ($q) use ($filters) {
                $q->where('user_id', $filters['user_id']);
            })
            ->when(isset($filters['size_min']), function ($q) use ($filters) {
                $q->where('size', '>=', $filters['size_min']);
            })
            ->when(isset($filters['size_max']), function ($q) use ($filters) {
                $q->where('size', '<=', $filters['size_max']);
            })
            ->orderByRaw($this->getRelevanceScore($query, 'media'))
            ->orderBy('created_at', 'desc');

        return $searchQuery->paginate($perPage);
    }

    /**
     * Get search suggestions.
     */
    public function getSearchSuggestions(string $query, int $limit = 10): array
    {
        $cacheKey = 'search_suggestions_' . md5($query) . '_' . $limit;
        
        return Cache::remember($cacheKey, 1800, function () use ($query, $limit) {
            $suggestions = [];
            
            // Get suggestions from popular searches
            $popularSearches = $this->getPopularSearches($limit);
            foreach ($popularSearches as $search) {
                if (str_contains(strtolower($search['query']), strtolower($query))) {
                    $suggestions[] = [
                        'text' => $search['query'],
                        'type' => 'popular',
                        'count' => $search['count']
                    ];
                }
            }
            
            // Get suggestions from post titles
            $postTitles = Post::where('status', 'published')
                ->where('title', 'like', '%' . $query . '%')
                ->orderBy('views', 'desc')
                ->limit($limit)
                ->pluck('title')
                ->toArray();
            
            foreach ($postTitles as $title) {
                $suggestions[] = [
                    'text' => $title,
                    'type' => 'post',
                    'count' => null
                ];
            }
            
            // Get suggestions from categories
            $categories = Category::where('is_active', true)
                ->where('name', 'like', '%' . $query . '%')
                ->orderBy('posts_count', 'desc')
                ->limit($limit)
                ->pluck('name')
                ->toArray();
            
            foreach ($categories as $category) {
                $suggestions[] = [
                    'text' => $category,
                    'type' => 'category',
                    'count' => null
                ];
            }
            
            // Get suggestions from user names
            $users = User::where('is_active', true)
                ->where('name', 'like', '%' . $query . '%')
                ->orderBy('posts_count', 'desc')
                ->limit($limit)
                ->pluck('name')
                ->toArray();
            
            foreach ($users as $user) {
                $suggestions[] = [
                    'text' => $user,
                    'type' => 'user',
                    'count' => null
                ];
            }
            
            // Remove duplicates and limit results
            $suggestions = collect($suggestions)
                ->unique('text')
                ->take($limit)
                ->values()
                ->toArray();
            
            return $suggestions;
        });
    }

    /**
     * Get popular searches.
     */
    public function getPopularSearches(int $limit = 10): array
    {
        return Cache::remember('popular_searches_' . $limit, 3600, function () use ($limit) {
            return DB::table('search_queries')
                ->select('query', DB::raw('COUNT(*) as count'))
                ->where('created_at', '>=', now()->subDays(30))
                ->groupBy('query')
                ->orderBy('count', 'desc')
                ->limit($limit)
                ->get()
                ->toArray();
        });
    }

    /**
     * Get trending searches.
     */
    public function getTrendingSearches(int $limit = 10): array
    {
        return Cache::remember('trending_searches_' . $limit, 1800, function () use ($limit) {
            return DB::table('search_queries')
                ->select('query', DB::raw('COUNT(*) as count'))
                ->where('created_at', '>=', now()->subHours(24))
                ->groupBy('query')
                ->orderBy('count', 'desc')
                ->limit($limit)
                ->get()
                ->toArray();
        });
    }

    /**
     * Get search statistics.
     */
    public function getSearchStatistics(): array
    {
        return Cache::remember('search_statistics', 3600, function () {
            return [
                'total_searches' => DB::table('search_queries')->count(),
                'unique_queries' => DB::table('search_queries')->distinct('query')->count(),
                'searches_today' => DB::table('search_queries')
                    ->whereDate('created_at', today())->count(),
                'searches_this_week' => DB::table('search_queries')
                    ->whereBetween('created_at', [
                        now()->startOfWeek(),
                        now()->endOfWeek()
                    ])->count(),
                'searches_this_month' => DB::table('search_queries')
                    ->whereMonth('created_at', now()->month)
                    ->whereYear('created_at', now()->year)->count(),
                'avg_results_per_search' => DB::table('search_queries')
                    ->avg('results_count'),
                'zero_result_searches' => DB::table('search_queries')
                    ->where('results_count', 0)->count(),
                'popular_searches' => $this->getPopularSearches(5),
                'trending_searches' => $this->getTrendingSearches(5),
            ];
        });
    }

    /**
     * Get search trends over time.
     */
    public function getSearchTrends(int $days = 30): array
    {
        $trends = [];
        
        for ($i = $days - 1; $i >= 0; $i--) {
            $date = now()->subDays($i);
            $trends[] = [
                'date' => $date->format('Y-m-d'),
                'searches' => DB::table('search_queries')
                    ->whereDate('created_at', $date)->count(),
                'unique_queries' => DB::table('search_queries')
                    ->whereDate('created_at', $date)
                    ->distinct('query')->count(),
                'zero_results' => DB::table('search_queries')
                    ->whereDate('created_at', $date)
                    ->where('results_count', 0)->count(),
            ];
        }
        
        return $trends;
    }

    /**
     * Advanced search with filters.
     */
    public function advancedSearch(array $criteria, int $perPage = 20): array
    {
        $results = [
            'posts' => collect(),
            'users' => collect(),
            'categories' => collect(),
            'media' => collect(),
            'total_results' => 0,
        ];
        
        // Search posts with advanced criteria
        if (isset($criteria['posts'])) {
            $postQuery = Post::with(['author', 'category', 'tags'])
                ->where('status', 'published');
            
            if (isset($criteria['posts']['title'])) {
                $postQuery->where('title', 'like', '%' . $criteria['posts']['title'] . '%');
            }
            
            if (isset($criteria['posts']['content'])) {
                $postQuery->where('content', 'like', '%' . $criteria['posts']['content'] . '%');
            }
            
            if (isset($criteria['posts']['author'])) {
                $postQuery->whereHas('author', function ($q) use ($criteria) {
                    $q->where('name', 'like', '%' . $criteria['posts']['author'] . '%');
                });
            }
            
            if (isset($criteria['posts']['category'])) {
                $postQuery->whereHas('category', function ($q) use ($criteria) {
                    $q->where('name', 'like', '%' . $criteria['posts']['category'] . '%');
                });
            }
            
            if (isset($criteria['posts']['tags'])) {
                $postQuery->whereHas('tags', function ($q) use ($criteria) {
                    $q->whereIn('name', $criteria['posts']['tags']);
                });
            }
            
            if (isset($criteria['posts']['date_range'])) {
                $postQuery->whereBetween('published_at', [
                    $criteria['posts']['date_range']['from'],
                    $criteria['posts']['date_range']['to']
                ]);
            }
            
            $results['posts'] = $postQuery->paginate($perPage);
            $results['total_results'] += $results['posts']->total();
        }
        
        return $results;
    }

    /**
     * Search within specific content.
     */
    public function searchInContent(string $content, string $query): array
    {
        $matches = [];
        $sentences = preg_split('/[.!?]+/', $content);
        
        foreach ($sentences as $index => $sentence) {
            if (stripos($sentence, $query) !== false) {
                $matches[] = [
                    'sentence' => trim($sentence),
                    'position' => $index,
                    'highlighted' => $this->highlightQuery(trim($sentence), $query)
                ];
            }
        }
        
        return $matches;
    }

    /**
     * Highlight search query in text.
     */
    public function highlightQuery(string $text, string $query): string
    {
        return preg_replace(
            '/(' . preg_quote($query, '/') . ')/i',
            '<mark>$1</mark>',
            $text
        );
    }

    /**
     * Generate search suggestions based on query.
     */
    protected function generateSearchSuggestions(string $query): array
    {
        if (strlen($query) < 2) {
            return [];
        }
        
        return $this->getSearchSuggestions($query, 5);
    }

    /**
     * Get relevance score for search results.
     */
    protected function getRelevanceScore(string $query, string $type): string
    {
        $query = strtolower($query);
        
        switch ($type) {
            case 'posts':
                return "(
                    CASE 
                        WHEN LOWER(title) LIKE '%{$query}%' THEN 10
                        WHEN LOWER(excerpt) LIKE '%{$query}%' THEN 5
                        WHEN LOWER(content) LIKE '%{$query}%' THEN 3
                        WHEN LOWER(slug) LIKE '%{$query}%' THEN 2
                        ELSE 1
                    END
                ) DESC";
                
            case 'users':
                return "(
                    CASE 
                        WHEN LOWER(name) LIKE '%{$query}%' THEN 10
                        WHEN LOWER(username) LIKE '%{$query}%' THEN 8
                        WHEN LOWER(bio) LIKE '%{$query}%' THEN 5
                        WHEN LOWER(location) LIKE '%{$query}%' THEN 3
                        ELSE 1
                    END
                ) DESC";
                
            case 'categories':
                return "(
                    CASE 
                        WHEN LOWER(name) LIKE '%{$query}%' THEN 10
                        WHEN LOWER(slug) LIKE '%{$query}%' THEN 8
                        WHEN LOWER(description) LIKE '%{$query}%' THEN 5
                        ELSE 1
                    END
                ) DESC";
                
            case 'media':
                return "(
                    CASE 
                        WHEN LOWER(title) LIKE '%{$query}%' THEN 10
                        WHEN LOWER(filename) LIKE '%{$query}%' THEN 8
                        WHEN LOWER(description) LIKE '%{$query}%' THEN 5
                        WHEN LOWER(alt_text) LIKE '%{$query}%' THEN 3
                        ELSE 1
                    END
                ) DESC";
                
            default:
                return '1 DESC';
        }
    }

    /**
     * Store search query for analytics.
     */
    protected function storeSearchQuery(string $query, int $resultsCount): void
    {
        try {
            DB::table('search_queries')->insert([
                'query' => $query,
                'results_count' => $resultsCount,
                'user_id' => auth()->id(),
                'ip_address' => request()->ip(),
                'user_agent' => request()->userAgent(),
                'created_at' => now(),
            ]);
        } catch (\Exception $e) {
            // Log error but don't fail the search
            \Log::error('Failed to store search query', [
                'query' => $query,
                'error' => $e->getMessage()
            ]);
        }
    }

    /**
     * Clean up old search queries.
     */
    public function cleanupOldSearchQueries(int $days = 90): int
    {
        return DB::table('search_queries')
            ->where('created_at', '<', now()->subDays($days))
            ->delete();
    }

    /**
     * Get search query analytics.
     */
    public function getSearchAnalytics(array $filters = []): array
    {
        $query = DB::table('search_queries');
        
        if (isset($filters['date_from'])) {
            $query->whereDate('created_at', '>=', $filters['date_from']);
        }
        
        if (isset($filters['date_to'])) {
            $query->whereDate('created_at', '<=', $filters['date_to']);
        }
        
        if (isset($filters['user_id'])) {
            $query->where('user_id', $filters['user_id']);
        }
        
        return [
            'total_searches' => $query->count(),
            'unique_queries' => $query->distinct('query')->count(),
            'zero_result_searches' => $query->where('results_count', 0)->count(),
            'avg_results' => $query->avg('results_count'),
            'top_queries' => $query->select('query', DB::raw('COUNT(*) as count'))
                ->groupBy('query')
                ->orderBy('count', 'desc')
                ->limit(10)
                ->get()
                ->toArray(),
            'search_volume_by_day' => $query->select(
                    DB::raw('DATE(created_at) as date'),
                    DB::raw('COUNT(*) as count')
                )
                ->groupBy('date')
                ->orderBy('date')
                ->get()
                ->toArray(),
        ];
    }

    /**
     * Export search data.
     */
    public function exportSearchData(array $filters = []): array
    {
        $query = DB::table('search_queries');
        
        if (isset($filters['date_from'])) {
            $query->whereDate('created_at', '>=', $filters['date_from']);
        }
        
        if (isset($filters['date_to'])) {
            $query->whereDate('created_at', '<=', $filters['date_to']);
        }
        
        return $query->orderBy('created_at', 'desc')
            ->get()
            ->toArray();
    }
}