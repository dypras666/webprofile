<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class Post extends Model
{
    protected $fillable = [
        'title',
        'slug',
        'content',
        'excerpt',
        'type',
        'featured_image',
        'featured_image_id',
        'video_url',
        'gallery_images',
        'is_slider',
        'is_featured',
        'is_published',
        'published_at',
        'meta_title',
        'meta_description',
        'meta_keywords',
        'category_id',
        'user_id',
        'views',
        'sort_order'
    ];

    protected $casts = [
        'gallery_images' => 'array',
        'is_slider' => 'boolean',
        'is_featured' => 'boolean',
        'is_published' => 'boolean',
        'published_at' => 'datetime',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($post) {
            if (empty($post->slug)) {
                $post->slug = Str::slug($post->title);
            }
        });

        static::updating(function ($post) {
            if ($post->isDirty('title') && empty($post->slug)) {
                $post->slug = Str::slug($post->title);
            }
        });
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function comments()
    {
        return $this->hasMany(Comment::class);
    }

    public function featuredImage(): BelongsTo
    {
        return $this->belongsTo(Media::class, 'featured_image_id');
    }

    public function scopePublished($query)
    {
        return $query->where('is_published', true)
            ->where('published_at', '<=', now());
    }

    public function scopeSlider($query)
    {
        return $query->where('is_slider', true);
    }

    public function scopeFeatured($query)
    {
        return $query->where('is_featured', true);
    }

    public function scopeByType($query, $type)
    {
        return $query->where('type', $type);
    }

    public function incrementViews()
    {
        $this->increment('views');
    }

    public function getFeaturedImageUrlAttribute()
    {
        // Load the relationship if not already loaded
        if (!$this->relationLoaded('featuredImage')) {
            $this->load('featuredImage');
        }

        // SISTEM BARU: featured_image_id + Media model (PRIORITAS UTAMA)
        if ($this->featuredImage) {
            return $this->featuredImage->url;
        }

        // SISTEM LAMA: featured_image field
        if ($this->featured_image) {
            // Jika berisi angka/ID (seperti "5", "2"), ambil dari Media model
            if (is_numeric($this->featured_image)) {
                $media = \App\Models\Media::find($this->featured_image);
                return $media ? $media->url : null;
            }

            // Jika berisi path file (seperti "posts/image.jpg"), gunakan sebagai path
            return asset('storage/' . $this->featured_image);
        }

        return null;
    }


    public function getFeaturedImageAltTextAttribute()
    {
        if (!$this->relationLoaded('featuredImage')) {
            $this->load('featuredImage');
        }

        if ($this->featuredImage) {
            return $this->featuredImage->alt_text;
        }
        return null;
    }

    // Removed getFeaturedImageIdAttribute() accessor to avoid conflicts with the database column
    // The featured_image_id column can be accessed directly as $post->featured_image_id
    /**
     * Get the resolved featured image ID.
     * Uses featured_image_id if available, otherwise attempts to find the Media ID
     * from the featured_image path string.
     */
    public function getResolvedFeaturedImageIdAttribute()
    {
        // 1. Use the explicit foreign key if available
        if ($this->featured_image_id) {
            return $this->featured_image_id;
        }

        // 2. Fallback: Lookup by path if featured_image is set (legacy string path)
        if ($this->featured_image) {
            // If it's already a numeric string, return it as ID
            if (is_numeric($this->featured_image)) {
                return (int) $this->featured_image;
            }

            // It's a path string. Look it up in Media table.
            // Note: featured_image usually stores relative path "posts/featured/xyz.jpg" or "uploads/xyz.jpg"
            // Media table stores in 'file_path' or 'path' columns depending on version
            // Based on Media model, it uses 'file_path'
            $media = \App\Models\Media::where('file_path', $this->featured_image)
                ->orWhere('file_path', 'like', '%' . $this->featured_image)
                ->first();

            if ($media) {
                return $media->id;
            }
        }

        return null;
    }
}
