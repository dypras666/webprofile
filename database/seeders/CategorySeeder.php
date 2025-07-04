<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Category;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = [
            [
                'name' => 'Berita Kampus',
                'slug' => 'berita-kampus',
                'description' => 'Berita terbaru seputar kegiatan dan perkembangan kampus Universitas Maju Mundur',
                'color' => '#3B82F6',
                'is_active' => true,
                'sort_order' => 1
            ],
            [
                'name' => 'Akademik',
                'slug' => 'akademik',
                'description' => 'Informasi akademik, kurikulum, dan program studi',
                'color' => '#10B981',
                'is_active' => true,
                'sort_order' => 2
            ],
            [
                'name' => 'Penelitian',
                'slug' => 'penelitian',
                'description' => 'Hasil penelitian dan publikasi ilmiah dari dosen dan mahasiswa',
                'color' => '#8B5CF6',
                'is_active' => true,
                'sort_order' => 3
            ],
            [
                'name' => 'Pengabdian Masyarakat',
                'slug' => 'pengabdian-masyarakat',
                'description' => 'Kegiatan pengabdian kepada masyarakat yang dilakukan universitas',
                'color' => '#F59E0B',
                'is_active' => true,
                'sort_order' => 4
            ],
            [
                'name' => 'Kemahasiswaan',
                'slug' => 'kemahasiswaan',
                'description' => 'Kegiatan dan prestasi mahasiswa Universitas Maju Mundur',
                'color' => '#EF4444',
                'is_active' => true,
                'sort_order' => 5
            ],
            [
                'name' => 'Alumni',
                'slug' => 'alumni',
                'description' => 'Berita dan kegiatan alumni Universitas Maju Mundur',
                'color' => '#6B7280',
                'is_active' => true,
                'sort_order' => 6
            ],
            [
                'name' => 'Kerjasama',
                'slug' => 'kerjasama',
                'description' => 'Kerjasama dengan institusi dalam dan luar negeri',
                'color' => '#14B8A6',
                'is_active' => true,
                'sort_order' => 7
            ],
            [
                'name' => 'Galeri',
                'slug' => 'galeri',
                'description' => 'Dokumentasi kegiatan dan fasilitas kampus',
                'color' => '#F97316',
                'is_active' => true,
                'sort_order' => 8
            ]
        ];

        foreach ($categories as $category) {
            Category::create($category);
        }
    }
}