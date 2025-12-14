<?php

namespace App\Http\Controllers;

use App\Http\Requests\PostRequest;
use App\Http\Requests\PageRequest;
use App\Models\Post;
use App\Models\Category;
use App\Services\FileUploadService;
use App\Services\PostService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class PostController extends Controller
{
    protected $fileUploadService;
    protected $postService;

    public function __construct(FileUploadService $fileUploadService, PostService $postService)
    {
        $this->fileUploadService = $fileUploadService;
        $this->postService = $postService;
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
        $query = Post::with(['category', 'user', 'featuredImage']);

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
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                    ->orWhere('content', 'like', "%{$search}%")
                    ->orWhere('excerpt', 'like', "%{$search}%");
            });
        }

        $posts = $query->orderByRaw('COALESCE(published_at, created_at) DESC')->paginate(15);
        $categories = Category::active()->ordered()->get();

        return view('admin.posts.index', compact('posts', 'categories'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request)
    {
        $type = $request->get('type', 'berita');
        $categories = Category::active()->ordered()->get();

        // Redirect to specific create view based on type
        if ($type === 'page') {
            return view('admin.posts.create-page');
        }

        return view('admin.posts.create', compact('categories', 'type'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(PostRequest $request)
    {
        try {
            $data = $request->validated();
            $post = $this->postService->createPost($data);

            // Redirect based on post type
            $redirectRoute = 'admin.posts.index';
            $redirectParams = ['type' => $post->type];

            return redirect()->route($redirectRoute, $redirectParams)
                ->with('success', 'Post berhasil dibuat.');
        } catch (\Exception $e) {
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
            $data = $request->validated();

            \Log::info('=== CONTROLLER DEBUG START ===');
            \Log::info('Request data received in controller', ['data' => $data]);
            \Log::info('Gallery images in request', ['gallery_images' => $request->input('gallery_images')]);
            \Log::info('All request data', ['all' => $request->all()]);

            $updatedPost = $this->postService->updatePost($post->id, $data);

            // Redirect based on post type
            $redirectRoute = 'admin.posts.index';
            $redirectParams = ['type' => $updatedPost->type];

            return redirect()->route($redirectRoute, $redirectParams)
                ->with('success', 'Post berhasil diperbarui.');
        } catch (\Exception $e) {
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
            $this->postService->deletePost($post->id);

            return redirect()->route('admin.posts.index')
                ->with('success', 'Post berhasil dihapus.');
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal menghapus post: ' . $e->getMessage());
        }
    }

    /**
     * Show the form for creating a new page
     */
    public function createPage()
    {
        return view('admin.posts.create-page');
    }

    /**
     * Store a newly created page in storage.
     */
    public function storePage(PageRequest $request)
    {
        try {
            DB::beginTransaction();

            $data = $request->validated();
            $data['user_id'] = auth()->id();
            $data['type'] = 'page';

            // Handle featured image upload
            if ($request->hasFile('featured_image')) {
                $media = $this->fileUploadService->uploadImage(
                    $request->file('featured_image'),
                    'posts/featured',
                    auth()->id(),
                    ['width' => 1200, 'height' => 630]
                );
                $data['featured_image'] = $media->file_path;
                $data['featured_image_id'] = $media->id;
            }
            // Handle media picker selection (Media ID)
            elseif ($request->filled('featured_image') && is_numeric($request->featured_image)) {
                $media = \App\Models\Media::find($request->featured_image);
                if ($media) {
                    $data['featured_image'] = $media->file_path;
                    $data['featured_image_id'] = $media->id;
                }
            }

            $post = Post::create($data);

            DB::commit();

            return redirect()->route('admin.posts.index', ['type' => 'page'])
                ->with('success', 'Page berhasil dibuat.');
        } catch (\Exception $e) {
            DB::rollback();
            return back()->withInput()
                ->with('error', 'Gagal membuat page: ' . $e->getMessage());
        }
    }

    /**
     * Show the form for editing a page
     */
    public function editPage(Post $post)
    {
        // Ensure this is a page type post
        if ($post->type !== 'page') {
            return redirect()->route('admin.posts.edit', $post->id)
                ->with('info', 'This post is not a page type. Redirected to regular edit form.');
        }

        return view('admin.posts.edit-page', compact('post'));
    }

    /**
     * Update the specified page in storage.
     */
    public function updatePage(PageRequest $request, Post $post)
    {
        try {
            DB::beginTransaction();

            // Ensure this is a page type post
            if ($post->type !== 'page') {
                return redirect()->route('admin.posts.update', $post->id)
                    ->with('info', 'This post is not a page type. Redirected to regular update.');
            }

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
                $data['featured_image_id'] = $media->id;
            }
            // Handle media picker selection (Media ID)
            elseif ($request->filled('featured_image') && is_numeric($request->featured_image)) {
                $media = \App\Models\Media::find($request->featured_image);
                if ($media) {
                    $data['featured_image'] = $media->file_path;
                    $data['featured_image_id'] = $media->id;
                }
            }

            $post->update($data);

            DB::commit();

            return redirect()->route('admin.posts.index', ['type' => 'page'])
                ->with('success', 'Page berhasil diperbarui.');
        } catch (\Exception $e) {
            DB::rollback();
            return back()->withInput()
                ->with('error', 'Gagal memperbarui page: ' . $e->getMessage());
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
