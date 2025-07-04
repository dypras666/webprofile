<?php

namespace App\Repositories;

use App\Models\Media;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class MediaRepository extends BaseRepository
{
    public function __construct(Media $model)
    {
        parent::__construct($model);
    }

    /**
     * Get media by type
     */
    public function getByType($type, $limit = null): Collection
    {
        $cacheKey = $this->getCacheKey('by_type', [$type, $limit]);
        
        return Cache::remember($cacheKey, $this->cacheTime, function () use ($type, $limit) {
            $query = $this->model->where('type', $type)
                ->with('user')
                ->latest();
            
            if ($limit) {
                $query->limit($limit);
            }
            
            return $query->get();
        });
    }

    /**
     * Get images
     */
    public function getImages($limit = null): Collection
    {
        return $this->getByType('image', $limit);
    }

    /**
     * Get videos
     */
    public function getVideos($limit = null): Collection
    {
        return $this->getByType('video', $limit);
    }

    /**
     * Get documents
     */
    public function getDocuments($limit = null): Collection
    {
        return $this->getByType('document', $limit);
    }

    /**
     * Get audio files
     */
    public function getAudio($limit = null): Collection
    {
        return $this->getByType('audio', $limit);
    }

    /**
     * Get media by user
     */
    public function getByUser($userId, $limit = null): Collection
    {
        $cacheKey = $this->getCacheKey('by_user', [$userId, $limit]);
        
        return Cache::remember($cacheKey, $this->cacheTime, function () use ($userId, $limit) {
            $query = $this->model->where('user_id', $userId)
                ->with('user')
                ->latest();
            
            if ($limit) {
                $query->limit($limit);
            }
            
            return $query->get();
        });
    }

    /**
     * Get recent media
     */
    public function getRecent($limit = 20): Collection
    {
        $cacheKey = $this->getCacheKey('recent', [$limit]);
        
        return Cache::remember($cacheKey, $this->cacheTime, function () use ($limit) {
            return $this->model->with('user')
                ->latest()
                ->limit($limit)
                ->get();
        });
    }

    /**
     * Get media with pagination and filters
     */
    public function getPaginatedWithFilters(array $filters = [], $perPage = 20): LengthAwarePaginator
    {
        $query = $this->model->with('user');
        
        // Apply search
        if (!empty($filters['search'])) {
            $search = $filters['search'];
            $query->where(function ($q) use ($search) {
                $q->where('original_name', 'LIKE', "%{$search}%")
                  ->orWhere('alt_text', 'LIKE', "%{$search}%")
                  ->orWhere('caption', 'LIKE', "%{$search}%");
            });
        }
        
        // Filter by type
        if (!empty($filters['type'])) {
            $query->where('type', $filters['type']);
        }
        
        // Filter by user
        if (!empty($filters['user_id'])) {
            $query->where('user_id', $filters['user_id']);
        }
        
        // Filter by file extension
        if (!empty($filters['extension'])) {
            $query->where('extension', $filters['extension']);
        }
        
        // Filter by size range
        if (!empty($filters['size_min'])) {
            $query->where('size', '>=', $filters['size_min']);
        }
        
        if (!empty($filters['size_max'])) {
            $query->where('size', '<=', $filters['size_max']);
        }
        
        // Date range filter
        if (!empty($filters['date_from'])) {
            $query->whereDate('created_at', '>=', $filters['date_from']);
        }
        
        if (!empty($filters['date_to'])) {
            $query->whereDate('created_at', '<=', $filters['date_to']);
        }
        
        // Sorting
        $sortBy = $filters['sort_by'] ?? 'created_at';
        $sortOrder = $filters['sort_order'] ?? 'desc';
        $query->orderBy($sortBy, $sortOrder);
        
        return $query->paginate($perPage);
    }

    /**
     * Search media
     */
    public function search($query, $fields = [], $limit = null): Collection
    {
        $modelQuery = $this->model->newQuery();
        
        // If specific fields are provided, search in those fields
        if (!empty($fields)) {
            $modelQuery->where(function ($q) use ($query, $fields) {
                foreach ($fields as $field) {
                    $q->orWhere($field, 'LIKE', "%{$query}%");
                }
            });
        } else {
            // Default search in original_name, alt_text, and caption
            $modelQuery->where(function ($q) use ($query) {
                $q->where('original_name', 'LIKE', "%{$query}%")
                  ->orWhere('alt_text', 'LIKE', "%{$query}%")
                  ->orWhere('caption', 'LIKE', "%{$query}%");
            });
        }
        
        // Apply limit if provided
        if ($limit) {
            $modelQuery->limit($limit);
        }
        
        return $modelQuery->with('user')
            ->latest()
            ->get();
    }

    /**
     * Get media by filename
     */
    public function findByFilename($filename): ?Media
    {
        $cacheKey = $this->getCacheKey('by_filename', [$filename]);
        
        return Cache::remember($cacheKey, $this->cacheTime, function () use ($filename) {
            return $this->model->where('filename', $filename)
                ->with('user')
                ->first();
        });
    }

    /**
     * Get media by path
     */
    public function findByPath($path): ?Media
    {
        $cacheKey = $this->getCacheKey('by_path', [$path]);
        
        return Cache::remember($cacheKey, $this->cacheTime, function () use ($path) {
            return $this->model->where('path', $path)
                ->with('user')
                ->first();
        });
    }

    /**
     * Get unused media (not referenced in posts)
     */
    public function getUnused($limit = null): Collection
    {
        $cacheKey = $this->getCacheKey('unused', [$limit]);
        
        return Cache::remember($cacheKey, $this->cacheTime, function () use ($limit) {
            $query = $this->model->whereDoesntHave('posts')
                ->with('user')
                ->latest();
            
            if ($limit) {
                $query->limit($limit);
            }
            
            return $query->get();
        });
    }

    /**
     * Get large files (above certain size)
     */
    public function getLargeFiles($sizeInBytes = 5242880, $limit = null): Collection // Default 5MB
    {
        $cacheKey = $this->getCacheKey('large_files', [$sizeInBytes, $limit]);
        
        return Cache::remember($cacheKey, $this->cacheTime, function () use ($sizeInBytes, $limit) {
            $query = $this->model->where('size', '>', $sizeInBytes)
                ->with('user')
                ->orderBy('size', 'desc');
            
            if ($limit) {
                $query->limit($limit);
            }
            
            return $query->get();
        });
    }

    /**
     * Get media count by type
     */
    public function getCountByType(): array
    {
        $cacheKey = $this->getCacheKey('count_by_type');
        
        return Cache::remember($cacheKey, $this->cacheTime, function () {
            return $this->model->select('type', DB::raw('count(*) as count'))
                ->groupBy('type')
                ->pluck('count', 'type')
                ->toArray();
        });
    }

    /**
     * Get media count by extension
     */
    public function getCountByExtension(): array
    {
        $cacheKey = $this->getCacheKey('count_by_extension');
        
        return Cache::remember($cacheKey, $this->cacheTime, function () {
            return $this->model->select('extension', DB::raw('count(*) as count'))
                ->groupBy('extension')
                ->orderBy('count', 'desc')
                ->pluck('count', 'extension')
                ->toArray();
        });
    }

    /**
     * Get total storage used
     */
    public function getTotalStorageUsed(): int
    {
        $cacheKey = $this->getCacheKey('total_storage');
        
        return Cache::remember($cacheKey, $this->cacheTime, function () {
            return $this->model->sum('size');
        });
    }

    /**
     * Get storage used by type
     */
    public function getStorageByType(): array
    {
        $cacheKey = $this->getCacheKey('storage_by_type');
        
        return Cache::remember($cacheKey, $this->cacheTime, function () {
            return $this->model->select('type', DB::raw('sum(size) as total_size'))
                ->groupBy('type')
                ->pluck('total_size', 'type')
                ->toArray();
        });
    }

    /**
     * Get storage used by user
     */
    public function getStorageByUser(): array
    {
        $cacheKey = $this->getCacheKey('storage_by_user');
        
        return Cache::remember($cacheKey, $this->cacheTime, function () {
            return $this->model->select('user_id', DB::raw('sum(size) as total_size'))
                ->with('user:id,name')
                ->groupBy('user_id')
                ->orderBy('total_size', 'desc')
                ->get()
                ->mapWithKeys(function ($item) {
                    return [$item->user->name ?? 'Unknown' => $item->total_size];
                })
                ->toArray();
        });
    }

    /**
     * Delete media with file
     */
    public function deleteWithFile($id): bool
    {
        DB::beginTransaction();
        
        try {
            $media = $this->find($id);
            
            if (!$media) {
                return false;
            }
            
            // Delete file from storage
            if (Storage::disk($media->disk)->exists($media->path)) {
                Storage::disk($media->disk)->delete($media->path);
            }
            
            // Delete thumbnails if they exist
            if ($media->thumbnails) {
                foreach ($media->thumbnails as $thumbnail) {
                    if (Storage::disk($media->disk)->exists($thumbnail)) {
                        Storage::disk($media->disk)->delete($thumbnail);
                    }
                }
            }
            
            // Delete database record
            $deleted = $this->delete($id);
            
            $this->clearCache();
            
            DB::commit();
            
            return $deleted;
        } catch (\Exception $e) {
            DB::rollback();
            throw $e;
        }
    }

    /**
     * Bulk delete media with files
     */
    public function bulkDeleteWithFiles(array $ids): int
    {
        DB::beginTransaction();
        
        try {
            $mediaItems = $this->model->whereIn('id', $ids)->get();
            $deletedCount = 0;
            
            foreach ($mediaItems as $media) {
                // Delete file from storage
                if (Storage::disk($media->disk)->exists($media->path)) {
                    Storage::disk($media->disk)->delete($media->path);
                }
                
                // Delete thumbnails if they exist
                if ($media->thumbnails) {
                    foreach ($media->thumbnails as $thumbnail) {
                        if (Storage::disk($media->disk)->exists($thumbnail)) {
                            Storage::disk($media->disk)->delete($thumbnail);
                        }
                    }
                }
                
                $deletedCount++;
            }
            
            // Delete database records
            $this->model->whereIn('id', $ids)->delete();
            
            $this->clearCache();
            
            DB::commit();
            
            return $deletedCount;
        } catch (\Exception $e) {
            DB::rollback();
            throw $e;
        }
    }

    /**
     * Update media metadata
     */
    public function updateMetadata($id, array $metadata): bool
    {
        DB::beginTransaction();
        
        try {
            $updated = $this->model->where('id', $id)
                ->update([
                    'alt_text' => $metadata['alt_text'] ?? null,
                    'caption' => $metadata['caption'] ?? null,
                    'description' => $metadata['description'] ?? null,
                ]);
            
            $this->clearCache();
            
            DB::commit();
            
            return $updated > 0;
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
        $cacheKey = $this->getCacheKey('statistics');
        
        return Cache::remember($cacheKey, $this->cacheTime, function () {
            $base = parent::getStatistics();
            
            return array_merge($base, [
                'by_type' => $this->getCountByType(),
                'by_extension' => $this->getCountByExtension(),
                'total_storage' => $this->getTotalStorageUsed(),
                'storage_by_type' => $this->getStorageByType(),
                'avg_file_size' => round($this->model->avg('size'), 2),
                'largest_file' => $this->model->orderBy('size', 'desc')
                    ->first(['original_name', 'size', 'type']),
                'unused_count' => $this->model->whereDoesntHave('posts')->count(),
                'recent_uploads' => $this->model->where('created_at', '>=', now()->subDays(7))->count(),
                'images_count' => $this->model->where('type', 'image')->count(),
                'videos_count' => $this->model->where('type', 'video')->count(),
                'documents_count' => $this->model->where('type', 'document')->count(),
                'audio_count' => $this->model->where('type', 'audio')->count(),
            ]);
        });
    }

    /**
     * Clean up unused media files
     */
    public function cleanupUnused($daysOld = 30): int
    {
        DB::beginTransaction();
        
        try {
            $unusedMedia = $this->model->whereDoesntHave('posts')
                ->where('created_at', '<', now()->subDays($daysOld))
                ->get();
            
            $deletedCount = 0;
            
            foreach ($unusedMedia as $media) {
                if ($this->deleteWithFile($media->id)) {
                    $deletedCount++;
                }
            }
            
            DB::commit();
            
            return $deletedCount;
        } catch (\Exception $e) {
            DB::rollback();
            throw $e;
        }
    }

    /**
     * Get media upload trends
     */
    public function getUploadTrends($days = 30): array
    {
        $cacheKey = $this->getCacheKey('upload_trends', [$days]);
        
        return Cache::remember($cacheKey, $this->cacheTime, function () use ($days) {
            return $this->model->selectRaw('DATE(created_at) as date, COUNT(*) as count')
                ->where('created_at', '>=', now()->subDays($days))
                ->groupBy('date')
                ->orderBy('date')
                ->pluck('count', 'date')
                ->toArray();
        });
    }
}