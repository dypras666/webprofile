<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PostResource extends JsonResource
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
            'title' => $this->title,
            'slug' => $this->slug,
            'excerpt' => $this->excerpt,
            'content' => $this->when(
                $request->routeIs('*.show') || $request->get('include_content'),
                $this->content
            ),
            'status' => $this->status,
            'type' => $this->type,
            'featured_image' => $this->when(
                $this->featured_image,
                new MediaResource($this->whenLoaded('featuredImage'))
            ),
            'featured_image_url' => $this->featured_image_url,
            'gallery' => MediaResource::collection($this->whenLoaded('gallery')),
            'category' => new CategoryResource($this->whenLoaded('category')),
            'author' => new UserResource($this->whenLoaded('user')),
            'tags' => $this->tags,
            'meta' => [
                'title' => $this->meta_title,
                'description' => $this->meta_description,
                'keywords' => $this->meta_keywords,
                'canonical_url' => $this->canonical_url,
                'og_title' => $this->og_title,
                'og_description' => $this->og_description,
                'og_image' => $this->og_image,
                'twitter_title' => $this->twitter_title,
                'twitter_description' => $this->twitter_description,
                'twitter_image' => $this->twitter_image,
            ],
            'settings' => [
                'is_featured' => $this->is_featured,
                'is_sticky' => $this->is_sticky,
                'allow_comments' => $this->allow_comments,
                'is_password_protected' => !empty($this->password),
                'reading_time' => $this->reading_time,
                'template' => $this->template,
            ],
            'stats' => [
                'views' => $this->views,
                'likes' => $this->whenLoaded('likes', function () {
                    return $this->likes->count();
                }),
                'comments' => $this->whenLoaded('comments', function () {
                    return $this->comments->where('status', 'approved')->count();
                }),
                'shares' => $this->shares ?? 0,
            ],
            'dates' => [
                'published_at' => $this->published_at?->toISOString(),
                'created_at' => $this->created_at->toISOString(),
                'updated_at' => $this->updated_at->toISOString(),
                'scheduled_at' => $this->scheduled_at?->toISOString(),
            ],
            'urls' => [
                'public' => $this->public_url,
                'edit' => $this->when(
                    $request->user()?->can('update', $this->resource),
                    route('admin.posts.edit', $this->id)
                ),
                'preview' => $this->when(
                    $this->status === 'draft',
                    route('posts.preview', $this->slug)
                ),
            ],
            'relationships' => [
                'related_posts' => $this->when(
                    $request->get('include_related'),
                    PostResource::collection($this->whenLoaded('relatedPosts'))
                ),
                'previous_post' => $this->when(
                    $request->get('include_navigation'),
                    new PostResource($this->whenLoaded('previousPost'))
                ),
                'next_post' => $this->when(
                    $request->get('include_navigation'),
                    new PostResource($this->whenLoaded('nextPost'))
                ),
            ],
            'custom_fields' => $this->when(
                !empty($this->custom_fields),
                $this->custom_fields
            ),
            'permissions' => $this->when(
                $request->user(),
                [
                    'can_edit' => $request->user()?->can('update', $this->resource) ?? false,
                    'can_delete' => $request->user()?->can('delete', $this->resource) ?? false,
                    'can_publish' => $request->user()?->can('publish', $this->resource) ?? false,
                    'can_comment' => $this->allow_comments && ($request->user()?->can('comment', $this->resource) ?? true),
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
        // Add custom headers
        $response->header('X-Resource-Type', 'Post');
        $response->header('X-Resource-ID', $this->id);
        
        // Add cache headers for public posts
        if ($this->status === 'published' && !$request->user()) {
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
     * Get a minimal version of the post resource for listings.
     */
    public function toMinimalArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'slug' => $this->slug,
            'excerpt' => $this->excerpt,
            'status' => $this->status,
            'type' => $this->type,
            'featured_image_url' => $this->featured_image_url,
            'category' => [
                'id' => $this->category?->id,
                'name' => $this->category?->name,
                'slug' => $this->category?->slug,
                'color' => $this->category?->color,
            ],
            'author' => [
                'id' => $this->user?->id,
                'name' => $this->user?->name,
                'avatar_url' => $this->user?->avatar_url,
            ],
            'stats' => [
                'views' => $this->views,
                'reading_time' => $this->reading_time,
            ],
            'dates' => [
                'published_at' => $this->published_at?->toISOString(),
                'updated_at' => $this->updated_at->toISOString(),
            ],
            'urls' => [
                'public' => $this->public_url,
            ],
            'is_featured' => $this->is_featured,
        ];
    }

    /**
     * Get a search result version of the post resource.
     */
    public function toSearchArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'slug' => $this->slug,
            'excerpt' => $this->excerpt,
            'type' => $this->type,
            'featured_image_url' => $this->featured_image_url,
            'category' => $this->category?->name,
            'author' => $this->user?->name,
            'published_at' => $this->published_at?->format('M j, Y'),
            'url' => $this->public_url,
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
     * Get an admin version of the post resource.
     */
    public function toAdminArray(Request $request): array
    {
        return array_merge($this->toArray($request), [
            'password' => $this->when(
                $request->user()?->can('update', $this->resource),
                !empty($this->password)
            ),
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
            'sort_order' => $this->sort_order,
            'revision_count' => $this->whenLoaded('revisions', function () {
                return $this->revisions->count();
            }),
            'last_editor' => $this->when(
                $this->updated_by,
                new UserResource($this->whenLoaded('lastEditor'))
            ),
        ]);
    }
}