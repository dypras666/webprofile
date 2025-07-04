<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
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
                ->with('error', 'Please login to continue.');
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
                ->with('error', 'Your account has been deactivated.');
        }
        
        // Check if user has required role
        if (!empty($roles) && !in_array($user->role, $roles)) {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Insufficient permissions',
                    'required_roles' => $roles,
                    'user_role' => $user->role
                ], 403);
            }
            
            // Redirect based on user role
            $redirectRoute = $this->getRedirectRoute($user->role);
            
            return redirect()->route($redirectRoute)
                ->with('error', 'You do not have permission to access this area.');
        }
        
        // Check additional permissions
        if (!$this->hasAdditionalPermissions($request, $user)) {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Access denied for this specific resource'
                ], 403);
            }
            
            $redirectRoute = $this->getRedirectRoute($user->role);
            
            return redirect()->route($redirectRoute)
                ->with('error', 'You do not have permission to access this specific resource.');
        }
        
        return $next($request);
    }
    
    /**
     * Get redirect route based on user role
     */
    protected function getRedirectRoute(string $role): string
    {
        return match($role) {
            'admin', 'editor', 'author' => 'admin.dashboard',
            'user' => 'dashboard',
            default => 'home'
        };
    }
    
    /**
     * Check additional permissions based on context
     */
    protected function hasAdditionalPermissions(Request $request, $user): bool
    {
        $route = $request->route();
        if (!$route) {
            return true;
        }
        
        $routeName = $route->getName();
        $userRole = $user->role;
        
        // Define specific permission rules
        $permissionRules = [
            // User management permissions
            'admin.users.create' => ['admin'],
            'admin.users.store' => ['admin'],
            'admin.users.destroy' => ['admin'],
            'admin.users.bulk' => ['admin'],
            'admin.users.reset-password' => ['admin'],
            'admin.users.change-role' => ['admin'],
            
            // Settings permissions
            'admin.settings.email' => ['admin'],
            'admin.settings.appearance' => ['admin', 'editor'],
            'admin.settings.backup' => ['admin'],
            'admin.settings.restore' => ['admin'],
            'admin.settings.reset' => ['admin'],
            
            // Category management
            'admin.categories.destroy' => ['admin', 'editor'],
            'admin.categories.bulk' => ['admin', 'editor'],
            
            // Media management
            'admin.media.destroy' => ['admin', 'editor'],
            'admin.media.bulk' => ['admin', 'editor'],
            'admin.media.cleanup' => ['admin', 'editor'],
        ];
        
        // Check if route has specific permission requirements
        if (isset($permissionRules[$routeName])) {
            return in_array($userRole, $permissionRules[$routeName]);
        }
        
        // Check resource ownership for authors
        if ($userRole === 'author') {
            return $this->checkAuthorPermissions($request, $user);
        }
        
        // Check time-based restrictions
        if (!$this->checkTimeRestrictions($user)) {
            return false;
        }
        
        // Check IP restrictions
        if (!$this->checkIpRestrictions($request, $user)) {
            return false;
        }
        
        return true;
    }
    
    /**
     * Check permissions specific to author role
     */
    protected function checkAuthorPermissions(Request $request, $user): bool
    {
        $route = $request->route();
        $routeName = $route->getName();
        
        // Authors can only manage their own content
        if (str_contains($routeName, 'posts.')) {
            $allowedActions = ['index', 'create', 'store', 'show', 'edit', 'update', 'duplicate', 'autosave'];
            $action = last(explode('.', $routeName));
            
            if (!in_array($action, $allowedActions)) {
                return false;
            }
            
            // Check ownership for specific post operations
            if (in_array($action, ['show', 'edit', 'update', 'duplicate'])) {
                $postId = $route->parameter('post');
                if ($postId) {
                    $post = \App\Models\Post::find($postId);
                    return $post && $post->user_id === $user->id;
                }
            }
        }
        
        // Authors can only manage their own media
        if (str_contains($routeName, 'media.')) {
            $allowedActions = ['index', 'create', 'store', 'show', 'edit', 'update'];
            $action = last(explode('.', $routeName));
            
            if (!in_array($action, $allowedActions)) {
                return false;
            }
            
            // Check ownership for specific media operations
            if (in_array($action, ['show', 'edit', 'update', 'destroy'])) {
                $mediaId = $route->parameter('media');
                if ($mediaId) {
                    $media = \App\Models\Media::find($mediaId);
                    return $media && $media->user_id === $user->id;
                }
            }
        }
        
        return true;
    }
    
    /**
     * Check time-based access restrictions
     */
    protected function checkTimeRestrictions($user): bool
    {
        // Check if user has time-based restrictions
        $restrictions = $user->access_restrictions ?? [];
        
        if (empty($restrictions['time_based'])) {
            return true;
        }
        
        $now = now();
        $currentHour = $now->hour;
        $currentDay = $now->dayOfWeek; // 0 = Sunday, 6 = Saturday
        
        // Check allowed hours
        if (isset($restrictions['allowed_hours'])) {
            $allowedHours = $restrictions['allowed_hours'];
            if (!in_array($currentHour, $allowedHours)) {
                return false;
            }
        }
        
        // Check allowed days
        if (isset($restrictions['allowed_days'])) {
            $allowedDays = $restrictions['allowed_days'];
            if (!in_array($currentDay, $allowedDays)) {
                return false;
            }
        }
        
        // Check access window
        if (isset($restrictions['access_window'])) {
            $window = $restrictions['access_window'];
            $startTime = \Carbon\Carbon::createFromTimeString($window['start']);
            $endTime = \Carbon\Carbon::createFromTimeString($window['end']);
            
            if (!$now->between($startTime, $endTime)) {
                return false;
            }
        }
        
        return true;
    }
    
    /**
     * Check IP-based access restrictions
     */
    protected function checkIpRestrictions(Request $request, $user): bool
    {
        $restrictions = $user->access_restrictions ?? [];
        
        if (empty($restrictions['ip_based'])) {
            return true;
        }
        
        $userIp = $request->ip();
        
        // Check allowed IPs
        if (isset($restrictions['allowed_ips'])) {
            $allowedIps = $restrictions['allowed_ips'];
            if (!in_array($userIp, $allowedIps)) {
                return false;
            }
        }
        
        // Check blocked IPs
        if (isset($restrictions['blocked_ips'])) {
            $blockedIps = $restrictions['blocked_ips'];
            if (in_array($userIp, $blockedIps)) {
                return false;
            }
        }
        
        // Check IP ranges
        if (isset($restrictions['allowed_ranges'])) {
            $allowedRanges = $restrictions['allowed_ranges'];
            $ipInRange = false;
            
            foreach ($allowedRanges as $range) {
                if ($this->ipInRange($userIp, $range)) {
                    $ipInRange = true;
                    break;
                }
            }
            
            if (!$ipInRange) {
                return false;
            }
        }
        
        return true;
    }
    
    /**
     * Check if IP is in given range
     */
    protected function ipInRange(string $ip, string $range): bool
    {
        if (str_contains($range, '/')) {
            // CIDR notation
            list($subnet, $mask) = explode('/', $range);
            $mask = (int) $mask;
            
            $ipLong = ip2long($ip);
            $subnetLong = ip2long($subnet);
            
            $maskLong = -1 << (32 - $mask);
            
            return ($ipLong & $maskLong) === ($subnetLong & $maskLong);
        } else {
            // Single IP
            return $ip === $range;
        }
    }
}