<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;

class Media extends Model
{
    protected $fillable = [
        'name',
        'original_name',
        'file_path',
        'disk',
        'mime_type',
        'size',
        'extension',
        'type',
        'metadata',
        'alt_text',
        'description',
        'user_id'
    ];

    protected $casts = [
        'metadata' => 'array',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function getUrlAttribute()
    {
        if ($this->disk === 's3') {
            return Storage::disk('s3')->url($this->file_path);
        }
        
        return Storage::disk('public')->url($this->file_path);
    }

    public function getFullPathAttribute()
    {
        return Storage::disk($this->disk)->path($this->file_path);
    }

    public function getSizeForHumansAttribute()
    {
        $bytes = $this->size;
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];
        
        for ($i = 0; $bytes > 1024; $i++) {
            $bytes /= 1024;
        }
        
        return round($bytes, 2) . ' ' . $units[$i];
    }

    public function scopeImages($query)
    {
        return $query->where('type', 'image');
    }

    public function scopeVideos($query)
    {
        return $query->where('type', 'video');
    }

    public function scopeAudios($query)
    {
        return $query->where('type', 'audio');
    }

    public function scopeDocuments($query)
    {
        return $query->where('type', 'file');
    }

    public function scopeByType($query, $type)
    {
        return $query->where('type', $type);
    }
}
