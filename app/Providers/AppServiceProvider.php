<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Cache;
use App\Models\SiteSetting;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Share site settings with all frontend views
        View::composer(['layouts.app', 'frontend.*', 'components.*'], function ($view) {
            $settings = Cache::remember('site_settings_frontend', 3600, function () {
                return SiteSetting::all()->pluck('value', 'key')->toArray();
            });
            
            $view->with('siteSettings', $settings);
        });
    }
}
