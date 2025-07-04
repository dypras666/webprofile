<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class SiteSetting extends Model
{
    protected $fillable = [
        'key',
        'value',
        'type',
        'group',
        'description'
    ];

    public static function get($key, $default = null)
    {
        return Cache::remember("site_setting_{$key}", 3600, function () use ($key, $default) {
            $setting = static::where('key', $key)->first();
            return $setting ? $setting->value : $default;
        });
    }

    public static function set($key, $value, $type = 'text', $group = 'general', $description = null)
    {
        $setting = static::updateOrCreate(
            ['key' => $key],
            [
                'value' => $value,
                'type' => $type,
                'group' => $group,
                'description' => $description
            ]
        );

        Cache::forget("site_setting_{$key}");
        
        return $setting;
    }

    public static function getByGroup($group)
    {
        return Cache::remember("site_settings_group_{$group}", 3600, function () use ($group) {
            return static::where('group', $group)->pluck('value', 'key')->toArray();
        });
    }

    public static function getAllSettings()
    {
        return Cache::remember('all_site_settings', 3600, function () {
            return static::pluck('value', 'key')->toArray();
        });
    }

    public static function getValue($key, $default = null)
    {
        return Cache::remember("site_setting_{$key}", 3600, function () use ($key, $default) {
            $setting = static::where('key', $key)->first();
            return $setting ? $setting->value : $default;
        });
    }

    protected static function boot()
    {
        parent::boot();

        static::saved(function ($setting) {
            Cache::forget("site_setting_{$setting->key}");
            Cache::forget("site_settings_group_{$setting->group}");
            Cache::forget('all_site_settings');
        });

        static::deleted(function ($setting) {
            Cache::forget("site_setting_{$setting->key}");
            Cache::forget("site_settings_group_{$setting->group}");
            Cache::forget('all_site_settings');
        });
    }
}
