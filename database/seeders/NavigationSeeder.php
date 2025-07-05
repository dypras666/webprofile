<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\NavigationMenu;

class NavigationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create parent menus first
        $parentMenus = [
            [
                'title' => 'Home',
                'url' => 'https://lpm.iimsabak.ac.id',
                'type' => 'custom',
                'reference_id' => null,
                'parent_id' => null,
                'sort_order' => 1,
                'is_active' => true,
                'target' => '_self',
                'css_class' => 'nav-home',
                'icon' => 'fas fa-home'
            ],
            [
                'title' => 'Web Utama',
                'url' => 'https://iimsabak.ac.id',
                'type' => 'custom',
                'reference_id' => null,
                'parent_id' => null,
                'sort_order' => 2,
                'is_active' => true,
                'target' => '_blank',
                'css_class' => 'nav-main-site',
                'icon' => 'fas fa-external-link-alt'
            ],
            [
                'title' => 'Profil',
                'url' => '#',
                'type' => 'custom',
                'reference_id' => null,
                'parent_id' => null,
                'sort_order' => 3,
                'is_active' => true,
                'target' => '_self',
                'css_class' => 'nav-profile dropdown',
                'icon' => 'fas fa-info-circle'
            ],
            [
                'title' => 'Layanan',
                'url' => '#',
                'type' => 'custom',
                'reference_id' => null,
                'parent_id' => null,
                'sort_order' => 4,
                'is_active' => true,
                'target' => '_self',
                'css_class' => 'nav-services dropdown',
                'icon' => 'fas fa-cogs'
            ],
            [
                'title' => 'Berita',
                'url' => '/posts',
                'type' => 'custom',
                'reference_id' => null,
                'parent_id' => null,
                'sort_order' => 5,
                'is_active' => true,
                'target' => '_self',
                'css_class' => 'nav-news',
                 'icon' => 'fas fa-newspaper'
             ],
             [
                'title' => 'Media',
                'url' => '#',
                'type' => 'custom',
                'reference_id' => null,
                'parent_id' => null,
                'sort_order' => 6,
                'is_active' => true,
                'target' => '_self',
                'css_class' => 'nav-media dropdown',
                'icon' => 'fas fa-photo-video'
            ],
            [
                'title' => 'Kontak',
                'url' => '/post/kontak-lpm',
                'type' => 'page',
                'reference_id' => null,
                'parent_id' => null,
                'sort_order' => 7,
                'is_active' => true,
                'target' => '_self',
                'css_class' => 'nav-contact',
                'icon' => 'fas fa-envelope'
            ]
        ];

        // Create parent menus and store their IDs
        $createdParents = [];
        foreach ($parentMenus as $menu) {
            $createdMenu = NavigationMenu::create($menu);
            $createdParents[$menu['title']] = $createdMenu->id;
        }

        // Create submenus
        $subMenus = [
            // Profil submenus
            [
                'title' => 'Tentang Kami',
                'url' => '/post/tentang-lpm-institut-islam-al-mujaddid-sabak',
                'type' => 'page',
                'reference_id' => null,
                'parent_id' => $createdParents['Profil'],
                'sort_order' => 1,
                'is_active' => true,
                'target' => '_self',
                'css_class' => 'nav-about',
                'icon' => null
            ],
            [
                'title' => 'Struktur Organisasi',
                'url' => '/post/struktur-organisasi-lpm',
                'type' => 'page',
                'reference_id' => null,
                'parent_id' => $createdParents['Profil'],
                'sort_order' => 2,
                'is_active' => true,
                'target' => '_self',
                'css_class' => 'nav-structure',
                'icon' => null
            ],
            [
                'title' => 'Visi & Misi',
                'url' => '/post/tentang-lpm-institut-islam-al-mujaddid-sabak#visi-misi',
                'type' => 'custom',
                'reference_id' => null,
                'parent_id' => $createdParents['Profil'],
                'sort_order' => 3,
                'is_active' => true,
                'target' => '_self',
                'css_class' => 'nav-vision',
                'icon' => null
            ],
            // Layanan submenus
            [
                'title' => 'Audit Mutu Internal',
                'url' => '/post/prosedur-audit-mutu-internal',
                'type' => 'page',
                'reference_id' => null,
                'parent_id' => $createdParents['Layanan'],
                'sort_order' => 1,
                'is_active' => true,
                'target' => '_self',
                'css_class' => 'nav-audit',
                'icon' => null
            ],
            [
                'title' => 'Standar Mutu',
                'url' => '/post/standar-mutu-pendidikan',
                'type' => 'page',
                'reference_id' => null,
                'parent_id' => $createdParents['Layanan'],
                'sort_order' => 2,
                'is_active' => true,
                'target' => '_self',
                'css_class' => 'nav-standards',
                'icon' => null
            ],
            [
                'title' => 'Monitoring & Evaluasi',
                'url' => '/posts?search=monitoring+evaluasi',
                'type' => 'custom',
                'reference_id' => null,
                'parent_id' => $createdParents['Layanan'],
                'sort_order' => 3,
                'is_active' => true,
                'target' => '_self',
                'css_class' => 'nav-monitoring',
                'icon' => null
            ],
            [
                'title' => 'Pelatihan & Workshop',
                'url' => '/posts?search=workshop+pelatihan',
                'type' => 'custom',
                'reference_id' => null,
                'parent_id' => $createdParents['Layanan'],
                'sort_order' => 4,
                'is_active' => true,
                'target' => '_self',
                'css_class' => 'nav-training',
                'icon' => null
            ],
            // Media submenus
            [
                'title' => 'Galeri Foto',
                'url' => '/gallery',
                'type' => 'custom',
                'reference_id' => null,
                'parent_id' => $createdParents['Media'],
                'sort_order' => 1,
                'is_active' => true,
                'target' => '_self',
                'css_class' => 'nav-gallery',
                'icon' => null
            ],
            [
                'title' => 'Video',
                'url' => '/posts?type=video',
                'type' => 'custom',
                'reference_id' => null,
                'parent_id' => $createdParents['Media'],
                'sort_order' => 2,
                'is_active' => true,
                'target' => '_self',
                'css_class' => 'nav-video',
                 'icon' => null
             ],
             [
                'title' => 'Dokumentasi',
                'url' => '/posts?search=dokumentasi',
                'type' => 'custom',
                'reference_id' => null,
                'parent_id' => $createdParents['Media'],
                'sort_order' => 3,
                'is_active' => true,
                'target' => '_self',
                'css_class' => 'nav-documentation',
                'icon' => null
            ],
            // Additional specific post links
            [
                'title' => 'Sertifikasi ISO 9001:2015',
                'url' => '/post/sistem-penjaminan-mutu-internal-meraih-sertifikasi-iso-9001-2015',
                'type' => 'post',
                'reference_id' => null,
                'parent_id' => $createdParents['Layanan'],
                'sort_order' => 5,
                'is_active' => true,
                'target' => '_self',
                'css_class' => 'nav-certification',
                'icon' => null
            ],
            [
                'title' => 'Akreditasi Program Studi',
                'url' => '/post/program-studi-pendidikan-agama-islam-meraih-akreditasi-a-dari-ban-pt',
                'type' => 'post',
                'reference_id' => null,
                'parent_id' => $createdParents['Layanan'],
                'sort_order' => 6,
                'is_active' => true,
                'target' => '_self',
                'css_class' => 'nav-accreditation',
                'icon' => null
            ]
        ];

        // Create submenus
        foreach ($subMenus as $submenu) {
            NavigationMenu::create($submenu);
        }
    }
}