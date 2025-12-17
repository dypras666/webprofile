<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Admin\BaseAdminController;
use App\Http\Requests\SettingRequest;
use App\Services\SiteSettingService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Artisan;

class AdminSettingController extends BaseAdminController
{
    protected $settingService;

    public function __construct(SiteSettingService $settingService)
    {
        parent::__construct();
        $this->settingService = $settingService;

        // Set permissions
        $this->middleware('permission:manage_settings');
    }

    /**
     * Display a listing of settings
     */
    public function index(Request $request): View
    {
        $category = $request->get('category', 'general');
        $settings = $this->settingService->getSettingsByCategory($category);
        $categories = $this->settingService->getCategories();
        $settingTypes = $this->settingService->getSettingTypes();

        return view('admin.settings.index', compact('settings', 'categories', 'settingTypes', 'category'));
    }

    /**
     * Show the form for creating a new setting
     */
    public function create(): View
    {
        $categories = $this->settingService->getCategories();
        $settingTypes = $this->settingService->getSettingTypes();

        return view('admin.settings.create', compact('categories', 'settingTypes'));
    }

    /**
     * Store a newly created setting
     */
    public function store(SettingRequest $request): RedirectResponse
    {
        try {
            $setting = $this->settingService->createSetting($request->validated());

            $this->logActivity('setting_created', 'Created setting: ' . $setting->key, $setting->id);

            return redirect()->route('admin.settings.index', ['category' => $setting->category])
                ->with('success', 'Setting created successfully.');
        } catch (\Exception $e) {
            return back()->withInput()
                ->with('error', 'Failed to create setting: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified setting
     */
    public function show($id): View
    {
        $setting = $this->settingService->getSetting($id);

        if (!$setting) {
            abort(404, 'Setting not found');
        }

        return view('admin.settings.show', compact('setting'));
    }

    /**
     * Show the form for editing the specified setting
     */
    public function edit($id): View
    {
        $setting = $this->settingService->getSetting($id);

        if (!$setting) {
            abort(404, 'Setting not found');
        }

        $categories = $this->settingService->getCategories();
        $settingTypes = $this->settingService->getSettingTypes();

        return view('admin.settings.edit', compact('setting', 'categories', 'settingTypes'));
    }

    /**
     * Update the specified setting
     */
    public function update(SettingRequest $request, $id): RedirectResponse
    {
        try {
            $setting = $this->settingService->updateSetting($id, $request->validated());

            if (!$setting) {
                return back()->with('error', 'Setting not found.');
            }

            $this->logActivity('setting_updated', 'Updated setting: ' . $setting->key, $setting->id);

            return redirect()->route('admin.settings.index', ['category' => $setting->category])
                ->with('success', 'Setting updated successfully.');
        } catch (\Exception $e) {
            return back()->withInput()
                ->with('error', 'Failed to update setting: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified setting
     */
    public function destroy($id): RedirectResponse
    {
        try {
            $setting = $this->settingService->getSetting($id);

            if (!$setting) {
                return back()->with('error', 'Setting not found.');
            }

            $key = $setting->key;
            $category = $setting->category;
            $deleted = $this->settingService->deleteSetting($id);

            if ($deleted) {
                $this->logActivity('setting_deleted', 'Deleted setting: ' . $key, $id);
                return redirect()->route('admin.settings.index', ['category' => $category])
                    ->with('success', 'Setting deleted successfully.');
            }

            return back()->with('error', 'Failed to delete setting.');
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to delete setting: ' . $e->getMessage());
        }
    }

    /**
     * Update multiple settings at once
     */
    public function updateMultiple(Request $request): RedirectResponse
    {
        try {
            $settings = $request->get('settings', []);
            $category = $request->get('category', 'general');

            if (empty($settings)) {
                return back()->with('error', 'No settings to update.');
            }

            $updated = $this->settingService->updateMultipleSettings($settings);

            $this->logActivity('settings_bulk_updated', 'Updated ' . count($updated) . ' settings in category: ' . $category);

            return back()->with('success', 'Settings updated successfully.');
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to update settings: ' . $e->getMessage());
        }
    }

    /**
     * General settings page
     */
    public function general(): View
    {
        $settings = $this->settingService->getGeneralSettings();

        return view('admin.settings.general', compact('settings'));
    }

    /**
     * Update general settings
     */
    public function updateGeneral(Request $request): RedirectResponse
    {
        $request->validate([
            'site_name' => 'required|string|max:255',
            'site_description' => 'nullable|string|max:500',
            'site_keywords' => 'nullable|string|max:500',
            'contact_email' => 'required|email|max:255',
            'timezone' => 'required|string|max:50',
            'date_format' => 'required|string|max:20',
            'time_format' => 'required|string|max:20',
            'posts_per_page' => 'required|integer|min:1|max:100',
            'logo' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'favicon' => 'nullable|image|mimes:ico,png|max:1024'
        ]);

        try {
            $settings = $request->except(['_token', '_method']);
            $this->settingService->updateMultipleSettings($settings);

            $this->logActivity('general_settings_updated', 'Updated general settings');

            return back()->with('success', 'General settings updated successfully.');
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to update general settings: ' . $e->getMessage());
        }
    }

    /**
     * SEO settings page
     */
    public function seo(): View
    {
        $settings = $this->settingService->getSeoSettings();

        return view('admin.settings.seo', compact('settings'));
    }

    /**
     * Update SEO settings
     */
    public function updateSeo(Request $request): RedirectResponse
    {
        $request->validate([
            'meta_title' => 'nullable|string|max:60',
            'meta_description' => 'nullable|string|max:160',
            'meta_keywords' => 'nullable|string|max:255',
            'og_title' => 'nullable|string|max:60',
            'og_description' => 'nullable|string|max:160',
            'og_image' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'twitter_card' => 'nullable|in:summary,summary_large_image',
            'twitter_site' => 'nullable|string|max:50',
            'google_analytics_id' => 'nullable|string|max:50',
            'google_search_console' => 'nullable|string|max:100',
            'robots_txt' => 'nullable|string|max:2000',
            'sitemap_enabled' => 'boolean',
            'breadcrumbs_enabled' => 'boolean'
        ]);

        try {
            $settings = $request->except(['_token', '_method']);
            $this->settingService->updateMultipleSettings($settings);

            $this->logActivity('seo_settings_updated', 'Updated SEO settings');

            return back()->with('success', 'SEO settings updated successfully.');
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to update SEO settings: ' . $e->getMessage());
        }
    }

    /**
     * Social media settings page
     */
    public function socialMedia(): View
    {
        $settings = $this->settingService->getSocialMediaSettings();

        return view('admin.settings.social-media', compact('settings'));
    }

    /**
     * Update social media settings
     */
    public function updateSocialMedia(Request $request): RedirectResponse
    {
        $request->validate([
            'facebook_url' => 'nullable|url|max:255',
            'twitter_url' => 'nullable|url|max:255',
            'instagram_url' => 'nullable|url|max:255',
            'linkedin_url' => 'nullable|url|max:255',
            'youtube_url' => 'nullable|url|max:255',
            'github_url' => 'nullable|url|max:255',
            'social_sharing_enabled' => 'boolean',
            'social_login_enabled' => 'boolean',
            'facebook_app_id' => 'nullable|string|max:50',
            'facebook_app_secret' => 'nullable|string|max:100',
            'twitter_api_key' => 'nullable|string|max:100',
            'twitter_api_secret' => 'nullable|string|max:100',
            'google_client_id' => 'nullable|string|max:100',
            'google_client_secret' => 'nullable|string|max:100'
        ]);

        try {
            $settings = $request->except(['_token', '_method']);
            $this->settingService->updateMultipleSettings($settings);

            $this->logActivity('social_media_settings_updated', 'Updated social media settings');

            return back()->with('success', 'Social media settings updated successfully.');
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to update social media settings: ' . $e->getMessage());
        }
    }

    /**
     * Email settings page
     */
    public function email(): View
    {
        $settings = $this->settingService->getEmailSettings();

        return view('admin.settings.email', compact('settings'));
    }

    /**
     * Update email settings
     */
    public function updateEmail(Request $request): RedirectResponse
    {
        $request->validate([
            'mail_driver' => 'required|in:smtp,sendmail,mailgun,ses,postmark',
            'mail_host' => 'required_if:mail_driver,smtp|nullable|string|max:255',
            'mail_port' => 'required_if:mail_driver,smtp|nullable|integer|min:1|max:65535',
            'mail_username' => 'required_if:mail_driver,smtp|nullable|string|max:255',
            'mail_password' => 'required_if:mail_driver,smtp|nullable|string|max:255',
            'mail_encryption' => 'nullable|in:tls,ssl',
            'mail_from_address' => 'required|email|max:255',
            'mail_from_name' => 'required|string|max:255',
            'mailgun_domain' => 'required_if:mail_driver,mailgun|nullable|string|max:255',
            'mailgun_secret' => 'required_if:mail_driver,mailgun|nullable|string|max:255',
            'ses_key' => 'required_if:mail_driver,ses|nullable|string|max:255',
            'ses_secret' => 'required_if:mail_driver,ses|nullable|string|max:255',
            'ses_region' => 'required_if:mail_driver,ses|nullable|string|max:50',
            'postmark_token' => 'required_if:mail_driver,postmark|nullable|string|max:255'
        ]);

        try {
            $settings = $request->except(['_token', '_method']);
            $this->settingService->updateMultipleSettings($settings);

            $this->logActivity('email_settings_updated', 'Updated email settings');

            return back()->with('success', 'Email settings updated successfully.');
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to update email settings: ' . $e->getMessage());
        }
    }

    /**
     * Appearance settings page
     */
    public function appearance(): View
    {
        $settings = $this->settingService->getAppearanceSettings();

        return view('admin.settings.appearance', compact('settings'));
    }

    /**
     * Update appearance settings
     */
    public function updateAppearance(Request $request): RedirectResponse
    {
        $request->validate([
            'theme' => 'required|string|max:50',
            'primary_color' => 'required|string|max:7',
            'secondary_color' => 'required|string|max:7',
            'accent_color' => 'required|string|max:7',
            'font_family' => 'required|string|max:100',
            'font_size' => 'required|integer|min:12|max:20',
            'sidebar_position' => 'required|in:left,right',
            'layout_width' => 'required|in:boxed,full-width',
            'header_style' => 'required|in:default,minimal,centered',
            'footer_style' => 'required|in:default,minimal,extended',
            'custom_css' => 'nullable|string|max:10000',
            'custom_js' => 'nullable|string|max:10000'
        ]);

        try {
            $settings = $request->except(['_token', '_method']);
            $this->settingService->updateMultipleSettings($settings);

            $this->logActivity('appearance_settings_updated', 'Updated appearance settings');

            return back()->with('success', 'Appearance settings updated successfully.');
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to update appearance settings: ' . $e->getMessage());
        }
    }

    /**
     * Export settings
     */
    public function export(Request $request)
    {
        try {
            $category = $request->get('category');
            $format = $request->get('format', 'json');

            $data = $this->settingService->exportSettings($category, $format);

            $filename = 'settings';
            if ($category) {
                $filename .= '_' . $category;
            }
            $filename .= '_' . date('Y-m-d') . '.' . $format;

            $contentType = $format === 'json' ? 'application/json' : 'text/csv';

            return response($data)
                ->header('Content-Type', $contentType)
                ->header('Content-Disposition', 'attachment; filename="' . $filename . '"');
        } catch (\Exception $e) {
            return back()->with('error', 'Export failed: ' . $e->getMessage());
        }
    }

    /**
     * Import settings
     */
    public function import(Request $request): RedirectResponse
    {
        $request->validate([
            'file' => 'required|file|mimes:json,csv|max:2048',
            'overwrite' => 'boolean'
        ]);

        try {
            $file = $request->file('file');
            $overwrite = $request->boolean('overwrite');

            $imported = $this->settingService->importSettings($file, $overwrite);

            $this->logActivity('settings_imported', 'Imported ' . count($imported) . ' settings');

            return back()->with('success', 'Settings imported successfully. ' . count($imported) . ' settings were imported.');
        } catch (\Exception $e) {
            return back()->with('error', 'Import failed: ' . $e->getMessage());
        }
    }

    /**
     * Reset settings to default
     */
    public function reset(Request $request): RedirectResponse
    {
        $request->validate([
            'category' => 'nullable|string|max:50',
            'confirm' => 'required|accepted'
        ]);

        try {
            $category = $request->get('category');
            $count = $this->settingService->resetSettings($category);

            $message = $category
                ? "Reset {$count} settings in category: {$category}"
                : "Reset {$count} settings to default values";

            $this->logActivity('settings_reset', $message);

            return back()->with('success', 'Settings reset successfully.');
        } catch (\Exception $e) {
            return back()->with('error', 'Reset failed: ' . $e->getMessage());
        }
    }

    /**
     * Backup settings
     */
    public function backup(): JsonResponse
    {
        try {
            $backup = $this->settingService->backupSettings();

            $this->logActivity('settings_backup_created', 'Created settings backup');

            return $this->successResponse('Settings backup created successfully', [
                'backup_id' => $backup['id'],
                'created_at' => $backup['created_at']
            ]);
        } catch (\Exception $e) {
            return $this->errorResponse('Backup failed: ' . $e->getMessage());
        }
    }

    /**
     * Restore settings from backup
     */
    public function restore(Request $request): RedirectResponse
    {
        $request->validate([
            'backup_id' => 'required|string',
            'confirm' => 'required|accepted'
        ]);

        try {
            $restored = $this->settingService->restoreSettings($request->backup_id);

            $this->logActivity('settings_restored', 'Restored settings from backup: ' . $request->backup_id);

            return back()->with('success', 'Settings restored successfully from backup.');
        } catch (\Exception $e) {
            return back()->with('error', 'Restore failed: ' . $e->getMessage());
        }
    }

    /**
     * Clear settings cache
     */
    public function clearCache(): JsonResponse
    {
        try {
            $this->settingService->clearCache();

            // Also clear application cache
            Artisan::call('cache:clear');
            Artisan::call('config:clear');

            $this->logActivity('settings_cache_cleared', 'Cleared settings cache');

            return $this->successResponse('Settings cache cleared successfully');
        } catch (\Exception $e) {
            return $this->errorResponse('Failed to clear cache: ' . $e->getMessage());
        }
    }

    /**
     * Test email configuration
     */
    public function testEmail(Request $request): JsonResponse
    {
        $request->validate([
            'email' => 'required|email'
        ]);

        try {
            $sent = $this->settingService->testEmailConfiguration($request->email);

            if ($sent) {
                $this->logActivity('email_test_sent', 'Sent test email to: ' . $request->email);
                return $this->successResponse('Test email sent successfully');
            }

            return $this->errorResponse('Failed to send test email');
        } catch (\Exception $e) {
            return $this->errorResponse('Email test failed: ' . $e->getMessage());
        }
    }

    /**
     * Get setting by key (AJAX)
     */
    public function getSetting(Request $request): JsonResponse
    {
        $request->validate([
            'key' => 'required|string'
        ]);

        try {
            $value = $this->settingService->getSettingValue($request->key);

            return $this->successResponse('Setting retrieved', [
                'key' => $request->key,
                'value' => $value
            ]);
        } catch (\Exception $e) {
            return $this->errorResponse('Failed to get setting: ' . $e->getMessage());
        }
    }

    /**
     * Update single setting (AJAX)
     */
    public function updateSetting(Request $request): JsonResponse
    {
        $request->validate([
            'key' => 'required|string',
            'value' => 'nullable'
        ]);

        try {
            $updated = $this->settingService->updateSettingValue($request->key, $request->value);

            if ($updated) {
                $this->logActivity('setting_updated', 'Updated setting: ' . $request->key);
                return $this->successResponse('Setting updated successfully');
            }

            return $this->errorResponse('Failed to update setting');
        } catch (\Exception $e) {
            return $this->errorResponse('Failed to update setting: ' . $e->getMessage());
        }
    }

    /**
     * Get all categories
     */
    public function categories(): JsonResponse
    {
        try {
            $categories = $this->settingService->getCategories();

            return $this->successResponse('Categories retrieved', $categories);
        } catch (\Exception $e) {
            return $this->errorResponse('Failed to get categories: ' . $e->getMessage());
        }
    }

    /**
     * Get setting types
     */
    public function types(): JsonResponse
    {
        try {
            $types = $this->settingService->getSettingTypes();

            return $this->successResponse('Setting types retrieved', $types);
        } catch (\Exception $e) {
            return $this->errorResponse('Failed to get setting types: ' . $e->getMessage());
        }
    }
}