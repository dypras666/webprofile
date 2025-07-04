<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\SiteSetting;

class LppmSiteSettingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $settings = [
            // General Settings
            [
                'key' => 'site_name',
                'value' => 'Website LPPM Institut Islam Al-Mujaddid Sabak',
                'type' => 'text',
                'group' => 'general',
                'description' => 'The name of your website'
            ],
            [
                'key' => 'site_description',
                'value' => 'Lembaga Penelitian dan Pengabdian kepada Masyarakat Institut Islam Al-Mujaddid Sabak',
                'type' => 'textarea',
                'group' => 'general',
                'description' => 'A brief description of your website'
            ],
            [
                'key' => 'site_url',
                'value' => 'https://lppm.iimsabak.ac.id',
                'type' => 'url',
                'group' => 'general',
                'description' => 'The main URL of your website'
            ],
            [
                'key' => 'admin_email',
                'value' => 'lppm@iimsabak.ac.id',
                'type' => 'email',
                'group' => 'general',
                'description' => 'Primary email address for administration'
            ],
            [
                'key' => 'timezone',
                'value' => 'Asia/Jakarta',
                'type' => 'select',
                'group' => 'general',
                'description' => 'Default timezone for the website'
            ],
            [
                'key' => 'language',
                'value' => 'id',
                'type' => 'select',
                'group' => 'general',
                'description' => 'Default language for the website'
            ],

            // Contact Information
            [
                'key' => 'contact_address',
                'value' => 'Jl. Wr. Soepratman Kel. Talang babat, Kec. Muara Sabak Barat, Tanjung Jabung Timur, Jambi',
                'type' => 'textarea',
                'group' => 'contact',
                'description' => 'Physical address of the institution'
            ],
            [
                'key' => 'contact_phone',
                'value' => '082282113175',
                'type' => 'text',
                'group' => 'contact',
                'description' => 'Primary phone number'
            ],
            [
                'key' => 'contact_email',
                'value' => 'lppm@iimsabak.ac.id',
                'type' => 'email',
                'group' => 'contact',
                'description' => 'Primary contact email address'
            ],

            // SEO Settings
            [
                'key' => 'meta_title',
                'value' => 'LPPM Institut Islam Al-Mujaddid Sabak - Lembaga Penelitian dan Pengabdian Masyarakat',
                'type' => 'text',
                'group' => 'seo',
                'description' => 'Default meta title for SEO'
            ],
            [
                'key' => 'meta_description',
                'value' => 'Lembaga Penelitian dan Pengabdian kepada Masyarakat Institut Islam Al-Mujaddid Sabak, Tanjung Jabung Timur, Jambi. Melayani penelitian dan pengabdian masyarakat untuk kemajuan pendidikan Islam.',
                'type' => 'textarea',
                'group' => 'seo',
                'description' => 'Default meta description for SEO'
            ],
            [
                'key' => 'meta_keywords',
                'value' => 'LPPM, Institut Islam Al-Mujaddid Sabak, penelitian, pengabdian masyarakat, pendidikan Islam, Jambi, Tanjung Jabung Timur',
                'type' => 'text',
                'group' => 'seo',
                'description' => 'Default meta keywords for SEO'
            ],

            // Appearance Settings
            [
                'key' => 'theme_color',
                'value' => '#2563eb',
                'type' => 'color',
                'group' => 'appearance',
                'description' => 'Main color theme for the website'
            ],
            [
                'key' => 'posts_per_page',
                'value' => '10',
                'type' => 'number',
                'group' => 'appearance',
                'description' => 'Number of posts to display per page'
            ],
            [
                'key' => 'show_excerpts',
                'value' => '1',
                'type' => 'boolean',
                'group' => 'appearance',
                'description' => 'Show post excerpts on homepage'
            ],

            // Security Settings
            [
                'key' => 'enable_registration',
                'value' => '0',
                'type' => 'boolean',
                'group' => 'security',
                'description' => 'Allow user registration'
            ],
            [
                'key' => 'require_email_verification',
                'value' => '1',
                'type' => 'boolean',
                'group' => 'security',
                'description' => 'Require email verification for new users'
            ],
            [
                'key' => 'enable_comments',
                'value' => '1',
                'type' => 'boolean',
                'group' => 'security',
                'description' => 'Enable comments on posts'
            ],
            [
                'key' => 'moderate_comments',
                'value' => '1',
                'type' => 'boolean',
                'group' => 'security',
                'description' => 'Moderate comments before publishing'
            ],

            // Advanced Settings
            [
                'key' => 'maintenance_mode',
                'value' => '0',
                'type' => 'boolean',
                'group' => 'advanced',
                'description' => 'Enable maintenance mode'
            ],
            [
                'key' => 'cache_duration',
                'value' => '60',
                'type' => 'number',
                'group' => 'advanced',
                'description' => 'Cache duration in minutes'
            ]
        ];

        foreach ($settings as $setting) {
            SiteSetting::updateOrCreate(
                ['key' => $setting['key']],
                $setting
            );
        }
    }
}