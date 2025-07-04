<?php

namespace App\Services;

use App\Models\SiteSetting;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\UploadedFile;

class SiteSettingService
{
    protected $fileUploadService;
    protected $cacheKey = 'site_settings';
    protected $cacheTTL = 3600; // 1 hour

    public function __construct(FileUploadService $fileUploadService)
    {
        $this->fileUploadService = $fileUploadService;
    }

    /**
     * Get all settings grouped by category
     */
    public function getAllSettings(): array
    {
        return Cache::remember($this->cacheKey . '_all', $this->cacheTTL, function () {
            $settings = SiteSetting::orderBy('category')->orderBy('sort_order')->get();
            
            $grouped = [];
            foreach ($settings as $setting) {
                $grouped[$setting->category][] = [
                    'id' => $setting->id,
                    'key' => $setting->key,
                    'value' => $this->formatValue($setting),
                    'type' => $setting->type,
                    'label' => $setting->label,
                    'description' => $setting->description,
                    'options' => $setting->options ? json_decode($setting->options, true) : null,
                    'validation_rules' => $setting->validation_rules,
                    'is_required' => $setting->is_required,
                    'sort_order' => $setting->sort_order
                ];
            }
            
            return $grouped;
        });
    }

    /**
     * Get settings by category
     */
    public function getSettingsByCategory($category): array
    {
        return Cache::remember($this->cacheKey . '_category_' . $category, $this->cacheTTL, function () use ($category) {
            return SiteSetting::where('category', $category)
                ->orderBy('sort_order')
                ->get()
                ->map(function ($setting) {
                    return [
                        'id' => $setting->id,
                        'key' => $setting->key,
                        'value' => $this->formatValue($setting),
                        'type' => $setting->type,
                        'label' => $setting->label,
                        'description' => $setting->description,
                        'options' => $setting->options ? json_decode($setting->options, true) : null,
                        'validation_rules' => $setting->validation_rules,
                        'is_required' => $setting->is_required,
                        'sort_order' => $setting->sort_order
                    ];
                })
                ->toArray();
        });
    }

    /**
     * Get single setting value
     */
    public function getSetting($key, $default = null)
    {
        $cacheKey = $this->cacheKey . '_' . $key;
        
        return Cache::remember($cacheKey, $this->cacheTTL, function () use ($key, $default) {
            $setting = SiteSetting::where('key', $key)->first();
            
            if (!$setting) {
                return $default;
            }
            
            return $this->formatValue($setting);
        });
    }

    /**
     * Update single setting
     */
    public function updateSetting($key, $value): bool
    {
        DB::beginTransaction();
        
        try {
            $setting = SiteSetting::where('key', $key)->first();
            
            if (!$setting) {
                throw new \Exception('Setting not found: ' . $key);
            }
            
            // Handle file uploads
            if ($value instanceof UploadedFile) {
                // Delete old file if exists
                if ($setting->value && $setting->type === 'file') {
                    $this->fileUploadService->delete($setting->value);
                }
                
                $media = $this->fileUploadService->upload($value, 'settings');
                $value = $media->path;
            }
            
            // Validate value
            $this->validateSettingValue($setting, $value);
            
            // Update setting
            $setting->update(['value' => $value]);
            
            // Clear caches
            $this->clearSettingCache($key);
            
            DB::commit();
            
            return true;
        } catch (\Exception $e) {
            DB::rollback();
            throw $e;
        }
    }

    /**
     * Update multiple settings
     */
    public function updateSettings(array $settings): bool
    {
        DB::beginTransaction();
        
        try {
            foreach ($settings as $key => $value) {
                $setting = SiteSetting::where('key', $key)->first();
                
                if (!$setting) {
                    continue; // Skip non-existent settings
                }
                
                // Handle file uploads
                if ($value instanceof UploadedFile) {
                    // Delete old file if exists
                    if ($setting->value && $setting->type === 'file') {
                        $this->fileUploadService->delete($setting->value);
                    }
                    
                    $media = $this->fileUploadService->upload($value, 'settings');
                    $value = $media->path;
                }
                
                // Validate value
                $this->validateSettingValue($setting, $value);
                
                // Update setting
                $setting->update(['value' => $value]);
            }
            
            // Clear all setting caches
            $this->clearAllSettingCaches();
            
            DB::commit();
            
            return true;
        } catch (\Exception $e) {
            DB::rollback();
            throw $e;
        }
    }

    /**
     * Create new setting
     */
    public function createSetting(array $data): SiteSetting
    {
        DB::beginTransaction();
        
        try {
            // Check if key already exists
            if (SiteSetting::where('key', $data['key'])->exists()) {
                throw new \Exception('Setting key already exists: ' . $data['key']);
            }
            
            // Handle file upload
            if (isset($data['value']) && $data['value'] instanceof UploadedFile) {
                $media = $this->fileUploadService->upload($data['value'], 'settings');
                $data['value'] = $media->path;
            }
            
            // Set default values
            $data['category'] = $data['category'] ?? 'general';
            $data['type'] = $data['type'] ?? 'text';
            $data['is_required'] = $data['is_required'] ?? false;
            $data['sort_order'] = $data['sort_order'] ?? 0;
            
            // Encode options if provided
            if (isset($data['options']) && is_array($data['options'])) {
                $data['options'] = json_encode($data['options']);
            }
            
            $setting = SiteSetting::create($data);
            
            // Clear caches
            $this->clearAllSettingCaches();
            
            DB::commit();
            
            return $setting;
        } catch (\Exception $e) {
            DB::rollback();
            throw $e;
        }
    }

    /**
     * Delete setting
     */
    public function deleteSetting($key): bool
    {
        DB::beginTransaction();
        
        try {
            $setting = SiteSetting::where('key', $key)->first();
            
            if (!$setting) {
                return false;
            }
            
            // Delete associated file if exists
            if ($setting->value && $setting->type === 'file') {
                $this->fileUploadService->delete($setting->value);
            }
            
            $deleted = $setting->delete();
            
            // Clear caches
            $this->clearSettingCache($key);
            $this->clearAllSettingCaches();
            
            DB::commit();
            
            return $deleted;
        } catch (\Exception $e) {
            DB::rollback();
            throw $e;
        }
    }

    /**
     * Get available categories
     */
    public function getCategories(): array
    {
        return Cache::remember($this->cacheKey . '_categories', $this->cacheTTL, function () {
            return SiteSetting::distinct('category')
                ->orderBy('category')
                ->pluck('category')
                ->toArray();
        });
    }

    /**
     * Get setting types
     */
    public function getSettingTypes(): array
    {
        return [
            'text' => 'Text',
            'textarea' => 'Textarea',
            'number' => 'Number',
            'email' => 'Email',
            'url' => 'URL',
            'password' => 'Password',
            'select' => 'Select',
            'radio' => 'Radio',
            'checkbox' => 'Checkbox',
            'boolean' => 'Boolean',
            'file' => 'File',
            'image' => 'Image',
            'color' => 'Color',
            'date' => 'Date',
            'datetime' => 'DateTime',
            'json' => 'JSON'
        ];
    }

    /**
     * Export settings to array
     */
    public function exportSettings(): array
    {
        $settings = SiteSetting::all();
        
        $export = [];
        foreach ($settings as $setting) {
            $export[$setting->key] = [
                'value' => $setting->value,
                'type' => $setting->type,
                'category' => $setting->category,
                'label' => $setting->label,
                'description' => $setting->description,
                'options' => $setting->options,
                'validation_rules' => $setting->validation_rules,
                'is_required' => $setting->is_required,
                'sort_order' => $setting->sort_order
            ];
        }
        
        return $export;
    }

    /**
     * Import settings from array
     */
    public function importSettings(array $settings, $overwrite = false): int
    {
        DB::beginTransaction();
        
        try {
            $imported = 0;
            
            foreach ($settings as $key => $data) {
                $existingSetting = SiteSetting::where('key', $key)->first();
                
                if ($existingSetting && !$overwrite) {
                    continue; // Skip existing settings if not overwriting
                }
                
                $settingData = array_merge($data, ['key' => $key]);
                
                if ($existingSetting) {
                    $existingSetting->update($settingData);
                } else {
                    SiteSetting::create($settingData);
                }
                
                $imported++;
            }
            
            // Clear all caches
            $this->clearAllSettingCaches();
            
            DB::commit();
            
            return $imported;
        } catch (\Exception $e) {
            DB::rollback();
            throw $e;
        }
    }

    /**
     * Reset settings to default
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
            $reset = 0;
            
            foreach ($settings as $setting) {
                if ($setting->default_value !== null) {
                    $setting->update(['value' => $setting->default_value]);
                    $reset++;
                }
            }
            
            // Clear all caches
            $this->clearAllSettingCaches();
            
            DB::commit();
            
            return $reset;
        } catch (\Exception $e) {
            DB::rollback();
            throw $e;
        }
    }

    /**
     * Get settings for frontend
     */
    public function getFrontendSettings(): array
    {
        return Cache::remember($this->cacheKey . '_frontend', $this->cacheTTL, function () {
            $settings = SiteSetting::where('is_public', true)->get();
            
            $frontendSettings = [];
            foreach ($settings as $setting) {
                $frontendSettings[$setting->key] = $this->formatValue($setting);
            }
            
            return $frontendSettings;
        });
    }

    /**
     * Get SEO settings
     */
    public function getSEOSettings(): array
    {
        return $this->getSettingsByCategory('seo');
    }

    /**
     * Get social media settings
     */
    public function getSocialSettings(): array
    {
        return $this->getSettingsByCategory('social');
    }

    /**
     * Get email settings
     */
    public function getEmailSettings(): array
    {
        return $this->getSettingsByCategory('email');
    }

    /**
     * Get appearance settings
     */
    public function getAppearanceSettings(): array
    {
        return $this->getSettingsByCategory('appearance');
    }

    /**
     * Validate setting value
     */
    protected function validateSettingValue(SiteSetting $setting, $value): void
    {
        // Check if required
        if ($setting->is_required && empty($value)) {
            throw new \Exception('Setting "' . $setting->label . '" is required');
        }
        
        // Type-specific validation
        switch ($setting->type) {
            case 'email':
                if ($value && !filter_var($value, FILTER_VALIDATE_EMAIL)) {
                    throw new \Exception('Invalid email format for "' . $setting->label . '"');
                }
                break;
                
            case 'url':
                if ($value && !filter_var($value, FILTER_VALIDATE_URL)) {
                    throw new \Exception('Invalid URL format for "' . $setting->label . '"');
                }
                break;
                
            case 'number':
                if ($value && !is_numeric($value)) {
                    throw new \Exception('"' . $setting->label . '" must be a number');
                }
                break;
                
            case 'boolean':
                if ($value && !in_array($value, [true, false, 1, 0, '1', '0', 'true', 'false'])) {
                    throw new \Exception('"' . $setting->label . '" must be a boolean value');
                }
                break;
                
            case 'json':
                if ($value && !is_array($value) && !json_decode($value)) {
                    throw new \Exception('"' . $setting->label . '" must be valid JSON');
                }
                break;
                
            case 'select':
            case 'radio':
                if ($value && $setting->options) {
                    $options = json_decode($setting->options, true);
                    $validValues = array_keys($options);
                    if (!in_array($value, $validValues)) {
                        throw new \Exception('Invalid value for "' . $setting->label . '"');
                    }
                }
                break;
        }
        
        // Custom validation rules
        if ($setting->validation_rules && $value) {
            $rules = explode('|', $setting->validation_rules);
            
            foreach ($rules as $rule) {
                if (strpos($rule, 'min:') === 0) {
                    $min = (int) substr($rule, 4);
                    if (strlen($value) < $min) {
                        throw new \Exception('"' . $setting->label . '" must be at least ' . $min . ' characters');
                    }
                }
                
                if (strpos($rule, 'max:') === 0) {
                    $max = (int) substr($rule, 4);
                    if (strlen($value) > $max) {
                        throw new \Exception('"' . $setting->label . '" must not exceed ' . $max . ' characters');
                    }
                }
            }
        }
    }

    /**
     * Format setting value based on type
     */
    protected function formatValue(SiteSetting $setting)
    {
        $value = $setting->value;
        
        switch ($setting->type) {
            case 'boolean':
                return in_array($value, [1, '1', 'true', true]);
                
            case 'number':
                return is_numeric($value) ? (float) $value : $value;
                
            case 'json':
                return is_string($value) ? json_decode($value, true) : $value;
                
            case 'checkbox':
                return is_string($value) ? explode(',', $value) : $value;
                
            case 'file':
            case 'image':
                return $value ? asset('storage/' . $value) : $value;
                
            default:
                return $value;
        }
    }

    /**
     * Clear setting cache
     */
    protected function clearSettingCache($key): void
    {
        Cache::forget($this->cacheKey . '_' . $key);
    }

    /**
     * Clear all setting caches
     */
    protected function clearAllSettingCaches(): void
    {
        // Check if cache driver supports tagging
        if (method_exists(Cache::getStore(), 'tags')) {
            Cache::tags(['settings', 'admin'])->flush();
        } else {
            // Fallback for cache drivers that don't support tagging (like database)
            Cache::flush();
        }
        
        // Also clear specific caches
        $patterns = [
            $this->cacheKey . '_all',
            $this->cacheKey . '_frontend',
            $this->cacheKey . '_categories'
        ];
        
        foreach ($patterns as $pattern) {
            Cache::forget($pattern);
        }
        
        // Clear category caches
        $categories = $this->getCategories();
        foreach ($categories as $category) {
            Cache::forget($this->cacheKey . '_category_' . $category);
        }
    }

    /**
     * Backup settings
     */
    public function backupSettings(): string
    {
        $settings = $this->exportSettings();
        
        $backup = [
            'timestamp' => now()->toISOString(),
            'version' => '1.0',
            'settings' => $settings
        ];
        
        return json_encode($backup, JSON_PRETTY_PRINT);
    }

    /**
     * Restore settings from backup
     */
    public function restoreSettings(string $backupData): int
    {
        $backup = json_decode($backupData, true);
        
        if (!$backup || !isset($backup['settings'])) {
            throw new \Exception('Invalid backup data');
        }
        
        return $this->importSettings($backup['settings'], true);
    }

    /**
     * Get setting form configuration
     */
    public function getFormConfiguration($category = null): array
    {
        $query = SiteSetting::orderBy('sort_order');
        
        if ($category) {
            $query->where('category', $category);
        }
        
        $settings = $query->get();
        
        $config = [];
        foreach ($settings as $setting) {
            $config[] = [
                'key' => $setting->key,
                'type' => $setting->type,
                'label' => $setting->label,
                'description' => $setting->description,
                'value' => $this->formatValue($setting),
                'options' => $setting->options ? json_decode($setting->options, true) : null,
                'validation_rules' => $setting->validation_rules,
                'is_required' => $setting->is_required,
                'placeholder' => $setting->placeholder,
                'help_text' => $setting->help_text
            ];
        }
        
        return $config;
    }
}