<?php

namespace App\Services;

use App\Models\User;
use App\Models\Post;
use App\Models\Comment;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Notification;
use Illuminate\Notifications\DatabaseNotification;
use Carbon\Carbon;

class NotificationService
{
    /**
     * Get paginated notifications for a user.
     */
    public function getUserNotifications(User $user, array $filters = [], int $perPage = 20): LengthAwarePaginator
    {
        $query = $user->notifications()
            ->when(isset($filters['type']), function ($q) use ($filters) {
                $q->where('type', $filters['type']);
            })
            ->when(isset($filters['read']), function ($q) use ($filters) {
                if ($filters['read']) {
                    $q->whereNotNull('read_at');
                } else {
                    $q->whereNull('read_at');
                }
            })
            ->when(isset($filters['date_from']), function ($q) use ($filters) {
                $q->whereDate('created_at', '>=', $filters['date_from']);
            })
            ->when(isset($filters['date_to']), function ($q) use ($filters) {
                $q->whereDate('created_at', '<=', $filters['date_to']);
            })
            ->orderBy('created_at', 'desc');

        return $query->paginate($perPage);
    }

    /**
     * Get unread notification count for a user.
     */
    public function getUnreadCount(User $user): int
    {
        $cacheKey = "user_{$user->id}_unread_notifications";
        
        return Cache::remember($cacheKey, 300, function () use ($user) {
            return $user->unreadNotifications()->count();
        });
    }

    /**
     * Mark notification as read.
     */
    public function markAsRead(string $notificationId, User $user): bool
    {
        $notification = $user->notifications()->find($notificationId);
        
        if (!$notification || $notification->read_at) {
            return false;
        }
        
        $notification->markAsRead();
        
        // Clear cache
        $this->clearUserNotificationCache($user);
        
        return true;
    }

    /**
     * Mark all notifications as read for a user.
     */
    public function markAllAsRead(User $user): int
    {
        $count = $user->unreadNotifications()->update([
            'read_at' => now()
        ]);
        
        // Clear cache
        $this->clearUserNotificationCache($user);
        
        return $count;
    }

    /**
     * Mark notifications as read by type.
     */
    public function markAsReadByType(User $user, string $type): int
    {
        $count = $user->unreadNotifications()
            ->where('type', $type)
            ->update(['read_at' => now()]);
        
        // Clear cache
        $this->clearUserNotificationCache($user);
        
        return $count;
    }

    /**
     * Delete notification.
     */
    public function deleteNotification(string $notificationId, User $user): bool
    {
        $notification = $user->notifications()->find($notificationId);
        
        if (!$notification) {
            return false;
        }
        
        $deleted = $notification->delete();
        
        // Clear cache
        $this->clearUserNotificationCache($user);
        
        return $deleted;
    }

    /**
     * Delete all notifications for a user.
     */
    public function deleteAllNotifications(User $user): int
    {
        $count = $user->notifications()->delete();
        
        // Clear cache
        $this->clearUserNotificationCache($user);
        
        return $count;
    }

    /**
     * Delete old notifications.
     */
    public function deleteOldNotifications(int $days = 30): int
    {
        $count = DatabaseNotification::where('created_at', '<', now()->subDays($days))
            ->delete();
        
        // Clear all notification caches
        $this->clearAllNotificationCaches();
        
        return $count;
    }

    /**
     * Send welcome notification to new user.
     */
    public function sendWelcomeNotification(User $user): void
    {
        $data = [
            'title' => 'Welcome to ' . config('app.name'),
            'message' => 'Thank you for joining our community! Start by exploring our content and customizing your profile.',
            'action_text' => 'Complete Profile',
            'action_url' => route('profile.edit'),
            'icon' => 'welcome',
            'type' => 'welcome'
        ];
        
        $this->createNotification($user, 'App\\Notifications\\WelcomeNotification', $data);
    }

    /**
     * Send post published notification.
     */
    public function sendPostPublishedNotification(Post $post): void
    {
        // Notify followers of the author
        $followers = $post->author->followers;
        
        foreach ($followers as $follower) {
            $data = [
                'title' => 'New Post Published',
                'message' => $post->author->name . ' published a new post: ' . $post->title,
                'action_text' => 'Read Post',
                'action_url' => route('posts.show', $post->slug),
                'icon' => 'post',
                'type' => 'post_published',
                'post_id' => $post->id,
                'author_id' => $post->author->id
            ];
            
            $this->createNotification($follower, 'App\\Notifications\\PostPublishedNotification', $data);
        }
    }

    /**
     * Send comment notification.
     */
    public function sendCommentNotification(Comment $comment): void
    {
        $post = $comment->post;
        $commenter = $comment->user;
        
        // Notify post author if they're not the commenter
        if ($post->author->id !== $commenter->id) {
            $data = [
                'title' => 'New Comment on Your Post',
                'message' => $commenter->name . ' commented on your post: ' . $post->title,
                'action_text' => 'View Comment',
                'action_url' => route('posts.show', $post->slug) . '#comment-' . $comment->id,
                'icon' => 'comment',
                'type' => 'comment',
                'post_id' => $post->id,
                'comment_id' => $comment->id,
                'commenter_id' => $commenter->id
            ];
            
            $this->createNotification($post->author, 'App\\Notifications\\CommentNotification', $data);
        }
        
        // Notify other commenters on the same post
        $otherCommenters = $post->comments()
            ->where('user_id', '!=', $commenter->id)
            ->where('user_id', '!=', $post->author->id)
            ->with('user')
            ->get()
            ->pluck('user')
            ->unique('id');
        
        foreach ($otherCommenters as $otherCommenter) {
            $data = [
                'title' => 'New Comment on Post You Commented',
                'message' => $commenter->name . ' also commented on: ' . $post->title,
                'action_text' => 'View Comment',
                'action_url' => route('posts.show', $post->slug) . '#comment-' . $comment->id,
                'icon' => 'comment',
                'type' => 'comment_reply',
                'post_id' => $post->id,
                'comment_id' => $comment->id,
                'commenter_id' => $commenter->id
            ];
            
            $this->createNotification($otherCommenter, 'App\\Notifications\\CommentReplyNotification', $data);
        }
    }

    /**
     * Send like notification.
     */
    public function sendLikeNotification(Post $post, User $liker): void
    {
        // Don't notify if user likes their own post
        if ($post->author->id === $liker->id) {
            return;
        }
        
        $data = [
            'title' => 'Someone Liked Your Post',
            'message' => $liker->name . ' liked your post: ' . $post->title,
            'action_text' => 'View Post',
            'action_url' => route('posts.show', $post->slug),
            'icon' => 'heart',
            'type' => 'post_liked',
            'post_id' => $post->id,
            'liker_id' => $liker->id
        ];
        
        $this->createNotification($post->author, 'App\\Notifications\\PostLikedNotification', $data);
    }

    /**
     * Send follow notification.
     */
    public function sendFollowNotification(User $follower, User $following): void
    {
        $data = [
            'title' => 'New Follower',
            'message' => $follower->name . ' started following you',
            'action_text' => 'View Profile',
            'action_url' => route('users.show', $follower->username ?? $follower->id),
            'icon' => 'user-plus',
            'type' => 'user_followed',
            'follower_id' => $follower->id
        ];
        
        $this->createNotification($following, 'App\\Notifications\\UserFollowedNotification', $data);
    }

    /**
     * Send system notification to all users.
     */
    public function sendSystemNotification(array $data, array $userIds = []): int
    {
        $query = User::query();
        
        if (!empty($userIds)) {
            $query->whereIn('id', $userIds);
        }
        
        $users = $query->get();
        $sentCount = 0;
        
        foreach ($users as $user) {
            $this->createNotification($user, 'App\\Notifications\\SystemNotification', $data);
            $sentCount++;
        }
        
        return $sentCount;
    }

    /**
     * Send admin notification.
     */
    public function sendAdminNotification(array $data): int
    {
        $admins = User::where('role', 'admin')->get();
        $sentCount = 0;
        
        foreach ($admins as $admin) {
            $this->createNotification($admin, 'App\\Notifications\\AdminNotification', $data);
            $sentCount++;
        }
        
        return $sentCount;
    }

    /**
     * Get notification statistics.
     */
    public function getStatistics(): array
    {
        return Cache::remember('notification_statistics', 3600, function () {
            return [
                'total' => DatabaseNotification::count(),
                'unread' => DatabaseNotification::whereNull('read_at')->count(),
                'read' => DatabaseNotification::whereNotNull('read_at')->count(),
                'today' => DatabaseNotification::whereDate('created_at', today())->count(),
                'this_week' => DatabaseNotification::whereBetween('created_at', [
                    now()->startOfWeek(),
                    now()->endOfWeek()
                ])->count(),
                'this_month' => DatabaseNotification::whereMonth('created_at', now()->month)
                    ->whereYear('created_at', now()->year)->count(),
                'by_type' => DatabaseNotification::select('type')
                    ->selectRaw('COUNT(*) as count')
                    ->groupBy('type')
                    ->pluck('count', 'type')
                    ->toArray(),
                'avg_read_time' => DatabaseNotification::whereNotNull('read_at')
                    ->selectRaw('AVG(TIMESTAMPDIFF(MINUTE, created_at, read_at)) as avg_minutes')
                    ->value('avg_minutes'),
            ];
        });
    }

    /**
     * Get notification trends.
     */
    public function getNotificationTrends(int $days = 30): array
    {
        $trends = [];
        
        for ($i = $days - 1; $i >= 0; $i--) {
            $date = now()->subDays($i);
            $trends[] = [
                'date' => $date->format('Y-m-d'),
                'total' => DatabaseNotification::whereDate('created_at', $date)->count(),
                'read' => DatabaseNotification::whereDate('created_at', $date)
                    ->whereNotNull('read_at')->count(),
                'unread' => DatabaseNotification::whereDate('created_at', $date)
                    ->whereNull('read_at')->count(),
            ];
        }
        
        return $trends;
    }

    /**
     * Get user notification preferences.
     */
    public function getUserPreferences(User $user): array
    {
        return $user->notification_preferences ?? [
            'email_notifications' => true,
            'push_notifications' => true,
            'comment_notifications' => true,
            'like_notifications' => true,
            'follow_notifications' => true,
            'post_notifications' => true,
            'system_notifications' => true,
            'marketing_notifications' => false,
        ];
    }

    /**
     * Update user notification preferences.
     */
    public function updateUserPreferences(User $user, array $preferences): void
    {
        $user->update([
            'notification_preferences' => array_merge(
                $this->getUserPreferences($user),
                $preferences
            )
        ]);
        
        // Clear cache
        $this->clearUserNotificationCache($user);
    }

    /**
     * Check if user should receive notification type.
     */
    public function shouldReceiveNotification(User $user, string $type): bool
    {
        $preferences = $this->getUserPreferences($user);
        
        $typeMap = [
            'comment' => 'comment_notifications',
            'comment_reply' => 'comment_notifications',
            'post_liked' => 'like_notifications',
            'user_followed' => 'follow_notifications',
            'post_published' => 'post_notifications',
            'system' => 'system_notifications',
            'welcome' => 'system_notifications',
        ];
        
        $preferenceKey = $typeMap[$type] ?? 'system_notifications';
        
        return $preferences[$preferenceKey] ?? true;
    }

    /**
     * Send email notification if enabled.
     */
    public function sendEmailNotification(User $user, array $data): void
    {
        $preferences = $this->getUserPreferences($user);
        
        if (!($preferences['email_notifications'] ?? true)) {
            return;
        }
        
        try {
            // This would typically use a mailable class
            Mail::send('emails.notification', $data, function ($message) use ($user, $data) {
                $message->to($user->email, $user->name)
                    ->subject($data['title'] ?? 'Notification');
            });
        } catch (\Exception $e) {
            \Log::error('Failed to send email notification', [
                'user_id' => $user->id,
                'error' => $e->getMessage()
            ]);
        }
    }

    /**
     * Create a notification.
     */
    protected function createNotification(User $user, string $type, array $data): void
    {
        // Check if user should receive this type of notification
        if (!$this->shouldReceiveNotification($user, $data['type'] ?? 'system')) {
            return;
        }
        
        // Create database notification
        $user->notifications()->create([
            'id' => \Str::uuid(),
            'type' => $type,
            'data' => $data,
            'created_at' => now(),
        ]);
        
        // Send email notification if enabled
        $this->sendEmailNotification($user, $data);
        
        // Clear cache
        $this->clearUserNotificationCache($user);
    }

    /**
     * Clear user notification cache.
     */
    protected function clearUserNotificationCache(User $user): void
    {
        $keys = [
            "user_{$user->id}_unread_notifications",
        ];
        
        foreach ($keys as $key) {
            Cache::forget($key);
        }
    }

    /**
     * Clear all notification caches.
     */
    protected function clearAllNotificationCaches(): void
    {
        Cache::forget('notification_statistics');
        
        // Clear user-specific caches
        $userIds = User::pluck('id');
        foreach ($userIds as $userId) {
            Cache::forget("user_{$userId}_unread_notifications");
        }
    }

    /**
     * Get recent notifications for dashboard.
     */
    public function getRecentNotifications(User $user, int $limit = 10): Collection
    {
        return $user->notifications()
            ->latest()
            ->limit($limit)
            ->get();
    }

    /**
     * Bulk mark notifications as read.
     */
    public function bulkMarkAsRead(array $notificationIds, User $user): int
    {
        $count = $user->notifications()
            ->whereIn('id', $notificationIds)
            ->whereNull('read_at')
            ->update(['read_at' => now()]);
        
        // Clear cache
        $this->clearUserNotificationCache($user);
        
        return $count;
    }

    /**
     * Bulk delete notifications.
     */
    public function bulkDeleteNotifications(array $notificationIds, User $user): int
    {
        $count = $user->notifications()
            ->whereIn('id', $notificationIds)
            ->delete();
        
        // Clear cache
        $this->clearUserNotificationCache($user);
        
        return $count;
    }

    /**
     * Get notification summary for user.
     */
    public function getNotificationSummary(User $user): array
    {
        $cacheKey = "user_{$user->id}_notification_summary";
        
        return Cache::remember($cacheKey, 1800, function () use ($user) {
            return [
                'total' => $user->notifications()->count(),
                'unread' => $user->unreadNotifications()->count(),
                'today' => $user->notifications()->whereDate('created_at', today())->count(),
                'this_week' => $user->notifications()->whereBetween('created_at', [
                    now()->startOfWeek(),
                    now()->endOfWeek()
                ])->count(),
                'by_type' => $user->notifications()
                    ->select('type')
                    ->selectRaw('COUNT(*) as count')
                    ->groupBy('type')
                    ->pluck('count', 'type')
                    ->toArray(),
                'recent_unread' => $user->unreadNotifications()
                    ->latest()
                    ->limit(5)
                    ->get(),
            ];
        });
    }
}