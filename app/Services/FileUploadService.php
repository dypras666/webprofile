<?php

namespace App\Services;

use App\Models\Media;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;

class FileUploadService
{
    protected $disk;
    protected $imageManager;

    public function __construct()
    {
        $this->disk = env('FILE_UPLOAD_DISK', 'local');
        $this->imageManager = new ImageManager(new Driver());
    }

    public function upload(UploadedFile $file, $folder = 'uploads', $userId = null)
    {
        $originalName = $file->getClientOriginalName();
        $extension = $file->getClientOriginalExtension();
        $mimeType = $file->getMimeType();
        $size = $file->getSize();

        // Generate unique filename
        $filename = Str::uuid() . '.' . $extension;
        $filePath = $folder . '/' . $filename;

        // Store file
        if ($this->disk === 's3') {
            $path = Storage::disk('s3')->putFileAs($folder, $file, $filename);
        } else {
            $path = Storage::disk('public')->putFileAs($folder, $file, $filename);
        }

        // Determine file type based on MIME type
        $type = $this->getFileType($mimeType);

        // Create media record
        $media = Media::create([
            'name' => $filename,
            'original_name' => $originalName,
            'file_path' => $path,
            'disk' => $this->disk,
            'mime_type' => $mimeType,
            'size' => $size,
            'extension' => $extension,
            'type' => $type,
            'user_id' => $userId ?? auth()->id(),
            'metadata' => $this->getFileMetadata($file)
        ]);

        return $media;
    }

    public function uploadImage(UploadedFile $file, $folder = 'images', $userId = null, $resize = null)
    {
        // Validate image
        if (!str_starts_with($file->getMimeType(), 'image/')) {
            throw new \InvalidArgumentException('File must be an image');
        }

        $media = $this->upload($file, $folder, $userId);

        // Resize image if specified
        if ($resize && is_array($resize)) {
            $this->resizeImage($media, $resize);
        }

        return $media;
    }

    public function delete($media)
    {
        // Handle both Media object and string path
        if ($media instanceof Media) {
            // Delete file from storage
            if ($media->disk === 's3') {
                Storage::disk('s3')->delete($media->file_path);
            } else {
                Storage::disk('public')->delete($media->file_path);
            }

            // Delete media record
            $media->delete();
        } elseif (is_string($media)) {
            // Delete file by path (legacy support)
            Storage::disk('public')->delete($media);
        }

        return true;
    }

    public function getFileMetadata(UploadedFile $file)
    {
        $metadata = [];

        // Get image dimensions if it's an image
        if (str_starts_with($file->getMimeType(), 'image/')) {
            try {
                $image = $this->imageManager->read($file->getPathname());
                $metadata['width'] = $image->width();
                $metadata['height'] = $image->height();
            } catch (\Exception $e) {
                // Ignore errors
            }
        }

        return $metadata;
    }

    protected function resizeImage(Media $media, array $dimensions)
    {
        try {
            $disk = $media->disk === 's3' ? 's3' : 'public';
            $content = Storage::disk($disk)->get($media->file_path);

            $image = $this->imageManager->read($content);

            if (isset($dimensions['width']) && isset($dimensions['height'])) {
                $image->resize($dimensions['width'], $dimensions['height']);
            } elseif (isset($dimensions['width'])) {
                $image->scale(width: $dimensions['width']);
            } elseif (isset($dimensions['height'])) {
                $image->scale(height: $dimensions['height']);
            }

            // Save resized image
            $resizedContent = $image->encode();
            Storage::disk($disk)->put($media->file_path, $resizedContent);

            // Update metadata
            $metadata = $media->metadata ?? [];
            $metadata['width'] = $image->width();
            $metadata['height'] = $image->height();
            $media->update(['metadata' => $metadata]);

        } catch (\Exception $e) {
            // Log error but don't fail
            Log::error('Failed to resize image: ' . $e->getMessage());
        }
    }

    public function getUrl(Media $media)
    {
        if ($media->disk === 's3') {
            return Storage::disk('s3')->url($media->file_path);
        }

        return Storage::disk('public')->url($media->file_path);
    }

    protected function getFileType($mimeType)
    {
        if (str_starts_with($mimeType, 'image/')) {
            return 'image';
        } elseif (str_starts_with($mimeType, 'video/')) {
            return 'video';
        } elseif (str_starts_with($mimeType, 'audio/')) {
            return 'audio';
        } else {
            return 'file';
        }
    }
    public function generateFavicons($sourcePath)
    {
        $disk = 'public'; // Force public disk for favicons
        $content = Storage::disk($disk)->get($sourcePath);
        $directory = dirname($sourcePath) . '/favicons';

        // Ensure directory exists
        if (!Storage::disk($disk)->exists($directory)) {
            Storage::disk($disk)->makeDirectory($directory);
        }

        $sizes = [
            ['width' => 16, 'height' => 16, 'name' => 'favicon-16x16.png'],
            ['width' => 32, 'height' => 32, 'name' => 'favicon-32x32.png'],
            ['width' => 180, 'height' => 180, 'name' => 'apple-touch-icon.png'],
            ['width' => 192, 'height' => 192, 'name' => 'android-chrome-192x192.png'],
            ['width' => 512, 'height' => 512, 'name' => 'android-chrome-512x512.png'],
        ];

        // Process standard PNGs
        foreach ($sizes as $size) {
            try {
                $image = $this->imageManager->read($content);
                $image->resize($size['width'], $size['height']);
                $encoded = $image->toPng();
                Storage::disk($disk)->put($directory . '/' . $size['name'], $encoded);
            } catch (\Exception $e) {
                Log::error("Failed to generate favicon {$size['name']}: " . $e->getMessage());
            }
        }

        // Generate favicon.ico (32x32)
        try {
            $image = $this->imageManager->read($content);
            $image->resize(32, 32);
            // Intervention Image v3 might not support direct .ico encoding easily without extensions, 
            // but standard practice often uses 32x32 png renamed or specifically processed. 
            // For simplicity and compatibility with v3 drivers, we often save as png but named ico or just use the 32png.
            // Let's stick to standard modern browser support which prefers PNG. 
            // However, legacy support usually wants a real .ico. 
            // If checking driver capabilities is complex, we'll try to save as ico if supported, else png.
            // Note: Intervention Image 3 usually requires specialized encoders or just rely on format detection.
            // Lets save a 32x32 png as favicon.ico for basic compatibility if the driver allows, 
            // effectively just a rename which works in many modern contexts, but ideally we'd use a dedicated ICO encoder.
            // Given the constraints, we will just use the 32x32 png.
            // UPDATE: Common practice for simple setups is just relying on the PNGs.
            // We'll skip complex .ico generation and rely on the PNGs we made, 
            // BUT we will allow 'favicon.ico' to be a copy of the 32x32 png for fallback.
            $encoded = $image->toPng();
            Storage::disk($disk)->put($directory . '/favicon.ico', $encoded);
        } catch (\Exception $e) {
            Log::error("Failed to generate favicon.ico: " . $e->getMessage());
        }

        // Generate site.webmanifest
        $manifest = [
            "name" => config('app.name'),
            "short_name" => config('app.name'),
            "icons" => [
                [
                    "src" => "/storage/" . $directory . "/android-chrome-192x192.png",
                    "sizes" => "192x192",
                    "type" => "image/png"
                ],
                [
                    "src" => "/storage/" . $directory . "/android-chrome-512x512.png",
                    "sizes" => "512x512",
                    "type" => "image/png"
                ]
            ],
            "theme_color" => "#ffffff",
            "background_color" => "#ffffff",
            "display" => "standalone"
        ];

        Storage::disk($disk)->put($directory . '/site.webmanifest', json_encode($manifest, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));

        return $directory;
    }
}
