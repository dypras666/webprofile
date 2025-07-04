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
    
    public function delete(Media $media)
    {
        // Delete file from storage
        if ($media->disk === 's3') {
            Storage::disk('s3')->delete($media->file_path);
        } else {
            Storage::disk('public')->delete($media->file_path);
        }
        
        // Delete media record
        $media->delete();
        
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
}
