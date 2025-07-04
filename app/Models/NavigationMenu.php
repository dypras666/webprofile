<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class NavigationMenu extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'url',
        'type',
        'reference_id',
        'parent_id',
        'sort_order',
        'is_active',
        'target',
        'css_class',
        'icon'
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'sort_order' => 'integer',
    ];

    /**
     * Get the parent menu item
     */
    public function parent(): BelongsTo
    {
        return $this->belongsTo(NavigationMenu::class, 'parent_id');
    }

    /**
     * Get the child menu items
     */
    public function children(): HasMany
    {
        return $this->hasMany(NavigationMenu::class, 'parent_id')
                    ->orderBy('sort_order');
    }

    /**
     * Get the referenced post/page
     */
    public function referencedPost(): BelongsTo
    {
        return $this->belongsTo(Post::class, 'reference_id');
    }

    /**
     * Get the referenced category
     */
    public function referencedCategory(): BelongsTo
    {
        return $this->belongsTo(Category::class, 'reference_id');
    }

    /**
     * Scope to get only root menu items
     */
    public function scopeRoots($query)
    {
        return $query->whereNull('parent_id')->orderBy('sort_order');
    }

    /**
     * Scope to get only active menu items
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Get the final URL for this menu item
     */
    public function getFinalUrlAttribute(): string
    {
        if ($this->type === 'custom') {
            return $this->url ?? '#';
        }

        if ($this->type === 'post' && $this->referencedPost) {
            return route('frontend.post', $this->referencedPost->slug);
        }

        if ($this->type === 'page' && $this->referencedPost) {
            return route('frontend.post', $this->referencedPost->slug);
        }

        if ($this->type === 'category' && $this->referencedCategory) {
            return route('frontend.category', $this->referencedCategory->slug);
        }

        return $this->url ?? '#';
    }

    /**
     * Get hierarchical menu structure
     */
    public static function getMenuTree()
    {
        return self::with(['children' => function ($query) {
            $query->active()->orderBy('sort_order');
        }])
        ->active()
        ->roots()
        ->get();
    }

    /**
     * Update sort orders for menu items
     */
    public static function updateSortOrder(array $items, $parentId = null)
    {
        foreach ($items as $index => $item) {
            $menu = self::find($item['id']);
            if ($menu) {
                $menu->update([
                    'sort_order' => $index + 1,
                    'parent_id' => $parentId
                ]);

                if (isset($item['children']) && is_array($item['children'])) {
                    self::updateSortOrder($item['children'], $item['id']);
                }
            }
        }
    }
}