<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
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
            'email' => $this->when(
                $this->shouldShowEmail($request),
                $this->email
            ),
            'role' => $this->role,
            'avatar_url' => $this->avatar_url,
            'bio' => $this->bio,
            'website' => $this->website,
            'location' => $this->location,
            'phone' => $this->when(
                $this->shouldShowPrivateInfo($request),
                $this->phone
            ),
            'social_links' => $this->when(
                !empty($this->social_links),
                $this->social_links
            ),
            'preferences' => $this->when(
                $this->shouldShowPrivateInfo($request),
                $this->preferences
            ),
            'stats' => [
                'posts_count' => $this->whenLoaded('posts', function () {
                    return $this->posts->where('status', 'published')->count();
                }),
                'total_posts' => $this->when(
                    $this->shouldShowPrivateInfo($request),
                    $this->whenLoaded('posts', function () {
                        return $this->posts->count();
                    })
                ),
                'media_count' => $this->when(
                    $this->shouldShowPrivateInfo($request),
                    $this->whenLoaded('media', function () {
                        return $this->media->count();
                    })
                ),
                'comments_count' => $this->whenLoaded('comments', function () {
                    return $this->comments->where('status', 'approved')->count();
                }),
                'followers_count' => $this->when(
                    method_exists($this->resource, 'followers'),
                    $this->whenLoaded('followers', function () {
                        return $this->followers->count();
                    })
                ),
                'following_count' => $this->when(
                    method_exists($this->resource, 'following'),
                    $this->whenLoaded('following', function () {
                        return $this->following->count();
                    })
                ),
            ],
            'status' => [
                'is_active' => $this->is_active,
                'is_verified' => $this->email_verified_at !== null,
                'is_online' => $this->when(
                    $this->last_login_at,
                    $this->last_login_at->diffInMinutes(now()) < 15
                ),
            ],
            'dates' => [
                'joined_at' => $this->created_at->toISOString(),
                'last_login_at' => $this->when(
                    $this->shouldShowPrivateInfo($request),
                    $this->last_login_at?->toISOString()
                ),
                'email_verified_at' => $this->when(
                    $this->shouldShowPrivateInfo($request),
                    $this->email_verified_at?->toISOString()
                ),
                'updated_at' => $this->when(
                    $this->shouldShowPrivateInfo($request),
                    $this->updated_at->toISOString()
                ),
            ],
            'urls' => [
                'profile' => route('users.show', $this->id),
                'avatar' => $this->avatar_url,
                'edit' => $this->when(
                    $this->shouldShowPrivateInfo($request),
                    route('admin.users.edit', $this->id)
                ),
            ],
            'recent_posts' => $this->when(
                $request->get('include_recent_posts'),
                PostResource::collection(
                    $this->whenLoaded('recentPosts')
                )
            ),
            'permissions' => $this->when(
                $request->user(),
                [
                    'can_edit' => $request->user()?->can('update', $this->resource) ?? false,
                    'can_delete' => $request->user()?->can('delete', $this->resource) ?? false,
                    'can_impersonate' => $request->user()?->can('impersonate', $this->resource) ?? false,
                    'can_change_role' => $request->user()?->can('changeRole', $this->resource) ?? false,
                    'can_message' => $request->user()?->can('message', $this->resource) ?? false,
                ]
            ),
            'activity' => $this->when(
                $this->shouldShowPrivateInfo($request) && $request->get('include_activity'),
                [
                    'login_count' => $this->login_count ?? 0,
                    'last_ip' => $this->last_ip,
                    'last_user_agent' => $this->last_user_agent,
                    'timezone' => $this->preferences['timezone'] ?? null,
                    'language' => $this->preferences['language'] ?? 'en',
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
        $response->header('X-Resource-Type', 'User');
        $response->header('X-Resource-ID', $this->id);
        
        // Add cache headers for public profiles
        if (!$this->shouldShowPrivateInfo($request)) {
            $response->header('Cache-Control', 'public, max-age=1800');
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
     * Get a minimal version of the user resource for listings.
     */
    public function toMinimalArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'role' => $this->role,
            'avatar_url' => $this->avatar_url,
            'is_active' => $this->is_active,
            'joined_at' => $this->created_at->format('M j, Y'),
            'posts_count' => $this->whenLoaded('posts', function () {
                return $this->posts->where('status', 'published')->count();
            }),
        ];
    }

    /**
     * Get an author version of the user resource.
     */
    public function toAuthorArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'bio' => $this->bio,
            'avatar_url' => $this->avatar_url,
            'website' => $this->website,
            'location' => $this->location,
            'social_links' => $this->social_links,
            'stats' => [
                'posts_count' => $this->whenLoaded('posts', function () {
                    return $this->posts->where('status', 'published')->count();
                }),
            ],
            'joined_at' => $this->created_at->format('M j, Y'),
            'url' => route('users.show', $this->id),
        ];
    }

    /**
     * Get an admin version of the user resource.
     */
    public function toAdminArray(Request $request): array
    {
        return array_merge($this->toArray($request), [
            'email' => $this->email,
            'phone' => $this->phone,
            'preferences' => $this->preferences,
            'access_restrictions' => $this->when(
                $request->user()?->can('viewRestrictions', $this->resource),
                $this->access_restrictions
            ),
            'admin_notes' => $this->when(
                $request->user()?->can('viewAdminNotes', $this->resource),
                $this->admin_notes
            ),
            'security' => [
                'two_factor_enabled' => !empty($this->two_factor_secret),
                'password_changed_at' => $this->password_changed_at?->toISOString(),
                'failed_login_attempts' => $this->failed_login_attempts ?? 0,
                'locked_until' => $this->locked_until?->toISOString(),
            ],
            'activity_summary' => $this->when(
                $request->get('include_activity_summary'),
                [
                    'total_logins' => $this->login_count ?? 0,
                    'posts_this_month' => $this->whenLoaded('posts', function () {
                        return $this->posts->where('created_at', '>=', now()->startOfMonth())->count();
                    }),
                    'media_this_month' => $this->whenLoaded('media', function () {
                        return $this->media->where('created_at', '>=', now()->startOfMonth())->count();
                    }),
                    'storage_used' => $this->getStorageUsage(),
                    'storage_limit' => $this->getStorageLimit(),
                ]
            ),
        ]);
    }

    /**
     * Get a profile version of the user resource.
     */
    public function toProfileArray(Request $request): array
    {
        $isOwnProfile = $request->user() && $request->user()->id === $this->id;
        
        return [
            'id' => $this->id,
            'name' => $this->name,
            'email' => $this->when($isOwnProfile, $this->email),
            'bio' => $this->bio,
            'avatar_url' => $this->avatar_url,
            'website' => $this->website,
            'location' => $this->location,
            'phone' => $this->when($isOwnProfile, $this->phone),
            'social_links' => $this->social_links,
            'preferences' => $this->when($isOwnProfile, $this->preferences),
            'stats' => [
                'posts_count' => $this->whenLoaded('posts', function () {
                    return $this->posts->where('status', 'published')->count();
                }),
                'comments_count' => $this->whenLoaded('comments', function () {
                    return $this->comments->where('status', 'approved')->count();
                }),
            ],
            'recent_posts' => PostResource::collection(
                $this->whenLoaded('recentPosts')
            ),
            'joined_at' => $this->created_at->format('M j, Y'),
            'last_active' => $this->when(
                $this->last_login_at,
                $this->last_login_at->diffForHumans()
            ),
            'is_following' => $this->when(
                $request->user() && method_exists($this->resource, 'isFollowedBy'),
                $this->isFollowedBy($request->user())
            ),
        ];
    }

    /**
     * Determine if email should be shown.
     */
    protected function shouldShowEmail(Request $request): bool
    {
        $user = $request->user();
        
        // Show email if it's the user's own profile
        if ($user && $user->id === $this->id) {
            return true;
        }
        
        // Show email if user has admin privileges
        if ($user && in_array($user->role, ['admin', 'editor'])) {
            return true;
        }
        
        return false;
    }

    /**
     * Determine if private information should be shown.
     */
    protected function shouldShowPrivateInfo(Request $request): bool
    {
        $user = $request->user();
        
        // Show private info if it's the user's own profile
        if ($user && $user->id === $this->id) {
            return true;
        }
        
        // Show private info if user has admin privileges
        if ($user && $user->role === 'admin') {
            return true;
        }
        
        // Show limited private info if user is editor and target is not admin
        if ($user && $user->role === 'editor' && $this->role !== 'admin') {
            return true;
        }
        
        return false;
    }
}