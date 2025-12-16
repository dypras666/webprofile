<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TeamMember extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'position',
        'image',
        'social_links',
        'custom_fields',
        'order',
        'status',
    ];

    protected $casts = [
        'social_links' => 'array',
        'custom_fields' => 'array',
        'status' => 'boolean',
        'order' => 'integer',
    ];

    public function getImageUrlAttribute()
    {
        if (empty($this->image)) {
            return null;
        }

        if (is_numeric($this->image)) {
            $media = \App\Models\Media::find($this->image);
            return $media ? $media->url : null;
        }

        if (filter_var($this->image, FILTER_VALIDATE_URL)) {
            return $this->image;
        }

        return \Illuminate\Support\Facades\Storage::url($this->image);
    }
}
