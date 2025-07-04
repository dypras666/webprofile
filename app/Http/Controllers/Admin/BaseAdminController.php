<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;

abstract class BaseAdminController extends Controller
{
    protected $model;
    protected $viewPath;
    protected $routePrefix;
    protected $perPage = 15;
    protected $cacheTime = 3600; // 1 hour

    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('verified');
    }

    /**
     * Get base query for index
     */
    protected function getIndexQuery(Request $request)
    {
        return $this->model::query();
    }

    /**
     * Apply common filters
     */
    protected function applyFilters($query, Request $request)
    {
        // Search functionality
        if ($request->filled('search')) {
            $search = $request->search;
            $query = $this->applySearch($query, $search);
        }

        // Date range filter
        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        // Status filter
        if ($request->filled('status')) {
            $query = $this->applyStatusFilter($query, $request->status);
        }

        return $query;
    }

    /**
     * Apply search logic - override in child controllers
     */
    protected function applySearch($query, $search)
    {
        return $query;
    }

    /**
     * Apply status filter - override in child controllers
     */
    protected function applyStatusFilter($query, $status)
    {
        return $query;
    }

    /**
     * Apply sorting
     */
    protected function applySorting($query, Request $request)
    {
        $sortBy = $request->get('sort_by', 'created_at');
        $sortOrder = $request->get('sort_order', 'desc');

        return $query->orderBy($sortBy, $sortOrder);
    }

    /**
     * Handle bulk actions
     */
    public function bulkAction(Request $request)
    {
        $request->validate([
            'action' => 'required|string',
            'ids' => 'required|array',
            'ids.*' => 'integer|exists:' . $this->getTableName() . ',id'
        ]);

        try {
            DB::beginTransaction();

            $result = $this->processBulkAction($request->action, $request->ids);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => $result['message'] ?? 'Bulk action completed successfully',
                'count' => count($request->ids)
            ]);
        } catch (\Exception $e) {
            DB::rollback();
            Log::error('Bulk action error: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'An error occurred while processing bulk action'
            ], 500);
        }
    }

    /**
     * Process bulk action - override in child controllers
     */
    protected function processBulkAction($action, $ids)
    {
        switch ($action) {
            case 'delete':
                $this->model::whereIn('id', $ids)->delete();
                return ['message' => 'Items deleted successfully'];
            default:
                throw new \InvalidArgumentException('Invalid bulk action');
        }
    }

    /**
     * Get table name from model
     */
    protected function getTableName()
    {
        return (new $this->model)->getTable();
    }

    /**
     * Handle AJAX requests
     */
    protected function handleAjaxRequest(Request $request, $data)
    {
        if ($request->ajax()) {
            return response()->json($data);
        }

        return $data;
    }

    /**
     * Cache key generator
     */
    protected function getCacheKey($key, $params = [])
    {
        $baseKey = strtolower(class_basename($this->model)) . '.' . $key;
        
        if (!empty($params)) {
            $baseKey .= '.' . md5(serialize($params));
        }

        return $baseKey;
    }

    /**
     * Clear model cache
     */
    protected function clearCache($pattern = null)
    {
        $modelName = strtolower(class_basename($this->model));
        
        if ($pattern) {
            Cache::forget($modelName . '.' . $pattern);
        } else {
            // Clear all cache for this model
            $keys = Cache::getRedis()->keys($modelName . '.*');
            foreach ($keys as $key) {
                Cache::forget($key);
            }
        }
    }

    /**
     * Success response helper
     */
    protected function successResponse($message, $data = null, $redirect = null)
    {
        $response = ['success' => true, 'message' => $message];
        
        if ($data) {
            $response['data'] = $data;
        }
        
        if ($redirect) {
            $response['redirect'] = $redirect;
        }

        return response()->json($response);
    }

    /**
     * Error response helper
     */
    protected function errorResponse($message, $errors = null, $code = 400)
    {
        $response = ['success' => false, 'message' => $message];
        
        if ($errors) {
            $response['errors'] = $errors;
        }

        return response()->json($response, $code);
    }

    /**
     * Log activity
     */
    protected function logActivity($action, $model = null, $properties = [])
    {
        $logData = [
            'user_id' => auth()->id(),
            'action' => $action,
            'model_type' => $model ? get_class($model) : $this->model,
            'model_id' => $model ? $model->id : null,
            'properties' => $properties,
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'created_at' => now()
        ];

        Log::info('Admin Activity', $logData);
    }

    /**
     * Export data
     */
    public function export(Request $request)
    {
        $query = $this->getIndexQuery($request);
        $query = $this->applyFilters($query, $request);
        
        $data = $query->get();
        
        return $this->generateExport($data, $request->get('format', 'xlsx'));
    }

    /**
     * Generate export - override in child controllers
     */
    protected function generateExport($data, $format)
    {
        throw new \BadMethodCallException('Export method not implemented');
    }

    /**
     * Get statistics for dashboard
     */
    protected function getStatistics()
    {
        $cacheKey = $this->getCacheKey('statistics');
        
        return Cache::remember($cacheKey, $this->cacheTime, function () {
            return [
                'total' => $this->model::count(),
                'today' => $this->model::whereDate('created_at', today())->count(),
                'this_week' => $this->model::whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()])->count(),
                'this_month' => $this->model::whereMonth('created_at', now()->month)->count(),
            ];
        });
    }

    /**
     * Validate permissions
     */
    protected function checkPermission($permission)
    {
        if (!auth()->user()->can($permission)) {
            abort(403, 'Unauthorized action.');
        }
    }

    /**
     * Handle file upload
     */
    protected function handleFileUpload($file, $folder = 'uploads')
    {
        if (!$file || !$file->isValid()) {
            return null;
        }

        $filename = time() . '_' . $file->getClientOriginalName();
        $path = $file->storeAs($folder, $filename, 'public');

        return $path;
    }

    /**
     * Delete file
     */
    protected function deleteFile($path)
    {
        if ($path && \Storage::disk('public')->exists($path)) {
            \Storage::disk('public')->delete($path);
        }
    }
}