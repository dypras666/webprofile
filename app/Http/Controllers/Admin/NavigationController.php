<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\NavigationMenu;
use App\Models\Post;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;

class NavigationController extends Controller
{
    /**
     * Display navigation menu management page
     */
    public function index(): View
    {
        $menus = NavigationMenu::with(['children', 'referencedPost', 'referencedCategory'])
                              ->roots()
                              ->get();
        
        $posts = Post::where('is_published', true)
                    ->select('id', 'title', 'slug', 'type')
                    ->orderBy('title')
                    ->get();
        
        $categories = Category::select('id', 'name', 'slug')
                             ->orderBy('name')
                             ->get();

        return view('admin.navigation.index', compact('menus', 'posts', 'categories'));
    }

    /**
     * Store a new navigation menu item
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'type' => 'required|in:custom,post,page,category',
            'url' => 'nullable|string|max:255',
            'reference_id' => 'nullable|integer',
            'parent_id' => 'nullable|exists:navigation_menus,id',
            'target' => 'in:_self,_blank',
            'css_class' => 'nullable|string|max:255',
            'icon' => 'nullable|string|max:255',
            'is_active' => 'boolean'
        ]);

        // Get the next sort order
        $maxOrder = NavigationMenu::where('parent_id', $validated['parent_id'] ?? null)
                                 ->max('sort_order');
        $validated['sort_order'] = ($maxOrder ?? 0) + 1;

        // Set defaults
        $validated['target'] = $validated['target'] ?? '_self';
        $validated['is_active'] = $validated['is_active'] ?? true;

        $menu = NavigationMenu::create($validated);
        $menu->load(['referencedPost', 'referencedCategory']);

        return response()->json([
            'success' => true,
            'message' => 'Menu item berhasil ditambahkan',
            'menu' => $menu
        ]);
    }

    /**
     * Update navigation menu item
     */
    public function update(Request $request, NavigationMenu $menu): JsonResponse
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'type' => 'required|in:custom,post,page,category',
            'url' => 'nullable|string|max:255',
            'reference_id' => 'nullable|integer',
            'target' => 'in:_self,_blank',
            'css_class' => 'nullable|string|max:255',
            'icon' => 'nullable|string|max:255',
            'is_active' => 'boolean'
        ]);

        $validated['target'] = $validated['target'] ?? '_self';
        $validated['is_active'] = $validated['is_active'] ?? true;

        $menu->update($validated);
        $menu->load(['referencedPost', 'referencedCategory']);

        return response()->json([
            'success' => true,
            'message' => 'Menu item berhasil diperbarui',
            'menu' => $menu
        ]);
    }

    /**
     * Delete navigation menu item
     */
    public function destroy(NavigationMenu $menu): JsonResponse
    {
        $menu->delete();

        return response()->json([
            'success' => true,
            'message' => 'Menu item berhasil dihapus'
        ]);
    }

    /**
     * Update menu order via drag and drop
     */
    public function updateOrder(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'menus' => 'required|array',
            'menus.*.id' => 'required|exists:navigation_menus,id',
            'menus.*.children' => 'sometimes|array'
        ]);

        try {
            NavigationMenu::updateSortOrder($validated['menus']);

            return response()->json([
                'success' => true,
                'message' => 'Urutan menu berhasil diperbarui'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal memperbarui urutan menu: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Toggle menu item active status
     */
    public function toggleActive(NavigationMenu $menu): JsonResponse
    {
        $menu->update(['is_active' => !$menu->is_active]);

        return response()->json([
            'success' => true,
            'message' => 'Status menu berhasil diperbarui',
            'is_active' => $menu->is_active
        ]);
    }

    /**
     * Get posts for AJAX requests
     */
    public function getPosts(Request $request): JsonResponse
    {
        $type = $request->get('type', 'all');
        $search = $request->get('search', '');

        $query = Post::where('is_published', true)
                    ->select('id', 'title', 'slug', 'type');

        if ($type !== 'all') {
            $query->where('type', $type);
        }

        if ($search) {
            $query->where('title', 'like', '%' . $search . '%');
        }

        $posts = $query->orderBy('title')
                      ->limit(20)
                      ->get();

        return response()->json($posts);
    }

    /**
     * Get categories for AJAX requests
     */
    public function getCategories(Request $request): JsonResponse
    {
        $search = $request->get('search', '');

        $query = Category::select('id', 'name', 'slug');

        if ($search) {
            $query->where('name', 'like', '%' . $search . '%');
        }

        $categories = $query->orderBy('name')
                           ->limit(20)
                           ->get();

        return response()->json($categories);
    }
}