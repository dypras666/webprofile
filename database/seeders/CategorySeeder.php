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
                'name' => 'Berita LPM',
                'slug' => 'berita-lpm',
                'description' => 'Berita terbaru seputar kegiatan dan perkembangan Lembaga Penjaminan Mutu Institut Islam Al-Mujaddid Sabak',
                'color' => '#3B82F6',
                'is_active' => true,
                'sort_order' => 1
            ],
            [
                'name' => 'Penjaminan Mutu',
                'slug' => 'penjaminan-mutu',
                'description' => 'Informasi sistem penjaminan mutu internal dan eksternal',
                'color' => '#10B981',
                'is_active' => true,
                'sort_order' => 2
            ],
            [
                'name' => 'Akreditasi',
                'slug' => 'akreditasi',
                'description' => 'Informasi dan proses akreditasi program studi dan institusi',
                'color' => '#8B5CF6',
                'is_active' => true,
                'sort_order' => 3
            ],
            [
                'name' => 'Audit Mutu Internal',
                'slug' => 'audit-mutu-internal',
                'description' => 'Kegiatan audit mutu internal dan evaluasi diri institusi',
                'color' => '#F59E0B',
                'is_active' => true,
                'sort_order' => 4
            ],
            [
                'name' => 'Standar Mutu',
                'slug' => 'standar-mutu',
                'description' => 'Dokumen standar mutu dan kebijakan penjaminan mutu',
                'color' => '#EF4444',
                'is_active' => true,
                'sort_order' => 5
            ],
            [
                'name' => 'Pelatihan & Workshop',
                'slug' => 'pelatihan-workshop',
                'description' => 'Kegiatan pelatihan dan workshop terkait penjaminan mutu',
                'color' => '#6B7280',
                'is_active' => true,
                'sort_order' => 6
            ],
            [
                'name' => 'Monitoring & Evaluasi',
                'slug' => 'monitoring-evaluasi',
                'description' => 'Kegiatan monitoring dan evaluasi pelaksanaan penjaminan mutu',
                'color' => '#14B8A6',
                'is_active' => true,
                'sort_order' => 7
            ],
            [
                'name' => 'Dokumentasi',
                'slug' => 'dokumentasi',
                'description' => 'Dokumentasi kegiatan dan pencapaian LPM',
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