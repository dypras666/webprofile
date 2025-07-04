<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CategoryResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'slug' => $this->slug,
            'description' => $this->description,
            'color' => $this->color,
            'icon' => $this->icon,
            'image_url' => $this->image_url,
            'parent' => $this->when(
                $this->parent_id,
                new CategoryResource($this->whenLoaded('parent'))
            ),
            'children' => CategoryResource::collection($this->whenLoaded('children')),
            'settings' => [
                'is_active' => $this->is_active,
                'is_featured' => $this->is_featured,
                'show_in_menu' => $this->settings['show_in_menu'] ?? true,
                'show_post_count' => $this->settings['show_post_count'] ?? true,
                'posts_per_page' => $this->settings['posts_per_page'] ?? null,
                'default_sort' => $this->settings['default_sort'] ?? 'latest',
                'allow_comments' => $this->settings['allow_comments'] ?? true,
                'require_approval' => $this->settings['require_approval'] ?? false,
                'template' => $this->template,
            ],
            'stats' => [
                'posts_count' => $this->whenLoaded('posts', function () {
                    return $this->posts->where('status', 'published')->count();
                }),
                'total_posts' => $this->when(
                    $request->user()?->can('viewStats', $this->resource),
                    $this->whenLoaded('posts', function () {
                        return $this->posts->count();
                    })
                ),
                'children_count' => $this->whenLoaded('children', function () {
                    return $this->children->count();
                }),
                'views' => $this->when(
                    $request->user()?->can('viewStats', $this->resource),
                    $this->views ?? 0
                ),
            ],
            'meta' => [
                'title' => $this->meta_title,
                'description' => $this->meta_description,
                'keywords' => $this->meta_keywords,
                'canonical_url' => $this->canonical_url,
                'og_title' => $this->og_title ?? $this->name,
                'og_description' => $this->og_description ?? $this->description,
                'og_image' => $this->og_image ?? $this->image_url,
            ],
            'hierarchy' => $this->when(
                $request->get('include_hierarchy'),
                $this->getHierarchy()
            ),
            'breadcrumbs' => $this->when(
                $request->get('include_breadcrumbs'),
                $this->getBreadcrumbs()
            ),
            'recent_posts' => $this->when(
                $request->get('include_recent_posts'),
                PostResource::collection(
                    $this->whenLoaded('recentPosts')
                )
            ),
            'popular_posts' => $this->when(
                $request->get('include_popular_posts'),
                PostResource::collection(
                    $this->whenLoaded('popularPosts')
                )
            ),
            'dates' => [
                'created_at' => $this->created_at->toISOString(),
                'updated_at' => $this->updated_at->toISOString(),
            ],
            'urls' => [
                'public' => route('categories.show', $this->slug),
                'feed' => route('categories.feed', $this->slug),
                'edit' => $this->when(
                    $request->user()?->can('update', $this->resource),
                    route('admin.categories.edit', $this->id)
                ),
            ],
            'sort_order' => $this->when(
                $request->user()?->can('update', $this->resource),
                $this->sort_order
            ),
            'custom_fields' => $this->when(
                !empty($this->custom_fields),
                $this->custom_fields
            ),
            'permissions' => $this->when(
                $request->user(),
                [
                    'can_edit' => $request->user()?->can('update', $this->resource) ?? false,
                    'can_delete' => $request->user()?->can('delete', $this->resource) ?? false,
                    'can_create_posts' => $request->user()?->can('createPosts', $this->resource) ?? false,
                    'can_manage_children' => $request->user()?->can('manageChildren', $this->resource) ?? false,
                ]
            ),
        ];
    }

    /**
     * Get additional data that should be returned with the resource array.
     *
     * @return array<string, mixed>
     */
    public function with(Request $request): array
    {
        return [
            'meta' => [
                'version' => '1.0',
                'generated_at' => now()->toISOString(),
            ],
        ];
    }

    /**
     * Customize the response for a request.
     */
    public function withResponse(Request $request, \Illuminate\Http\JsonResponse $response): void
    {
        $response->header('X-Resource-Type', 'Category');
        $response->header('X-Resource-ID', $this->id);
        
        // Add cache headers for public categories
        if ($this->is_active && !$request->user()) {
            $response->header('Cache-Control', 'public, max-age=3600');
            $response->header('ETag', md5($this->updated_at));
        }
    }

    /**
     * Create a new resource instance for listing.
     */
    public static function collection($resource)
    {
        return parent::collection($resource)->additional([
            'meta' => [
                'total_count' => $resource instanceof \Illuminate\Pagination\LengthAwarePaginator 
                    ? $resource->total() 
                    : $resource->count(),
                'generated_at' => now()->toISOString(),
            ],
        ]);
    }

    /**
     * Get a minimal version of the category resource for listings.
     */
    public function toMinimalArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'slug' => $this->slug,
            'color' => $this->color,
            'icon' => $this->icon,
            'is_active' => $this->is_active,
            'is_featured' => $this->is_featured,
            'posts_count' => $this->whenLoaded('posts', function () {
                return $this->posts->where('status', 'published')->count();
            }),
            'url' => route('categories.show', $this->slug),
        ];
    }

    /**
     * Get a dropdown version of the category resource.
     */
    public function toDropdownArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'slug' => $this->slug,
            'color' => $this->color,
            'icon' => $this->icon,
            'parent_id' => $this->parent_id,
            'level' => $this->getLevel(),
            'is_active' => $this->is_active,
            'children' => CategoryResource::collection($this->whenLoaded('children'))
                ->map(fn($child) => $child->toDropdownArray($request)),
        ];
    }

    /**
     * Get a navigation version of the category resource.
     */
    public function toNavigationArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'slug' => $this->slug,
            'color' => $this->color,
            'icon' => $this->icon,
            'url' => route('categories.show', $this->slug),
            'is_active' => $this->is_active,
            'show_in_menu' => $this->settings['show_in_menu'] ?? true,
            'posts_count' => $this->when(
                $this->settings['show_post_count'] ?? true,
                $this->whenLoaded('posts', function () {
                    return $this->posts->where('status', 'published')->count();
                })
            ),
            'children' => $this->when(
                $this->relationLoaded('children'),
                CategoryResource::collection($this->children)
                    ->map(fn($child) => $child->toNavigationArray($request))
            ),
        ];
    }

    /**
     * Get an admin version of the category resource.
     */
    public function toAdminArray(Request $request): array
    {
        return array_merge($this->toArray($request), [
            'custom_css' => $this->when(
                $request->user()?->can('update', $this->resource),
                $this->custom_css
            ),
            'custom_js' => $this->when(
                $request->user()?->can('update', $this->resource),
                $this->custom_js
            ),
            'admin_notes' => $this->when(
                $request->user()?->can('update', $this->resource),
                $this->admin_notes
            ),
            'created_by' => $this->when(
                $this->created_by,
                new UserResource($this->whenLoaded('creator'))
            ),
            'updated_by' => $this->when(
                $this->updated_by,
                new UserResource($this->whenLoaded('updater'))
            ),
            'analytics' => $this->when(
                $request->get('include_analytics'),
                [
                    'views_this_month' => $this->getViewsThisMonth(),
                    'posts_this_month' => $this->getPostsThisMonth(),
                    'trending_score' => $this->getTrendingScore(),
                    'engagement_rate' => $this->getEngagementRate(),
                ]
            ),
        ]);
    }

    /**
     * Get a search result version of the category resource.
     */
    public function toSearchArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'slug' => $this->slug,
            'description' => $this->description,
            'color' => $this->color,
            'icon' => $this->icon,
            'posts_count' => $this->whenLoaded('posts', function () {
                return $this->posts->where('status', 'published')->count();
            }),
            'url' => route('categories.show', $this->slug),
            'highlight' => $this->when(
                isset($this->search_highlight),
                $this->search_highlight
            ),
            'score' => $this->when(
                isset($this->search_score),
                $this->search_score
            ),
        ];
    }

    /**
     * Get the category hierarchy.
     */
    protected function getHierarchy(): array
    {
        $hierarchy = [];
        $current = $this->resource;
        
        while ($current) {
            array_unshift($hierarchy, [
                'id' => $current->id,
                'name' => $current->name,
                'slug' => $current->slug,
                'url' => route('categories.show', $current->slug),
            ]);
            $current = $current->parent;
        }
        
        return $hierarchy;
    }

    /**
     * Get the category breadcrumbs.
     */
    protected function getBreadcrumbs(): array
    {
        $breadcrumbs = [
            [
                'name' => 'Home',
                'url' => route('home'),
            ],
            [
                'name' => 'Categories',
                'url' => route('categories.index'),
            ],
        ];
        
        $hierarchy = $this->getHierarchy();
        
        foreach ($hierarchy as $item) {
            $breadcrumbs[] = $item;
        }
        
        return $breadcrumbs;
    }

    /**
     * Get the category level in hierarchy.
     */
    protected function getLevel(): int
    {
        $level = 0;
        $current = $this->resource;
        
        while ($current->parent) {
            $level++;
            $current = $current->parent;
        }
        
        return $level;
    }

    /**
     * Get views for this month.
     */
    protected function getViewsThisMonth(): int
    {
        // This would typically come from an analytics service or database
        return $this->views_this_month ?? 0;
    }

    /**
     * Get posts created this month.
     */
    protected function getPostsThisMonth(): int
    {
        if (!$this->relationLoaded('posts')) {
            return 0;
        }
        
        return $this->posts
            ->where('created_at', '>=', now()->startOfMonth())
            ->where('status', 'published')
            ->count();
    }

    /**
     * Get trending score.
     */
    protected function getTrendingScore(): float
    {
        // Calculate trending score based on recent activity
        $recentViews = $this->getViewsThisMonth();
        $recentPosts = $this->getPostsThisMonth();
        
        return ($recentViews * 0.7) + ($recentPosts * 0.3);
    }

    /**
     * Get engagement rate.
     */
    protected function getEngagementRate(): float
    {
        // This would typically be calculated from user interactions
        return $this->engagement_rate ?? 0.0;
    }
}