<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Admin\BaseAdminController;
use App\Services\PostService;
use App\Services\CategoryService;
use App\Services\UserService;
use App\Services\MediaService;
use App\Services\SiteSettingService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\View\View;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Carbon\Carbon;

class AdminDashboardController extends BaseAdminController
{
    protected $postService;
    protected $categoryService;
    protected $userService;
    protected $mediaService;
    protected $settingService;

    public function __construct(
        PostService $postService,
        CategoryService $categoryService,
        UserService $userService,
        MediaService $mediaService,
        SiteSettingService $settingService
    ) {
        parent::__construct();
        $this->postService = $postService;
        $this->categoryService = $categoryService;
        $this->userService = $userService;
        $this->mediaService = $mediaService;
        $this->settingService = $settingService;
    }

    /**
     * Display the admin dashboard
     */
    public function index(): View
    {
        $dashboardData = $this->getDashboardData();
        
        return view('admin.dashboard.index', $dashboardData);
    }

    /**
     * Get dashboard data
     */
    protected function getDashboardData(): array
    {
        // Cache dashboard data for 5 minutes
        return Cache::remember('admin_dashboard_data', 300, function () {
            return [
                'statistics' => $this->getStatistics(),
                'recent_posts' => $this->getRecentPosts(),
                'recent_users' => $this->getRecentUsers(),
                'recent_media' => $this->getRecentMedia(),
                'popular_posts' => $this->getPopularPosts(),
                'top_categories' => $this->getTopCategories(),
                'system_info' => $this->getSystemInfo(),
                'activity_log' => $this->getRecentActivity(),
                'charts_data' => $this->getChartsData(),
                'quick_stats' => $this->getQuickStats(),
                'notifications' => $this->getNotifications()
            ];
        });
    }

    /**
     * Get general statistics
     */
    protected function getStatistics(): array
    {
        return [
            'posts' => [
                'total' => $this->postService->getStatistics()['total'] ?? 0,
                'published' => $this->postService->getStatistics()['published'] ?? 0,
                'draft' => $this->postService->getStatistics()['draft'] ?? 0,
                'this_month' => $this->postService->getCountByMonth()[date('Y-m')] ?? 0
            ],
            'users' => [
                'total' => $this->userService->getStatistics()['total'] ?? 0,
                'active' => $this->userService->getStatistics()['active'] ?? 0,
                'new_this_month' => $this->userService->getCountByMonth()[date('Y-m')] ?? 0,
                'online' => $this->getOnlineUsersCount()
            ],
            'categories' => [
                'total' => $this->categoryService->getStatistics()['total'] ?? 0,
                'active' => $this->categoryService->getStatistics()['active'] ?? 0
            ],
            'media' => [
                'total' => $this->mediaService->getStatistics()['total'] ?? 0,
                'total_size' => $this->mediaService->getStorageStatistics()['total_size'] ?? 0,
                'this_month' => $this->mediaService->getStatistics()['this_month'] ?? 0
            ]
        ];
    }

    /**
     * Get recent posts
     */
    protected function getRecentPosts(int $limit = 5): array
    {
        $posts = $this->postService->getRecentPosts($limit);
        
        return $posts->map(function ($post) {
            return [
                'id' => $post->id,
                'title' => $post->title,
                'status' => $post->status,
                'author' => $post->user->name ?? 'Unknown',
                'created_at' => $post->created_at->format('M d, Y'),
                'url' => route('admin.posts.edit', $post->id)
            ];
        })->toArray();
    }

    /**
     * Get recent users
     */
    protected function getRecentUsers(int $limit = 5): array
    {
        $users = $this->userService->getNewUsers(30, $limit);
        
        return $users->map(function ($user) {
            return [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'role' => $user->role,
                'created_at' => $user->created_at->format('M d, Y'),
                'url' => route('admin.users.show', $user->id)
            ];
        })->toArray();
    }

    /**
     * Get recent media
     */
    protected function getRecentMedia(int $limit = 5): array
    {
        $media = $this->mediaService->getRecentMedia($limit);
        
        return $media->map(function ($item) {
            return [
                'id' => $item->id,
                'filename' => $item->filename,
                'type' => $item->type,
                'size' => $this->formatBytes($item->size),
                'created_at' => $item->created_at->format('M d, Y'),
                'url' => $item->url,
                'thumbnail' => $item->thumbnail_url
            ];
        })->toArray();
    }

    /**
     * Get popular posts
     */
    protected function getPopularPosts(int $limit = 5): array
    {
        $posts = $this->postService->getPopularPosts($limit);
        
        return $posts->map(function ($post) {
            return [
                'id' => $post->id,
                'title' => $post->title,
                'views' => $post->views ?? 0,
                'comments' => $post->comments_count ?? 0,
                'created_at' => $post->created_at->format('M d, Y'),
                'url' => route('admin.posts.edit', $post->id)
            ];
        })->toArray();
    }

    /**
     * Get top categories
     */
    protected function getTopCategories(int $limit = 5): array
    {
        $categories = $this->categoryService->getPopularCategories($limit);
        
        return $categories->map(function ($category) {
            return [
                'id' => $category->id,
                'name' => $category->name,
                'posts_count' => $category->posts_count ?? 0,
                'color' => $category->color,
                'url' => route('admin.categories.edit', $category->id)
            ];
        })->toArray();
    }

    /**
     * Get system information
     */
    protected function getSystemInfo(): array
    {
        return [
            'php_version' => PHP_VERSION,
            'laravel_version' => app()->version(),
            'server_software' => $_SERVER['SERVER_SOFTWARE'] ?? 'Unknown',
            'database_type' => config('database.default'),
            'memory_limit' => ini_get('memory_limit'),
            'max_execution_time' => ini_get('max_execution_time'),
            'upload_max_filesize' => ini_get('upload_max_filesize'),
            'disk_space' => $this->getDiskSpace(),
            'cache_driver' => config('cache.default'),
            'queue_driver' => config('queue.default')
        ];
    }

    /**
     * Get recent activity log
     */
    protected function getRecentActivity(int $limit = 10): array
    {
        // This would typically come from an activity log table
        // For now, return mock data
        return [
            [
                'user' => Auth::user()->name,
                'action' => 'Created post',
                'description' => 'New blog post published',
                'created_at' => now()->subMinutes(5)->format('M d, Y H:i')
            ],
            [
                'user' => 'Admin',
                'action' => 'Updated settings',
                'description' => 'Site settings modified',
                'created_at' => now()->subHours(2)->format('M d, Y H:i')
            ]
        ];
    }

    /**
     * Get charts data
     */
    protected function getChartsData(): array
    {
        $last30Days = collect(range(29, 0))->map(function ($daysAgo) {
            return Carbon::now()->subDays($daysAgo)->format('Y-m-d');
        });

        return [
            'posts_chart' => [
                'labels' => $last30Days->map(function ($date) {
                    return Carbon::parse($date)->format('M d');
                })->toArray(),
                'data' => $this->getPostsChartData($last30Days)
            ],
            'users_chart' => [
                'labels' => $last30Days->map(function ($date) {
                    return Carbon::parse($date)->format('M d');
                })->toArray(),
                'data' => $this->getUsersChartData($last30Days)
            ],
            'media_chart' => [
                'labels' => ['Images', 'Videos', 'Documents', 'Audio', 'Others'],
                'data' => $this->getMediaTypeChartData()
            ]
        ];
    }

    /**
     * Get posts chart data
     */
    protected function getPostsChartData($dates): array
    {
        return $dates->map(function ($date) {
            // This would typically query the database for posts created on each date
            return rand(0, 10); // Mock data
        })->toArray();
    }

    /**
     * Get users chart data
     */
    protected function getUsersChartData($dates): array
    {
        return $dates->map(function ($date) {
            // This would typically query the database for users created on each date
            return rand(0, 5); // Mock data
        })->toArray();
    }

    /**
     * Get media type chart data
     */
    protected function getMediaTypeChartData(): array
    {
        $typeStats = $this->mediaService->getCountByType();
        
        return [
            $typeStats['image'] ?? 0,
            $typeStats['video'] ?? 0,
            $typeStats['document'] ?? 0,
            $typeStats['audio'] ?? 0,
            $typeStats['other'] ?? 0
        ];
    }

    /**
     * Get quick stats
     */
    protected function getQuickStats(): array
    {
        return [
            'today_posts' => $this->getTodayPostsCount(),
            'today_users' => $this->getTodayUsersCount(),
            'today_media' => $this->getTodayMediaCount(),
            'pending_comments' => $this->getPendingCommentsCount(),
            'storage_used' => $this->getStorageUsedPercentage(),
            'cache_hit_rate' => $this->getCacheHitRate()
        ];
    }

    /**
     * Get notifications
     */
    protected function getNotifications(): array
    {
        $notifications = [];
        
        // Check for system updates
        if ($this->checkForUpdates()) {
            $notifications[] = [
                'type' => 'info',
                'title' => 'System Update Available',
                'message' => 'A new version is available for download.',
                'action_url' => route('admin.system.updates')
            ];
        }
        
        // Check for low disk space
        if ($this->getDiskSpacePercentage() > 90) {
            $notifications[] = [
                'type' => 'warning',
                'title' => 'Low Disk Space',
                'message' => 'Disk space is running low. Consider cleaning up old files.',
                'action_url' => route('admin.media.cleanup')
            ];
        }
        
        // Check for pending comments
        $pendingComments = $this->getPendingCommentsCount();
        if ($pendingComments > 0) {
            $notifications[] = [
                'type' => 'info',
                'title' => 'Pending Comments',
                'message' => "You have {$pendingComments} comments waiting for approval.",
                'action_url' => route('admin.comments.pending')
            ];
        }
        
        return $notifications;
    }

    /**
     * Get dashboard data via AJAX
     */
    public function getData(Request $request): JsonResponse
    {
        try {
            $type = $request->get('type', 'all');
            
            switch ($type) {
                case 'statistics':
                    $data = $this->getStatistics();
                    break;
                case 'charts':
                    $data = $this->getChartsData();
                    break;
                case 'activity':
                    $data = $this->getRecentActivity();
                    break;
                case 'notifications':
                    $data = $this->getNotifications();
                    break;
                default:
                    $data = $this->getDashboardData();
            }
            
            return $this->successResponse('Dashboard data retrieved', $data);
        } catch (\Exception $e) {
            return $this->errorResponse('Failed to get dashboard data: ' . $e->getMessage());
        }
    }

    /**
     * Refresh dashboard cache
     */
    public function refreshCache(): JsonResponse
    {
        try {
            Cache::forget('admin_dashboard_data');
            $data = $this->getDashboardData();
            
            $this->logActivity('dashboard_cache_refreshed', 'Dashboard cache refreshed');
            
            return $this->successResponse('Dashboard cache refreshed', $data);
        } catch (\Exception $e) {
            return $this->errorResponse('Failed to refresh cache: ' . $e->getMessage());
        }
    }

    /**
     * Get widget data
     */
    public function getWidget(Request $request): JsonResponse
    {
        $request->validate([
            'widget' => 'required|in:recent_posts,recent_users,popular_posts,top_categories,system_info,activity_log'
        ]);
        
        try {
            $widget = $request->widget;
            $data = null;
            
            switch ($widget) {
                case 'recent_posts':
                    $data = $this->getRecentPosts();
                    break;
                case 'recent_users':
                    $data = $this->getRecentUsers();
                    break;
                case 'popular_posts':
                    $data = $this->getPopularPosts();
                    break;
                case 'top_categories':
                    $data = $this->getTopCategories();
                    break;
                case 'system_info':
                    $data = $this->getSystemInfo();
                    break;
                case 'activity_log':
                    $data = $this->getRecentActivity();
                    break;
            }
            
            return $this->successResponse('Widget data retrieved', $data);
        } catch (\Exception $e) {
            return $this->errorResponse('Failed to get widget data: ' . $e->getMessage());
        }
    }

    /**
     * Helper methods
     */
    
    protected function getOnlineUsersCount(): int
    {
        // This would typically check for users active in the last 5 minutes
        return rand(1, 10); // Mock data
    }
    
    protected function getTodayPostsCount(): int
    {
        return $this->postService->getCountByDate(today());
    }
    
    protected function getTodayUsersCount(): int
    {
        return $this->userService->getCountByDate(today());
    }
    
    protected function getTodayMediaCount(): int
    {
        return $this->mediaService->getCountByDate(today());
    }
    
    protected function getPendingCommentsCount(): int
    {
        // This would typically query a comments table
        return rand(0, 20); // Mock data
    }
    
    protected function getStorageUsedPercentage(): float
    {
        $diskSpace = $this->getDiskSpace();
        return round(($diskSpace['used'] / $diskSpace['total']) * 100, 2);
    }
    
    protected function getCacheHitRate(): float
    {
        // This would typically come from cache statistics
        return rand(85, 99); // Mock data
    }
    
    protected function getDiskSpace(): array
    {
        $total = disk_total_space(storage_path());
        $free = disk_free_space(storage_path());
        $used = $total - $free;
        
        return [
            'total' => $total,
            'used' => $used,
            'free' => $free,
            'total_formatted' => $this->formatBytes($total),
            'used_formatted' => $this->formatBytes($used),
            'free_formatted' => $this->formatBytes($free)
        ];
    }
    
    protected function getDiskSpacePercentage(): float
    {
        $diskSpace = $this->getDiskSpace();
        return round(($diskSpace['used'] / $diskSpace['total']) * 100, 2);
    }
    
    protected function checkForUpdates(): bool
    {
        // This would typically check for system updates
        return rand(0, 1) === 1; // Mock data
    }
    
    protected function formatBytes($bytes, $precision = 2): string
    {
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];
        
        for ($i = 0; $bytes > 1024 && $i < count($units) - 1; $i++) {
            $bytes /= 1024;
        }
        
        return round($bytes, $precision) . ' ' . $units[$i];
    }
}