<?php

namespace App\Http\Controllers;

use App\Models\Post;
use App\Models\Category;
use App\Models\Media;
use App\Models\Download;
use App\Models\SiteSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

use App\Helpers\TemplateHelper;

class FrontendController extends Controller
{
    /**
     * Display the homepage
     */
    public function team()
    {
        $teamMembers = \App\Models\TeamMember::where('status', true)
            ->orderBy('order', 'asc')
            ->get();

        return view(TemplateHelper::view('team'), compact('teamMembers'));
    }

    /**
     * Display homepage
     */
    public function index()
    {
        // Get slider posts
        $sliderPosts = Post::published()
            ->where('type', 'berita')
            ->where('is_slider', true)
            ->with(['category', 'user'])
            ->orderByRaw('COALESCE(published_at, created_at) DESC')
            ->limit(5)
            ->get();

        // Get featured posts
        $featuredPosts = Post::published()
            ->where('type', 'berita')
            ->where('is_featured', true)
            ->with(['category', 'user'])
            ->orderByRaw('COALESCE(published_at, created_at) DESC')
            ->limit(6)
            ->get();

        // Get latest posts
        $latestPosts = Post::published()
            ->where('type', 'berita')
            ->with(['category', 'user'])
            ->orderByRaw('COALESCE(published_at, created_at) DESC')
            ->limit(6)
            ->get();

        // Get popular posts (by views)
        $popularPosts = Post::published()
            ->where('type', 'berita')
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

        // Get partners
        $partners = Post::where('type', 'partner')
            ->published()
            ->orderBy('title', 'asc')
            ->get();

        // Get announcements
        // Get announcements
        $announcements = Post::published()
            ->whereHas('category', function ($q) {
                $q->where('slug', 'pengumuman')
                    ->orWhere('name', 'Pengumuman');
            })
            ->orderByRaw('COALESCE(published_at, created_at) DESC')
            ->limit(5)
            ->get();

        // Get latest downloads
        $latestDownloads = Download::active()
            ->public()
            ->ordered()
            ->limit(5)
            ->get();

        // Get latest video
        $latestVideo = Post::published()
            ->where('type', 'video')
            ->orderByRaw('COALESCE(published_at, created_at) DESC')
            ->first();


        // Get latest events
        $events = Post::published()
            ->where('type', 'event')
            ->orderByRaw('COALESCE(published_at, created_at) DESC')
            ->limit(7)
            ->get();

        // Get facilities
        $facilities = Post::published()
            ->where('type', 'fasilitas')
            ->orderByRaw('COALESCE(published_at, created_at) DESC')
            ->limit(3)
            ->get();

        // Get testimonials
        $testimonials = Post::published()
            ->where('type', 'testimonial')
            ->orderByRaw('COALESCE(published_at, created_at) DESC')
            ->limit(5)
            ->get();

        // Get Team Members
        $teamMembers = \App\Models\TeamMember::where('status', true)
            ->orderBy('order', 'asc')
            ->get();

        // Get Program Studi
        $programStudis = \App\Models\ProgramStudi::orderBy('sort_order', 'asc')
            ->orderBy('name', 'asc')
            ->get();

        return view(TemplateHelper::view('index'), compact(
            'sliderPosts',
            'featuredPosts',
            'latestPosts',
            'popularPosts',
            'categories',
            'galleryImages',
            'announcements',
            'partners',
            'latestDownloads',
            'latestVideo',
            'events',
            'facilities',
            'testimonials',
            'teamMembers',
            'programStudis'
        ));
    }

    /**
     * Display posts listing
     */
    public function posts(Request $request)
    {
        // Redirect events to dedicated page
        if ($request->input('type') === 'event') {
            return redirect()->route('frontend.events', $request->except('type'));
        }

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
        } else {
            // Default to 'berita' if no type specified
            $query->where('type', 'berita');
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

        $posts = $query->orderByRaw('COALESCE(published_at, created_at) DESC')
            ->paginate(SiteSetting::getValue('posts_per_page', 12));

        $categories = Category::active()->withCount('posts')->ordered()->get();
        $popularPosts = Post::published()->orderBy('views', 'desc')->limit(5)->get();

        return view(TemplateHelper::view('posts'), compact('posts', 'categories', 'popularPosts'));
    }

    /**
     * Display events listing
     */
    public function events(Request $request)
    {
        $query = Post::published()
            ->where('type', 'event')
            ->with(['category', 'user']);

        // Search
        if ($request->filled('q')) {
            $search = $request->q;
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                    ->orWhere('content', 'like', "%{$search}%")
                    ->orWhere('excerpt', 'like', "%{$search}%");
            });
        }

        $posts = $query->orderByRaw('COALESCE(published_at, created_at) DESC')
            ->paginate(SiteSetting::getValue('events_per_page', 12));

        // Get all events for calendar
        $calendarEvents = Post::published()
            ->where('type', 'event')
            ->select('id', 'title', 'slug', 'published_at', 'created_at', 'featured_image')
            ->orderByRaw('COALESCE(published_at, created_at) ASC')
            ->get();

        return view(TemplateHelper::view('events'), compact('posts', 'calendarEvents'));
    }

    /**
     * Display facilities listing
     */
    public function facilities()
    {
        $posts = Post::published()
            ->where('type', 'fasilitas')
            ->orderBy('sort_order', 'asc')
            ->paginate(12);

        return view(TemplateHelper::view('facilities'), compact('posts'));
    }

    /**
     * Get all facilities for AJAX
     */
    public function getAllFacilities()
    {
        $posts = Post::published()
            ->where('type', 'fasilitas')
            ->orderByRaw('sort_order ASC, COALESCE(published_at, created_at) DESC')
            ->get()
            ->map(function ($post) {
                return [
                    'id' => $post->id,
                    'title' => $post->title,
                    'excerpt' => $post->excerpt,
                    'featured_image_url' => !empty($post->featured_image_url) ? $post->featured_image_url : asset('images/default-post.jpg'),
                ];
            });

        return response()->json($posts);
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

        // Redirect 'page' type to new route
        if ($post->type === 'page') {
            return redirect()->route('frontend.page', $post->slug);
        }

        // Increment views
        $post->incrementViews();

        // Get related posts
        $relatedPosts = Post::published()
            ->where('category_id', $post->category_id)
            ->where('id', '!=', $post->id)
            ->where('type', 'berita')
            ->with(['category', 'user'])
            ->orderByRaw('COALESCE(published_at, created_at) DESC')
            ->limit(9)
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

        // Get comments
        $comments = $post->comments()->where('status', 'approved')->orderBy('created_at', 'desc')->get();

        return view(TemplateHelper::view('post'), compact(
            'post',
            'relatedPosts',
            'previousPost',
            'nextPost',
            'comments'
        ));
    }

    /**
     * Display single page
     */
    public function page($slug)
    {
        $post = Post::published()
            ->where('slug', $slug)
            ->where('type', 'page')
            ->with(['category', 'user'])
            ->firstOrFail();

        $post->incrementViews();

        // For pages, we pass filtered/empty variables for the common view
        $relatedPosts = collect();
        $previousPost = null;
        $nextPost = null;

        // Get comments if any (though usually disabled for pages)
        $comments = $post->comments()->where('status', 'approved')->orderBy('created_at', 'desc')->get();

        return view(TemplateHelper::view('post'), compact(
            'post',
            'relatedPosts',
            'previousPost',
            'nextPost',
            'comments'
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

        return view(TemplateHelper::view('posts'), compact(
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

        return view(TemplateHelper::view('gallery'), compact('images', 'categories'));
    }

    /**
     * Display about page
     */
    public function about()
    {
        // Fetch dynamic content from Pages (Posts with specific slugs)
        // Using 'first()' to get a single object or null
        $aboutPost = Post::where('slug', 'about')->orWhere('slug', 'tentang-kami')->published()->first();
        $visiPost = Post::where('slug', 'visi')->published()->first();
        $misiPost = Post::where('slug', 'misi')->published()->first();

        // Fallback or additional data
        $teamMembers = [];

        return view(TemplateHelper::view('about'), compact('aboutPost', 'visiPost', 'misiPost', 'teamMembers'));
    }

    /**
     * Display contact page
     */
    public function contact()
    {
        return view(TemplateHelper::view('contact'));
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
     * Display Program Studi Detail
     */
    public function programStudi($code)
    {
        $prodi = \App\Models\ProgramStudi::where('code', $code)
            ->with('programHead')
            ->firstOrFail();

        // Get related prodi for sidebar
        $otherProdi = \App\Models\ProgramStudi::where('id', '!=', $prodi->id)
            ->orderBy('sort_order')
            ->orderBy('name')
            ->limit(5)
            ->get();

        return view(TemplateHelper::view('prodi'), compact('prodi', 'otherProdi'));
    }

    /**
     * Search posts
     */
    public function search(Request $request)
    {
        $request->validate([
            'q' => 'required|string|min:1|max:255'
        ]);

        $query = $request->q;

        $posts = Post::published()
            ->where('type', '!=', 'ads') // Exclude ads
            ->with(['category', 'user'])
            ->where(function ($q) use ($query) {
                $q->where('title', 'like', "%{$query}%")
                    ->orWhere('content', 'like', "%{$query}%")
                    ->orWhere('excerpt', 'like', "%{$query}%");
            })
            ->orderByRaw('COALESCE(published_at, created_at) DESC')
            ->paginate(12);

        $categories = Category::active()->withCount('posts')->ordered()->get();
        $popularPosts = Post::published()->orderBy('views', 'desc')->limit(5)->get();

        return view(TemplateHelper::view('search'), compact('posts', 'categories', 'popularPosts', 'query'));
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
            'posts' => $posts->map(function ($post) {
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
        $stats = Cache::remember('site_stats', 3600, function () {
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