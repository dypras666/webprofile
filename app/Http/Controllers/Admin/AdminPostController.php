<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Admin\BaseAdminController;
use App\Http\Requests\PostRequest;
use App\Services\PostService;
use App\Services\CategoryService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;

class AdminPostController extends BaseAdminController
{
    protected $postService;
    protected $categoryService;

    public function __construct(
        PostService $postService,
        CategoryService $categoryService
    ) {
        parent::__construct();
        $this->postService = $postService;
        $this->categoryService = $categoryService;

        // Set permissions
        $this->middleware('permission:manage_posts');
    }

    /**
     * Display a listing of posts
     */
    public function index(Request $request): View
    {
        $filters = $this->getFilters($request, [
            'search',
            'type',
            'category_id',
            'status',
            'is_featured',
            'is_slider',
            'date_from',
            'date_to',
            'author_id'
        ]);

        $posts = $this->postService->getAllPosts($filters, $request->get('per_page', 15));
        $categories = $this->categoryService->getCategoriesForDropdown();
        $statistics = $this->postService->getStatistics();

        return view('admin.posts.index', compact('posts', 'categories', 'statistics', 'filters'));
    }

    /**
     * Show the form for creating a new post
     */
    public function create(Request $request): View
    {
        $categories = $this->categoryService->getCategoriesForDropdown();
        $currentType = $request->get('type', 'berita');

        return view('admin.posts.create', compact('categories', 'currentType'));
    }

    /**
     * Store a newly created post
     */
    public function store(PostRequest $request): RedirectResponse
    {
        try {
            $post = $this->postService->createPost($request->validated());

            $this->logActivity('post_created', 'Created post: ' . $post->title, $post->id);

            // Redirect based on post type
            $redirectRoute = 'admin.posts.index';
            $redirectParams = ['type' => $post->type];

            return redirect()->route($redirectRoute, $redirectParams)
                ->with('success', 'Post created successfully.');
        } catch (\Exception $e) {
            return back()->withInput()
                ->with('error', 'Failed to create post: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified post
     */
    public function show($id): View
    {
        $post = $this->postService->findPost($id);

        if (!$post) {
            abort(404, 'Post not found');
        }

        $relatedPosts = $this->postService->getRelatedPosts($id, 5);
        $analytics = $this->postService->getPostAnalytics($id);

        return view('admin.posts.show', compact('post', 'relatedPosts', 'analytics'));
    }

    /**
     * Show the form for editing the specified post
     */
    public function edit($id): View
    {
        $post = $this->postService->findPost($id);

        if (!$post) {
            abort(404, 'Post not found');
        }

        $categories = $this->categoryService->getCategoriesForDropdown();

        return view('admin.posts.edit', compact('post', 'categories'));
    }

    /**
     * Update the specified post
     */
    public function update(PostRequest $request, $id): RedirectResponse
    {
        try {
            \Log::info('=== ADMIN CONTROLLER DEBUG START ===');
            \Log::info('Admin Request data received', ['data' => $request->validated()]);
            \Log::info('Admin Gallery images in request', ['gallery_images' => $request->input('gallery_images')]);
            \Log::info('Admin All request data', ['all' => $request->all()]);

            $post = $this->postService->updatePost($id, $request->validated());

            if (!$post) {
                return back()->with('error', 'Post not found.');
            }

            $this->logActivity('post_updated', 'Updated post: ' . $post->title, $post->id);

            // Redirect based on post type
            $redirectRoute = 'admin.posts.index';
            $redirectParams = ['type' => $post->type];

            return redirect()->route($redirectRoute, $redirectParams)
                ->with('success', 'Post updated successfully.');
        } catch (\Exception $e) {
            return back()->withInput()
                ->with('error', 'Failed to update post: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified post
     */
    public function destroy($id): RedirectResponse
    {
        try {
            $post = $this->postService->findPost($id);

            if (!$post) {
                return back()->with('error', 'Post not found.');
            }

            $title = $post->title;
            $deleted = $this->postService->deletePost($id);

            if ($deleted) {
                $this->logActivity('post_deleted', 'Deleted post: ' . $title, $id);
                // Redirect to index with type parameter to preserve filter
                return redirect()->route('admin.posts.index', ['type' => $post->type])
                    ->with('success', 'Post deleted successfully.');
            }

            return back()->with('error', 'Failed to delete post.');
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to delete post: ' . $e->getMessage());
        }
    }

    /**
     * Bulk actions for posts
     */
    public function bulkAction(Request $request): JsonResponse
    {
        $request->validate([
            'action' => 'required|in:publish,unpublish,delete,feature,unfeature,slider,unslider',
            'ids' => 'required|array|min:1',
            'ids.*' => 'integer|exists:posts,id'
        ]);

        try {
            $action = $request->action;
            $ids = $request->ids;
            $count = 0;

            switch ($action) {
                case 'publish':
                    $count = $this->postService->bulkPublish($ids);
                    $message = "Published {$count} posts";
                    break;

                case 'unpublish':
                    $count = $this->postService->bulkUnpublish($ids);
                    $message = "Unpublished {$count} posts";
                    break;

                case 'delete':
                    $count = $this->postService->bulkDelete($ids);
                    $message = "Deleted {$count} posts";
                    break;

                case 'feature':
                    $count = $this->postService->bulkFeature($ids);
                    $message = "Featured {$count} posts";
                    break;

                case 'unfeature':
                    $count = $this->postService->bulkUnfeature($ids);
                    $message = "Unfeatured {$count} posts";
                    break;

                case 'slider':
                    $count = $this->postService->bulkSlider($ids);
                    $message = "Added {$count} posts to slider";
                    break;

                case 'unslider':
                    $count = $this->postService->bulkUnslider($ids);
                    $message = "Removed {$count} posts from slider";
                    break;

                default:
                    return $this->errorResponse('Invalid action');
            }

            $this->logActivity('posts_bulk_' . $action, $message, null, ['ids' => $ids]);

            return $this->successResponse($message, ['count' => $count]);
        } catch (\Exception $e) {
            return $this->errorResponse('Bulk action failed: ' . $e->getMessage());
        }
    }

    /**
     * Duplicate post
     */
    public function duplicate($id): RedirectResponse
    {
        try {
            $newPost = $this->postService->duplicatePost($id);

            if (!$newPost) {
                return back()->with('error', 'Post not found.');
            }

            $this->logActivity('post_duplicated', 'Duplicated post: ' . $newPost->title, $newPost->id);

            return redirect()->route('admin.posts.edit', $newPost->id)
                ->with('success', 'Post duplicated successfully. You can now edit the copy.');
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to duplicate post: ' . $e->getMessage());
        }
    }

    /**
     * Get post statistics
     */
    public function statistics(): JsonResponse
    {
        try {
            $stats = $this->postService->getStatistics();
            $monthlyStats = $this->postService->getCountByMonth();
            $typeStats = $this->postService->getCountByType();

            return $this->successResponse('Statistics retrieved', [
                'general' => $stats,
                'monthly' => $monthlyStats,
                'by_type' => $typeStats
            ]);
        } catch (\Exception $e) {
            return $this->errorResponse('Failed to get statistics: ' . $e->getMessage());
        }
    }

    /**
     * Export posts
     */
    public function export(Request $request)
    {
        try {
            $format = $request->get('format', 'csv');

            if ($format === 'csv') {
                $csvData = $this->postService->exportToCSV();

                return response($csvData)
                    ->header('Content-Type', 'text/csv')
                    ->header('Content-Disposition', 'attachment; filename="posts_' . date('Y-m-d') . '.csv"');
            }

            return back()->with('error', 'Unsupported export format.');
        } catch (\Exception $e) {
            return back()->with('error', 'Export failed: ' . $e->getMessage());
        }
    }

    /**
     * Search posts (AJAX)
     */
    public function search(Request $request): JsonResponse
    {
        $request->validate([
            'query' => 'required|string|min:2',
            'limit' => 'integer|min:1|max:50'
        ]);

        try {
            $posts = $this->postService->searchPosts(
                $request->query,
                [],
                $request->get('limit', 10)
            );

            $results = $posts->map(function ($post) {
                return [
                    'id' => $post->id,
                    'title' => $post->title,
                    'slug' => $post->slug,
                    'type' => $post->type,
                    'status' => $post->is_published ? 'Published' : 'Draft',
                    'created_at' => $post->created_at->format('M d, Y'),
                    'url' => route('admin.posts.edit', $post->id)
                ];
            });

            return $this->successResponse('Search completed', $results);
        } catch (\Exception $e) {
            return $this->errorResponse('Search failed: ' . $e->getMessage());
        }
    }

    /**
     * Get trending posts
     */
    public function trending(): JsonResponse
    {
        try {
            $trendingPosts = $this->postService->getTrendingPosts(10);

            $results = $trendingPosts->map(function ($post) {
                return [
                    'id' => $post->id,
                    'title' => $post->title,
                    'views' => $post->views,
                    'created_at' => $post->created_at->format('M d, Y'),
                    'url' => route('admin.posts.show', $post->id)
                ];
            });

            return $this->successResponse('Trending posts retrieved', $results);
        } catch (\Exception $e) {
            return $this->errorResponse('Failed to get trending posts: ' . $e->getMessage());
        }
    }

    /**
     * Schedule post publication
     */
    public function schedule(Request $request, $id): JsonResponse
    {
        $request->validate([
            'published_at' => 'required|date|after:now'
        ]);

        try {
            $post = $this->postService->schedulePost($id, $request->published_at);

            if (!$post) {
                return $this->errorResponse('Post not found');
            }

            $this->logActivity('post_scheduled', 'Scheduled post: ' . $post->title, $post->id);

            return $this->successResponse('Post scheduled successfully', [
                'published_at' => $post->published_at->format('Y-m-d H:i:s')
            ]);
        } catch (\Exception $e) {
            return $this->errorResponse('Failed to schedule post: ' . $e->getMessage());
        }
    }

    /**
     * Get post analytics
     */
    public function analytics($id): JsonResponse
    {
        try {
            $analytics = $this->postService->getPostAnalytics($id);

            if (!$analytics) {
                return $this->errorResponse('Post not found');
            }

            return $this->successResponse('Analytics retrieved', $analytics);
        } catch (\Exception $e) {
            return $this->errorResponse('Failed to get analytics: ' . $e->getMessage());
        }
    }

    /**
     * Generate post suggestions
     */
    public function suggestions(Request $request): JsonResponse
    {
        $request->validate([
            'content' => 'required|string|min:10'
        ]);

        try {
            $suggestions = $this->postService->getContentSuggestions($request->content);

            return $this->successResponse('Suggestions generated', $suggestions);
        } catch (\Exception $e) {
            return $this->errorResponse('Failed to generate suggestions: ' . $e->getMessage());
        }
    }

    /**
     * Calculate reading time
     */
    public function readingTime(Request $request): JsonResponse
    {
        $request->validate([
            'content' => 'required|string'
        ]);

        try {
            $readingTime = $this->postService->calculateReadingTime($request->content);

            return $this->successResponse('Reading time calculated', [
                'reading_time' => $readingTime,
                'formatted' => $readingTime . ' min read'
            ]);
        } catch (\Exception $e) {
            return $this->errorResponse('Failed to calculate reading time: ' . $e->getMessage());
        }
    }

    /**
     * Auto-save post (AJAX)
     */
    public function autoSave(Request $request, $id): JsonResponse
    {
        try {
            $data = $request->only(['title', 'content', 'excerpt']);
            $data['is_published'] = false; // Auto-save as draft

            $post = $this->postService->updatePost($id, $data);

            if (!$post) {
                return $this->errorResponse('Post not found');
            }

            return $this->successResponse('Post auto-saved', [
                'saved_at' => now()->format('H:i:s')
            ]);
        } catch (\Exception $e) {
            return $this->errorResponse('Auto-save failed: ' . $e->getMessage());
        }
    }
}