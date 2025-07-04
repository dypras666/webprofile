<?php

namespace App\Services;

use App\Models\Media;
use App\Repositories\MediaRepository;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Str;

class MediaService
{
    protected $mediaRepository;
    protected $fileUploadService;

    public function __construct(
        MediaRepository $mediaRepository,
        FileUploadService $fileUploadService
    ) {
        $this->mediaRepository = $mediaRepository;
        $this->fileUploadService = $fileUploadService;
    }

    /**
     * Get all media with filters and pagination
     */
    public function getAllMedia(array $filters = [], $perPage = 20): LengthAwarePaginator
    {
        return $this->mediaRepository->getPaginatedWithFilters($filters, $perPage);
    }

    /**
     * Get media by type
     */
    public function getMediaByType($type, $limit = null): Collection
    {
        return $this->mediaRepository->getByType($type, $limit);
    }

    /**
     * Get images
     */
    public function getImages($limit = null): Collection
    {
        return $this->mediaRepository->getImages($limit);
    }

    /**
     * Get videos
     */
    public function getVideos($limit = null): Collection
    {
        return $this->mediaRepository->getVideos($limit);
    }

    /**
     * Get documents
     */
    public function getDocuments($limit = null): Collection
    {
        return $this->mediaRepository->getDocuments($limit);
    }

    /**
     * Get audio files
     */
    public function getAudio($limit = null): Collection
    {
        return $this->mediaRepository->getAudio($limit);
    }

    /**
     * Get media by user
     */
    public function getMediaByUser($userId, $limit = null): Collection
    {
        return $this->mediaRepository->getByUser($userId, $limit);
    }

    /**
     * Get recent media
     */
    public function getRecentMedia($limit = 20): Collection
    {
        return $this->mediaRepository->getRecent($limit);
    }

    /**
     * Find media by ID
     */
    public function findMedia($id): ?Media
    {
        return $this->mediaRepository->find($id);
    }

    /**
     * Find media by filename
     */
    public function findByFilename($filename): ?Media
    {
        return $this->mediaRepository->findByFilename($filename);
    }

    /**
     * Find media by path
     */
    public function findByPath($path): ?Media
    {
        return $this->mediaRepository->findByPath($path);
    }

    /**
     * Upload single file
     */
    public function uploadFile(UploadedFile $file, $folder = 'uploads', array $metadata = []): Media
    {
        DB::beginTransaction();
        
        try {
            // Upload file using FileUploadService
            $media = $this->fileUploadService->upload($file, $folder);
            
            // Update with additional metadata if provided
            if (!empty($metadata)) {
                $updateData = array_intersect_key($metadata, array_flip([
                    'title', 'alt_text', 'caption', 'description'
                ]));
                
                if (!empty($updateData)) {
                    $media = $this->mediaRepository->update($media->id, $updateData);
                }
            }
            
            // Clear related caches
            $this->clearRelatedCaches();
            
            DB::commit();
            
            return $media;
        } catch (\Exception $e) {
            DB::rollback();
            throw $e;
        }
    }

    /**
     * Upload multiple files
     */
    public function uploadMultipleFiles(array $files, $folder = 'uploads', array $metadata = []): Collection
    {
        $uploadedMedia = collect();
        
        DB::beginTransaction();
        
        try {
            foreach ($files as $file) {
                if ($file instanceof UploadedFile) {
                    $media = $this->uploadFile($file, $folder, $metadata);
                    $uploadedMedia->push($media);
                }
            }
            
            DB::commit();
            
            return $uploadedMedia;
        } catch (\Exception $e) {
            DB::rollback();
            
            // Clean up any uploaded files
            foreach ($uploadedMedia as $media) {
                $this->fileUploadService->delete($media->path);
            }
            
            throw $e;
        }
    }

    /**
     * Update media metadata
     */
    public function updateMedia($id, array $data): Media
    {
        $media = $this->mediaRepository->find($id);
        
        if (!$media) {
            throw new \Exception('Media not found');
        }
        
        // Filter allowed fields
        $allowedFields = [
            'title', 'alt_text', 'caption', 'description'
        ];
        
        $updateData = array_intersect_key($data, array_flip($allowedFields));
        
        $updatedMedia = $this->mediaRepository->update($id, $updateData);
        
        // Clear related caches
        $this->clearRelatedCaches();
        
        return $updatedMedia;
    }

    /**
     * Delete media
     */
    public function deleteMedia($id): bool
    {
        DB::beginTransaction();
        
        try {
            $media = $this->mediaRepository->find($id);
            
            if (!$media) {
                return false;
            }
            
            // Check if media is being used
            $usage = $this->getMediaUsage($id);
            if (!empty($usage)) {
                throw new \Exception('Cannot delete media that is being used in: ' . implode(', ', $usage));
            }
            
            // Delete physical files
            $deleted = $this->mediaRepository->deleteWithFiles($id);
            
            // Clear related caches
            $this->clearRelatedCaches();
            
            DB::commit();
            
            return $deleted;
        } catch (\Exception $e) {
            DB::rollback();
            throw $e;
        }
    }

    /**
     * Bulk delete media
     */
    public function bulkDelete(array $ids): int
    {
        DB::beginTransaction();
        
        try {
            // Check if any media is being used
            foreach ($ids as $id) {
                $usage = $this->getMediaUsage($id);
                if (!empty($usage)) {
                    $media = $this->mediaRepository->find($id);
                    throw new \Exception('Cannot delete media "' . $media->filename . '" that is being used');
                }
            }
            
            $deleted = $this->mediaRepository->bulkDeleteWithFiles($ids);
            
            // Clear related caches
            $this->clearRelatedCaches();
            
            DB::commit();
            
            return $deleted;
        } catch (\Exception $e) {
            DB::rollback();
            throw $e;
        }
    }

    /**
     * Search media
     */
    public function searchMedia($query, array $filters = []): Collection
    {
        return $this->mediaRepository->search($query, $filters);
    }

    /**
     * Get unused media
     */
    public function getUnusedMedia($olderThanDays = 30): Collection
    {
        return $this->mediaRepository->getUnused($olderThanDays);
    }

    /**
     * Get large files
     */
    public function getLargeFiles($minSizeMB = 10): Collection
    {
        return $this->mediaRepository->getLargeFiles($minSizeMB);
    }

    /**
     * Clean up old unused media
     */
    public function cleanupOldUnusedMedia($olderThanDays = 30): int
    {
        DB::beginTransaction();
        
        try {
            $deleted = $this->mediaRepository->cleanupOldUnused($olderThanDays);
            
            // Clear related caches
            $this->clearRelatedCaches();
            
            DB::commit();
            
            return $deleted;
        } catch (\Exception $e) {
            DB::rollback();
            throw $e;
        }
    }

    /**
     * Get media statistics
     */
    public function getStatistics(): array
    {
        return $this->mediaRepository->getStatistics();
    }

    /**
     * Get storage statistics
     */
    public function getStorageStatistics(): array
    {
        $stats = $this->mediaRepository->getStatistics();
        
        return [
            'total_files' => $stats['total_count'],
            'total_storage' => $this->formatBytes($stats['total_storage']),
            'total_storage_bytes' => $stats['total_storage'],
            'average_file_size' => $this->formatBytes($stats['average_file_size']),
            'largest_file' => $stats['largest_file'],
            'storage_by_type' => $stats['storage_by_type'],
            'storage_by_user' => $stats['storage_by_user'],
            'count_by_type' => $stats['count_by_type'],
            'count_by_extension' => $stats['count_by_extension'],
            'unused_count' => $stats['unused_count'],
            'recent_uploads' => $stats['recent_uploads']
        ];
    }

    /**
     * Get upload trends
     */
    public function getUploadTrends($months = 12): array
    {
        return $this->mediaRepository->getUploadTrends($months);
    }

    /**
     * Get media usage information
     */
    public function getMediaUsage($mediaId): array
    {
        $media = $this->mediaRepository->find($mediaId);
        
        if (!$media) {
            return [];
        }
        
        $usage = [];
        
        // Check if used in posts (featured image)
        $postsAsFeatured = DB::table('posts')
            ->where('featured_image', $media->path)
            ->pluck('title')
            ->toArray();
        
        if (!empty($postsAsFeatured)) {
            $usage[] = 'Posts (Featured Image): ' . implode(', ', $postsAsFeatured);
        }
        
        // Check if used in post content
        $postsInContent = DB::table('posts')
            ->where('content', 'LIKE', '%' . $media->path . '%')
            ->pluck('title')
            ->toArray();
        
        if (!empty($postsInContent)) {
            $usage[] = 'Posts (Content): ' . implode(', ', $postsInContent);
        }
        
        // Check if used in user avatars
        $usersAsAvatar = DB::table('users')
            ->where('avatar', $media->path)
            ->pluck('name')
            ->toArray();
        
        if (!empty($usersAsAvatar)) {
            $usage[] = 'User Avatars: ' . implode(', ', $usersAsAvatar);
        }
        
        // Check if used in site settings
        $settingsUsage = DB::table('site_settings')
            ->where('value', $media->path)
            ->pluck('key')
            ->toArray();
        
        if (!empty($settingsUsage)) {
            $usage[] = 'Site Settings: ' . implode(', ', $settingsUsage);
        }
        
        return $usage;
    }

    /**
     * Generate thumbnail for image
     */
    public function generateThumbnail($mediaId, $width = 150, $height = 150): ?string
    {
        $media = $this->mediaRepository->find($mediaId);
        
        if (!$media || !$this->isImage($media)) {
            return null;
        }
        
        try {
            return $this->fileUploadService->generateThumbnail($media->path, $width, $height);
        } catch (\Exception $e) {
            \Log::error('Failed to generate thumbnail: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Check if media is an image
     */
    public function isImage(Media $media): bool
    {
        $imageExtensions = ['jpg', 'jpeg', 'png', 'gif', 'bmp', 'webp', 'svg'];
        return in_array(strtolower($media->extension), $imageExtensions);
    }

    /**
     * Check if media is a video
     */
    public function isVideo(Media $media): bool
    {
        $videoExtensions = ['mp4', 'avi', 'mov', 'wmv', 'flv', 'webm', 'mkv'];
        return in_array(strtolower($media->extension), $videoExtensions);
    }

    /**
     * Check if media is audio
     */
    public function isAudio(Media $media): bool
    {
        $audioExtensions = ['mp3', 'wav', 'ogg', 'aac', 'flac', 'm4a'];
        return in_array(strtolower($media->extension), $audioExtensions);
    }

    /**
     * Check if media is a document
     */
    public function isDocument(Media $media): bool
    {
        $documentExtensions = ['pdf', 'doc', 'docx', 'xls', 'xlsx', 'ppt', 'pptx', 'txt', 'rtf'];
        return in_array(strtolower($media->extension), $documentExtensions);
    }

    /**
     * Get media type
     */
    public function getMediaType(Media $media): string
    {
        if ($this->isImage($media)) return 'image';
        if ($this->isVideo($media)) return 'video';
        if ($this->isAudio($media)) return 'audio';
        if ($this->isDocument($media)) return 'document';
        return 'other';
    }

    /**
     * Get media icon class
     */
    public function getMediaIconClass(Media $media): string
    {
        $type = $this->getMediaType($media);
        
        $icons = [
            'image' => 'fas fa-image',
            'video' => 'fas fa-video',
            'audio' => 'fas fa-music',
            'document' => 'fas fa-file-alt',
            'other' => 'fas fa-file'
        ];
        
        return $icons[$type] ?? $icons['other'];
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

    /**
     * Clear related caches
     */
    protected function clearRelatedCaches(): void
    {
        // Check if cache driver supports tagging
        if (method_exists(Cache::getStore(), 'tags')) {
            Cache::tags(['media', 'posts', 'admin'])->flush();
        } else {
            // Fallback for cache drivers that don't support tagging (like database)
            Cache::flush();
        }
    }

    /**
     * Optimize images
     */
    public function optimizeImages(array $ids = []): int
    {
        $query = $this->mediaRepository->getImages();
        
        if (!empty($ids)) {
            $query = $query->whereIn('id', $ids);
        }
        
        $images = $query->get();
        $optimized = 0;
        
        foreach ($images as $image) {
            try {
                if ($this->fileUploadService->optimizeImage($image->path)) {
                    $optimized++;
                }
            } catch (\Exception $e) {
                \Log::error('Failed to optimize image ' . $image->filename . ': ' . $e->getMessage());
            }
        }
        
        return $optimized;
    }

    /**
     * Duplicate media
     */
    public function duplicateMedia($id): Media
    {
        $originalMedia = $this->mediaRepository->find($id);
        
        if (!$originalMedia) {
            throw new \Exception('Media not found');
        }
        
        DB::beginTransaction();
        
        try {
            // Copy the physical file
            $newPath = $this->fileUploadService->copyFile($originalMedia->path);
            
            // Create new media record
            $newMedia = $this->mediaRepository->create([
                'filename' => 'copy_' . $originalMedia->filename,
                'original_name' => 'copy_' . $originalMedia->original_name,
                'path' => $newPath,
                'url' => Storage::url($newPath),
                'mime_type' => $originalMedia->mime_type,
                'extension' => $originalMedia->extension,
                'size' => $originalMedia->size,
                'title' => 'Copy of ' . $originalMedia->title,
                'alt_text' => $originalMedia->alt_text,
                'caption' => $originalMedia->caption,
                'description' => $originalMedia->description,
                'user_id' => $originalMedia->user_id
            ]);
            
            // Clear related caches
            $this->clearRelatedCaches();
            
            DB::commit();
            
            return $newMedia;
        } catch (\Exception $e) {
            DB::rollback();
            throw $e;
        }
    }

    /**
     * Export media list to CSV
     */
    public function exportToCSV(): string
    {
        $media = $this->mediaRepository->all();
        
        $csvData = "ID,Filename,Original Name,Type,Size,Upload Date,User,Title,Usage\n";
        
        foreach ($media as $item) {
            $usage = $this->getMediaUsage($item->id);
            $usageText = empty($usage) ? 'Unused' : implode('; ', $usage);
            
            $csvData .= sprintf(
                "%d,\"%s\",\"%s\",\"%s\",\"%s\",\"%s\",\"%s\",\"%s\",\"%s\"\n",
                $item->id,
                str_replace('"', '""', $item->filename),
                str_replace('"', '""', $item->original_name),
                $this->getMediaType($item),
                $this->formatBytes($item->size),
                $item->created_at->format('Y-m-d H:i:s'),
                $item->user ? str_replace('"', '""', $item->user->name) : 'Unknown',
                str_replace('"', '""', $item->title ?? ''),
                str_replace('"', '""', $usageText)
            );
        }
        
        return $csvData;
    }

    /**
     * Get media library for picker
     */
    public function getMediaLibrary(array $filters = [], $perPage = 20): array
    {
        $media = $this->getAllMedia($filters, $perPage);
        
        $items = $media->map(function ($item) {
            return [
                'id' => $item->id,
                'filename' => $item->filename,
                'original_name' => $item->original_name,
                'url' => $item->url,
                'path' => $item->path,
                'type' => $this->getMediaType($item),
                'size' => $this->formatBytes($item->size),
                'size_bytes' => $item->size,
                'extension' => $item->extension,
                'mime_type' => $item->mime_type,
                'title' => $item->title,
                'alt_text' => $item->alt_text,
                'caption' => $item->caption,
                'description' => $item->description,
                'created_at' => $item->created_at->format('Y-m-d H:i:s'),
                'icon_class' => $this->getMediaIconClass($item),
                'is_image' => $this->isImage($item),
                'thumbnail' => $this->isImage($item) ? $item->url : null
            ];
        });
        
        return [
            'items' => $items,
            'pagination' => [
                'current_page' => $media->currentPage(),
                'last_page' => $media->lastPage(),
                'per_page' => $media->perPage(),
                'total' => $media->total(),
                'from' => $media->firstItem(),
                'to' => $media->lastItem()
            ]
        ];
    }
}