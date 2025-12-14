<?php

namespace App\Http\Controllers;

use App\Models\Download;
use App\Models\DownloadCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class DownloadController extends Controller
{
    /**
     * Display a listing of downloads for frontend
     */
    public function index(Request $request)
    {
        $query = Download::active()->ordered();

        // Filter by category
        if ($request->filled('category')) {
            $query->byCategory($request->category);
        }

        // Search functionality
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%");
            });
        }

        // Filter by type (public/protected)
        if ($request->filled('type')) {
            if ($request->type === 'public') {
                $query->public()->whereNull('password');
            } elseif ($request->type === 'protected') {
                $query->protected();
            }
        }

        $downloads = $query->with('user')->paginate(12);
        $categories = Download::getCategories();

        return view('frontend.downloads.index', compact('downloads', 'categories'));
    }

    /**
     * Get downloads data as JSON for AJAX requests
     */
    public function getDownloadsJson(Request $request)
    {
        $query = Download::active()->ordered();

        // Filter by category
        if ($request->filled('category')) {
            $query->byCategory($request->category);
        }

        // Search functionality
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%");
            });
        }

        // Filter by type (public/protected)
        if ($request->filled('type')) {
            if ($request->type === 'public') {
                $query->public()->whereNull('password');
            } elseif ($request->type === 'protected') {
                $query->protected();
            }
        }

        // Sorting
        $sortBy = $request->get('sort', 'created_at');
        $sortOrder = $request->get('order', 'desc');

        if (in_array($sortBy, ['title', 'created_at', 'download_count', 'file_size'])) {
            $query->orderBy($sortBy, $sortOrder);
        }

        $perPage = $request->get('per_page', 12);
        $downloads = $query->with('user')->paginate($perPage);

        return response()->json([
            'data' => $downloads->items(),
            'pagination' => [
                'current_page' => $downloads->currentPage(),
                'last_page' => $downloads->lastPage(),
                'per_page' => $downloads->perPage(),
                'total' => $downloads->total(),
                'from' => $downloads->firstItem(),
                'to' => $downloads->lastItem(),
            ]
        ]);
    }

    /**
     * Show download details
     */
    public function show(Download $download)
    {
        if (!$download->is_active) {
            abort(404);
        }

        return view('frontend.downloads.show', compact('download'));
    }

    /**
     * Download file
     */
    public function download(Request $request, Download $download)
    {
        if (!$download->is_active) {
            abort(404);
        }

        // Debug logging
        \Log::info('Download attempt', [
            'download_id' => $download->id,
            'title' => $download->title,
            'is_public' => $download->is_public,
            'has_password' => !empty($download->password),
            'user_authenticated' => auth()->check(),
            'request_has_password' => $request->has('password')
        ]);

        // Check if user is authenticated for private files (non-public files)
        if (!$download->is_public && !auth()->check()) {
            \Log::info('Redirecting to login - private file, user not authenticated');
            return redirect()->route('login')->with('error', 'Anda harus login untuk mengunduh file ini.');
        }

        // Validate password if required (for password-protected files)
        if ($download->password) {
            $request->validate([
                'password' => 'required|string'
            ]);

            \Log::info('Password validation details', [
                'input_password' => $request->password,
                'stored_hash' => $download->password,
                'hash_check_result' => \Hash::check($request->password, $download->password)
            ]);

            if (!$download->checkPassword($request->password)) {
                \Log::info('Password check failed');
                return back()->withErrors(['password' => 'Password salah.']);
            }
            \Log::info('Password check passed');
        }

        // Check if file exists
        if (!Storage::exists($download->file_path)) {
            return back()->with('error', 'File tidak ditemukan.');
        }

        // Increment download count
        $download->incrementDownloadCount();

        // Return file download
        return Storage::download($download->file_path, $download->file_name);
    }

    /**
     * Show password form for protected files
     */
    public function showPasswordForm(Download $download)
    {
        if (!$download->is_active || !$download->password) {
            abort(404);
        }

        return view('frontend.downloads.password', compact('download'));
    }

    /**
     * Admin: Display a listing of downloads
     */
    public function adminIndex(Request $request)
    {
        $query = Download::with('user');

        // Search functionality
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%")
                    ->orWhere('file_name', 'like', "%{$search}%");
            });
        }

        // Filter by category
        if ($request->filled('category')) {
            $query->byCategory($request->category);
        }

        // Filter by status
        if ($request->filled('status')) {
            if ($request->status === 'active') {
                $query->where('is_active', true);
            } elseif ($request->status === 'inactive') {
                $query->where('is_active', false);
            }
        }

        $downloads = $query->orderBy('sort_order')->orderBy('updated_at', 'desc')->paginate(15);
        $categories = Download::getCategories();

        return view('admin.downloads.index', compact('downloads', 'categories'));
    }

    /**
     * Admin: Show the form for creating a new download
     */
    public function create()
    {
        $categories = DownloadCategory::all();
        return view('admin.downloads.create', compact('categories'));
    }

    /**
     * Admin: Store a newly created download
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'file' => 'required|file|max:51200', // 50MB max
            'category_id' => 'nullable|exists:download_categories,id',
            'is_public' => 'boolean',
            'password' => 'nullable|string|min:4',
            'sort_order' => 'nullable|integer|min:0',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        try {
            $file = $request->file('file');
            $fileName = $file->getClientOriginalName();
            $fileExtension = $file->getClientOriginalExtension();
            $fileSize = $file->getSize();
            $fileMimeType = $file->getMimeType();

            // Generate unique file path
            $filePath = 'downloads/' . Str::uuid() . '.' . $fileExtension;

            // Store file
            $file->storeAs('', $filePath, 'public');

            // Create download record
            $download = Download::create([
                'title' => $request->title,
                'description' => $request->description,
                'file_name' => $fileName,
                'file_path' => $filePath,
                'file_type' => $fileMimeType,
                'file_size' => $fileSize,
                'is_public' => $request->boolean('is_public', true),
                'password' => $request->password,
                'category_id' => $request->category_id,
                'sort_order' => $request->sort_order ?? 0,
                'user_id' => auth()->id(),
            ]);

            return redirect()->route('admin.downloads.index')
                ->with('success', 'Download berhasil ditambahkan.');

        } catch (\Exception $e) {
            return back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage())->withInput();
        }
    }

    /**
     * Admin: Show the form for editing the specified download
     */
    public function edit(Download $download)
    {
        $categories = DownloadCategory::all();
        return view('admin.downloads.edit', compact('download', 'categories'));
    }

    /**
     * Admin: Update the specified download
     */
    public function update(Request $request, Download $download)
    {
        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'file' => 'nullable|file|max:51200', // 50MB max
            'category_id' => 'nullable|exists:download_categories,id',
            'is_public' => 'boolean',
            'password' => 'nullable|string|min:4',
            'sort_order' => 'nullable|integer|min:0',
            'is_active' => 'boolean',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        try {
            $updateData = [
                'title' => $request->title,
                'description' => $request->description,
                'category_id' => $request->category_id,
                'is_public' => $request->boolean('is_public', true),
                'sort_order' => $request->sort_order ?? 0,
                'is_active' => $request->boolean('is_active', true),
            ];

            // Update password if provided
            if ($request->filled('password')) {
                $updateData['password'] = $request->password;
            } elseif ($request->has('remove_password')) {
                $updateData['password'] = null;
            }

            // Handle file upload if new file is provided
            if ($request->hasFile('file')) {
                // Delete old file
                if (Storage::exists($download->file_path)) {
                    Storage::delete($download->file_path);
                }

                $file = $request->file('file');
                $fileName = $file->getClientOriginalName();
                $fileExtension = $file->getClientOriginalExtension();
                $fileSize = $file->getSize();
                $fileMimeType = $file->getMimeType();

                // Generate unique file path
                $filePath = 'downloads/' . Str::uuid() . '.' . $fileExtension;

                // Store file
                $file->storeAs('', $filePath, 'public');

                $updateData = array_merge($updateData, [
                    'file_name' => $fileName,
                    'file_path' => $filePath,
                    'file_type' => $fileMimeType,
                    'file_size' => $fileSize,
                ]);
            }

            $download->update($updateData);

            return redirect()->route('admin.downloads.index')
                ->with('success', 'Download berhasil diperbarui.');

        } catch (\Exception $e) {
            return back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage())->withInput();
        }
    }

    /**
     * Admin: Remove the specified download
     */
    public function destroy(Download $download)
    {
        try {
            // Delete file from storage
            if (Storage::exists($download->file_path)) {
                Storage::delete($download->file_path);
            }

            $download->delete();

            return redirect()->route('admin.downloads.index')
                ->with('success', 'Download berhasil dihapus.');

        } catch (\Exception $e) {
            return back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    /**
     * Admin: Toggle download status
     */
    public function toggleStatus(Download $download)
    {
        $download->update([
            'is_active' => !$download->is_active
        ]);

        $status = $download->is_active ? 'diaktifkan' : 'dinonaktifkan';

        return back()->with('success', "Download berhasil {$status}.");
    }
}