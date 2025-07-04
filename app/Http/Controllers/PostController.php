<?php

namespace App\Http\Controllers;

use App\Http\Requests\PostRequest;
use App\Models\Post;
use App\Models\Category;
use App\Services\FileUploadService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class PostController extends Controller
{
    protected $fileUploadService;

    public function __construct(FileUploadService $fileUploadService)
    {
        $this->fileUploadService = $fileUploadService;
        $this->middleware('auth');
        $this->middleware('permission:view posts')->only(['index', 'show']);
        $this->middleware('permission:create posts')->only(['create', 'store']);
        $this->middleware('permission:edit posts')->only(['edit', 'update']);
        $this->middleware('permission:delete posts')->only(['destroy']);
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Post::with(['category', 'user']);

        // Filter by type
        if ($request->filled('type')) {
            $query->byType($request->type);
        }

        // Filter by category
        if ($request->filled('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        // Filter by status
        if ($request->filled('status')) {
            if ($request->status === 'published') {
                $query->published();
            } elseif ($request->status === 'draft') {
                $query->where('is_published', false);
            }
        }

        // Search
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('content', 'like', "%{$search}%")
                  ->orWhere('excerpt', 'like', "%{$search}%");
            });
        }

        $posts = $query->orderBy('created_at', 'desc')->paginate(15);
        $categories = Category::active()->ordered()->get();

        return view('admin.posts.index', compact('posts', 'categories'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $categories = Category::active()->ordered()->get();
        return view('admin.posts.create', compact('categories'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(PostRequest $request)
    {
        try {
            DB::beginTransaction();

            $data = $request->validated();
            $data['user_id'] = auth()->id();

            // Handle featured image upload
            if ($request->hasFile('featured_image')) {
                $media = $this->fileUploadService->uploadImage(
                    $request->file('featured_image'),
                    'posts/featured',
                    auth()->id(),
                    ['width' => 1200, 'height' => 630]
                );
                $data['featured_image'] = $media->file_path;
            }

            // Handle gallery images upload
            if ($request->hasFile('gallery_images')) {
                $galleryPaths = [];
                foreach ($request->file('gallery_images') as $file) {
                    $media = $this->fileUploadService->uploadImage(
                        $file,
                        'posts/gallery',
                        auth()->id(),
                        ['width' => 800]
                    );
                    $galleryPaths[] = $media->file_path;
                }
                $data['gallery_images'] = json_encode($galleryPaths);
            }

            $post = Post::create($data);

            DB::commit();

            return redirect()->route('admin.posts.index')
                           ->with('success', 'Post berhasil dibuat.');
        } catch (\Exception $e) {
            DB::rollback();
            return back()->withInput()
                        ->with('error', 'Gagal membuat post: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Post $post)
    {
        $post->load(['category', 'user']);
        $post->incrementViews();
        
        return view('admin.posts.show', compact('post'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Post $post)
    {
        $categories = Category::active()->ordered()->get();
        return view('admin.posts.edit', compact('post', 'categories'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(PostRequest $request, Post $post)
    {
        try {
            DB::beginTransaction();

            $data = $request->validated();

            // Handle featured image upload
            if ($request->hasFile('featured_image')) {
                // Delete old featured image
                if ($post->featured_image) {
                    Storage::disk('public')->delete($post->featured_image);
                }

                $media = $this->fileUploadService->uploadImage(
                    $request->file('featured_image'),
                    'posts/featured',
                    auth()->id(),
                    ['width' => 1200, 'height' => 630]
                );
                $data['featured_image'] = $media->file_path;
            }

            // Handle gallery images upload
            if ($request->hasFile('gallery_images')) {
                // Delete old gallery images
                if ($post->gallery_images) {
                    $oldImages = json_decode($post->gallery_images, true);
                    foreach ($oldImages as $imagePath) {
                        Storage::disk('public')->delete($imagePath);
                    }
                }

                $galleryPaths = [];
                foreach ($request->file('gallery_images') as $file) {
                    $media = $this->fileUploadService->uploadImage(
                        $file,
                        'posts/gallery',
                        auth()->id(),
                        ['width' => 800]
                    );
                    $galleryPaths[] = $media->file_path;
                }
                $data['gallery_images'] = json_encode($galleryPaths);
            }

            $post->update($data);

            DB::commit();

            return redirect()->route('admin.posts.index')
                           ->with('success', 'Post berhasil diperbarui.');
        } catch (\Exception $e) {
            DB::rollback();
            return back()->withInput()
                        ->with('error', 'Gagal memperbarui post: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Post $post)
    {
        try {
            DB::beginTransaction();

            // Delete featured image
            if ($post->featured_image) {
                Storage::disk('public')->delete($post->featured_image);
            }

            // Delete gallery images
            if ($post->gallery_images) {
                $galleryImages = json_decode($post->gallery_images, true);
                foreach ($galleryImages as $imagePath) {
                    Storage::disk('public')->delete($imagePath);
                }
            }

            $post->delete();

            DB::commit();

            return redirect()->route('admin.posts.index')
                           ->with('success', 'Post berhasil dihapus.');
        } catch (\Exception $e) {
            DB::rollback();
            return back()->with('error', 'Gagal menghapus post: ' . $e->getMessage());
        }
    }

    /**
     * Toggle post publication status
     */
    public function togglePublish(Post $post)
    {
        $post->update([
            'is_published' => !$post->is_published,
            'published_at' => !$post->is_published ? now() : null
        ]);

        $status = $post->is_published ? 'dipublikasikan' : 'dijadikan draft';
        return back()->with('success', "Post berhasil {$status}.");
    }

    /**
     * Toggle slider status
     */
    public function toggleSlider(Post $post)
    {
        $post->update(['is_slider' => !$post->is_slider]);
        
        $status = $post->is_slider ? 'ditambahkan ke slider' : 'dihapus dari slider';
        return back()->with('success', "Post berhasil {$status}.");
    }

    /**
     * Toggle featured status
     */
    public function toggleFeatured(Post $post)
    {
        $post->update(['is_featured' => !$post->is_featured]);
        
        $status = $post->is_featured ? 'dijadikan unggulan' : 'dihapus dari unggulan';
        return back()->with('success', "Post berhasil {$status}.");
    }
}
