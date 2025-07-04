<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Admin\BaseAdminController;
use App\Http\Requests\MediaRequest;
use App\Services\MediaService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class AdminMediaController extends BaseAdminController
{
    protected $mediaService;

    public function __construct(MediaService $mediaService)
    {
        parent::__construct();
        $this->mediaService = $mediaService;
        
        // Set permissions
        $this->middleware('permission:manage_media');
    }

    /**
     * Display a listing of media
     */
    public function index(Request $request): View
    {
        $filters = $this->getFilters($request, [
            'search', 'type', 'user_id', 'extension', 
            'size_min', 'size_max', 'date_from', 'date_to'
        ]);
        
        $media = $this->mediaService->getAllMedia($filters, $request->get('per_page', 24));
        $statistics = $this->mediaService->getStatistics();
        $storageStats = $this->mediaService->getStorageStatistics();
        $typeStats = $this->mediaService->getCountByType();
        
        return view('admin.media.index', compact('media', 'statistics', 'storageStats', 'typeStats', 'filters'));
    }

    /**
     * Show the form for uploading new media
     */
    public function create(): View
    {
        $allowedTypes = $this->getAllowedFileTypes();
        $maxFileSize = $this->getMaxFileSize();
        
        return view('admin.media.create', compact('allowedTypes', 'maxFileSize'));
    }

    /**
     * Store newly uploaded media
     */
    public function store(MediaRequest $request): JsonResponse
    {
        try {
            $files = $request->file('files');
            $uploadedMedia = [];
            
            if (!is_array($files)) {
                $files = [$files];
            }
            
            foreach ($files as $file) {
                $media = $this->mediaService->uploadFile($file, [
                    'alt_text' => $request->get('alt_text'),
                    'description' => $request->get('description'),
                    'folder' => $request->get('folder', 'uploads')
                ]);
                
                if ($media) {
                    $uploadedMedia[] = $media;
                }
            }
            
            $this->logActivity('media_uploaded', 'Uploaded ' . count($uploadedMedia) . ' media files');
            
            return $this->successResponse('Media uploaded successfully', [
                'media' => $uploadedMedia,
                'count' => count($uploadedMedia)
            ]);
        } catch (\Exception $e) {
            return $this->errorResponse('Failed to upload media: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified media
     */
    public function show($id): View
    {
        $media = $this->mediaService->findMedia($id);
        
        if (!$media) {
            abort(404, 'Media not found');
        }
        
        $usage = $this->mediaService->getMediaUsage($id);
        $metadata = $this->getMediaMetadata($media);
        
        return view('admin.media.show', compact('media', 'usage', 'metadata'));
    }

    /**
     * Show the form for editing the specified media
     */
    public function edit($id): View
    {
        $media = $this->mediaService->findMedia($id);
        
        if (!$media) {
            abort(404, 'Media not found');
        }
        
        return view('admin.media.edit', compact('media'));
    }

    /**
     * Update the specified media
     */
    public function update(Request $request, $id): RedirectResponse
    {
        $request->validate([
            'filename' => 'required|string|max:255',
            'alt_text' => 'nullable|string|max:255',
            'description' => 'nullable|string|max:1000'
        ]);
        
        try {
            $media = $this->mediaService->updateMedia($id, $request->only([
                'filename', 'alt_text', 'description'
            ]));
            
            if (!$media) {
                return back()->with('error', 'Media not found.');
            }
            
            $this->logActivity('media_updated', 'Updated media: ' . $media->filename, $media->id);
            
            return redirect()->route('admin.media.index')
                ->with('success', 'Media updated successfully.');
        } catch (\Exception $e) {
            return back()->withInput()
                ->with('error', 'Failed to update media: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified media
     */
    public function destroy($id): RedirectResponse
    {
        try {
            $media = $this->mediaService->findMedia($id);
            
            if (!$media) {
                return back()->with('error', 'Media not found.');
            }
            
            // Check if media is being used
            $usage = $this->mediaService->getMediaUsage($id);
            if ($usage['total'] > 0) {
                return back()->with('error', 'Cannot delete media that is currently being used.');
            }
            
            $filename = $media->filename;
            $deleted = $this->mediaService->deleteMedia($id);
            
            if ($deleted) {
                $this->logActivity('media_deleted', 'Deleted media: ' . $filename, $id);
                return back()->with('success', 'Media deleted successfully.');
            }
            
            return back()->with('error', 'Failed to delete media.');
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to delete media: ' . $e->getMessage());
        }
    }

    /**
     * Bulk actions for media
     */
    public function bulkAction(Request $request): JsonResponse
    {
        $request->validate([
            'action' => 'required|in:delete,optimize,generate_thumbnails,move_folder',
            'ids' => 'required|array|min:1',
            'ids.*' => 'integer|exists:media,id',
            'folder' => 'required_if:action,move_folder|string|max:255'
        ]);
        
        try {
            $action = $request->action;
            $ids = $request->ids;
            $count = 0;
            
            switch ($action) {
                case 'delete':
                    $count = $this->mediaService->bulkDelete($ids);
                    $message = "Deleted {$count} media files";
                    break;
                    
                case 'optimize':
                    foreach ($ids as $id) {
                        if ($this->mediaService->optimizeImage($id)) {
                            $count++;
                        }
                    }
                    $message = "Optimized {$count} images";
                    break;
                    
                case 'generate_thumbnails':
                    foreach ($ids as $id) {
                        if ($this->mediaService->generateThumbnail($id)) {
                            $count++;
                        }
                    }
                    $message = "Generated thumbnails for {$count} images";
                    break;
                    
                case 'move_folder':
                    foreach ($ids as $id) {
                        $media = $this->mediaService->findMedia($id);
                        if ($media && $this->moveMediaToFolder($media, $request->folder)) {
                            $count++;
                        }
                    }
                    $message = "Moved {$count} files to {$request->folder}";
                    break;
                    
                default:
                    return $this->errorResponse('Invalid action');
            }
            
            $this->logActivity('media_bulk_' . $action, $message, null, ['ids' => $ids]);
            
            return $this->successResponse($message, ['count' => $count]);
        } catch (\Exception $e) {
            return $this->errorResponse('Bulk action failed: ' . $e->getMessage());
        }
    }

    /**
     * Upload multiple files via AJAX
     */
    public function uploadMultiple(Request $request): JsonResponse
    {
        $request->validate([
            'files' => 'required|array|min:1|max:10',
            'files.*' => 'file|mimes:jpeg,png,jpg,gif,svg,pdf,doc,docx,xls,xlsx,ppt,pptx,txt,zip,rar|max:10240',
            'folder' => 'nullable|string|max:255'
        ]);
        
        try {
            $uploadedMedia = $this->mediaService->uploadMultipleFiles(
                $request->file('files'),
                ['folder' => $request->get('folder', 'uploads')]
            );
            
            $this->logActivity('media_bulk_uploaded', 'Uploaded ' . count($uploadedMedia) . ' media files');
            
            return $this->successResponse('Files uploaded successfully', [
                'media' => $uploadedMedia,
                'count' => count($uploadedMedia)
            ]);
        } catch (\Exception $e) {
            return $this->errorResponse('Failed to upload files: ' . $e->getMessage());
        }
    }

    /**
     * Get media statistics
     */
    public function statistics(): JsonResponse
    {
        try {
            $stats = $this->mediaService->getStatistics();
            $storageStats = $this->mediaService->getStorageStatistics();
            $typeStats = $this->mediaService->getCountByType();
            $extensionStats = $this->mediaService->getCountByExtension();
            $userStats = $this->mediaService->getStorageByUser();
            
            return $this->successResponse('Statistics retrieved', [
                'general' => $stats,
                'storage' => $storageStats,
                'by_type' => $typeStats,
                'by_extension' => $extensionStats,
                'by_user' => $userStats
            ]);
        } catch (\Exception $e) {
            return $this->errorResponse('Failed to get statistics: ' . $e->getMessage());
        }
    }

    /**
     * Search media (AJAX)
     */
    public function search(Request $request): JsonResponse
    {
        $request->validate([
            'query' => 'required|string|min:2',
            'type' => 'nullable|in:image,video,document,audio',
            'limit' => 'integer|min:1|max:50'
        ]);
        
        try {
            $filters = [
                'type' => $request->type
            ];
            
            $media = $this->mediaService->searchMedia(
                $request->query,
                $filters,
                $request->get('limit', 20)
            );
            
            $results = $media->map(function ($item) {
                return [
                    'id' => $item->id,
                    'filename' => $item->filename,
                    'original_name' => $item->original_name,
                    'type' => $item->type,
                    'extension' => $item->extension,
                    'size' => $this->formatBytes($item->size),
                    'url' => $item->url,
                    'thumbnail' => $item->thumbnail_url,
                    'created_at' => $item->created_at->format('M d, Y'),
                    'edit_url' => route('admin.media.edit', $item)
                ];
            });
            
            return $this->successResponse('Search completed', $results);
        } catch (\Exception $e) {
            return $this->errorResponse('Search failed: ' . $e->getMessage());
        }
    }

    /**
     * Get unused media
     */
    public function unused(): JsonResponse
    {
        try {
            $unusedMedia = $this->mediaService->getUnusedMedia(50);
            
            $results = $unusedMedia->map(function ($item) {
                return [
                    'id' => $item->id,
                    'filename' => $item->filename,
                    'type' => $item->type,
                    'size' => $this->formatBytes($item->size),
                    'url' => $item->url,
                    'created_at' => $item->created_at->format('M d, Y'),
                    'days_unused' => $item->created_at->diffInDays(now())
                ];
            });
            
            return $this->successResponse('Unused media retrieved', $results);
        } catch (\Exception $e) {
            return $this->errorResponse('Failed to get unused media: ' . $e->getMessage());
        }
    }

    /**
     * Get large files
     */
    public function largeFiles(Request $request): JsonResponse
    {
        try {
            $minSize = $request->get('min_size', 5 * 1024 * 1024); // 5MB default
            $largeFiles = $this->mediaService->getLargeFiles($minSize, 50);
            
            $results = $largeFiles->map(function ($item) {
                return [
                    'id' => $item->id,
                    'filename' => $item->filename,
                    'type' => $item->type,
                    'size' => $this->formatBytes($item->size),
                    'size_bytes' => $item->size,
                    'url' => $item->url,
                    'created_at' => $item->created_at->format('M d, Y')
                ];
            });
            
            return $this->successResponse('Large files retrieved', $results);
        } catch (\Exception $e) {
            return $this->errorResponse('Failed to get large files: ' . $e->getMessage());
        }
    }

    /**
     * Clean up old unused media
     */
    public function cleanup(Request $request): JsonResponse
    {
        $request->validate([
            'days' => 'integer|min:1|max:365'
        ]);
        
        try {
            $days = $request->get('days', 30);
            $count = $this->mediaService->cleanupOldUnusedMedia($days);
            
            $this->logActivity('media_cleanup', "Cleaned up {$count} old unused media files (older than {$days} days)");
            
            return $this->successResponse("Cleaned up {$count} old unused media files", ['count' => $count]);
        } catch (\Exception $e) {
            return $this->errorResponse('Cleanup failed: ' . $e->getMessage());
        }
    }

    /**
     * Optimize image
     */
    public function optimize($id): JsonResponse
    {
        try {
            $media = $this->mediaService->findMedia($id);
            
            if (!$media) {
                return $this->errorResponse('Media not found');
            }
            
            if (!in_array($media->type, ['image'])) {
                return $this->errorResponse('Only images can be optimized');
            }
            
            $originalSize = $media->size;
            $optimized = $this->mediaService->optimizeImage($id);
            
            if ($optimized) {
                $newSize = $this->mediaService->findMedia($id)->size;
                $savedBytes = $originalSize - $newSize;
                $savedPercentage = round(($savedBytes / $originalSize) * 100, 2);
                
                $this->logActivity('media_optimized', 'Optimized image: ' . $media->filename, $media->id);
                
                return $this->successResponse('Image optimized successfully', [
                    'original_size' => $this->formatBytes($originalSize),
                    'new_size' => $this->formatBytes($newSize),
                    'saved_bytes' => $this->formatBytes($savedBytes),
                    'saved_percentage' => $savedPercentage
                ]);
            }
            
            return $this->errorResponse('Failed to optimize image');
        } catch (\Exception $e) {
            return $this->errorResponse('Optimization failed: ' . $e->getMessage());
        }
    }

    /**
     * Generate thumbnail
     */
    public function generateThumbnail($id): JsonResponse
    {
        try {
            $media = $this->mediaService->findMedia($id);
            
            if (!$media) {
                return $this->errorResponse('Media not found');
            }
            
            if (!in_array($media->type, ['image'])) {
                return $this->errorResponse('Only images can have thumbnails');
            }
            
            $thumbnail = $this->mediaService->generateThumbnail($id);
            
            if ($thumbnail) {
                $this->logActivity('media_thumbnail_generated', 'Generated thumbnail for: ' . $media->filename, $media->id);
                
                return $this->successResponse('Thumbnail generated successfully', [
                    'thumbnail_url' => $thumbnail
                ]);
            }
            
            return $this->errorResponse('Failed to generate thumbnail');
        } catch (\Exception $e) {
            return $this->errorResponse('Thumbnail generation failed: ' . $e->getMessage());
        }
    }

    /**
     * Duplicate media
     */
    public function duplicate($id): JsonResponse
    {
        try {
            $duplicated = $this->mediaService->duplicateMedia($id);
            
            if ($duplicated) {
                $this->logActivity('media_duplicated', 'Duplicated media: ' . $duplicated->filename, $duplicated->id);
                
                return $this->successResponse('Media duplicated successfully', [
                    'media' => [
                        'id' => $duplicated->id,
                        'filename' => $duplicated->filename,
                        'url' => $duplicated->url
                    ]
                ]);
            }
            
            return $this->errorResponse('Failed to duplicate media');
        } catch (\Exception $e) {
            return $this->errorResponse('Duplication failed: ' . $e->getMessage());
        }
    }

    /**
     * Export media list
     */
    public function export(Request $request)
    {
        try {
            $format = $request->get('format', 'csv');
            
            if ($format === 'csv') {
                $csvData = $this->mediaService->exportToCSV();
                
                return response($csvData)
                    ->header('Content-Type', 'text/csv')
                    ->header('Content-Disposition', 'attachment; filename="media_' . date('Y-m-d') . '.csv"');
            }
            
            return back()->with('error', 'Unsupported export format.');
        } catch (\Exception $e) {
            return back()->with('error', 'Export failed: ' . $e->getMessage());
        }
    }

    /**
     * Get media library data for picker
     */
    public function library(Request $request): JsonResponse
    {
        try {
            $data = $this->mediaService->getMediaLibraryData([
                'type' => $request->get('type'),
                'search' => $request->get('search'),
                'per_page' => $request->get('per_page', 20)
            ]);
            
            return $this->successResponse('Media library data retrieved', $data);
        } catch (\Exception $e) {
            return $this->errorResponse('Failed to get media library data: ' . $e->getMessage());
        }
    }

    /**
     * Get upload trends
     */
    public function uploadTrends(Request $request): JsonResponse
    {
        try {
            $period = $request->get('period', 'month'); // day, week, month, year
            $trends = $this->mediaService->getUploadTrends($period);
            
            return $this->successResponse('Upload trends retrieved', $trends);
        } catch (\Exception $e) {
            return $this->errorResponse('Failed to get upload trends: ' . $e->getMessage());
        }
    }

    /**
     * Get media usage information
     */
    public function usage($id): JsonResponse
    {
        try {
            $usage = $this->mediaService->getMediaUsage($id);
            
            return $this->successResponse('Media usage retrieved', $usage);
        } catch (\Exception $e) {
            return $this->errorResponse('Failed to get media usage: ' . $e->getMessage());
        }
    }

    /**
     * Get allowed file types
     */
    protected function getAllowedFileTypes(): array
    {
        return [
            'image' => ['jpeg', 'jpg', 'png', 'gif', 'svg', 'webp'],
            'video' => ['mp4', 'avi', 'mov', 'wmv', 'flv', 'webm'],
            'document' => ['pdf', 'doc', 'docx', 'xls', 'xlsx', 'ppt', 'pptx', 'txt'],
            'audio' => ['mp3', 'wav', 'ogg', 'aac', 'flac'],
            'archive' => ['zip', 'rar', '7z', 'tar', 'gz']
        ];
    }

    /**
     * Get maximum file size
     */
    protected function getMaxFileSize(): int
    {
        return 10 * 1024 * 1024; // 10MB
    }

    /**
     * Get media metadata
     */
    protected function getMediaMetadata($media): array
    {
        $metadata = [
            'filename' => $media->filename,
            'original_name' => $media->original_name,
            'type' => $media->type,
            'extension' => $media->extension,
            'size' => $this->formatBytes($media->size),
            'mime_type' => $media->mime_type,
            'path' => $media->path,
            'url' => $media->url,
            'created_at' => $media->created_at->format('M d, Y H:i:s'),
            'updated_at' => $media->updated_at->format('M d, Y H:i:s')
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

    /**
     * Move media to folder
     */
    protected function moveMediaToFolder($media, $folder): bool
    {
        try {
            $oldPath = $media->path;
            $newPath = $folder . '/' . $media->filename;
            
            // Move the actual file
            if (Storage::disk('public')->move($oldPath, $newPath)) {
                // Update database
                $media->update(['path' => $newPath]);
                return true;
            }
            
            return false;
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * Format bytes to human readable format
     */
    protected function formatBytes($bytes, $precision = 2): string
    {
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];
        
        for ($i = 0; $bytes > 1024 && $i < count($units) - 1; $i++) {
            $bytes /= 1024;
        }
        
        return round($bytes, $precision) . ' ' . $units[$i];
    }
}