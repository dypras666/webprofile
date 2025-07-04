<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\SiteSetting;

class SiteSettingSeeder extends Seeder
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
                'value' => 'Universitas Maju Mundur',
                'type' => 'text',
                'group' => 'general',
                'description' => 'Nama situs web'
            ],
            [
                'key' => 'site_tagline',
                'value' => 'Membangun Masa Depan Melalui Pendidikan Berkualitas',
                'type' => 'text',
                'group' => 'general',
                'description' => 'Tagline atau slogan situs'
            ],
            [
                'key' => 'site_description',
                'value' => 'Website resmi Universitas Maju Mundur - institusi pendidikan tinggi terdepan yang berkomitmen menghasilkan lulusan berkualitas dan berdaya saing global.',
                'type' => 'textarea',
                'group' => 'general',
                'description' => 'Deskripsi situs untuk SEO'
            ],
            [
                'key' => 'site_keywords',
                'value' => 'universitas, pendidikan tinggi, kampus, mahasiswa, dosen, penelitian, akademik, beasiswa',
                'type' => 'text',
                'group' => 'general',
                'description' => 'Keywords untuk SEO (pisahkan dengan koma)'
            ],
            [
                'key' => 'logo',
                'value' => '',
                'type' => 'image',
                'group' => 'general',
                'description' => 'Logo situs'
            ],
            [
                'key' => 'favicon',
                'value' => '',
                'type' => 'image',
                'group' => 'general',
                'description' => 'Favicon situs'
            ],
            
            // Contact Information
            [
                'key' => 'contact_email',
                'value' => 'info@umm.ac.id',
                'type' => 'email',
                'group' => 'contact',
                'description' => 'Email kontak utama'
            ],
            [
                'key' => 'contact_phone',
                'value' => '+62 21 8765 4321',
                'type' => 'text',
                'group' => 'contact',
                'description' => 'Nomor telepon kontak'
            ],
            [
                'key' => 'contact_address',
                'value' => 'Jl. Pendidikan Raya No. 100, Jakarta Selatan 12345, Indonesia',
                'type' => 'textarea',
                'group' => 'contact',
                'description' => 'Alamat lengkap'
            ],
            [
                'key' => 'contact_whatsapp',
                'value' => '+6281234567890',
                'type' => 'text',
                'group' => 'contact',
                'description' => 'Nomor WhatsApp (dengan kode negara)'
            ],
            
            // Social Media
            [
                'key' => 'social_facebook',
                'value' => '',
                'type' => 'url',
                'group' => 'social',
                'description' => 'URL Facebook'
            ],
            [
                'key' => 'social_twitter',
                'value' => '',
                'type' => 'url',
                'group' => 'social',
                'description' => 'URL Twitter'
            ],
            [
                'key' => 'social_instagram',
                'value' => '',
                'type' => 'url',
                'group' => 'social',
                'description' => 'URL Instagram'
            ],
            [
                'key' => 'social_youtube',
                'value' => '',
                'type' => 'url',
                'group' => 'social',
                'description' => 'URL YouTube'
            ],
            [
                'key' => 'social_linkedin',
                'value' => '',
                'type' => 'url',
                'group' => 'social',
                'description' => 'URL LinkedIn'
            ],
            
            // SEO Settings
            [
                'key' => 'seo_meta_title',
                'value' => 'Universitas Maju Mundur - Membangun Masa Depan Melalui Pendidikan Berkualitas',
                'type' => 'text',
                'group' => 'seo',
                'description' => 'Meta title default untuk halaman'
            ],
            [
                'key' => 'seo_meta_description',
                'value' => 'Website resmi Universitas Maju Mundur. Institusi pendidikan tinggi terdepan dengan program studi berkualitas, fasilitas modern, dan tenaga pengajar berpengalaman.',
                'type' => 'textarea',
                'group' => 'seo',
                'description' => 'Meta description default untuk halaman'
            ],
            [
                'key' => 'seo_og_image',
                'value' => '',
                'type' => 'image',
                'group' => 'seo',
                'description' => 'Gambar default untuk Open Graph (Facebook, Twitter)'
            ],
            
            // Homepage Settings
            [
                'key' => 'homepage_hero_title',
                'value' => 'Selamat Datang di Web Profile',
                'type' => 'text',
                'group' => 'homepage',
                'description' => 'Judul utama di halaman beranda'
            ],
            [
                'key' => 'homepage_hero_subtitle',
                'value' => 'Kami menyediakan solusi terbaik untuk kebutuhan bisnis Anda',
                'type' => 'text',
                'group' => 'homepage',
                'description' => 'Subjudul di halaman beranda'
            ],
            [
                'key' => 'homepage_hero_image',
                'value' => '',
                'type' => 'image',
                'group' => 'homepage',
                'description' => 'Gambar hero di halaman beranda'
            ],
            [
                'key' => 'homepage_about_title',
                'value' => 'Tentang Kami',
                'type' => 'text',
                'group' => 'homepage',
                'description' => 'Judul section tentang kami'
            ],
            [
                'key' => 'homepage_about_content',
                'value' => 'Kami adalah perusahaan yang berkomitmen untuk memberikan layanan terbaik kepada klien. Dengan pengalaman bertahun-tahun, kami siap membantu mewujudkan visi dan misi perusahaan Anda.',
                'type' => 'textarea',
                'group' => 'homepage',
                'description' => 'Konten section tentang kami'
            ],
            
            // Display Settings
            [
                'key' => 'posts_per_page',
                'value' => '10',
                'type' => 'number',
                'group' => 'display',
                'description' => 'Jumlah post per halaman'
            ],
            [
                'key' => 'slider_posts_count',
                'value' => '5',
                'type' => 'number',
                'group' => 'display',
                'description' => 'Jumlah post yang ditampilkan di slider'
            ],
            [
                'key' => 'featured_posts_count',
                'value' => '6',
                'type' => 'number',
                'group' => 'display',
                'description' => 'Jumlah post unggulan yang ditampilkan'
            ],
            [
                'key' => 'gallery_images_per_page',
                'value' => '12',
                'type' => 'number',
                'group' => 'display',
                'description' => 'Jumlah gambar galeri per halaman'
            ],
            
            // Email Settings
            [
                'key' => 'email_from_name',
                'value' => 'Web Profile',
                'type' => 'text',
                'group' => 'email',
                'description' => 'Nama pengirim email'
            ],
            [
                'key' => 'email_from_address',
                'value' => 'noreply@webprofile.com',
                'type' => 'email',
                'group' => 'email',
                'description' => 'Alamat email pengirim'
            ],
            
            // Analytics
            [
                'key' => 'google_analytics_id',
                'value' => '',
                'type' => 'text',
                'group' => 'analytics',
                'description' => 'Google Analytics Tracking ID'
            ],
            [
                'key' => 'google_tag_manager_id',
                'value' => '',
                'type' => 'text',
                'group' => 'analytics',
                'description' => 'Google Tag Manager ID'
            ],
            
            // Maintenance
            [
                'key' => 'maintenance_mode',
                'value' => 'false',
                'type' => 'boolean',
                'group' => 'maintenance',
                'description' => 'Mode maintenance situs'
            ],
            [
                'key' => 'maintenance_message',
                'value' => 'Situs sedang dalam pemeliharaan. Silakan kembali lagi nanti.',
                'type' => 'textarea',
                'group' => 'maintenance',
                'description' => 'Pesan yang ditampilkan saat maintenance'
            ],
        ];

        foreach ($settings as $setting) {
            SiteSetting::create($setting);
        }

        $this->command->info('Site settings created successfully!');
    }
}
