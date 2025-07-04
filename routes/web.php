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
