<?php

namespace App\Services;

use App\Models\Post;
use App\Repositories\PostRepository;
use App\Repositories\CategoryRepository;
use App\Repositories\MediaRepository;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Http\UploadedFile;

class PostService
{
    protected $postRepository;
    protected $categoryRepository;
    protected $mediaRepository;
    protected $fileUploadService;

    public function __construct(
        PostRepository $postRepository,
        CategoryRepository $categoryRepository,
        MediaRepository $mediaRepository,
        FileUploadService $fileUploadService
    ) {
        $this->postRepository = $postRepository;
        $this->categoryRepository = $categoryRepository;
        $this->mediaRepository = $mediaRepository;
        $this->fileUploadService = $fileUploadService;
    }

    /**
     * Get all posts with filters and pagination
     */
    public function getAllPosts(array $filters = [], $perPage = 15): LengthAwarePaginator
    {
        return $this->postRepository->getPaginatedWithFilters($filters, $perPage);
    }

    /**
     * Get published posts
     */
    public function getPublishedPosts($limit = null): Collection
    {
        return $this->postRepository->getPublished($limit);
    }

    /**
     * Get featured posts
     */
    public function getFeaturedPosts($limit = 5): Collection
    {
        return $this->postRepository->getFeatured($limit);
    }

    /**
     * Get slider posts
     */
    public function getSliderPosts($limit = 5): Collection
    {
        return $this->postRepository->getSlider($limit);
    }

    /**
     * Get popular posts
     */
    public function getPopularPosts($limit = 10): Collection
    {
        return $this->postRepository->getPopular($limit);
    }

    /**
     * Get recent posts
     */
    public function getRecentPosts($limit = 10): Collection
    {
        return $this->postRepository->getRecent($limit);
    }

    /**
     * Get posts by category
     */
    public function getPostsByCategory($categoryId, $limit = null): Collection
    {
        return $this->postRepository->getByCategory($categoryId, $limit);
    }

    /**
     * Get posts by type
     */
    public function getPostsByType($type, $limit = null): Collection
    {
        return $this->postRepository->getByType($type, $limit);
    }

    /**
     * Find post by ID
     */
    public function findPost($id): ?Post
    {
        return $this->postRepository->find($id);
    }

    /**
     * Find post by slug
     */
    public function findPostBySlug($slug): ?Post
    {
        return $this->postRepository->findBySlug($slug);
    }

    /**
     * Create new post
     */
    public function createPost(array $data): Post
    {
        DB::beginTransaction();

        try {
            // Generate slug if not provided
            if (empty($data['slug'])) {
                $data['slug'] = $this->generateUniqueSlug($data['title']);
            }

            // Set user_id to current authenticated user if not provided
            if (empty($data['user_id'])) {
                $data['user_id'] = Auth::id();
            }

            // Handle featured image upload or selection from media library
            if (isset($data['featured_image'])) {
                if ($data['featured_image'] instanceof UploadedFile) {
                    $media = $this->fileUploadService->upload($data['featured_image'], 'posts/featured');
                    $data['featured_image'] = $media->file_path;
                } elseif (is_numeric($data['featured_image'])) {
                    // If it's a numeric ID, look up the media
                    $media = \App\Models\Media::find($data['featured_image']);
                    if ($media) {
                        $data['featured_image'] = $media->file_path;
                    } else {
                        $data['featured_image'] = null; // Or handle error
                    }
                } elseif (is_string($data['featured_image']) && !empty($data['featured_image'])) {
                    // Keep the media library path as is
                    $data['featured_image'] = $data['featured_image'];
                } else {
                    // Remove featured image if empty
                    $data['featured_image'] = null;
                }
            }

            // Handle gallery images upload or selection from media library
            if (isset($data['gallery_images'])) {
                \Log::info('=== GALLERY DEBUG START ===');
                \Log::info('Processing gallery_images', ['data' => $data['gallery_images'], 'type' => gettype($data['gallery_images'])]);

                if (is_string($data['gallery_images']) && !empty($data['gallery_images'])) {
                    // Data from media picker (JSON string of media IDs)
                    $mediaIds = json_decode($data['gallery_images'], true);
                    \Log::info('Decoded media IDs', ['mediaIds' => $mediaIds]);

                    if (is_array($mediaIds) && !empty($mediaIds)) {
                        $galleryPaths = [];
                        foreach ($mediaIds as $mediaId) {
                            $media = \App\Models\Media::find($mediaId);
                            if ($media) {
                                $galleryPaths[] = $media->file_path;
                                \Log::info('Found media', ['id' => $mediaId, 'path' => $media->file_path]);
                            } else {
                                \Log::warning('Media not found', ['id' => $mediaId]);
                            }
                        }
                        $data['gallery_images'] = $galleryPaths;
                        \Log::info('Final gallery_images JSON', ['json' => $data['gallery_images']]);
                        \Log::info('Gallery paths array', ['paths' => $galleryPaths]);
                    } else {
                        \Log::info('No valid media IDs found');
                        $data['gallery_images'] = null;
                    }
                } elseif (is_array($data['gallery_images'])) {
                    // Handle direct file uploads (if any)
                    \Log::info('Processing direct file uploads');
                    $galleryPaths = [];
                    foreach ($data['gallery_images'] as $image) {
                        if ($image instanceof UploadedFile) {
                            $media = $this->fileUploadService->upload($image, 'posts/gallery');
                            $galleryPaths[] = $media->file_path;
                        }
                    }
                    $data['gallery_images'] = $galleryPaths;
                } else {
                    \Log::info('Gallery images is null or empty');
                    $data['gallery_images'] = null;
                }
            } else {
                \Log::info('No gallery_images in data');
            }

            // Set published_at if publishing
            if (!empty($data['is_published']) && empty($data['published_at'])) {
                $data['published_at'] = now();
            }

            // Generate excerpt if not provided
            if (empty($data['excerpt']) && !empty($data['content'])) {
                $data['excerpt'] = $this->generateExcerpt($data['content']);
            }

            // Generate meta title and description if not provided
            if (empty($data['meta_title'])) {
                $data['meta_title'] = $data['title'];
            }

            if (empty($data['meta_description']) && !empty($data['excerpt'])) {
                $data['meta_description'] = Str::limit(strip_tags($data['excerpt']), 160);
            }

            $post = $this->postRepository->create($data);

            \Log::info('=== POST CREATED ===');
            \Log::info('Post ID: ' . $post->id);
            \Log::info('Post gallery_images from DB', ['gallery_images' => $post->gallery_images]);
            \Log::info('Post gallery_images raw', ['raw' => $post->getRawOriginal('gallery_images')]);
            \Log::info('=== GALLERY DEBUG END ===');

            // Clear related caches
            $this->clearRelatedCaches();

            DB::commit();

            return $post;
        } catch (\Exception $e) {
            DB::rollback();
            throw $e;
        }
    }

    /**
     * Update post
     */
    public function updatePost($id, array $data): Post
    {
        DB::beginTransaction();

        try {
            $post = $this->postRepository->find($id);

            if (!$post) {
                throw new \Exception('Post not found');
            }

            // Generate slug if changed
            if (isset($data['title']) && $data['title'] !== $post->title) {
                if (empty($data['slug'])) {
                    $data['slug'] = $this->generateUniqueSlug($data['title'], $id);
                }
            }

            // Handle featured image upload or selection from media library
            if (isset($data['featured_image'])) {
                if ($data['featured_image'] instanceof UploadedFile) {
                    // Delete old featured image if exists
                    if ($post->featured_image) {
                        $this->fileUploadService->delete($post->featured_image);
                    }

                    $media = $this->fileUploadService->upload($data['featured_image'], 'posts/featured');
                    $data['featured_image'] = $media->file_path;
                } elseif (is_numeric($data['featured_image'])) {
                    // If it's a numeric ID, look up the media
                    $media = \App\Models\Media::find($data['featured_image']);
                    if ($media) {
                        // Only delete old image if we're changing to a different one
                        if ($post->featured_image && $post->featured_image !== $media->file_path) {
                            $this->fileUploadService->delete($post->featured_image);
                        }
                        $data['featured_image'] = $media->file_path;
                    }
                } elseif (is_string($data['featured_image']) && !empty($data['featured_image'])) {
                    // Only delete old image if we're changing to a different one
                    if ($post->featured_image && $post->featured_image !== $data['featured_image']) {
                        $this->fileUploadService->delete($post->featured_image);
                    }
                    // Keep the media library path as is
                    $data['featured_image'] = $data['featured_image'];
                } elseif ($data['featured_image'] === '' || $data['featured_image'] === null) {
                    // Remove featured image
                    if ($post->featured_image) {
                        $this->fileUploadService->delete($post->featured_image);
                    }
                    $data['featured_image'] = null;
                }
            }

            // Handle gallery images upload or selection from media library
            if (isset($data['gallery_images'])) {
                if (is_string($data['gallery_images']) && !empty($data['gallery_images'])) {
                    // Data from media picker (JSON string of media IDs)
                    $mediaIds = json_decode($data['gallery_images'], true);
                    if (is_array($mediaIds) && !empty($mediaIds)) {
                        // Only delete old images if we're changing to different ones
                        $newPaths = [];
                        foreach ($mediaIds as $mediaId) {
                            $media = \App\Models\Media::find($mediaId);
                            if ($media) {
                                $newPaths[] = $media->file_path;
                            }
                        }

                        // Check if gallery images actually changed
                        $oldPaths = $post->gallery_images ? json_decode($post->gallery_images, true) : [];
                        if ($oldPaths !== $newPaths) {
                            // Delete old gallery images if they're different
                            foreach ($oldPaths as $imagePath) {
                                if (!in_array($imagePath, $newPaths)) {
                                    $this->fileUploadService->delete($imagePath);
                                }
                            }
                        }

                        $data['gallery_images'] = $newPaths;
                    } else {
                        // Remove gallery images
                        if ($post->gallery_images) {
                            $oldImages = json_decode($post->gallery_images, true);
                            foreach ($oldImages as $imagePath) {
                                $this->fileUploadService->delete($imagePath);
                            }
                        }
                        $data['gallery_images'] = null;
                    }
                } elseif (is_array($data['gallery_images'])) {
                    // Handle direct file uploads (if any)
                    // Delete old gallery images if exists
                    if ($post->gallery_images) {
                        $oldImages = json_decode($post->gallery_images, true);
                        foreach ($oldImages as $imagePath) {
                            $this->fileUploadService->delete($imagePath);
                        }
                    }

                    $galleryPaths = [];
                    foreach ($data['gallery_images'] as $image) {
                        if ($image instanceof UploadedFile) {
                            $media = $this->fileUploadService->upload($image, 'posts/gallery');
                            $galleryPaths[] = $media->file_path;
                        }
                    }
                    $data['gallery_images'] = json_encode($galleryPaths);
                } elseif ($data['gallery_images'] === '' || $data['gallery_images'] === null) {
                    // Remove gallery images
                    if ($post->gallery_images) {
                        $oldImages = json_decode($post->gallery_images, true);
                        foreach ($oldImages as $imagePath) {
                            $this->fileUploadService->delete($imagePath);
                        }
                    }
                    $data['gallery_images'] = null;
                }
            }

            // Set published_at if publishing for the first time
            if (!empty($data['is_published']) && !$post->is_published && empty($data['published_at'])) {
                $data['published_at'] = now();
            }

            // Unset published_at if unpublishing
            if (isset($data['is_published']) && !$data['is_published']) {
                $data['published_at'] = null;
            }

            // Generate excerpt if not provided
            if (empty($data['excerpt']) && !empty($data['content'])) {
                $data['excerpt'] = $this->generateExcerpt($data['content']);
            }

            // Update meta title and description if not provided
            if (empty($data['meta_title']) && !empty($data['title'])) {
                $data['meta_title'] = $data['title'];
            }

            if (empty($data['meta_description']) && !empty($data['excerpt'])) {
                $data['meta_description'] = Str::limit(strip_tags($data['excerpt']), 160);
            }

            $updatedPost = $this->postRepository->update($id, $data);

            \Log::info('=== POST UPDATED ===');
            \Log::info('Updated Post ID: ' . $updatedPost->id);
            \Log::info('Updated Post gallery_images from DB', ['gallery_images' => $updatedPost->gallery_images]);
            \Log::info('Updated Post gallery_images raw', ['raw' => $updatedPost->getRawOriginal('gallery_images')]);
            \Log::info('=== GALLERY DEBUG END ===');

            // Clear related caches
            $this->clearRelatedCaches();

            DB::commit();

            return $updatedPost;
        } catch (\Exception $e) {
            DB::rollback();
            throw $e;
        }
    }

    /**
     * Delete post
     */
    public function deletePost($id): bool
    {
        DB::beginTransaction();

        try {
            $post = $this->postRepository->find($id);

            if (!$post) {
                return false;
            }

            // Delete featured image if exists
            if ($post->featured_image) {
                $this->fileUploadService->delete($post->featured_image);
            }

            // Delete gallery images if exists
            if ($post->gallery_images) {
                $galleryImages = json_decode($post->gallery_images, true);
                foreach ($galleryImages as $imagePath) {
                    $this->fileUploadService->delete($imagePath);
                }
            }

            $deleted = $this->postRepository->delete($id);

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
     * Bulk publish posts
     */
    public function bulkPublish(array $ids): int
    {
        $updated = $this->postRepository->bulkPublish($ids);
        $this->clearRelatedCaches();
        return $updated;
    }

    /**
     * Bulk unpublish posts
     */
    public function bulkUnpublish(array $ids): int
    {
        $updated = $this->postRepository->bulkUnpublish($ids);
        $this->clearRelatedCaches();
        return $updated;
    }

    /**
     * Bulk delete posts
     */
    public function bulkDelete(array $ids): int
    {
        DB::beginTransaction();

        try {
            $posts = $this->postRepository->whereIn('id', $ids);

            // Delete associated files
            foreach ($posts as $post) {
                if ($post->featured_image) {
                    $this->fileUploadService->delete($post->featured_image);
                }

                if ($post->gallery_images) {
                    $galleryImages = json_decode($post->gallery_images, true);
                    foreach ($galleryImages as $imagePath) {
                        $this->fileUploadService->delete($imagePath);
                    }
                }
            }

            $deleted = $this->postRepository->bulkDelete($ids);

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
     * Search posts
     */
    public function searchPosts($query, array $filters = []): Collection
    {
        return $this->postRepository->search($query, $filters);
    }

    /**
     * Increment post views
     */
    public function incrementViews($id): bool
    {
        return $this->postRepository->incrementViews($id);
    }

    /**
     * Get related posts
     */
    public function getRelatedPosts($postId, $categoryId, $limit = 5): Collection
    {
        return $this->postRepository->getRelated($postId, $categoryId, $limit);
    }

    /**
     * Get post statistics
     */
    public function getStatistics(): array
    {
        return $this->postRepository->getStatistics();
    }

    /**
     * Get posts count by type
     */
    public function getCountByType(): array
    {
        return $this->postRepository->getCountByType();
    }

    /**
     * Get posts count by month
     */
    public function getCountByMonth($year = null): array
    {
        return $this->postRepository->getCountByMonth($year);
    }

    /**
     * Generate unique slug
     */
    protected function generateUniqueSlug($title, $excludeId = null): string
    {
        $slug = Str::slug($title);
        $originalSlug = $slug;
        $counter = 1;

        while ($this->slugExists($slug, $excludeId)) {
            $slug = $originalSlug . '-' . $counter;
            $counter++;
        }

        return $slug;
    }

    /**
     * Check if slug exists
     */
    protected function slugExists($slug, $excludeId = null): bool
    {
        $query = Post::where('slug', $slug);

        if ($excludeId) {
            $query->where('id', '!=', $excludeId);
        }

        return $query->exists();
    }

    /**
     * Generate excerpt from content
     */
    protected function generateExcerpt($content, $length = 160): string
    {
        $text = strip_tags($content);
        return Str::limit($text, $length);
    }

    /**
     * Clear related caches
     */
    protected function clearRelatedCaches(): void
    {
        // Check if cache driver supports tagging
        if (method_exists(Cache::getStore(), 'tags')) {
            Cache::tags(['posts', 'categories', 'homepage'])->flush();
        } else {
            // Fallback for cache drivers that don't support tagging (like database)
            Cache::flush();
        }
    }

    /**
     * Duplicate post
     */
    public function duplicatePost($id): Post
    {
        DB::beginTransaction();

        try {
            $originalPost = $this->postRepository->find($id);

            if (!$originalPost) {
                throw new \Exception('Post not found');
            }

            $data = $originalPost->toArray();

            // Remove ID and timestamps
            unset($data['id'], $data['created_at'], $data['updated_at']);

            // Modify title and slug
            $data['title'] = $data['title'] . ' (Copy)';
            $data['slug'] = $this->generateUniqueSlug($data['title']);

            // Set as draft
            $data['is_published'] = false;
            $data['published_at'] = null;

            // Set current user as author
            $data['user_id'] = Auth::id();

            $duplicatedPost = $this->postRepository->create($data);

            DB::commit();

            return $duplicatedPost;
        } catch (\Exception $e) {
            DB::rollback();
            throw $e;
        }
    }

    /**
     * Get post analytics
     */
    public function getPostAnalytics($id): array
    {
        $post = $this->postRepository->find($id);

        if (!$post) {
            return [];
        }

        return [
            'views' => $post->views,
            'comments_count' => $post->comments()->count(),
            'shares_count' => $post->shares_count ?? 0,
            'reading_time' => $this->calculateReadingTime($post->content),
            'word_count' => str_word_count(strip_tags($post->content)),
            'character_count' => strlen(strip_tags($post->content)),
            'published_days_ago' => $post->published_at ? $post->published_at->diffInDays(now()) : null,
            'last_updated_days_ago' => $post->updated_at->diffInDays(now()),
        ];
    }

    /**
     * Calculate reading time
     */
    protected function calculateReadingTime($content): int
    {
        $wordCount = str_word_count(strip_tags($content));
        $wordsPerMinute = 200; // Average reading speed

        return max(1, ceil($wordCount / $wordsPerMinute));
    }

    /**
     * Get trending posts
     */
    public function getTrendingPosts($days = 7, $limit = 10): Collection
    {
        return $this->postRepository->where('created_at', '>=', now()->subDays($days))
            ->published()
            ->orderBy('views', 'desc')
            ->limit($limit)
            ->get();
    }

    /**
     * Schedule post publication
     */
    public function schedulePost($id, $publishAt): bool
    {
        return $this->postRepository->update($id, [
            'is_published' => false,
            'published_at' => $publishAt,
            'status' => 'scheduled'
        ]);
    }

    /**
     * Get scheduled posts
     */
    public function getScheduledPosts(): Collection
    {
        return $this->postRepository->where('is_published', false)
            ->where('published_at', '>', now())
            ->orderBy('published_at')
            ->get();
    }
}