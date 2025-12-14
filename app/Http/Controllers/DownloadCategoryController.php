<?php

namespace App\Http\Controllers;

use App\Models\DownloadCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class DownloadCategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $categories = DownloadCategory::latest()->paginate(10);
        return view('admin.download-categories.index', compact('categories'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('admin.download-categories.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:download_categories,name',
            'description' => 'nullable|string',
        ]);

        DownloadCategory::create([
            'name' => $request->name,
            'slug' => Str::slug($request->name),
            'description' => $request->description,
        ]);

        return redirect()->route('admin.download-categories.index')
            ->with('success', 'Kategori download berhasil ditambahkan.');
    }

    /**
     * Display the specified resource.
     */
    public function show(DownloadCategory $downloadCategory)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(DownloadCategory $downloadCategory)
    {
        return view('admin.download-categories.edit', compact('downloadCategory'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, DownloadCategory $downloadCategory)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:download_categories,name,' . $downloadCategory->id,
            'description' => 'nullable|string',
        ]);

        $downloadCategory->update([
            'name' => $request->name,
            'slug' => Str::slug($request->name),
            'description' => $request->description,
        ]);

        return redirect()->route('admin.download-categories.index')
            ->with('success', 'Kategori download berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(DownloadCategory $downloadCategory)
    {
        if ($downloadCategory->downloads()->count() > 0) {
            return back()->with('error', 'Kategori ini tidak dapat dihapus karena masih digunakan oleh file download.');
        }

        $downloadCategory->delete();

        return redirect()->route('admin.download-categories.index')
            ->with('success', 'Kategori download berhasil dihapus.');
    }
}
