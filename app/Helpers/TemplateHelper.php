<?php

namespace App\Helpers;

use App\Models\SiteSetting;
use Illuminate\Support\Facades\View;

class TemplateHelper
{
    /**
     * Get the active template name.
     *
     * @return string
     */
    public static function getActiveTemplate()
    {
        return SiteSetting::getValue('active_template', 'default');
    }

    /**
     * Get the view path for the active template.
     *
     * @param string $view
     * @return string
     */
    public static function view($view)
    {
        $template = self::getActiveTemplate();
        $templateView = "template.{$template}.{$view}";

        if ($template !== 'default' && View::exists($templateView)) {
            return $templateView;
        }

        return "frontend.{$view}";
    }

    /**
     * Get the asset URL for the active template.
     *
     * @param string $path
     * @return string
     */
    public static function asset($path)
    {
        $template = self::getActiveTemplate();

        if ($template !== 'default') {
            return asset("template/{$template}/{$path}");
        }

        return asset($path);
    }

    /**
     * Get a theme-specific configuration value.
     * Hierarchy:
     * 1. DB Override (site_settings table: theme_{theme}_{key})
     * 2. Config File (resources/views/template/{theme}/theme-config.php)
     * 3. Default value
     *
     * @param string $key Dot notation key (e.g., 'hero.title')
     * @param mixed $default
     * @return mixed
     */
    public static function getThemeConfig($key, $default = null)
    {
        $template = self::getActiveTemplate();

        // 1. Check DB Override first
        // Convention: theme_{template}_{key_slug}
        // Example: theme_university_hero_title
        $dbKey = 'theme_' . $template . '_' . str_replace('.', '_', $key);
        $dbValue = SiteSetting::getValue($dbKey);

        if ($dbValue !== null) {
            return $dbValue;
        }

        // 2. Load from Config File
        // We use a static variable to cache the config per request
        static $themeConfigs = [];

        if (!isset($themeConfigs[$template])) {
            $configPath = resource_path("views/template/{$template}/theme-config.php");

            if (file_exists($configPath)) {
                $themeConfigs[$template] = include $configPath;
            } else {
                $themeConfigs[$template] = [];
            }
        }

        // Helper to get array value by dot notation
        $array = $themeConfigs[$template];

        if (strpos($key, '.') === false) {
            return $array[$key] ?? $default;
        }

        foreach (explode('.', $key) as $segment) {
            if (isset($array[$segment]) && is_array($array)) {
                $array = $array[$segment];
            } else {
                return $default;
            }
        }

        return $array;
    }
}
