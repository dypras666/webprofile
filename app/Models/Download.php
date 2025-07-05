<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class Download extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'description',
        'file_name',
        'file_path',
        'file_type',
        'file_size',
        'is_public',
        'password',
        'category',
        'download_count',
        'is_active',
        'sort_order',
        'user_id',
    ];

    protected $casts = [
        'is_public' => 'boolean',
        'is_active' => 'boolean',
        'file_size' => 'integer',
        'download_count' => 'integer',
        'sort_order' => 'integer',
    ];

    protected $hidden = [
        'password',
    ];

    /**
     * Relationship with User
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Set password attribute (encrypt)
     */
    public function setPasswordAttribute($value)
    {
        if ($value) {
            $this->attributes['password'] = Hash::make($value);
        }
    }

    /**
     * Check if password is correct
     */
    public function checkPassword($password)
    {
        return Hash::check($password, $this->password);
    }

    /**
     * Get file URL
     */
    public function getFileUrlAttribute()
    {
        return Storage::url($this->file_path);
    }

    /**
     * Get formatted file size
     */
    public function getFormattedFileSizeAttribute()
    {
        $bytes = $this->file_size;
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];
        
        for ($i = 0; $bytes > 1024 && $i < count($units) - 1; $i++) {
            $bytes /= 1024;
        }
        
        return round($bytes, 2) . ' ' . $units[$i];
    }

    /**
     * Check if file is protected
     */
    public function getIsProtectedAttribute()
    {
        return !$this->is_public || !empty($this->password);
    }

    /**
     * Increment download count
     */
    public function incrementDownloadCount()
    {
        $this->increment('download_count');
    }

    /**
     * Scope for active downloads
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope for public downloads
     */
    public function scopePublic($query)
    {
        return $query->where('is_public', true);
    }

    /**
     * Scope for protected downloads
     */
    public function scopeProtected($query)
    {
        return $query->where('is_public', false)->orWhereNotNull('password');
    }

    /**
     * Scope by category
     */
    public function scopeByCategory($query, $category)
    {
        return $query->where('category', $category);
    }

    /**
     * Scope ordered
     */
    public function scopeOrdered($query)
    {
        return $query->orderBy('sort_order')->orderBy('created_at', 'desc');
    }

    /**
     * Get all categories
     */
    public static function getCategories()
    {
        return self::whereNotNull('category')
                   ->where('category', '!=', '')
                   ->distinct()
                   ->pluck('category')
                   ->sort()
                   ->values();
    }
}