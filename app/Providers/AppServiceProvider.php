<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Cache;
use App\Models\SiteSetting;
use Illuminate\Pagination\Paginator;

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
        Paginator::useTailwind();

        // Share Ads globally
        View::composer('*', function ($view) {
            $ads = \App\Models\Post::where('type', 'ads')
                ->where('is_published', true)
                ->with('category')
                ->get();

            $adsPopup = $ads->filter(fn($ad) => $ad->category && $ad->category->slug === 'ads-popup')->first();
            $adsSidebar = $ads->filter(fn($ad) => $ad->category && $ad->category->slug === 'ads-sidebar')->first();
            $adsFooter = $ads->filter(fn($ad) => $ad->category && $ad->category->slug === 'ads-footer')->first();

            $view->with('adsPopup', $adsPopup)
                ->with('adsSidebar', $adsSidebar)
                ->with('adsFooter', $adsFooter);
        });

        // Share site settings with all frontend views
        View::composer(['layouts.app', 'frontend.*', 'components.*'], function ($view) {
            $settings = Cache::remember('site_settings_frontend', 3600, function () {
                return SiteSetting::all()->pluck('value', 'key')->toArray();
            });

            $view->with('siteSettings', $settings);
        });
    }
}
