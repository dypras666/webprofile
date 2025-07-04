<?php

namespace App\Services;

use App\Models\SiteSetting;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Str;
use Carbon\Carbon;

class SettingService
{
    /**
     * Get all settings grouped by category.
     */
    public function getAllSettings(bool $publicOnly = false): array
    {
        $cacheKey = $publicOnly ? 'settings_public' : 'settings_all';
        
        return Cache::remember($cacheKey, 3600, function () use ($publicOnly) {
            $query = SiteSetting::query();
            
            if ($publicOnly) {
                $query->where('is_public', true);
            }
            
            $settings = $query->orderBy('category')
                ->orderBy('sort_order')
                ->orderBy('key')
                ->get();
            
            return $settings->groupBy('category')->map(function ($categorySettings) {
                return $categorySettings->keyBy('key')->map(function ($setting) {
                    return $this->formatSettingValue($setting);
                });
            })->toArray();
        });
    }

    /**
     * Get settings by category.
     */
    public function getSettingsByCategory(string $category, bool $publicOnly = false): array
    {
        $cacheKey = "settings_category_{$category}" . ($publicOnly ? '_public' : '');
        
        return Cache::remember($cacheKey, 3600, function () use ($category, $publicOnly) {
            $query = SiteSetting::where('category', $category);
            
            if ($publicOnly) {
                $query->where('is_public', true);
            }
            
            return $query->orderBy('sort_order')
                ->orderBy('key')
                ->get()
                ->keyBy('key')
                ->map(function ($setting) {
                    return $this->formatSettingValue($setting);
                })
                ->toArray();
        });
    }

    /**
     * Get a single setting value.
     */
    public function getSetting(string $key, $default = null)
    {
        $cacheKey = "setting_{$key}";
        
        return Cache::remember($cacheKey, 3600, function () use ($key, $default) {
            $setting = SiteSetting::where('key', $key)->first();
            
            if (!$setting) {
                return $default;
            }
            
            return $this->formatSettingValue($setting);
        });
    }

    /**
     * Set a single setting value.
     */
    public function setSetting(string $key, $value, array $options = []): SiteSetting
    {
        DB::beginTransaction();
        
        try {
            $setting = SiteSetting::where('key', $key)->first();
            
            if ($setting) {
                // Store history if enabled
                if ($setting->track_history) {
                    $this->storeSettingHistory($setting, $setting->value, $value);
                }
                
                $setting->update([
                    'value' => $this->prepareValueForStorage($value, $setting->type),
                    'updated_at' => now(),
                ]);
            } else {
                $setting = SiteSetting::create(array_merge([
                    'key' => $key,
                    'value' => $this->prepareValueForStorage($value, $options['type'] ?? 'string'),
                    'type' => $options['type'] ?? 'string',
                    'category' => $options['category'] ?? 'general',
                    'label' => $options['label'] ?? Str::title(str_replace('_', ' ', $key)),
                    'description' => $options['description'] ?? null,
                    'is_public' => $options['is_public'] ?? false,
                    'is_required' => $options['is_required'] ?? false,
                    'sort_order' => $options['sort_order'] ?? 0,
                ], $options));
            }
            
            // Clear relevant caches
            $this->clearSettingCaches($setting);
            
            // Clear application cache if needed
            if ($setting->clear_cache_on_change) {
                $this->clearApplicationCache();
            }
            
            DB::commit();
            
            return $setting;
            
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * Update multiple settings.
     */
    public function updateSettings(array $settings): array
    {
        DB::beginTransaction();
        
        try {
            $updatedSettings = [];
            
            foreach ($settings as $key => $value) {
                $updatedSettings[$key] = $this->setSetting($key, $value);
            }
            
            DB::commit();
            
            return $updatedSettings;
            
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * Delete a setting.
     */
    public function deleteSetting(string $key): bool
    {
        DB::beginTransaction();
        
        try {
            $setting = SiteSetting::where('key', $key)->first();
            
            if (!$setting) {
                return false;
            }
            
            // Check if setting is required
            if ($setting->is_required) {
                throw new \InvalidArgumentException('Cannot delete required setting');
            }
            
            $deleted = $setting->delete();
            
            // Clear relevant caches
            $this->clearSettingCaches($setting);
            
            DB::commit();
            
            return $deleted;
            
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * Get setting categories.
     */
    public function getCategories(): array
    {
        return Cache::remember('setting_categories', 3600, function () {
            return SiteSetting::select('category')
                ->selectRaw('COUNT(*) as count')
                ->selectRaw('MIN(sort_order) as min_sort_order')
                ->groupBy('category')
                ->orderBy('min_sort_order')
                ->orderBy('category')
                ->get()
                ->map(function ($item) {
                    return [
                        'name' => $item->category,
                        'label' => Str::title(str_replace('_', ' ', $item->category)),
                        'count' => $item->count,
                    ];
                })
                ->toArray();
        });
    }

    /**
     * Get settings for form display.
     */
    public function getSettingsForForm(string $category = null): array
    {
        $query = SiteSetting::query();
        
        if ($category) {
            $query->where('category', $category);
        }
        
        return $query->orderBy('category')
            ->orderBy('sort_order')
            ->orderBy('key')
            ->get()
            ->map(function ($setting) {
                return [
                    'key' => $setting->key,
                    'value' => $this->formatSettingValue($setting),
                    'type' => $setting->type,
                    'category' => $setting->category,
                    'label' => $setting->label,
                    'description' => $setting->description,
                    'options' => $setting->options,
                    'validation_rules' => $setting->validation_rules,
                    'is_required' => $setting->is_required,
                    'is_public' => $setting->is_public,
                    'input_type' => $setting->input_type,
                    'placeholder' => $setting->placeholder,
                    'help_text' => $setting->help_text,
                    'icon' => $setting->icon,
                    'group' => $setting->group,
                    'sort_order' => $setting->sort_order,
                    'is_visible' => $setting->is_visible,
                    'is_readonly' => $setting->is_readonly,
                ];
            })
            ->groupBy('category')
            ->toArray();
    }

    /**
     * Validate setting value.
     */
    public function validateSetting(string $key, $value): array
    {
        $setting = SiteSetting::where('key', $key)->first();
        
        if (!$setting) {
            return ['valid' => false, 'errors' => ['Setting not found']];
        }
        
        $errors = [];
        
        // Check required
        if ($setting->is_required && ($value === null || $value === '')) {
            $errors[] = 'This setting is required';
        }
        
        // Type validation
        if ($value !== null && $value !== '') {
            switch ($setting->type) {
                case 'integer':
                    if (!is_numeric($value) || (int)$value != $value) {
                        $errors[] = 'Value must be an integer';
                    }
                    break;
                    
                case 'float':
                    if (!is_numeric($value)) {
                        $errors[] = 'Value must be a number';
                    }
                    break;
                    
                case 'boolean':
                    if (!in_array($value, [true, false, 1, 0, '1', '0', 'true', 'false'])) {
                        $errors[] = 'Value must be true or false';
                    }
                    break;
                    
                case 'email':
                    if (!filter_var($value, FILTER_VALIDATE_EMAIL)) {
                        $errors[] = 'Value must be a valid email address';
                    }
                    break;
                    
                case 'url':
                    if (!filter_var($value, FILTER_VALIDATE_URL)) {
                        $errors[] = 'Value must be a valid URL';
                    }
                    break;
                    
                case 'json':
                    if (is_string($value)) {
                        json_decode($value);
                        if (json_last_error() !== JSON_ERROR_NONE) {
                            $errors[] = 'Value must be valid JSON';
                        }
                    }
                    break;
            }
        }
        
        // Options validation
        if ($setting->options && is_array($setting->options) && $value !== null) {
            $validOptions = array_keys($setting->options);
            if (!in_array($value, $validOptions)) {
                $errors[] = 'Value must be one of: ' . implode(', ', $validOptions);
            }
        }
        
        // Custom validation rules
        if ($setting->validation_rules) {
            $rules = is_string($setting->validation_rules) 
                ? explode('|', $setting->validation_rules)
                : $setting->validation_rules;
                
            foreach ($rules as $rule) {
                if (str_starts_with($rule, 'min:')) {
                    $min = (int)substr($rule, 4);
                    if (is_string($value) && strlen($value) < $min) {
                        $errors[] = "Value must be at least {$min} characters";
                    } elseif (is_numeric($value) && $value < $min) {
                        $errors[] = "Value must be at least {$min}";
                    }
                }
                
                if (str_starts_with($rule, 'max:')) {
                    $max = (int)substr($rule, 4);
                    if (is_string($value) && strlen($value) > $max) {
                        $errors[] = "Value must not exceed {$max} characters";
                    } elseif (is_numeric($value) && $value > $max) {
                        $errors[] = "Value must not exceed {$max}";
                    }
                }
            }
        }
        
        return [
            'valid' => empty($errors),
            'errors' => $errors
        ];
    }

    /**
     * Export settings.
     */
    public function exportSettings(array $categories = [], bool $includePrivate = false): array
    {
        $query = SiteSetting::query();
        
        if (!empty($categories)) {
            $query->whereIn('category', $categories);
        }
        
        if (!$includePrivate) {
            $query->where('is_public', true);
        }
        
        return $query->get()->map(function ($setting) {
            return [
                'key' => $setting->key,
                'value' => $setting->value,
                'type' => $setting->type,
                'category' => $setting->category,
                'label' => $setting->label,
                'description' => $setting->description,
                'options' => $setting->options,
                'validation_rules' => $setting->validation_rules,
                'is_public' => $setting->is_public,
                'is_required' => $setting->is_required,
                'sort_order' => $setting->sort_order,
            ];
        })->toArray();
    }

    /**
     * Import settings.
     */
    public function importSettings(array $settings, bool $overwrite = false): array
    {
        DB::beginTransaction();
        
        try {
            $imported = [];
            $skipped = [];
            $errors = [];
            
            foreach ($settings as $settingData) {
                try {
                    $key = $settingData['key'];
                    $existingSetting = SiteSetting::where('key', $key)->first();
                    
                    if ($existingSetting && !$overwrite) {
                        $skipped[] = $key;
                        continue;
                    }
                    
                    // Validate the setting
                    $validation = $this->validateSetting($key, $settingData['value']);
                    if (!$validation['valid']) {
                        $errors[$key] = $validation['errors'];
                        continue;
                    }
                    
                    if ($existingSetting) {
                        $existingSetting->update($settingData);
                    } else {
                        SiteSetting::create($settingData);
                    }
                    
                    $imported[] = $key;
                    
                } catch (\Exception $e) {
                    $errors[$settingData['key'] ?? 'unknown'] = [$e->getMessage()];
                }
            }
            
            // Clear all setting caches
            $this->clearAllSettingCaches();
            
            DB::commit();
            
            return [
                'imported' => $imported,
                'skipped' => $skipped,
                'errors' => $errors,
                'total' => count($settings),
                'success_count' => count($imported),
            ];
            
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * Reset settings to default.
     */
    public function resetToDefaults(array $keys = []): int
    {
        DB::beginTransaction();
        
        try {
            $query = SiteSetting::query();
            
            if (!empty($keys)) {
                $query->whereIn('key', $keys);
            }
            
            $settings = $query->get();
            $resetCount = 0;
            
            foreach ($settings as $setting) {
                if ($setting->default_value !== null) {
                    $setting->update(['value' => $setting->default_value]);
                    $resetCount++;
                }
            }
            
            // Clear all setting caches
            $this->clearAllSettingCaches();
            
            DB::commit();
            
            return $resetCount;
            
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * Get setting history.
     */
    public function getSettingHistory(string $key, int $limit = 50): Collection
    {
        return DB::table('setting_history')
            ->where('setting_key', $key)
            ->orderByDesc('created_at')
            ->limit($limit)
            ->get();
    }

    /**
     * Get settings statistics.
     */
    public function getStatistics(): array
    {
        return Cache::remember('settings_statistics', 3600, function () {
            return [
                'total' => SiteSetting::count(),
            'public' => SiteSetting::where('is_public', true)->count(),
            'private' => SiteSetting::where('is_public', false)->count(),
            'required' => SiteSetting::where('is_required', true)->count(),
            'categories' => SiteSetting::distinct('category')->count(),
            'by_type' => SiteSetting::select('type')
                    ->selectRaw('COUNT(*) as count')
                    ->groupBy('type')
                    ->pluck('count', 'type')
                    ->toArray(),
                'by_category' => SiteSetting::select('category')
                    ->selectRaw('COUNT(*) as count')
                    ->groupBy('category')
                    ->pluck('count', 'category')
                    ->toArray(),
                'recently_updated' => SiteSetting::where('updated_at', '>', now()->subDays(7))->count(),
            ];
        });
    }

    /**
     * Search settings.
     */
    public function searchSettings(string $query): Collection
    {
        return SiteSetting::where(function ($q) use ($query) {
            $q->where('key', 'like', '%' . $query . '%')
              ->orWhere('label', 'like', '%' . $query . '%')
              ->orWhere('description', 'like', '%' . $query . '%')
              ->orWhere('value', 'like', '%' . $query . '%');
        })
        ->orderBy('category')
        ->orderBy('sort_order')
        ->get();
    }

    /**
     * Format setting value based on type.
     */
    protected function formatSettingValue(Setting $setting)
    {
        $value = $setting->value;
        
        switch ($setting->type) {
            case 'boolean':
                return in_array($value, [1, '1', 'true', true], true);
                
            case 'integer':
                return (int)$value;
                
            case 'float':
                return (float)$value;
                
            case 'json':
            case 'array':
                return is_string($value) ? json_decode($value, true) : $value;
                
            case 'encrypted':
                return $setting->is_encrypted ? decrypt($value) : $value;
                
            default:
                return $value;
        }
    }

    /**
     * Prepare value for storage.
     */
    protected function prepareValueForStorage($value, string $type)
    {
        switch ($type) {
            case 'boolean':
                return in_array($value, [1, '1', 'true', true], true) ? 1 : 0;
                
            case 'json':
            case 'array':
                return is_array($value) ? json_encode($value) : $value;
                
            case 'encrypted':
                return encrypt($value);
                
            default:
                return $value;
        }
    }

    /**
     * Store setting history.
     */
    protected function storeSettingHistory(Setting $setting, $oldValue, $newValue): void
    {
        DB::table('setting_history')->insert([
            'setting_key' => $setting->key,
            'old_value' => $oldValue,
            'new_value' => $this->prepareValueForStorage($newValue, $setting->type),
            'user_id' => auth()->id(),
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'created_at' => now(),
        ]);
    }

    /**
     * Clear setting-related caches.
     */
    protected function clearSettingCaches(Setting $setting): void
    {
        $keys = [
            'settings_all',
            'settings_public',
            "settings_category_{$setting->category}",
            "settings_category_{$setting->category}_public",
            "setting_{$setting->key}",
            'setting_categories',
            'settings_statistics',
        ];
        
        foreach ($keys as $key) {
            Cache::forget($key);
        }
    }

    /**
     * Clear all setting caches.
     */
    protected function clearAllSettingCaches(): void
    {
        $patterns = [
            'settings_*',
            'setting_*',
        ];
        
        foreach ($patterns as $pattern) {
            Cache::flush(); // In production, you might want to use a more targeted approach
        }
    }

    /**
     * Clear application cache.
     */
    protected function clearApplicationCache(): void
    {
        try {
            Artisan::call('cache:clear');
            Artisan::call('config:clear');
            Artisan::call('view:clear');
        } catch (\Exception $e) {
            // Log error but don't fail the setting update
            \Log::warning('Failed to clear application cache', [
                'error' => $e->getMessage()
            ]);
        }
    }

    /**
     * Get environment-specific settings.
     */
    public function getEnvironmentSettings(): array
    {
        $environment = app()->environment();
        
        return Cache::remember("settings_env_{$environment}", 3600, function () use ($environment) {
            return SiteSetting::where('environment', $environment)
                ->orWhereNull('environment')
                ->get()
                ->keyBy('key')
                ->map(function ($setting) {
                    return $this->formatSettingValue($setting);
                })
                ->toArray();
        });
    }

    /**
     * Backup settings.
     */
    public function backupSettings(): string
    {
        $settings = $this->exportSettings([], true);
        
        $backup = [
            'version' => '1.0',
            'created_at' => now()->toISOString(),
            'environment' => app()->environment(),
            'settings' => $settings,
        ];
        
        $filename = 'settings_backup_' . now()->format('Y_m_d_H_i_s') . '.json';
        $path = storage_path('app/backups/' . $filename);
        
        if (!file_exists(dirname($path))) {
            mkdir(dirname($path), 0755, true);
        }
        
        file_put_contents($path, json_encode($backup, JSON_PRETTY_PRINT));
        
        return $path;
    }

    /**
     * Restore settings from backup.
     */
    public function restoreSettings(string $backupPath): array
    {
        if (!file_exists($backupPath)) {
            throw new \InvalidArgumentException('Backup file not found');
        }
        
        $backup = json_decode(file_get_contents($backupPath), true);
        
        if (!$backup || !isset($backup['settings'])) {
            throw new \InvalidArgumentException('Invalid backup file format');
        }
        
        return $this->importSettings($backup['settings'], true);
    }
}