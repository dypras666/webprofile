<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Post;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class AdminAdsController extends BaseAdminController
{
    public function index()
    {
        $ads = Post::where('type', 'ads')
            ->with('category')
            ->latest()
            ->paginate(10);

        return view('admin.ads.index', compact('ads'));
    }

    public function create()
    {
        // Fetch categories that start with "Ads" or just all for now
        // Ideally we should scope this, but for now getting all is fine or creating specific ones
        $categories = Category::where('name', 'like', 'Ads%')->get();
        if ($categories->isEmpty()) {
            // Fallback or prompting user might be needed, but we'll show all
            $categories = Category::all();
        }

        return view('admin.ads.create', compact('categories'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'featured_image' => 'required|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
            'category_id' => 'required|exists:categories,id', // Position
            'excerpt' => 'nullable|url', // Target URL
        ]);

        $data = $request->except(['featured_image']);
        $data['type'] = 'ads';
        $data['slug'] = Str::slug($request->title) . '-' . uniqid(); // Ensure unique slug for ads
        $data['is_published'] = $request->has('is_published');

        // Handle Image Upload
        if ($request->hasFile('featured_image')) {
            $path = $request->file('featured_image')->store('ads', 'public');
            $data['featured_image'] = $path;
        }

        $data['user_id'] = auth()->id(); // Assign current user

        Post::create($data);

        return redirect()->route('admin.ads.index')->with('success', 'Ad created successfully.');
    }

    public function edit($id)
    {
        $ad = Post::where('type', 'ads')->findOrFail($id);
        $categories = Category::where('name', 'like', 'Ads%')->get();
        if ($categories->isEmpty()) {
            $categories = Category::all();
        }
        return view('admin.ads.edit', compact('ad', 'categories'));
    }

    public function update(Request $request, $id)
    {
        $ad = Post::where('type', 'ads')->findOrFail($id);

        $request->validate([
            'title' => 'required|string|max:255',
            'featured_image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
            'category_id' => 'required|exists:categories,id',
            'excerpt' => 'nullable|url',
        ]);

        $data = $request->except(['featured_image']);
        $data['is_published'] = $request->has('is_published');

        if ($request->hasFile('featured_image')) {
            $path = $request->file('featured_image')->store('ads', 'public');
            $data['featured_image'] = $path;
        }

        $ad->update($data);

        return redirect()->route('admin.ads.index')->with('success', 'Ad updated successfully.');
    }

    public function destroy($id)
    {
        $ad = Post::where('type', 'ads')->findOrFail($id);
        $ad->delete();
        return redirect()->route('admin.ads.index')->with('success', 'Ad deleted successfully.');
    }
}
