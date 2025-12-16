<?php

namespace App\Http\Controllers;

use App\Models\SiteSetting;
use App\Services\FileUploadService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class SiteSettingController extends Controller
{
    protected $fileUploadService;

    public function __construct(FileUploadService $fileUploadService)
    {
        $this->fileUploadService = $fileUploadService;
        $this->middleware('auth');
        $this->middleware('permission:view site settings', ['only' => ['index', 'show']]);
        $this->middleware('permission:edit site settings', ['only' => ['edit', 'update']]);
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $settingsGrouped = SiteSetting::all()->groupBy('group');

        // Create a flat array for easy access in view
        $settings = [];
        foreach (SiteSetting::all() as $setting) {
            $value = $setting->value;

            // Convert file paths to URLs for image settings
            if (in_array($setting->key, ['logo', 'favicon', 'hero_image', 'og_image']) && $value) {
                $value = Storage::disk('public')->url($value);
            }

            $settings[$setting->key] = $value;
        }

        // Scan for templates
        $templates = [];
        $templatePath = resource_path('views/template');
        if (is_dir($templatePath)) {
            $dirs = scandir($templatePath);
            foreach ($dirs as $dir) {
                if ($dir !== '.' && $dir !== '..' && is_dir($templatePath . '/' . $dir)) {
                    $templates[] = [
                        'name' => $dir,
                        'label' => ucwords(str_replace('_', ' ', $dir)),
                        'path' => $dir,
                        'preview' => file_exists(public_path("template/{$dir}/show.png"))
                            ? asset("template/{$dir}/show.png")
                            : asset('images/default-template.png')
                    ];
                }
            }
        }

        return view('admin.settings.index', compact('settings', 'settingsGrouped', 'templates'));
    }

    /**
     * Show the form for editing the specified group.
     */
    public function edit($group)
    {
        $settings = SiteSetting::where('group', $group)->get();

        if ($settings->isEmpty()) {
            abort(404, 'Setting group not found');
        }

        return view('admin.settings.edit', compact('settings', 'group'));
    }

    public function updateAll(Request $request)
    {
        // Validate file uploads
        $request->validate([
            'logo' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
            'favicon' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp,ico|max:1024',
            'hero_image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:5120',
            'og_image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
        ], [
            'logo.image' => 'Logo harus berupa file gambar.',
            'logo.mimes' => 'Logo harus berformat: JPEG, PNG, JPG, GIF, atau WebP.',
            'logo.max' => 'Ukuran logo maksimal 2MB.',
            'favicon.image' => 'Favicon harus berupa file gambar.',
            'favicon.mimes' => 'Favicon harus berformat: JPEG, PNG, JPG, GIF, WebP, atau ICO.',
            'favicon.max' => 'Ukuran favicon maksimal 1MB.',
            'hero_image.image' => 'Hero image harus berupa file gambar.',
            'hero_image.mimes' => 'Hero image harus berformat: JPEG, PNG, JPG, GIF, atau WebP.',
            'hero_image.max' => 'Ukuran hero image maksimal 5MB.',
            'og_image.image' => 'OG image harus berupa file gambar.',
            'og_image.mimes' => 'OG image harus berformat: JPEG, PNG, JPG, GIF, atau WebP.',
            'og_image.max' => 'Ukuran OG image maksimal 2MB.',
        ]);

        try {
            DB::beginTransaction();

            // Get all form data except CSRF token
            $formData = $request->except(['_token', '_method']);

            // Log for debugging (REPLACE dd() with Log::info())
            Log::info('Settings update started', [
                'form_keys' => array_keys($formData),
                'has_files' => !empty($request->allFiles())
            ]);

            foreach ($formData as $key => $value) {
                // Find or create the setting
                $setting = SiteSetting::firstOrNew(['key' => $key]);

                // If it's a new setting, set default attributes based on key
                if (!$setting->exists) {
                    if (in_array($key, ['recaptcha_site_key', 'recaptcha_secret_key'])) {
                        $setting->group = 'security';
                        $setting->type = 'text';
                        // $setting->label = ucwords(str_replace('_', ' ', $key)); // Column 'label' does not exist
                    } else {
                        // Default fallback for other potential new keys
                        $setting->group = 'general';
                        $setting->type = 'text';
                        // $setting->label = ucwords(str_replace('_', ' ', $key)); // Column 'label' does not exist
                    }
                }

                // Handle file uploads
                if ($request->hasFile($key)) {
                    $file = $request->file($key);

                    // Log file upload attempt (INSTEAD OF dd())
                    Log::info("Processing file upload for: {$key}", [
                        'file_name' => $file->getClientOriginalName(),
                        'file_size' => $file->getSize(),
                        'mime_type' => $file->getMimeType(),
                        'is_valid' => $file->isValid(),
                    ]);

                    // Delete old file if exists
                    if ($setting->value && Storage::disk('public')->exists($setting->value)) {
                        Storage::disk('public')->delete($setting->value);
                        Log::info("Deleted old file: {$setting->value}");
                    }

                    try {
                        if (in_array($key, ['logo', 'favicon', 'hero_image', 'og_image'])) {
                            $uploadedMedia = $this->fileUploadService->uploadImage($file, 'settings');
                        } else {
                            $uploadedMedia = $this->fileUploadService->upload($file, 'settings');
                        }

                        $value = $uploadedMedia->file_path;
                        Log::info("File uploaded successfully for {$key}: {$value}");

                        // Generate favicon variations if this is the favicon
                        if ($key === 'favicon') {
                            try {
                                $this->fileUploadService->generateFavicons($value);
                                Log::info("Favicons generated successfully.");
                            } catch (\Exception $e) {
                                Log::error("Failed to generate favicons: " . $e->getMessage());
                            }
                        }

                    } catch (\Exception $e) {
                        Log::error("File upload failed for {$key}", [
                            'error' => $e->getMessage(),
                            'trace' => $e->getTraceAsString()
                        ]);
                        throw $e;
                    }
                }
                // Handle boolean values (checkboxes)
                elseif ($setting->type === 'boolean') {
                    $value = filter_var($request->input($key), FILTER_VALIDATE_BOOLEAN) ? '1' : '0';
                }
                // Handle JSON arrays
                elseif ($setting->type === 'json' && is_array($value)) {
                    $value = json_encode($value);
                }

                // Trim string values to remove accidental whitespace (especially for API keys)
                if (is_string($value)) {
                    $value = trim($value);
                }

                // Save individual setting
                $setting->value = $value;
                $setting->save();

                Log::info("Updated setting: {$key} = {$value}");
            }

            // Clear cache for all groups
            $groups = SiteSetting::distinct('group')->pluck('group');
            foreach ($groups as $group) {
                Cache::forget("site_settings_{$group}");
            }
            Cache::forget('all_site_settings');
            Cache::forget('site_settings_frontend');

            DB::commit();
            Log::info('Settings updated successfully');

            return redirect()->route('admin.settings.index')
                ->with('success', 'Settings updated successfully.');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Settings update failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return back()->withInput()
                ->with('error', 'Failed to update settings: ' . $e->getMessage());
        }
    }

    /**
     * Update the specified group settings.
     */
    public function updateGroup(Request $request, $group)
    {
        try {
            DB::beginTransaction();

            $settings = SiteSetting::where('group', $group)->get();

            if ($settings->isEmpty()) {
                abort(404, 'Setting group not found');
            }

            foreach ($settings as $setting) {
                $value = $request->input($setting->key);

                // Handle file uploads
                if ($request->hasFile($setting->key)) {
                    // Delete old file if exists
                    if ($setting->value && $setting->type === 'file') {
                        $this->fileUploadService->delete($setting->value);
                    }

                    // Upload new file
                    $file = $request->file($setting->key);
                    if (in_array($setting->key, ['logo', 'favicon', 'hero_image', 'og_image'])) {
                        $uploadedMedia = $this->fileUploadService->uploadImage($file, 'settings', 800, 600);
                    } else {
                        $uploadedMedia = $this->fileUploadService->upload($file, 'settings');
                    }
                    $value = $uploadedMedia->file_path;

                    // Generate favicon variations if this is the favicon
                    if ($setting->key === 'favicon') {
                        try {
                            $this->fileUploadService->generateFavicons($value);
                            Log::info("Favicons generated successfully via updateGroup.");
                        } catch (\Exception $e) {
                            Log::error("Failed to generate favicons in updateGroup: " . $e->getMessage());
                        }
                    }
                }

                // Handle boolean values
                if ($setting->type === 'boolean') {
                    $value = $request->boolean($setting->key);
                }

                // Handle array values (like social media links)
                if ($setting->type === 'json' && is_array($value)) {
                    $value = json_encode($value);
                }

                $setting->update(['value' => $value]);
            }

            // Clear cache
            Cache::forget("site_settings_{$group}");
            Cache::forget('all_site_settings');

            DB::commit();

            return redirect()->route('admin.settings.index')
                ->with('success', 'Pengaturan berhasil diperbarui.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()
                ->with('error', 'Gagal memperbarui pengaturan: ' . $e->getMessage());
        }
    }

    /**
     * Get setting value by key
     */
    public function getValue($key)
    {
        $setting = SiteSetting::where('key', $key)->first();

        if (!$setting) {
            return response()->json(['error' => 'Setting not found'], 404);
        }

        return response()->json([
            'key' => $setting->key,
            'value' => $setting->value,
            'type' => $setting->type
        ]);
    }

    /**
     * Update single setting
     */
    public function updateSingle(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'key' => 'required|exists:site_settings,key',
            'value' => 'required'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $setting = SiteSetting::where('key', $request->key)->first();

            if (!$setting) {
                return response()->json([
                    'success' => false,
                    'message' => 'Setting not found'
                ], 404);
            }

            $value = $request->value;

            // Handle boolean values
            if ($setting->type === 'boolean') {
                $value = filter_var($value, FILTER_VALIDATE_BOOLEAN);
            }

            // Handle numeric values
            if ($setting->type === 'number') {
                $value = is_numeric($value) ? (int) $value : 0;
            }

            $setting->update(['value' => $value]);

            // Clear cache
            Cache::forget("site_settings_{$setting->group}");
            Cache::forget('all_site_settings');

            return response()->json([
                'success' => true,
                'message' => 'Setting berhasil diperbarui.',
                'data' => [
                    'key' => $setting->key,
                    'value' => $setting->value
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal memperbarui setting: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Clear all settings cache
     */
    /**
     * Clear all settings cache
     */
    public function clearCache(Request $request)
    {
        try {
            // Clear system cache (View, Config, Route, Cache)
            \Illuminate\Support\Facades\Artisan::call('optimize:clear');

            // Clear specific settings cache
            $groups = SiteSetting::distinct('group')->pluck('group');
            foreach ($groups as $group) {
                Cache::forget("site_settings_{$group}");
            }
            Cache::forget('all_site_settings');
            Cache::forget('site_settings_frontend');

            if ($request->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Cache sistem dan pengaturan berhasil dibersihkan.'
                ]);
            }

            return back()->with('success', 'Cache sistem dan pengaturan berhasil dibersihkan.');

        } catch (\Exception $e) {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Gagal membersihkan cache: ' . $e->getMessage()
                ], 500);
            }
            return back()->with('error', 'Gagal membersihkan cache: ' . $e->getMessage());
        }
    }

    /**
     * Export settings
     */
    public function export()
    {
        try {
            $settings = SiteSetting::all();
            $exportData = [];

            foreach ($settings as $setting) {
                $exportData[$setting->key] = [
                    'value' => $setting->value,
                    'type' => $setting->type,
                    'group' => $setting->group,
                    'label' => $setting->label,
                    'description' => $setting->description
                ];
            }

            $filename = 'site_settings_' . date('Y-m-d_H-i-s') . '.json';

            return response()->json($exportData)
                ->header('Content-Disposition', 'attachment; filename="' . $filename . '"');
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal mengekspor pengaturan: ' . $e->getMessage());
        }
    }

    /**
     * Import settings
     */
    public function import(Request $request)
    {
        $request->validate([
            'settings_file' => 'required|file|mimes:json'
        ]);

        try {
            DB::beginTransaction();

            $file = $request->file('settings_file');
            $content = file_get_contents($file->getRealPath());
            $importData = json_decode($content, true);

            if (json_last_error() !== JSON_ERROR_NONE) {
                throw new \Exception('Invalid JSON file');
            }

            foreach ($importData as $key => $data) {
                $setting = SiteSetting::where('key', $key)->first();

                if ($setting) {
                    $setting->update([
                        'value' => $data['value'] ?? $setting->value,
                        'type' => $data['type'] ?? $setting->type,
                        'group' => $data['group'] ?? $setting->group,
                        'label' => $data['label'] ?? $setting->label,
                        'description' => $data['description'] ?? $setting->description
                    ]);
                } else {
                    SiteSetting::create([
                        'key' => $key,
                        'value' => $data['value'] ?? '',
                        'type' => $data['type'] ?? 'text',
                        'group' => $data['group'] ?? 'general',
                        'label' => $data['label'] ?? $key,
                        'description' => $data['description'] ?? ''
                    ]);
                }
            }

            // Clear all cache
            $groups = SiteSetting::distinct('group')->pluck('group');
            foreach ($groups as $group) {
                Cache::forget("site_settings_{$group}");
            }
            Cache::forget('all_site_settings');

            DB::commit();

            return redirect()->route('admin.settings.index')
                ->with('success', 'Pengaturan berhasil diimpor.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal mengimpor pengaturan: ' . $e->getMessage());
        }
    }
    /**
     * Show the theme customization form.
     */
    public function theme()
    {
        $template = \App\Models\SiteSetting::getValue('active_template', 'default');

        if ($template === 'default') {
            return redirect()->route('admin.settings.index')
                ->with('error', 'Default theme does not support customization.');
        }

        $configPath = resource_path("views/template/{$template}/theme-config.php");

        if (!file_exists($configPath)) {
            return redirect()->route('admin.settings.index')
                ->with('error', "Theme config file not found for '{$template}'.");
        }

        $themeConfig = include $configPath;

        // Helper to flatten array with dot notation
        $flatten = function ($array, $prefix = '') use (&$flatten) {
            $result = [];
            foreach ($array as $key => $value) {
                if (is_array($value)) {
                    $result = array_merge($result, $flatten($value, $prefix . $key . '.'));
                } else {
                    $result[$prefix . $key] = $value;
                }
            }
            return $result;
        };

        $flatConfig = $flatten($themeConfig);
        $dbValues = [];

        foreach ($flatConfig as $key => $default) {
            // DB Key format: theme_{template}_{key_slug}
            // We use underscores for DB keys, so replace dots with underscores
            $dbKey = 'theme_' . $template . '_' . str_replace('.', '_', $key);
            $dbValues[$key] = \App\Models\SiteSetting::getValue($dbKey);
        }

        return view('admin.settings.theme', compact('template', 'themeConfig', 'dbValues', 'flatConfig'));
    }

    /**
     * Update theme configuration.
     */
    public function updateTheme(Request $request)
    {
        $template = \App\Models\SiteSetting::getValue('active_template', 'default');

        if ($template === 'default') {
            return back()->with('error', 'Default theme cannot be updated.');
        }

        try {
            DB::beginTransaction();

            // Handle all inputs including files
            $allInputs = $request->except(['_token', '_method']);

            foreach ($allInputs as $key => $value) {
                // Key comes as dot notation from form, convert to DB format
                $dbKey = 'theme_' . $template . '_' . str_replace('.', '_', $key);

                // Check if this is a file upload
                if ($request->hasFile($key)) {
                    $file = $request->file($key);

                    // Delete old file if exists
                    $oldValue = \App\Models\SiteSetting::getValue($dbKey);
                    if ($oldValue && \Illuminate\Support\Facades\Storage::disk('public')->exists($oldValue)) {
                        \Illuminate\Support\Facades\Storage::disk('public')->delete($oldValue);
                    }

                    // Upload new file
                    $uploadService = app(\App\Services\FileUploadService::class);
                    $uploadedMedia = $uploadService->upload($file, 'theme');
                    $value = $uploadedMedia->file_path;

                    \App\Models\SiteSetting::set($dbKey, $value, 'image', 'theme_' . $template);
                } else {
                    // For text inputs (skip if it was a file input but empty - handled by hasFile check logic implicitly?
                    // No, if file input is empty, $request->file($key) is null.
                    // But $request->except() might typically not include file keys if they are empty?
                    // Actually browsers usually don't send file fields if empty, or send empty filename.
                    // Laravel's $request->all() / except() usually includes non-file inputs.
                    // If it's a text input, save it. If it's a file input but empty, we shouldn't overwrite with null unless intended.
                    // But here we are iterating over *inputs*.

                    // We should only save if it's NOT a file (already handled) AND not null/empty if that wipes data?
                    // Let's rely on standard handling: if user leaves file empty, it won't be in hasFile.
                    // IMPORTANT: If we have mixed inputs, we need to be careful.

                    if (!$request->hasFile($key)) {
                        \App\Models\SiteSetting::set($dbKey, $value, 'text', 'theme_' . $template);
                    }
                }
            }

            // Clear cache
            Cache::forget("site_settings_theme_{$template}");
            Cache::forget('all_site_settings');

            DB::commit();

            return back()->with('success', 'Theme settings updated successfully.');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Failed to update theme settings: ' . $e->getMessage());
        }
    }
}
