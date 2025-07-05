<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\PostController;
use App\Http\Controllers\MediaController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\SiteSettingController;
use App\Http\Controllers\FrontendController;
use App\Http\Controllers\Admin\NavigationController;
use App\Http\Controllers\DownloadController;

// Debug route for media
Route::get('/debug-media', function() {
    $media = App\Models\Media::all(['id', 'name', 'file_path', 'type']);
    return response()->json($media);
});

// Create test media
Route::get('/create-test-media', function () {
    // Get first available user or create one
    $user = App\Models\User::first();
    if (!$user) {
        $user = App\Models\User::create([
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => bcrypt('password')
        ]);
    }
    
    // Create test media entries
    $media1 = App\Models\Media::create([
        'name' => 'test-image-1.svg',
        'original_name' => 'test-image-1.svg',
        'file_path' => 'media/test-image-1.svg',
        'disk' => 'public',
        'mime_type' => 'image/svg+xml',
        'size' => 500,
        'extension' => 'svg',
        'type' => 'image',
        'user_id' => $user->id
    ]);
    
    $media2 = App\Models\Media::create([
        'name' => 'test-image-2.svg',
        'original_name' => 'test-image-2.svg',
        'file_path' => 'media/test-image-2.svg',
        'disk' => 'public',
        'mime_type' => 'image/svg+xml',
        'size' => 500,
        'extension' => 'svg',
        'type' => 'image',
        'user_id' => $user->id
    ]);
    
    return response()->json([
        'message' => 'Test media created',
        'user' => $user->toArray(),
        'media1' => $media1->toArray(),
        'media2' => $media2->toArray()
    ]);
});
 
 // Update post gallery images
 Route::get('/update-gallery-images', function() {
     $post = App\Models\Post::find(11);
     if (!$post) {
         return response()->json(['error' => 'Post not found']);
     }
     
     // Get available media IDs
     $mediaIds = App\Models\Media::pluck('id')->toArray();
     
     if (count($mediaIds) >= 2) {
         // Store as array, not JSON string - Laravel will handle the casting
         $post->gallery_images = [$mediaIds[0], $mediaIds[1]];
         $post->save();
         
         return response()->json([
             'message' => 'Gallery images updated',
             'post_id' => $post->id,
             'gallery_images' => $post->gallery_images,
             'gallery_images_raw' => $post->getRawOriginal('gallery_images'),
             'available_media' => $mediaIds
         ]);
     }
     
     return response()->json([
         'error' => 'Not enough media available',
         'available_media' => $mediaIds
     ]);
 });

Route::get('/check-users', function () {
    $users = App\Models\User::all();
    return response()->json([
        'users' => $users->toArray()
    ]);
});

// Debug gallery images data
Route::get('/debug-gallery-data', function() {
    $post = App\Models\Post::find(11);
    if (!$post) {
        return response()->json(['error' => 'Post not found']);
    }
    
    return response()->json([
        'post_id' => $post->id,
        'gallery_images_cast' => $post->gallery_images,
        'gallery_images_raw' => $post->getRawOriginal('gallery_images'),
        'is_array' => is_array($post->gallery_images),
        'count' => is_array($post->gallery_images) ? count($post->gallery_images) : 'not array',
        'type' => gettype($post->gallery_images)
    ]);
});

// Frontend Routes
Route::get('/', [FrontendController::class, 'index'])->name('frontend.index');
Route::get('/posts', [FrontendController::class, 'posts'])->name('frontend.posts');
Route::get('/post/{slug}', [FrontendController::class, 'post'])->name('frontend.post');
Route::get('/category/{slug}', [FrontendController::class, 'category'])->name('frontend.category');
Route::get('/gallery', [FrontendController::class, 'gallery'])->name('frontend.gallery');
Route::get('/about', [FrontendController::class, 'about'])->name('frontend.about');
Route::get('/contact', [FrontendController::class, 'contact'])->name('frontend.contact');
Route::post('/contact', [FrontendController::class, 'contactSubmit'])->name('frontend.contact.submit');
Route::get('/search', [FrontendController::class, 'search'])->name('frontend.search');

// Download Routes
Route::get('/downloads', [DownloadController::class, 'index'])->name('frontend.downloads');
Route::get('/downloads/{download}', [DownloadController::class, 'show'])->name('frontend.downloads.show');
Route::get('/downloads/{download}/password', [DownloadController::class, 'showPasswordForm'])->name('frontend.downloads.password');
Route::post('/downloads/{download}/download', [DownloadController::class, 'download'])->name('frontend.downloads.download');

// API Routes for Frontend
Route::get('/api/posts/type/{type}', [FrontendController::class, 'getPostsByType'])->name('api.posts.type');
Route::post('/api/posts/{post}/view', [FrontendController::class, 'updatePostView'])->name('api.posts.view');
Route::get('/api/stats', [FrontendController::class, 'getStats'])->name('api.stats');

// Authentication Routes
Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Admin Routes (Protected)
Route::prefix('admin')->name('admin.')->middleware(['auth'])->group(function () {
    // Dashboard
    Route::get('/', [AuthController::class, 'dashboard'])->name('dashboard');
    
    // Profile
    Route::get('/profile', [AuthController::class, 'profile'])->name('profile');
    Route::put('/profile', [AuthController::class, 'updateProfile'])->name('profile.update');
    
    // Categories
    Route::resource('categories', CategoryController::class);
    Route::patch('/categories/{category}/toggle-active', [CategoryController::class, 'toggleActive'])->name('categories.toggle-active');
    Route::post('/categories/update-order', [CategoryController::class, 'updateOrder'])->name('categories.update-order');
    
    // Posts
    Route::resource('posts', PostController::class);
    Route::patch('/posts/{post}/toggle-publish', [PostController::class, 'togglePublish'])->name('posts.toggle-publish');
    Route::patch('/posts/{post}/toggle-slider', [PostController::class, 'toggleSlider'])->name('posts.toggle-slider');
    Route::patch('/posts/{post}/toggle-featured', [PostController::class, 'toggleFeatured'])->name('posts.toggle-featured');
    
    // Pages (Special routes for page type posts)
    Route::get('/pages/create', [PostController::class, 'createPage'])->name('pages.create');
    Route::post('/pages', [PostController::class, 'storePage'])->name('pages.store');
    Route::get('/pages/{post}/edit', [PostController::class, 'editPage'])->name('pages.edit');
    Route::put('/pages/{post}', [PostController::class, 'updatePage'])->name('pages.update');
    
    // Media - API routes first to avoid conflicts with resource routes
    Route::get('/media/api', [MediaController::class, 'apiIndex'])->name('media.api');
    Route::get('/media/ajax', [MediaController::class, 'ajaxIndex'])->name('media.ajax');
    Route::post('/media/get-by-ids', [MediaController::class, 'getByIds'])->name('media.get-by-ids');
    Route::post('/media/upload', [MediaController::class, 'upload'])->name('media.upload');
    Route::delete('/media/bulk-delete', [MediaController::class, 'bulkDelete'])->name('media.bulk-delete');
    Route::resource('media', MediaController::class);
    
    // Users
    Route::resource('users', UserController::class);
    Route::delete('/users/bulk-delete', [UserController::class, 'bulkDelete'])->name('users.bulk-delete');
    
    // Navigation Management
    Route::prefix('navigation')->name('navigation.')->group(function () {
        Route::get('/', [NavigationController::class, 'index'])->name('index');
        Route::post('/', [NavigationController::class, 'store'])->name('store');
        Route::put('/{menu}', [NavigationController::class, 'update'])->name('update');
        Route::delete('/{menu}', [NavigationController::class, 'destroy'])->name('destroy');
        Route::post('/update-order', [NavigationController::class, 'updateOrder'])->name('update-order');
        Route::patch('/{menu}/toggle-active', [NavigationController::class, 'toggleActive'])->name('toggle-active');
        Route::get('/posts', [NavigationController::class, 'getPosts'])->name('posts');
        Route::get('/categories', [NavigationController::class, 'getCategories'])->name('categories');
    });
    
    // Downloads Management
    Route::prefix('downloads')->name('downloads.')->group(function () {
        Route::get('/', [DownloadController::class, 'adminIndex'])->name('index');
        Route::get('/create', [DownloadController::class, 'create'])->name('create');
        Route::post('/', [DownloadController::class, 'store'])->name('store');
        Route::get('/{download}/edit', [DownloadController::class, 'edit'])->name('edit');
        Route::put('/{download}', [DownloadController::class, 'update'])->name('update');
        Route::delete('/{download}', [DownloadController::class, 'destroy'])->name('destroy');
        Route::patch('/{download}/toggle-status', [DownloadController::class, 'toggleStatus'])->name('toggle-status');
    });
    
    // Site Settings
    Route::prefix('settings')->name('settings.')->group(function () {
        Route::get('/', [SiteSettingController::class, 'index'])->name('index');
        Route::put('/update', [SiteSettingController::class, 'updateAll'])->name('update'); // KEMBALI KE POST
        Route::get('/{group}/edit', [SiteSettingController::class, 'edit'])->name('edit');
        Route::put('/{group}/update', [SiteSettingController::class, 'updateGroup'])->name('update.group');
        Route::get('/value/{key}', [SiteSettingController::class, 'getValue'])->name('value');
        Route::post('/update-single', [SiteSettingController::class, 'updateSingle'])->name('update.single');
        Route::post('/clear-cache', [SiteSettingController::class, 'clearCache'])->name('clear.cache');
        Route::get('/export', [SiteSettingController::class, 'export'])->name('export');
        Route::post('/import', [SiteSettingController::class, 'import'])->name('import');
    });
});



// Redirect /admin to admin dashboard
// Route::redirect('/admin', '/admin/');
