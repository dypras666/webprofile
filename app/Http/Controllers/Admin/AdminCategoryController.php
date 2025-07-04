<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Admin\BaseAdminController;
use App\Http\Requests\CategoryRequest;
use App\Services\CategoryService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;

class AdminCategoryController extends BaseAdminController
{
    protected $categoryService;

    public function __construct(CategoryService $categoryService)
    {
        parent::__construct();
        $this->categoryService = $categoryService;
        
        // Set permissions
        $this->middleware('permission:manage_categories');
    }

    /**
     * Display a listing of categories
     */
    public function index(Request $request): View
    {
        $filters = $this->getFilters($request, [
            'search', 'status', 'color', 'date_from', 'date_to'
        ]);
        
        $categories = $this->categoryService->getAllCategories($filters, $request->get('per_page', 15));
        $statistics = $this->categoryService->getStatistics();
        $colors = $this->categoryService->getUniqueColors();
        
        return view('admin.categories.index', compact('categories', 'statistics', 'colors', 'filters'));
    }

    /**
     * Show the form for creating a new category
     */
    public function create(): View
    {
        return view('admin.categories.create');
    }

    /**
     * Store a newly created category
     */
    public function store(CategoryRequest $request): RedirectResponse
    {
        try {
            $category = $this->categoryService->createCategory($request->validated());
            
            $this->logActivity('category_created', 'Created category: ' . $category->name, $category->id);
            
            return redirect()->route('admin.categories.index')
                ->with('success', 'Category created successfully.');
        } catch (\Exception $e) {
            return back()->withInput()
                ->with('error', 'Failed to create category: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified category
     */
    public function show(Request $request, $id)
    {
        $category = $this->categoryService->findCategory($id);
        
        if (!$category) {
            abort(404, 'Category not found');
        }
        
        // Return JSON for AJAX requests, API requests, or when format=json parameter is present
        if ($request->expectsJson() || $request->ajax() || $request->get('format') === 'json' || $request->wantsJson()) {
            return response()->json([
                'id' => $category->id,
                'name' => $category->name,
                'slug' => $category->slug,
                'description' => $category->description,
                'color' => $category->color,
                'meta_title' => $category->meta_title,
                'meta_description' => $category->meta_description,
                'is_active' => $category->is_active,
                'sort_order' => $category->sort_order,
                'created_at' => $category->created_at,
                'updated_at' => $category->updated_at
            ]);
        }
        
        // Return view for regular requests
        $recentPosts = $this->categoryService->getCategoryWithRecentPosts($id, 10);
        $analytics = $this->categoryService->getCategoryAnalytics($id);
        
        return view('admin.categories.show', compact('category', 'recentPosts', 'analytics'));
    }

    /**
     * Show the form for editing the specified category
     */
    public function edit($id): View
    {
        $category = $this->categoryService->findCategory($id);
        
        if (!$category) {
            abort(404, 'Category not found');
        }
        
        return view('admin.categories.edit', compact('category'));
    }

    /**
     * Update the specified category
     */
    public function update(CategoryRequest $request, $id): RedirectResponse
    {
        try {
            $category = $this->categoryService->updateCategory($id, $request->validated());
            
            if (!$category) {
                return back()->with('error', 'Category not found.');
            }
            
            $this->logActivity('category_updated', 'Updated category: ' . $category->name, $category->id);
            
            return redirect()->route('admin.categories.index')
                ->with('success', 'Category updated successfully.');
        } catch (\Exception $e) {
            return back()->withInput()
                ->with('error', 'Failed to update category: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified category
     */
    public function destroy($id): RedirectResponse
    {
        try {
            $category = $this->categoryService->findCategory($id);
            
            if (!$category) {
                return back()->with('error', 'Category not found.');
            }
            
            $name = $category->name;
            $deleted = $this->categoryService->deleteCategory($id);
            
            if ($deleted) {
                $this->logActivity('category_deleted', 'Deleted category: ' . $name, $id);
                return back()->with('success', 'Category deleted successfully.');
            }
            
            return back()->with('error', 'Failed to delete category.');
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to delete category: ' . $e->getMessage());
        }
    }

    /**
     * Bulk actions for categories
     */
    public function bulkAction(Request $request): JsonResponse
    {
        $request->validate([
            'action' => 'required|in:activate,deactivate,delete,change_color',
            'ids' => 'required|array|min:1',
            'ids.*' => 'integer|exists:categories,id',
            'color' => 'required_if:action,change_color|string'
        ]);
        
        try {
            $action = $request->action;
            $ids = $request->ids;
            $count = 0;
            
            switch ($action) {
                case 'activate':
                    $count = $this->categoryService->bulkActivate($ids);
                    $message = "Activated {$count} categories";
                    break;
                    
                case 'deactivate':
                    $count = $this->categoryService->bulkDeactivate($ids);
                    $message = "Deactivated {$count} categories";
                    break;
                    
                case 'delete':
                    $count = $this->categoryService->bulkDelete($ids);
                    $message = "Deleted {$count} categories";
                    break;
                    
                case 'change_color':
                    $count = $this->categoryService->updateCategoryColors($ids, $request->color);
                    $message = "Updated color for {$count} categories";
                    break;
                    
                default:
                    return $this->errorResponse('Invalid action');
            }
            
            $this->logActivity('categories_bulk_' . $action, $message, null, ['ids' => $ids]);
            
            return $this->successResponse($message, ['count' => $count]);
        } catch (\Exception $e) {
            return $this->errorResponse('Bulk action failed: ' . $e->getMessage());
        }
    }

    /**
     * Duplicate category
     */
    public function duplicate($id): RedirectResponse
    {
        try {
            $newCategory = $this->categoryService->duplicateCategory($id);
            
            if (!$newCategory) {
                return back()->with('error', 'Category not found.');
            }
            
            $this->logActivity('category_duplicated', 'Duplicated category: ' . $newCategory->name, $newCategory->id);
            
            return redirect()->route('admin.categories.edit', $newCategory->id)
                ->with('success', 'Category duplicated successfully. You can now edit the copy.');
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to duplicate category: ' . $e->getMessage());
        }
    }

    /**
     * Get category statistics
     */
    public function statistics(): JsonResponse
    {
        try {
            $stats = $this->categoryService->getStatistics();
            $monthlyStats = $this->categoryService->getCountByMonth();
            $colorStats = $this->categoryService->getCountByColor();
            
            return $this->successResponse('Statistics retrieved', [
                'general' => $stats,
                'monthly' => $monthlyStats,
                'by_color' => $colorStats
            ]);
        } catch (\Exception $e) {
            return $this->errorResponse('Failed to get statistics: ' . $e->getMessage());
        }
    }

    /**
     * Export categories
     */
    public function export(Request $request)
    {
        try {
            $format = $request->get('format', 'csv');
            
            if ($format === 'csv') {
                $csvData = $this->categoryService->exportToCSV();
                
                return response($csvData)
                    ->header('Content-Type', 'text/csv')
                    ->header('Content-Disposition', 'attachment; filename="categories_' . date('Y-m-d') . '.csv"');
            }
            
            return back()->with('error', 'Unsupported export format.');
        } catch (\Exception $e) {
            return back()->with('error', 'Export failed: ' . $e->getMessage());
        }
    }

    /**
     * Search categories (AJAX)
     */
    public function search(Request $request): JsonResponse
    {
        $request->validate([
            'query' => 'required|string|min:2',
            'limit' => 'integer|min:1|max:50'
        ]);
        
        try {
            $categories = $this->categoryService->searchCategories(
                $request->query,
                [],
                $request->get('limit', 10)
            );
            
            $results = $categories->map(function ($category) {
                return [
                    'id' => $category->id,
                    'name' => $category->name,
                    'slug' => $category->slug,
                    'color' => $category->color,
                    'posts_count' => $category->posts_count ?? 0,
                    'is_active' => $category->is_active,
                    'created_at' => $category->created_at->format('M d, Y'),
                    'url' => route('admin.categories.edit', $category->id)
                ];
            });
            
            return $this->successResponse('Search completed', $results);
        } catch (\Exception $e) {
            return $this->errorResponse('Search failed: ' . $e->getMessage());
        }
    }

    /**
     * Get trending categories
     */
    public function trending(): JsonResponse
    {
        try {
            $trendingCategories = $this->categoryService->getTrendingCategories(10);
            
            $results = $trendingCategories->map(function ($category) {
                return [
                    'id' => $category->id,
                    'name' => $category->name,
                    'posts_count' => $category->posts_count ?? 0,
                    'color' => $category->color,
                    'created_at' => $category->created_at->format('M d, Y'),
                    'url' => route('admin.categories.show', $category->id)
                ];
            });
            
            return $this->successResponse('Trending categories retrieved', $results);
        } catch (\Exception $e) {
            return $this->errorResponse('Failed to get trending categories: ' . $e->getMessage());
        }
    }

    /**
     * Get category hierarchy
     */
    public function hierarchy(): JsonResponse
    {
        try {
            $hierarchy = $this->categoryService->getCategoryHierarchy();
            
            return $this->successResponse('Category hierarchy retrieved', $hierarchy);
        } catch (\Exception $e) {
            return $this->errorResponse('Failed to get category hierarchy: ' . $e->getMessage());
        }
    }

    /**
     * Merge categories
     */
    public function merge(Request $request): JsonResponse
    {
        $request->validate([
            'source_ids' => 'required|array|min:1',
            'source_ids.*' => 'integer|exists:categories,id',
            'target_id' => 'required|integer|exists:categories,id|different:source_ids.*'
        ]);
        
        try {
            $result = $this->categoryService->mergeCategories($request->source_ids, $request->target_id);
            
            $this->logActivity('categories_merged', 'Merged categories', null, [
                'source_ids' => $request->source_ids,
                'target_id' => $request->target_id,
                'posts_moved' => $result['posts_moved']
            ]);
            
            return $this->successResponse('Categories merged successfully', $result);
        } catch (\Exception $e) {
            return $this->errorResponse('Failed to merge categories: ' . $e->getMessage());
        }
    }

    /**
     * Get category suggestions based on content
     */
    public function suggestions(Request $request): JsonResponse
    {
        $request->validate([
            'content' => 'required|string|min:10'
        ]);
        
        try {
            $suggestions = $this->categoryService->getCategorySuggestions($request->content);
            
            return $this->successResponse('Suggestions generated', $suggestions);
        } catch (\Exception $e) {
            return $this->errorResponse('Failed to generate suggestions: ' . $e->getMessage());
        }
    }

    /**
     * Generate random color
     */
    public function randomColor(): JsonResponse
    {
        try {
            $color = $this->categoryService->generateRandomColor();
            
            return $this->successResponse('Random color generated', ['color' => $color]);
        } catch (\Exception $e) {
            return $this->errorResponse('Failed to generate color: ' . $e->getMessage());
        }
    }

    /**
     * Get categories for dropdown (AJAX)
     */
    public function dropdown(Request $request): JsonResponse
    {
        try {
            $activeOnly = $request->boolean('active_only', true);
            $categories = $this->categoryService->getCategoriesForDropdown($activeOnly);
            
            return $this->successResponse('Categories retrieved', $categories);
        } catch (\Exception $e) {
            return $this->errorResponse('Failed to get categories: ' . $e->getMessage());
        }
    }

    /**
     * Check slug availability
     */
    public function checkSlug(Request $request): JsonResponse
    {
        $request->validate([
            'slug' => 'required|string',
            'id' => 'nullable|integer|exists:categories,id'
        ]);
        
        try {
            $isUnique = $this->categoryService->isSlugUnique($request->slug, $request->id);
            
            return $this->successResponse('Slug checked', [
                'available' => $isUnique,
                'slug' => $request->slug
            ]);
        } catch (\Exception $e) {
            return $this->errorResponse('Failed to check slug: ' . $e->getMessage());
        }
    }

    /**
     * Get category analytics
     */
    public function analytics($id): JsonResponse
    {
        try {
            $analytics = $this->categoryService->getCategoryAnalytics($id);
            
            if (!$analytics) {
                return $this->errorResponse('Category not found');
            }
            
            return $this->successResponse('Analytics retrieved', $analytics);
        } catch (\Exception $e) {
            return $this->errorResponse('Failed to get analytics: ' . $e->getMessage());
        }
    }

    /**
     * Get category breadcrumbs
     */
    public function breadcrumbs($id): JsonResponse
    {
        try {
            $breadcrumbs = $this->categoryService->getCategoryBreadcrumbs($id);
            
            if (!$breadcrumbs) {
                return $this->errorResponse('Category not found');
            }
            
            return $this->successResponse('Breadcrumbs retrieved', $breadcrumbs);
        } catch (\Exception $e) {
            return $this->errorResponse('Failed to get breadcrumbs: ' . $e->getMessage());
        }
    }

    /**
     * Reorder categories
     */
    public function reorder(Request $request): JsonResponse
    {
        $request->validate([
            'orders' => 'required|array',
            'orders.*.id' => 'required|integer|exists:categories,id',
            'orders.*.sort_order' => 'required|integer|min:0'
        ]);
        
        try {
            $updated = 0;
            
            foreach ($request->orders as $order) {
                $category = $this->categoryService->updateCategory($order['id'], [
                    'sort_order' => $order['sort_order']
                ]);
                
                if ($category) {
                    $updated++;
                }
            }
            
            $this->logActivity('categories_reordered', 'Reordered categories', null, [
                'orders' => $request->orders
            ]);
            
            return $this->successResponse('Categories reordered successfully', [
                'updated' => $updated
            ]);
        } catch (\Exception $e) {
            return $this->errorResponse('Failed to reorder categories: ' . $e->getMessage());
        }
    }
}