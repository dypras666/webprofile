<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SettingResource extends JsonResource
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
            'key' => $this->key,
            'value' => $this->getFormattedValue(),
            'type' => $this->type,
            'category' => $this->category,
            'label' => $this->label,
            'description' => $this->description,
            'options' => $this->when(
                in_array($this->type, ['select', 'radio', 'checkbox']),
                $this->options
            ),
            'validation' => $this->when(
                $request->user()?->can('update', $this->resource),
                [
                    'rules' => $this->validation_rules,
                    'required' => $this->is_required,
                    'min' => $this->min_value,
                    'max' => $this->max_value,
                ]
            ),
            'settings' => [
                'is_public' => $this->is_public,
                'is_required' => $this->is_required,
                'is_encrypted' => $this->is_encrypted,
                'is_translatable' => $this->is_translatable,
                'is_cached' => $this->is_cached,
                'is_environment_specific' => $this->is_environment_specific,
            ],
            'ui' => [
                'input_type' => $this->type,
                'placeholder' => $this->placeholder,
                'help_text' => $this->help_text,
                'icon' => $this->icon,
                'group' => $this->group,
                'sort_order' => $this->sort_order,
                'is_visible' => $this->is_visible,
                'is_readonly' => $this->is_readonly,
            ],
            'metadata' => [
                'default_value' => $this->default_value,
                'environment' => $this->environment,
                'version' => $this->version,
                'tags' => $this->tags,
                'dependencies' => $this->dependencies,
            ],
            'history' => $this->when(
                $request->get('include_history') && $request->user()?->can('view-history', $this->resource),
                SettingHistoryResource::collection($this->whenLoaded('history'))
            ),
            'translations' => $this->when(
                $this->is_translatable && $request->get('include_translations'),
                $this->getTranslations()
            ),
            'dates' => [
                'created_at' => $this->created_at->toISOString(),
                'updated_at' => $this->updated_at->toISOString(),
                'last_modified_by' => $this->whenLoaded('lastModifiedBy', function () {
                    return [
                        'id' => $this->lastModifiedBy->id,
                        'name' => $this->lastModifiedBy->name,
                        'modified_at' => $this->updated_at->toISOString(),
                    ];
                }),
            ],
            'urls' => [
                'edit' => $this->when(
                    $request->user()?->can('update', $this->resource),
                    route('admin.settings.edit', $this->id)
                ),
                'history' => $this->when(
                    $request->user()?->can('view-history', $this->resource),
                    route('admin.settings.history', $this->id)
                ),
            ],
            'permissions' => $this->when(
                $request->user(),
                [
                    'can_edit' => $request->user()?->can('update', $this->resource) ?? false,
                    'can_delete' => $request->user()?->can('delete', $this->resource) ?? false,
                    'can_view_history' => $request->user()?->can('view-history', $this->resource) ?? false,
                    'can_export' => $request->user()?->can('export', $this->resource) ?? false,
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
                'cache_ttl' => $this->is_cached ? config('cache.ttl', 3600) : null,
            ],
        ];
    }

    /**
     * Customize the response for a request.
     */
    public function withResponse(Request $request, \Illuminate\Http\JsonResponse $response): void
    {
        $response->header('X-Resource-Type', 'Setting');
        $response->header('X-Resource-ID', $this->id);
        $response->header('X-Setting-Category', $this->category);
        
        // Add cache headers for cacheable settings
        if ($this->is_cached && $this->is_public) {
            $response->header('Cache-Control', 'public, max-age=3600');
            $response->header('ETag', md5($this->key . $this->updated_at));
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
                'categories' => $resource->pluck('category')->unique()->values(),
                'types' => $resource->pluck('type')->unique()->values(),
                'generated_at' => now()->toISOString(),
            ],
        ]);
    }

    /**
     * Get a minimal version of the setting resource for listings.
     */
    public function toMinimalArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'key' => $this->key,
            'value' => $this->getFormattedValue(),
            'type' => $this->type,
            'category' => $this->category,
            'label' => $this->label,
            'is_public' => $this->is_public,
            'updated_at' => $this->updated_at->format('M j, Y g:i A'),
        ];
    }

    /**
     * Get a public version of the setting resource.
     */
    public function toPublicArray(Request $request): array
    {
        if (!$this->is_public) {
            return [];
        }
        
        return [
            'key' => $this->key,
            'value' => $this->getFormattedValue(),
            'type' => $this->type,
            'category' => $this->category,
            'label' => $this->label,
            'description' => $this->description,
        ];
    }

    /**
     * Get a form version of the setting resource.
     */
    public function toFormArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'key' => $this->key,
            'value' => $this->getFormattedValue(),
            'type' => $this->type,
            'category' => $this->category,
            'label' => $this->label,
            'description' => $this->description,
            'placeholder' => $this->placeholder,
            'help_text' => $this->help_text,
            'options' => $this->when(
                in_array($this->type, ['select', 'radio', 'checkbox']),
                $this->options
            ),
            'validation' => [
                'required' => $this->is_required,
                'rules' => $this->validation_rules,
                'min' => $this->min_value,
                'max' => $this->max_value,
            ],
            'ui' => [
                'group' => $this->group,
                'sort_order' => $this->sort_order,
                'icon' => $this->icon,
                'is_readonly' => $this->is_readonly,
            ],
            'default_value' => $this->default_value,
        ];
    }

    /**
     * Get an admin version of the setting resource.
     */
    public function toAdminArray(Request $request): array
    {
        return array_merge($this->toArray($request), [
            'raw_value' => $this->when(
                $request->user()?->can('view-raw', $this->resource),
                $this->value
            ),
            'encrypted_value' => $this->when(
                $this->is_encrypted && $request->user()?->can('view-encrypted', $this->resource),
                $this->encrypted_value
            ),
            'system_info' => [
                'created_by' => $this->whenLoaded('createdBy', function () {
                    return [
                        'id' => $this->createdBy->id,
                        'name' => $this->createdBy->name,
                    ];
                }),
                'last_modified_by' => $this->whenLoaded('lastModifiedBy', function () {
                    return [
                        'id' => $this->lastModifiedBy->id,
                        'name' => $this->lastModifiedBy->name,
                    ];
                }),
                'modification_count' => $this->modification_count ?? 0,
                'last_backup_at' => $this->last_backup_at,
            ],
            'environment_values' => $this->when(
                $this->is_environment_specific && $request->get('include_environments'),
                $this->getEnvironmentValues()
            ),
            'analytics' => $this->when(
                $request->get('include_analytics'),
                [
                    'access_count' => $this->access_count ?? 0,
                    'last_accessed_at' => $this->last_accessed_at,
                    'modification_frequency' => $this->getModificationFrequency(),
                ]
            ),
        ]);
    }

    /**
     * Get a category group version of the setting resource.
     */
    public function toCategoryArray(Request $request): array
    {
        return [
            'category' => $this->category,
            'settings' => SettingResource::collection(
                $this->resource->where('category', $this->category)
            ),
            'meta' => [
                'count' => $this->resource->where('category', $this->category)->count(),
                'public_count' => $this->resource->where('category', $this->category)
                    ->where('is_public', true)->count(),
                'required_count' => $this->resource->where('category', $this->category)
                    ->where('is_required', true)->count(),
            ],
        ];
    }

    /**
     * Get formatted value based on type.
     */
    protected function getFormattedValue()
    {
        if ($this->is_encrypted && !request()->user()?->can('view-encrypted', $this->resource)) {
            return '***';
        }
        
        $value = $this->value;
        
        switch ($this->type) {
            case 'boolean':
                return (bool) $value;
            case 'integer':
                return (int) $value;
            case 'float':
                return (float) $value;
            case 'array':
            case 'json':
                return is_string($value) ? json_decode($value, true) : $value;
            case 'date':
                return $value ? \Carbon\Carbon::parse($value)->toISOString() : null;
            case 'datetime':
                return $value ? \Carbon\Carbon::parse($value)->toISOString() : null;
            case 'time':
                return $value ? \Carbon\Carbon::parse($value)->format('H:i:s') : null;
            case 'url':
                return $value ? filter_var($value, FILTER_VALIDATE_URL) ? $value : null : null;
            case 'email':
                return $value ? filter_var($value, FILTER_VALIDATE_EMAIL) ? $value : null : null;
            case 'color':
                return $value ? (str_starts_with($value, '#') ? $value : '#' . $value) : null;
            case 'file':
            case 'image':
                return $value ? asset('storage/' . $value) : null;
            default:
                return $value;
        }
    }

    /**
     * Get translations for translatable settings.
     */
    protected function getTranslations(): array
    {
        if (!$this->is_translatable) {
            return [];
        }
        
        return $this->translations ?? [];
    }

    /**
     * Get environment-specific values.
     */
    protected function getEnvironmentValues(): array
    {
        if (!$this->is_environment_specific) {
            return [];
        }
        
        return $this->environment_values ?? [];
    }

    /**
     * Get modification frequency data.
     */
    protected function getModificationFrequency(): array
    {
        return [
            'daily' => $this->modifications_last_day ?? 0,
            'weekly' => $this->modifications_last_week ?? 0,
            'monthly' => $this->modifications_last_month ?? 0,
            'yearly' => $this->modifications_last_year ?? 0,
        ];
    }
}

/**
 * Setting History Resource for tracking changes.
 */
class SettingHistoryResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'old_value' => $this->old_value,
            'new_value' => $this->new_value,
            'changed_by' => [
                'id' => $this->user->id,
                'name' => $this->user->name,
            ],
            'changed_at' => $this->created_at->toISOString(),
            'change_reason' => $this->change_reason,
            'ip_address' => $this->when(
                $request->user()?->can('view-ip', $this->resource),
                $this->ip_address
            ),
            'user_agent' => $this->when(
                $request->user()?->can('view-user-agent', $this->resource),
                $this->user_agent
            ),
        ];
    }
}