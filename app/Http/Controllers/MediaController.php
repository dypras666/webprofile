<?php

namespace App\Http\Controllers;

use App\Http\Requests\MediaRequest;
use App\Models\Media;
use App\Services\FileUploadService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class MediaController extends Controller
{
    protected $fileUploadService;

    public function __construct(FileUploadService $fileUploadService)
    {
        $this->fileUploadService = $fileUploadService;
        $this->middleware('auth');
        $this->middleware('permission:view media', ['only' => ['index', 'show']]);
        $this->middleware('permission:create media', ['only' => ['create', 'store']]);
        $this->middleware('permission:edit media', ['only' => ['edit', 'update']]);
        $this->middleware('permission:delete media', ['only' => ['destroy']]);
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Media::with('user');

        // Search functionality
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('alt_text', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        // Filter by type
        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        // Filter by user
        if ($request->filled('user_id')) {
            $query->where('user_id', $request->user_id);
        }

        // Sort
        $sortBy = $request->get('sort_by', 'created_at');
        $sortOrder = $request->get('sort_order', 'desc');
        $query->orderBy($sortBy, $sortOrder);

        $media = $query->paginate(20);

        return view('admin.media.index', compact('media'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('admin.media.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(MediaRequest $request)
    {
        try {
            DB::beginTransaction();

            $data = $request->validated();
            $data['user_id'] = auth()->id();

            // Handle file upload
            if ($request->hasFile('file')) {
                $file = $request->file('file');
                $uploadResult = $this->fileUploadService->upload($file, 'media');
                
                $data['file_path'] = $uploadResult['path'];
                $data['file_name'] = $uploadResult['name'];
                $data['file_size'] = $uploadResult['size'];
                $data['mime_type'] = $uploadResult['mime_type'];
                $data['type'] = $this->getMediaType($uploadResult['mime_type']);
                
                // Get image dimensions if it's an image
                if (str_starts_with($uploadResult['mime_type'], 'image/')) {
                    $metadata = $this->fileUploadService->getFileMetadata($uploadResult['path']);
                    $data['width'] = $metadata['width'] ?? null;
                    $data['height'] = $metadata['height'] ?? null;
                }
            }

            $media = Media::create($data);

            DB::commit();

            return redirect()->route('admin.media.index')
                           ->with('success', 'Media berhasil diunggah.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()
                        ->with('error', 'Gagal mengunggah media: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Media $medium)
    {
        $media = $medium; // For backward compatibility with views
        // Get media usage information
        $usage = $this->getMediaUsage($media->id);
        
        // Get media metadata
        $metadata = $this->getMediaMetadata($media);
        
        return view('admin.media.show', compact('media', 'usage', 'metadata'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Media $medium)
    {
        $media = $medium; // For backward compatibility with views
        return view('admin.media.edit', compact('media'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(MediaRequest $request, Media $medium)
    {
        $media = $medium; // For backward compatibility
        try {
            DB::beginTransaction();

            $data = $request->validated();

            // Handle file replacement
            if ($request->hasFile('file')) {
                // Delete old file
                $this->fileUploadService->delete($media->file_path);
                
                // Upload new file
                $file = $request->file('file');
                $uploadResult = $this->fileUploadService->upload($file, 'media');
                
                $data['file_path'] = $uploadResult['path'];
                $data['file_name'] = $uploadResult['name'];
                $data['file_size'] = $uploadResult['size'];
                $data['mime_type'] = $uploadResult['mime_type'];
                $data['type'] = $this->getMediaType($uploadResult['mime_type']);
                
                // Get image dimensions if it's an image
                if (str_starts_with($uploadResult['mime_type'], 'image/')) {
                    $metadata = $this->fileUploadService->getFileMetadata($uploadResult['path']);
                    $data['width'] = $metadata['width'] ?? null;
                    $data['height'] = $metadata['height'] ?? null;
                }
            }

            $media->update($data);

            DB::commit();

            return redirect()->route('admin.media.index')
                           ->with('success', 'Media berhasil diperbarui.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()
                        ->with('error', 'Gagal memperbarui media: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Media $medium)
    {
        $media = $medium; // For backward compatibility
        try {
            DB::beginTransaction();

            // Delete file from storage
            $this->fileUploadService->delete($media->file_path);
            
            // Delete database record
            $media->delete();

            DB::commit();

            return redirect()->route('admin.media.index')
                           ->with('success', 'Media berhasil dihapus.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal menghapus media: ' . $e->getMessage());
        }
    }

    /**
     * Bulk delete media
     */
    public function bulkDelete(Request $request)
    {
        $request->validate([
            'media_ids' => 'required|array',
            'media_ids.*' => 'exists:media,id'
        ]);

        try {
            DB::beginTransaction();

            $mediaItems = Media::whereIn('id', $request->media_ids)->get();
            
            foreach ($mediaItems as $media) {
                $this->fileUploadService->delete($media->file_path);
                $media->delete();
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => count($request->media_ids) . ' media berhasil dihapus.'
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Gagal menghapus media: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get media type based on MIME type
     */
    private function getMediaType($mimeType)
    {
        if (str_starts_with($mimeType, 'image/')) {
            return 'image';
        } elseif (str_starts_with($mimeType, 'video/')) {
            return 'video';
        } elseif (str_starts_with($mimeType, 'audio/')) {
            return 'audio';
        } elseif (in_array($mimeType, ['application/pdf', 'application/msword', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document'])) {
            return 'document';
        } else {
            return 'other';
        }
    }

    /**
     * Get media for AJAX requests (for media picker)
     */
    public function getMedia(Request $request)
    {
        $query = Media::query();

        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('file_name', 'like', "%{$search}%");
            });
        }

        $media = $query->orderBy('created_at', 'desc')
                      ->paginate(20);

        return response()->json($media);
    }

    /**
     * API endpoint for media picker
     */
    public function apiIndex(Request $request)
    {
        $query = Media::query();

        // Search functionality
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('original_name', 'like', "%{$search}%")
                  ->orWhere('file_path', 'like', "%{$search}%");
            });
        }

        // Filter by type
        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        // Pagination
        $perPage = $request->get('per_page', 50);
        $media = $query->orderBy('created_at', 'desc')
                      ->paginate($perPage);

        // Transform data for API response
        $transformedMedia = $media->getCollection()->map(function ($item) {
            return [
                'id' => $item->id,
                'filename' => $item->name,
                'original_name' => $item->original_name,
                'type' => $item->type,
                'extension' => $item->extension,
                'size' => $item->size,
                'size_formatted' => $this->formatBytes($item->size),
                'mime_type' => $item->mime_type,
                'url' => $item->url,
                'created_at' => $item->created_at,
                'updated_at' => $item->updated_at,
            ];
        });

        return response()->json([
            'media' => $transformedMedia,
            'pagination' => [
                'current_page' => $media->currentPage(),
                'last_page' => $media->lastPage(),
                'per_page' => $media->perPage(),
                'total' => $media->total(),
            ]
        ]);
    }

    /**
     * Get media by IDs
     */
    public function getByIds(Request $request)
    {
        $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'integer|exists:media,id'
        ]);

        $media = Media::whereIn('id', $request->ids)
                     ->get()
                     ->map(function ($item) {
                         return [
                             'id' => $item->id,
                             'filename' => $item->name,
                             'original_name' => $item->original_name,
                             'type' => $item->type,
                             'extension' => $item->extension,
                             'size' => $item->size,
                             'size_formatted' => $this->formatBytes($item->size),
                             'mime_type' => $item->mime_type,
                             'url' => $item->url,
                             'created_at' => $item->created_at,
                             'updated_at' => $item->updated_at,
                         ];
                     });

        return response()->json([
            'media' => $media
        ]);
    }

    /**
     * Upload media files
     */
    public function upload(Request $request)
    {
        $request->validate([
            'files' => 'required|array',
            'files.*' => 'file|max:10240', // 10MB max
        ]);

        try {
            DB::beginTransaction();

            $uploadedMedia = [];

            foreach ($request->file('files') as $file) {
                $media = $this->fileUploadService->upload($file, 'media');
                $uploadedMedia[] = $media;
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => count($uploadedMedia) . ' file(s) uploaded successfully.',
                'media' => $uploadedMedia
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Upload failed: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Format bytes to human readable format
     */
    private function formatBytes($bytes, $precision = 2)
    {
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];
        
        for ($i = 0; $bytes > 1024 && $i < count($units) - 1; $i++) {
            $bytes /= 1024;
        }
        
        return round($bytes, $precision) . ' ' . $units[$i];
    }

    /**
     * Get media usage information
     */
    private function getMediaUsage($mediaId)
    {
        $usage = [
            'posts' => [],
            'categories' => [],
            'total' => 0
        ];

        // Check usage in posts (featured_image and gallery_images)
        $posts = \App\Models\Post::where('featured_image', 'LIKE', '%' . $mediaId . '%')
                    ->orWhere('gallery_images', 'LIKE', '%' . $mediaId . '%')
                    ->orWhere('content', 'LIKE', '%' . $mediaId . '%')
                    ->get();
        
        $usage['posts'] = $posts;
        $usage['total'] += $posts->count();

        // Check usage in categories (image field)
        $categories = \App\Models\Category::where('image', 'LIKE', '%' . $mediaId . '%')
                        ->get();
        
        $usage['categories'] = $categories;
        $usage['total'] += $categories->count();

        return $usage;
    }

    /**
     * Get media metadata
     */
    private function getMediaMetadata($media)
    {
        $metadata = [
            'filename' => $media->name,
            'original_name' => $media->original_name,
            'type' => $media->type,
            'extension' => $media->extension,
            'size' => $this->formatBytes($media->size),
            'mime_type' => $media->mime_type,
            'path' => $media->file_path,
            'url' => $media->url,
            'created_at' => $media->created_at ? $media->created_at->format('M d, Y H:i:s') : null,
            'updated_at' => $media->updated_at ? $media->updated_at->format('M d, Y H:i:s') : null
        ];
        
        // Add image-specific metadata
        if ($media->type === 'image' && $media->metadata) {
            $imageData = json_decode($media->metadata, true);
            if ($imageData) {
                $metadata['dimensions'] = ($imageData['width'] ?? 0) . ' x ' . ($imageData['height'] ?? 0);
                $metadata['width'] = $imageData['width'] ?? null;
                $metadata['height'] = $imageData['height'] ?? null;
            }
        }
        
        return $metadata;
    }
}
