<?php

namespace App\Http\Controllers;

use App\Models\Post;
use App\Models\Category;
use App\Models\Media;
use App\Models\SiteSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class FrontendController extends Controller
{
    /**
     * Display the homepage
     */
    public function index()
    {
        // Get slider posts
        $sliderPosts = Post::published()
            ->where('is_slider', true)
            ->with(['category', 'user'])
            ->orderByRaw('COALESCE(published_at, created_at) DESC')
            ->limit(5)
            ->get();

        // Get featured posts
        $featuredPosts = Post::published()
            ->where('is_featured', true)
            ->with(['category', 'user'])
            ->orderByRaw('COALESCE(published_at, created_at) DESC')
            ->limit(6)
            ->get();

        // Get latest posts
        $latestPosts = Post::published()
            ->with(['category', 'user'])
            ->orderByRaw('COALESCE(published_at, created_at) DESC')
            ->limit(8)
            ->get();

        // Get popular posts (by views)
        $popularPosts = Post::published()
            ->with(['category', 'user'])
            ->orderBy('views', 'desc')
            ->limit(6)
            ->get();

        // Get active categories with post count
        $categories = Category::active()
            ->withCount('posts')
            ->ordered()
            ->limit(8)
            ->get();

        // Get recent gallery images from posts
        $galleryImages = Post::published()
            ->where('type', 'gallery')
            ->orderByRaw('COALESCE(published_at, created_at) DESC')
            ->limit(12)
            ->get();

        return view('frontend.index', compact(
            'sliderPosts',
            'featuredPosts', 
            'latestPosts',
            'popularPosts',
            'categories',
            'galleryImages'
        ));
    }

    /**
     * Display posts listing
     */
    public function posts(Request $request)
    {
        $query = Post::published()->with(['category', 'user']);

        // Filter by category
        if ($request->filled('category')) {
            $category = Category::where('slug', $request->category)->first();
            if ($category) {
                $query->where('category_id', $category->id);
            }
        }

        // Filter by type
        if ($request->filled('type')) {
            $query->byType($request->type);
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

        $posts = $query->orderByRaw('COALESCE(published_at, created_at) DESC')
                      ->paginate(SiteSetting::getValue('posts_per_page', 12));

        $categories = Category::active()->withCount('posts')->ordered()->get();
        $popularPosts = Post::published()->orderBy('views', 'desc')->limit(5)->get();

        return view('frontend.posts', compact('posts', 'categories', 'popularPosts'));
    }

    /**
     * Display single post
     */
    public function post($slug)
    {
        $post = Post::published()
            ->where('slug', $slug)
            ->with(['category', 'user'])
            ->firstOrFail();

        // Increment views
        $post->incrementViews();

        // Get related posts
        $relatedPosts = Post::published()
            ->where('category_id', $post->category_id)
            ->where('id', '!=', $post->id)
            ->with(['category', 'user'])
            ->orderByRaw('COALESCE(published_at, created_at) DESC')
            ->limit(4)
            ->get();

        // Get previous and next posts
        $previousPost = Post::published()
            ->whereRaw('COALESCE(published_at, created_at) < ?', [$post->published_at ?? $post->created_at])
            ->orderByRaw('COALESCE(published_at, created_at) DESC')
            ->first();

        $nextPost = Post::published()
            ->whereRaw('COALESCE(published_at, created_at) > ?', [$post->published_at ?? $post->created_at])
            ->orderByRaw('COALESCE(published_at, created_at) ASC')
            ->first();

        return view('frontend.post', compact(
            'post',
            'relatedPosts',
            'previousPost',
            'nextPost'
        ));
    }

    /**
     * Display category posts
     */
    public function category($slug)
    {
        $category = Category::active()
            ->where('slug', $slug)
            ->firstOrFail();

        $posts = Post::published()
            ->where('category_id', $category->id)
            ->with(['category', 'user'])
            ->orderByRaw('COALESCE(published_at, created_at) DESC')
            ->paginate(SiteSetting::getValue('posts_per_page', 12));

        $categories = Category::active()->withCount('posts')->ordered()->get();
        $popularPosts = Post::published()->orderBy('views', 'desc')->limit(5)->get();

        return view('frontend.posts', compact(
            'category',
            'posts',
            'categories',
            'popularPosts'
        ));
    }

    /**
     * Display gallery
     */
    public function gallery(Request $request)
    {
        $query = Post::published()->where('type', 'gallery')->with(['category', 'user']);

        // Filter by category if provided
        if ($request->filled('category')) {
            $category = Category::where('slug', $request->category)->first();
            if ($category) {
                $query->where('category_id', $category->id);
            }
        }
        
        $images = $query->orderByRaw('COALESCE(published_at, created_at) DESC')
                       ->paginate(SiteSetting::getValue('gallery_images_per_page', 24));

        $categories = Category::active()->withCount('posts')->ordered()->get();

        return view('frontend.gallery', compact('images', 'categories'));
    }

    /**
     * Display about page
     */
    public function about()
    {
        $aboutContent = SiteSetting::getValue('about_content', '');
        $teamMembers = []; // You can add team members data here if needed
        
        return view('frontend.about', compact('aboutContent', 'teamMembers'));
    }

    /**
     * Display contact page
     */
    public function contact()
    {
        return view('frontend.contact');
    }

    /**
     * Handle contact form submission
     */
    public function contactSubmit(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'subject' => 'required|string|max:255',
            'message' => 'required|string|max:2000',
        ], [
            'name.required' => 'Nama wajib diisi.',
            'email.required' => 'Email wajib diisi.',
            'email.email' => 'Format email tidak valid.',
            'subject.required' => 'Subjek wajib diisi.',
            'message.required' => 'Pesan wajib diisi.',
        ]);

        // Here you can add logic to send email or save to database
        // For now, we'll just return success message
        
        return back()->with('success', 'Pesan Anda berhasil dikirim. Terima kasih!');
    }

    /**
     * Search posts
     */
    public function search(Request $request)
    {
        $request->validate([
            'q' => 'required|string|min:3|max:255'
        ]);

        $query = $request->q;
        
        $posts = Post::published()
            ->with(['category', 'user'])
            ->where(function($q) use ($query) {
                $q->where('title', 'like', "%{$query}%")
                  ->orWhere('content', 'like', "%{$query}%")
                  ->orWhere('excerpt', 'like', "%{$query}%");
            })
            ->orderByRaw('COALESCE(published_at, created_at) DESC')
            ->paginate(12);

        $categories = Category::active()->withCount('posts')->ordered()->get();
        $popularPosts = Post::published()->orderBy('views', 'desc')->limit(5)->get();

        return view('frontend.search', compact('posts', 'categories', 'popularPosts', 'query'));
    }

    /**
     * Get posts by type (for AJAX)
     */
    public function getPostsByType($type)
    {
        $posts = Post::published()
            ->byType($type)
            ->with(['category', 'user'])
            ->orderByRaw('COALESCE(published_at, created_at) DESC')
            ->limit(12)
            ->get();

        return response()->json([
            'success' => true,
            'posts' => $posts->map(function($post) {
                return [
                    'id' => $post->id,
                    'title' => $post->title,
                    'slug' => $post->slug,
                    'excerpt' => $post->excerpt,
                    'featured_image' => $post->featured_image,
                    'category' => $post->category->name ?? '',
                    'created_at' => $post->created_at->format('d M Y'),
                    'url' => route('frontend.post', $post->slug)
                ];
            })
        ]);
    }

    /**
     * Get site statistics (for AJAX)
     */
    public function getStats()
    {
        $stats = Cache::remember('site_stats', 3600, function() {
            return [
                'total_posts' => Post::published()->count(),
                'total_categories' => Category::active()->count(),
                'total_images' => Media::where('type', 'image')->count(),
                'total_views' => Post::sum('views')
            ];
        });

        return response()->json($stats);
    }

    /**
     * Update post view count (for AJAX)
     */
    public function updatePostView(Post $post)
    {
        try {
            $post->incrementViews();
            
            return response()->json([
                'success' => true,
                'views' => $post->views
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update view count'
            ], 500);
        }
    }
}