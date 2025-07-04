<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class AdminMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, ...$roles): Response
    {
        // Check if user is authenticated
        if (!Auth::check()) {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Authentication required'
                ], 401);
            }
            
            return redirect()->route('login')
                ->with('error', 'Please login to access the admin area.');
        }
        
        $user = Auth::user();
        
        // Check if user account is active
        if (!$user->is_active) {
            Auth::logout();
            
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Account is deactivated'
                ], 403);
            }
            
            return redirect()->route('login')
                ->with('error', 'Your account has been deactivated. Please contact administrator.');
        }
        
        // Check if user has admin privileges
        $allowedRoles = empty($roles) ? ['admin', 'editor', 'author'] : $roles;
        
        if (!in_array($user->role, $allowedRoles)) {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Insufficient permissions'
                ], 403);
            }
            
            return redirect()->route('home')
                ->with('error', 'You do not have permission to access this area.');
        }
        
        // Check specific permissions for certain routes
        if (!$this->hasRoutePermission($request, $user)) {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Access denied for this resource'
                ], 403);
            }
            
            return redirect()->route('admin.dashboard')
                ->with('error', 'You do not have permission to access this resource.');
        }
        
        // Update last activity
        $user->update(['last_login_at' => now()]);
        
        // Log admin access for security
        $this->logAdminAccess($request, $user);
        
        return $next($request);
    }
    
    /**
     * Check if user has permission for specific route
     */
    protected function hasRoutePermission(Request $request, $user): bool
    {
        $route = $request->route();
        if (!$route) {
            return true;
        }
        
        $routeName = $route->getName();
        $userRole = $user->role;
        
        // Super admin has access to everything
        if ($userRole === 'admin') {
            return true;
        }
        
        // Define role-based permissions
        $permissions = [
            'editor' => [
                'admin.dashboard',
                'admin.posts.*',
                'admin.categories.*',
                'admin.media.*',
                'admin.settings.general',
                'admin.settings.seo',
                'admin.settings.social'
            ],
            'author' => [
                'admin.dashboard',
                'admin.posts.index',
                'admin.posts.create',
                'admin.posts.store',
                'admin.posts.show',
                'admin.posts.edit',
                'admin.posts.update',
                'admin.posts.duplicate',
                'admin.posts.autosave',
                'admin.categories.index',
                'admin.categories.show',
                'admin.media.index',
                'admin.media.create',
                'admin.media.store',
                'admin.media.show'
            ]
        ];
        
        if (!isset($permissions[$userRole])) {
            return false;
        }
        
        $allowedRoutes = $permissions[$userRole];
        
        // Check exact match or wildcard match
        foreach ($allowedRoutes as $allowedRoute) {
            if ($routeName === $allowedRoute) {
                return true;
            }
            
            // Check wildcard patterns
            if (str_ends_with($allowedRoute, '*')) {
                $prefix = rtrim($allowedRoute, '*');
                if (str_starts_with($routeName, $prefix)) {
                    return true;
                }
            }
        }
        
        // Additional checks for resource ownership
        return $this->checkResourceOwnership($request, $user);
    }
    
    /**
     * Check if user owns the resource they're trying to access
     */
    protected function checkResourceOwnership(Request $request, $user): bool
    {
        $route = $request->route();
        $routeName = $route->getName();
        
        // Authors can only edit their own posts
        if ($user->role === 'author' && str_contains($routeName, 'posts.')) {
            $postId = $route->parameter('post');
            if ($postId) {
                $post = \App\Models\Post::find($postId);
                return $post && $post->user_id === $user->id;
            }
        }
        
        // Authors can only manage their own media
        if ($user->role === 'author' && str_contains($routeName, 'media.')) {
            $mediaId = $route->parameter('media');
            if ($mediaId) {
                $media = \App\Models\Media::find($mediaId);
                return $media && $media->user_id === $user->id;
            }
        }
        
        return false;
    }
    
    /**
     * Log admin access for security monitoring
     */
    protected function logAdminAccess(Request $request, $user): void
    {
        try {
            \Illuminate\Support\Facades\Log::channel('admin')->info('Admin access', [
                'user_id' => $user->id,
                'user_email' => $user->email,
                'user_role' => $user->role,
                'route' => $request->route()?->getName(),
                'method' => $request->method(),
                'url' => $request->fullUrl(),
                'ip' => $request->ip(),
                'user_agent' => $request->userAgent(),
                'timestamp' => now()->toISOString()
            ]);
        } catch (\Exception $e) {
            // Silently fail if logging fails
        }
    }
}