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
                'value' => 'Website LPM Institut Islam Al-Mujaddid Sabak',
                'type' => 'text',
                'group' => 'general',
                'description' => 'The name of your website'
            ],
            [
                'key' => 'site_description',
                'value' => 'Lembaga Penjaminan Mutu Institut Islam Al-Mujaddid Sabak',
                'type' => 'textarea',
                'group' => 'general',
                'description' => 'A brief description of your website'
            ],
            [
                'key' => 'site_url',
                'value' => 'https://iimsabak.ac.id',
                'type' => 'url',
                'group' => 'general',
                'description' => 'The main URL of your website'
            ],
            [
                'key' => 'contact_email',
                'value' => 'info@iimsabak.ac.id',
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

            // Welcome Section
            [
                'key' => 'enable_welcome_section',
                'value' => '1',
                'type' => 'boolean',
                'group' => 'welcome',
                'description' => 'Enable or disable the welcome section on homepage'
            ],
            [
                'key' => 'welcome_label',
                'value' => 'SAMBUTAN',
                'type' => 'text',
                'group' => 'welcome',
                'description' => 'Label for the welcome section'
            ],
            [
                'key' => 'leader_title',
                'value' => 'K E T U A',
                'type' => 'text',
                'group' => 'welcome',
                'description' => 'Title of the leader (e.g. Ketua)'
            ],
            [
                'key' => 'leader_name',
                'value' => 'Dr. H. M. ZUHRI, S.Ag., M.Ag.',
                'type' => 'text',
                'group' => 'welcome',
                'description' => 'Name of the leader'
            ],
            [
                'key' => 'leader_photo',
                'value' => '',
                'type' => 'file',
                'group' => 'welcome',
                'description' => 'Photo of the leader'
            ],
            [
                'key' => 'welcome_text',
                'value' => 'Selamat datang di website resmi Lembaga Penjaminan Mutu (LPM) Institut Islam Al-Mujaddid Sabak. Kami berkomitmen untuk terus meningkatkan mutu pendidikan dan pelayanan demi terwujudnya visi dan misi institusi.',
                'type' => 'textarea',
                'group' => 'welcome',
                'description' => 'Welcome message text'
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
                'value' => 'lpm@iimsabak.ac.id',
                'type' => 'email',
                'group' => 'contact',
                'description' => 'Primary contact email address'
            ],

            // SEO Settings
            [
                'key' => 'meta_title',
                'value' => 'LPM Institut Islam Al-Mujaddid Sabak - Lembaga Penjaminan Mutu',
                'type' => 'text',
                'group' => 'seo',
                'description' => 'Default meta title for SEO'
            ],
            [
                'key' => 'meta_description',
                'value' => 'Lembaga Penjaminan Mutu Institut Islam Al-Mujaddid Sabak, Tanjung Jabung Timur, Jambi. Melayani penjaminan mutu pendidikan dan akreditasi untuk kemajuan pendidikan Islam.',
                'type' => 'textarea',
                'group' => 'seo',
                'description' => 'Default meta description for SEO'
            ],
            [
                'key' => 'meta_keywords',
                'value' => 'LPM, Institut Islam Al-Mujaddid Sabak, penjaminan mutu, akreditasi, pendidikan Islam, Jambi, Tanjung Jabung Timur',
                'type' => 'text',
                'group' => 'seo',
                'description' => 'Default meta keywords for SEO'
            ],
            [
                'key' => 'google_analytics',
                'value' => '',
                'type' => 'text',
                'group' => 'seo',
                'description' => 'Google Analytics tracking ID'
            ],
            [
                'key' => 'google_search_console',
                'value' => '',
                'type' => 'text',
                'group' => 'seo',
                'description' => 'Google Search Console verification code'
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
            [
                'key' => 'logo',
                'value' => '',
                'type' => 'file',
                'group' => 'appearance',
                'description' => 'Site logo image'
            ],
            [
                'key' => 'favicon',
                'value' => '',
                'type' => 'file',
                'group' => 'appearance',
                'description' => 'Site favicon image'
            ],

            // Social Media Settings
            [
                'key' => 'facebook_url',
                'value' => '',
                'type' => 'url',
                'group' => 'social',
                'description' => 'Facebook page URL'
            ],
            [
                'key' => 'twitter_url',
                'value' => '',
                'type' => 'url',
                'group' => 'social',
                'description' => 'Twitter profile URL'
            ],
            [
                'key' => 'instagram_url',
                'value' => '',
                'type' => 'url',
                'group' => 'social',
                'description' => 'Instagram profile URL'
            ],
            [
                'key' => 'linkedin_url',
                'value' => '',
                'type' => 'url',
                'group' => 'social',
                'description' => 'LinkedIn profile URL'
            ],
            [
                'key' => 'youtube_url',
                'value' => '',
                'type' => 'url',
                'group' => 'social',
                'description' => 'YouTube channel URL'
            ],

            // Email Settings
            [
                'key' => 'smtp_host',
                'value' => '',
                'type' => 'text',
                'group' => 'email',
                'description' => 'SMTP server host'
            ],
            [
                'key' => 'smtp_port',
                'value' => '587',
                'type' => 'number',
                'group' => 'email',
                'description' => 'SMTP server port'
            ],
            [
                'key' => 'smtp_username',
                'value' => '',
                'type' => 'text',
                'group' => 'email',
                'description' => 'SMTP username'
            ],
            [
                'key' => 'smtp_password',
                'value' => '',
                'type' => 'password',
                'group' => 'email',
                'description' => 'SMTP password'
            ],
            [
                'key' => 'from_email',
                'value' => 'lpm@iimsabak.ac.id',
                'type' => 'email',
                'group' => 'email',
                'description' => 'Default from email address'
            ],
            [
                'key' => 'from_name',
                'value' => 'LPM Institut Islam Al-Mujaddid Sabak',
                'type' => 'text',
                'group' => 'email',
                'description' => 'Default from name'
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