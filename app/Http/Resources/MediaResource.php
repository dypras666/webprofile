<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class MediaResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'filename' => $this->filename,
            'original_filename' => $this->original_filename,
            'description' => $this->description,
            'alt_text' => $this->alt_text,
            'caption' => $this->caption,
            'type' => $this->type,
            'mime_type' => $this->mime_type,
            'extension' => $this->extension,
            'size' => $this->size,
            'size_formatted' => $this->formatBytes($this->size),
            'folder' => $this->folder,
            'path' => $this->path,
            'url' => $this->url,
            'thumbnail_url' => $this->thumbnail_url,
            'tags' => $this->tags,
            'metadata' => [
                'width' => $this->metadata['width'] ?? null,
                'height' => $this->metadata['height'] ?? null,
                'duration' => $this->metadata['duration'] ?? null,
                'bitrate' => $this->metadata['bitrate'] ?? null,
                'fps' => $this->metadata['fps'] ?? null,
                'quality' => $this->metadata['quality'] ?? null,
                'camera' => $this->metadata['camera'] ?? null,
                'location' => $this->metadata['location'] ?? null,
                'taken_at' => $this->metadata['taken_at'] ?? null,
                'color_palette' => $this->metadata['color_palette'] ?? null,
                'dominant_color' => $this->metadata['dominant_color'] ?? null,
            ],
            'settings' => [
                'is_public' => $this->is_public,
                'is_optimized' => $this->is_optimized ?? false,
                'has_thumbnail' => !empty($this->thumbnail_url),
                'is_image' => $this->isImage(),
                'is_video' => $this->isVideo(),
                'is_audio' => $this->isAudio(),
                'is_document' => $this->isDocument(),
            ],
            'owner' => new UserResource($this->whenLoaded('user')),
            'usage' => $this->when(
                $request->get('include_usage'),
                [
                    'posts' => PostResource::collection($this->whenLoaded('posts')),
                    'usage_count' => $this->whenLoaded('posts', function () {
                        return $this->posts->count();
                    }),
                    'is_used' => $this->whenLoaded('posts', function () {
                        return $this->posts->count() > 0;
                    }),
                ]
            ),
            'variants' => $this->when(
                $request->get('include_variants'),
                $this->getVariants()
            ),
            'exif' => $this->when(
                $request->get('include_exif') && $this->isImage(),
                $this->getExifData()
            ),
            'dates' => [
                'uploaded_at' => $this->created_at->toISOString(),
                'modified_at' => $this->updated_at->toISOString(),
                'taken_at' => $this->metadata['taken_at'] ?? null,
            ],
            'urls' => [
                'original' => $this->url,
                'thumbnail' => $this->thumbnail_url,
                'download' => route('media.download', $this->id),
                'edit' => $this->when(
                    $request->user()?->can('update', $this->resource),
                    route('admin.media.edit', $this)
                ),
            ],
            'stats' => [
                'downloads' => $this->downloads ?? 0,
                'views' => $this->views ?? 0,
                'shares' => $this->shares ?? 0,
            ],
            'sort_order' => $this->when(
                $request->user()?->can('update', $this->resource),
                $this->sort_order
            ),
            'permissions' => $this->when(
                $request->user(),
                [
                    'can_edit' => $request->user()?->can('update', $this->resource) ?? false,
                    'can_delete' => $request->user()?->can('delete', $this->resource) ?? false,
                    'can_download' => $request->user()?->can('download', $this->resource) ?? $this->is_public,
                    'can_use' => $request->user()?->can('use', $this->resource) ?? $this->is_public,
                ]
            ),
        ];
    }

    /**
     * Get additional data that should be returned with the resource array.
     *
     * @return array<string, mixed>
     */
    public function with(Request $request): array
    {
        return [
            'meta' => [
                'version' => '1.0',
                'generated_at' => now()->toISOString(),
            ],
        ];
    }

    /**
     * Customize the response for a request.
     */
    public function withResponse(Request $request, \Illuminate\Http\JsonResponse $response): void
    {
        $response->header('X-Resource-Type', 'Media');
        $response->header('X-Resource-ID', $this->id);
        
        // Add cache headers for public media
        if ($this->is_public) {
            $response->header('Cache-Control', 'public, max-age=86400');
            $response->header('ETag', md5($this->updated_at . $this->size));
        }
    }

    /**
     * Create a new resource instance for listing.
     */
    public static function collection($resource)
    {
        return parent::collection($resource)->additional([
            'meta' => [
                'total_count' => $resource instanceof \Illuminate\Pagination\LengthAwarePaginator 
                    ? $resource->total() 
                    : $resource->count(),
                'total_size' => $resource->sum('size'),
                'total_size_formatted' => static::formatBytes($resource->sum('size')),
                'generated_at' => now()->toISOString(),
            ],
        ]);
    }

    /**
     * Get a minimal version of the media resource for listings.
     */
    public function toMinimalArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'filename' => $this->filename,
            'type' => $this->type,
            'mime_type' => $this->mime_type,
            'size' => $this->size,
            'size_formatted' => $this->formatBytes($this->size),
            'url' => $this->url,
            'thumbnail_url' => $this->thumbnail_url,
            'is_public' => $this->is_public,
            'uploaded_at' => $this->created_at->format('M j, Y'),
        ];
    }

    /**
     * Get a gallery version of the media resource.
     */
    public function toGalleryArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'description' => $this->description,
            'alt_text' => $this->alt_text,
            'caption' => $this->caption,
            'type' => $this->type,
            'url' => $this->url,
            'thumbnail_url' => $this->thumbnail_url,
            'width' => $this->metadata['width'] ?? null,
            'height' => $this->metadata['height'] ?? null,
            'size_formatted' => $this->formatBytes($this->size),
            'is_image' => $this->isImage(),
            'is_video' => $this->isVideo(),
        ];
    }

    /**
     * Get a picker version of the media resource.
     */
    public function toPickerArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'filename' => $this->filename,
            'type' => $this->type,
            'mime_type' => $this->mime_type,
            'url' => $this->url,
            'thumbnail_url' => $this->thumbnail_url,
            'width' => $this->metadata['width'] ?? null,
            'height' => $this->metadata['height'] ?? null,
            'size' => $this->size,
            'size_formatted' => $this->formatBytes($this->size),
            'uploaded_at' => $this->created_at->format('M j, Y g:i A'),
            'folder' => $this->folder,
            'is_image' => $this->isImage(),
            'is_video' => $this->isVideo(),
            'is_audio' => $this->isAudio(),
            'is_document' => $this->isDocument(),
        ];
    }

    /**
     * Get an admin version of the media resource.
     */
    public function toAdminArray(Request $request): array
    {
        return array_merge($this->toArray($request), [
            'original_filename' => $this->original_filename,
            'path' => $this->path,
            'hash' => $this->hash,
            'disk' => $this->disk,
            'folder' => $this->folder,
            'admin_notes' => $this->when(
                $request->user()?->can('update', $this->resource),
                $this->admin_notes
            ),
            'upload_session' => $this->upload_session,
            'processing_status' => $this->processing_status,
            'optimization_data' => $this->when(
                $this->is_optimized,
                $this->optimization_data
            ),
            'security' => [
                'virus_scan_status' => $this->virus_scan_status ?? 'pending',
                'virus_scan_result' => $this->virus_scan_result,
                'content_hash' => $this->content_hash,
            ],
            'analytics' => $this->when(
                $request->get('include_analytics'),
                [
                    'downloads_this_month' => $this->getDownloadsThisMonth(),
                    'views_this_month' => $this->getViewsThisMonth(),
                    'usage_trend' => $this->getUsageTrend(),
                ]
            ),
        ]);
    }

    /**
     * Get a search result version of the media resource.
     */
    public function toSearchArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'filename' => $this->filename,
            'description' => $this->description,
            'type' => $this->type,
            'mime_type' => $this->mime_type,
            'size_formatted' => $this->formatBytes($this->size),
            'url' => $this->url,
            'thumbnail_url' => $this->thumbnail_url,
            'uploaded_at' => $this->created_at->format('M j, Y'),
            'owner' => $this->user?->name,
            'highlight' => $this->when(
                isset($this->search_highlight),
                $this->search_highlight
            ),
            'score' => $this->when(
                isset($this->search_score),
                $this->search_score
            ),
        ];
    }

    /**
     * Check if media is an image.
     */
    protected function isImage(): bool
    {
        return str_starts_with($this->mime_type, 'image/');
    }

    /**
     * Check if media is a video.
     */
    protected function isVideo(): bool
    {
        return str_starts_with($this->mime_type, 'video/');
    }

    /**
     * Check if media is audio.
     */
    protected function isAudio(): bool
    {
        return str_starts_with($this->mime_type, 'audio/');
    }

    /**
     * Check if media is a document.
     */
    protected function isDocument(): bool
    {
        $documentTypes = [
            'application/pdf',
            'application/msword',
            'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
            'application/vnd.ms-excel',
            'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'application/vnd.ms-powerpoint',
            'application/vnd.openxmlformats-officedocument.presentationml.presentation',
            'text/plain',
            'text/csv',
        ];
        
        return in_array($this->mime_type, $documentTypes);
    }

    /**
     * Format bytes to human readable format.
     */
    protected function formatBytes(int $bytes, int $precision = 2): string
    {
        return static::formatBytes($bytes, $precision);
    }
 

    /**
     * Get media variants (different sizes/formats).
     */
    protected function getVariants(): array
    {
        if (!$this->isImage()) {
            return [];
        }
        
        $variants = [];
        $sizes = ['thumbnail', 'small', 'medium', 'large'];
        
        foreach ($sizes as $size) {
            $url = $this->getVariantUrl($size);
            if ($url) {
                $variants[$size] = [
                    'url' => $url,
                    'width' => $this->getVariantWidth($size),
                    'height' => $this->getVariantHeight($size),
                ];
            }
        }
        
        return $variants;
    }

    /**
     * Get EXIF data for images.
     */
    protected function getExifData(): array
    {
        return $this->exif_data ?? [];
    }

    /**
     * Get variant URL for a specific size.
     */
    protected function getVariantUrl(string $size): ?string
    {
        // This would typically generate or retrieve variant URLs
        return $this->variants[$size]['url'] ?? null;
    }

    /**
     * Get variant width for a specific size.
     */
    protected function getVariantWidth(string $size): ?int
    {
        return $this->variants[$size]['width'] ?? null;
    }

    /**
     * Get variant height for a specific size.
     */
    protected function getVariantHeight(string $size): ?int
    {
        return $this->variants[$size]['height'] ?? null;
    }

    /**
     * Get downloads for this month.
     */
    protected function getDownloadsThisMonth(): int
    {
        return $this->downloads_this_month ?? 0;
    }

    /**
     * Get views for this month.
     */
    protected function getViewsThisMonth(): int
    {
        return $this->views_this_month ?? 0;
    }

    /**
     * Get usage trend data.
     */
    protected function getUsageTrend(): array
    {
        return $this->usage_trend ?? [];
    }
}