<?php

namespace App\Http\Controllers;

use App\Http\Requests\CategoryRequest;
use App\Models\Category;
use App\Services\FileUploadService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class CategoryController extends Controller
{
    protected $fileUploadService;

    public function __construct(FileUploadService $fileUploadService)
    {
        $this->fileUploadService = $fileUploadService;
        $this->middleware('auth');
        $this->middleware('permission:view categories')->only(['index', 'show']);
        $this->middleware('permission:create categories')->only(['create', 'store']);
        $this->middleware('permission:edit categories')->only(['edit', 'update']);
        $this->middleware('permission:delete categories')->only(['destroy']);
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Category::withCount('posts');

        // Search
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        // Filter by status
        if ($request->filled('status')) {
            if ($request->status === 'active') {
                $query->active();
            } elseif ($request->status === 'inactive') {
                $query->where('is_active', false);
            }
        }

        $categories = $query->ordered()->paginate(15);

        return view('admin.categories.index', compact('categories'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('admin.categories.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(CategoryRequest $request)
    {
        try {
            DB::beginTransaction();

            $data = $request->validated();

            // Handle image upload
            if ($request->hasFile('image')) {
                $media = $this->fileUploadService->uploadImage(
                    $request->file('image'),
                    'categories',
                    auth()->id(),
                    ['width' => 400, 'height' => 400]
                );
                $data['image'] = $media->file_path;
            }

            $category = Category::create($data);

            DB::commit();

            return redirect()->route('admin.categories.index')
                           ->with('success', 'Kategori berhasil dibuat.');
        } catch (\Exception $e) {
            DB::rollback();
            return back()->withInput()
                        ->with('error', 'Gagal membuat kategori: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Category $category, Request $request)
    {
        // Return JSON for AJAX requests or when format=json is requested
        if ($request->expectsJson() || 
            $request->ajax() || 
            $request->get('format') === 'json' || 
            $request->wantsJson()) {
            return response()->json([
                'id' => $category->id,
                'name' => $category->name,
                'slug' => $category->slug,
                'description' => $category->description,
                'color' => $category->color,
                'image' => $category->image,
                'meta_title' => $category->meta_title,
                'meta_description' => $category->meta_description,
                'is_active' => $category->is_active,
                'sort_order' => $category->sort_order,
                'posts_count' => $category->posts_count ?? $category->posts()->count(),
                'created_at' => $category->created_at,
                'updated_at' => $category->updated_at
            ]);
        }
        
        $category->load('posts');
        $posts = $category->posts()->with('user')->latest()->paginate(10);
        
        return view('admin.categories.show', compact('category', 'posts'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Category $category)
    {
        return view('admin.categories.edit', compact('category'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(CategoryRequest $request, Category $category)
    {
        try {
            DB::beginTransaction();

            $data = $request->validated();

            // Handle image upload
            if ($request->hasFile('image')) {
                // Delete old image
                if ($category->image) {
                    Storage::disk('public')->delete($category->image);
                }

                $media = $this->fileUploadService->uploadImage(
                    $request->file('image'),
                    'categories',
                    auth()->id(),
                    ['width' => 400, 'height' => 400]
                );
                $data['image'] = $media->file_path;
            }

            $category->update($data);

            DB::commit();

            return redirect()->route('admin.categories.index')
                           ->with('success', 'Kategori berhasil diperbarui.');
        } catch (\Exception $e) {
            DB::rollback();
            return back()->withInput()
                        ->with('error', 'Gagal memperbarui kategori: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Category $category)
    {
        try {
            DB::beginTransaction();

            // Check if category has posts
            if ($category->posts()->count() > 0) {
                return back()->with('error', 'Kategori tidak dapat dihapus karena masih memiliki post.');
            }

            // Delete image
            if ($category->image) {
                Storage::disk('public')->delete($category->image);
            }

            $category->delete();

            DB::commit();

            return redirect()->route('admin.categories.index')
                           ->with('success', 'Kategori berhasil dihapus.');
        } catch (\Exception $e) {
            DB::rollback();
            return back()->with('error', 'Gagal menghapus kategori: ' . $e->getMessage());
        }
    }

    /**
     * Toggle category active status
     */
    public function toggleActive(Category $category)
    {
        $category->update(['is_active' => !$category->is_active]);
        
        $status = $category->is_active ? 'diaktifkan' : 'dinonaktifkan';
        return back()->with('success', "Kategori berhasil {$status}.");
    }

    /**
     * Update category sort order
     */
    public function updateOrder(Request $request)
    {
        $request->validate([
            'categories' => 'required|array',
            'categories.*.id' => 'required|exists:categories,id',
            'categories.*.sort_order' => 'required|integer|min:0'
        ]);

        try {
            DB::beginTransaction();

            foreach ($request->categories as $categoryData) {
                Category::where('id', $categoryData['id'])
                       ->update(['sort_order' => $categoryData['sort_order']]);
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Urutan kategori berhasil diperbarui.'
            ]);
        } catch (\Exception $e) {
            DB::rollback();
            return response()->json([
                'success' => false,
                'message' => 'Gagal memperbarui urutan kategori: ' . $e->getMessage()
            ], 500);
        }
    }
}
