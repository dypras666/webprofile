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
}
