<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Post;
use App\Models\User;
use App\Models\Category;
use Carbon\Carbon;

class VideoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get the first admin user
        $user = User::first();
        
        // Get categories
        $categories = Category::pluck('id', 'slug')->toArray();

        $videos = [
            [
                'title' => 'Profil Lembaga Penjaminan Mutu Institut Islam Al-Mujaddid Sabak',
                'slug' => 'profil-lembaga-penjaminan-mutu-institut-islam-al-mujaddid-sabak',
                'content' => '<p>Video profil Lembaga Penjaminan Mutu Institut Islam Al-Mujaddid Sabak yang menampilkan visi, misi, struktur organisasi, dan berbagai kegiatan yang telah dilaksanakan dalam rangka meningkatkan mutu pendidikan tinggi.</p><p>Video ini menjelaskan peran strategis LPM dalam mengembangkan sistem penjaminan mutu internal yang efektif dan berkelanjutan, serta komitmen institusi terhadap peningkatan kualitas pendidikan berbasis nilai-nilai Islam.</p>',
                'excerpt' => 'Video profil lengkap Lembaga Penjaminan Mutu Institut Islam Al-Mujaddid Sabak.',
                'type' => 'video',
                'video_url' => 'https://www.youtube.com/watch?v=dQw4w9WgXcQ',
                'is_featured' => true,
                'is_published' => true,
                'published_at' => Carbon::now()->subDays(3),
                'category_id' => $categories['berita-lpm'] ?? null,
                'user_id' => $user->id,
                'views' => 456,
                'sort_order' => 1
            ],
            [
                'title' => 'Tutorial Sistem Penjaminan Mutu Internal',
                'slug' => 'tutorial-sistem-penjaminan-mutu-internal',
                'content' => '<p>Video tutorial komprehensif tentang sistem penjaminan mutu internal di Institut Islam Al-Mujaddid Sabak. Video ini menjelaskan langkah-langkah implementasi SPMI, mulai dari penetapan standar hingga evaluasi dan perbaikan berkelanjutan.</p><p>Tutorial ini sangat bermanfaat bagi unit kerja yang ingin memahami lebih dalam tentang implementasi sistem penjaminan mutu sesuai dengan standar nasional pendidikan tinggi.</p>',
                'excerpt' => 'Video tutorial lengkap implementasi sistem penjaminan mutu internal.',
                'type' => 'video',
                'video_url' => 'https://www.youtube.com/watch?v=dQw4w9WgXcQ',
                'is_published' => true,
                'published_at' => Carbon::now()->subDays(10),
                'category_id' => $categories['penjaminan-mutu'] ?? null,
                'user_id' => $user->id,
                'views' => 324,
                'sort_order' => 2
            ],
            [
                'title' => 'Webinar Persiapan Akreditasi Program Studi',
                'slug' => 'webinar-persiapan-akreditasi-program-studi',
                'content' => '<p>Rekaman webinar tentang persiapan akreditasi program studi yang diselenggarakan oleh LPM Institut Islam Al-Mujaddid Sabak. Webinar ini menghadirkan narasumber dari BAN-PT dan praktisi akreditasi berpengalaman.</p><p>Materi webinar mencakup strategi persiapan akreditasi, penyusunan dokumen akreditasi, dan tips menghadapi visitasi asesor. Video ini sangat bermanfaat bagi program studi yang akan menghadapi akreditasi.</p>',
                'excerpt' => 'Rekaman webinar persiapan akreditasi program studi dengan narasumber ahli.',
                'type' => 'video',
                'video_url' => 'https://www.youtube.com/watch?v=dQw4w9WgXcQ',
                'is_published' => true,
                'published_at' => Carbon::now()->subDays(18),
                'category_id' => $categories['akreditasi'] ?? null,
                'user_id' => $user->id,
                'views' => 278,
                'sort_order' => 3
            ],
            [
                'title' => 'Dokumenter Audit Mutu Internal 2024',
                'slug' => 'dokumenter-audit-mutu-internal-2024',
                'content' => '<p>Film dokumenter tentang pelaksanaan Audit Mutu Internal tahun 2024 di Institut Islam Al-Mujaddid Sabak. Video ini menampilkan proses audit dari tahap persiapan hingga pelaporan hasil audit.</p><p>Dokumenter ini memberikan gambaran nyata tentang bagaimana audit mutu internal dilaksanakan, tantangan yang dihadapi, dan manfaat yang diperoleh dari kegiatan audit untuk peningkatan mutu institusi.</p>',
                'excerpt' => 'Film dokumenter pelaksanaan Audit Mutu Internal 2024 di Institut Islam Al-Mujaddid Sabak.',
                'type' => 'video',
                'video_url' => 'https://www.youtube.com/watch?v=dQw4w9WgXcQ',
                'is_published' => true,
                'published_at' => Carbon::now()->subDays(25),
                'category_id' => $categories['audit-mutu-internal'] ?? null,
                'user_id' => $user->id,
                'views' => 189,
                'sort_order' => 4
            ],
            [
                'title' => 'Workshop Pengembangan Standar Mutu Berbasis Islam',
                'slug' => 'workshop-pengembangan-standar-mutu-berbasis-islam',
                'content' => '<p>Rekaman workshop pengembangan standar mutu pendidikan berbasis nilai-nilai Islam yang diselenggarakan oleh LPM Institut Islam Al-Mujaddid Sabak. Workshop ini membahas integrasi nilai-nilai Islam dalam sistem penjaminan mutu.</p><p>Narasumber workshop adalah pakar pendidikan Islam dan praktisi penjaminan mutu dari perguruan tinggi Islam terkemuka. Materi workshop sangat relevan untuk pengembangan standar mutu yang sesuai dengan karakteristik institusi Islam.</p>',
                'excerpt' => 'Rekaman workshop pengembangan standar mutu pendidikan berbasis nilai Islam.',
                'type' => 'video',
                'video_url' => 'https://www.youtube.com/watch?v=dQw4w9WgXcQ',
                'is_published' => true,
                'published_at' => Carbon::now()->subDays(35),
                'category_id' => $categories['standar-mutu'] ?? null,
                'user_id' => $user->id,
                'views' => 156,
                'sort_order' => 5
            ],
            [
                'title' => 'Pelatihan Monitoring dan Evaluasi Mutu',
                'slug' => 'pelatihan-monitoring-evaluasi-mutu',
                'content' => '<p>Video pelatihan monitoring dan evaluasi mutu yang diselenggarakan untuk meningkatkan kapasitas tim LPM dan unit kerja dalam melakukan monitoring dan evaluasi sistem penjaminan mutu.</p><p>Pelatihan ini mencakup teknik monitoring yang efektif, penyusunan instrumen evaluasi, analisis data mutu, dan penyusunan laporan monitoring dan evaluasi yang komprehensif.</p>',
                'excerpt' => 'Video pelatihan monitoring dan evaluasi mutu untuk tim LPM dan unit kerja.',
                'type' => 'video',
                'video_url' => 'https://www.youtube.com/watch?v=dQw4w9WgXcQ',
                'is_published' => true,
                'published_at' => Carbon::now()->subDays(42),
                'category_id' => $categories['monitoring-evaluasi'] ?? null,
                'user_id' => $user->id,
                'views' => 134,
                'sort_order' => 6
            ],
            [
                'title' => 'Testimoni Stakeholder tentang Mutu Pendidikan',
                'slug' => 'testimoni-stakeholder-tentang-mutu-pendidikan',
                'content' => '<p>Video testimoni dari berbagai stakeholder tentang mutu pendidikan di Institut Islam Al-Mujaddid Sabak. Video ini menampilkan testimoni dari mahasiswa, alumni, dosen, tenaga kependidikan, dan mitra industri.</p><p>Testimoni ini memberikan gambaran nyata tentang dampak implementasi sistem penjaminan mutu terhadap peningkatan kualitas pendidikan dan kepuasan stakeholder.</p>',
                'excerpt' => 'Video testimoni stakeholder tentang mutu pendidikan di Institut Islam Al-Mujaddid Sabak.',
                'type' => 'video',
                'video_url' => 'https://www.youtube.com/watch?v=dQw4w9WgXcQ',
                'is_published' => true,
                'published_at' => Carbon::now()->subDays(50),
                'category_id' => $categories['dokumentasi'] ?? null,
                'user_id' => $user->id,
                'views' => 98,
                'sort_order' => 7
            ],
            [
                'title' => 'Sosialisasi Sistem Manajemen Mutu ISO 9001:2015',
                'slug' => 'sosialisasi-sistem-manajemen-mutu-iso-9001-2015',
                'content' => '<p>Video sosialisasi sistem manajemen mutu ISO 9001:2015 yang telah diterapkan di Institut Islam Al-Mujaddid Sabak. Video ini menjelaskan prinsip-prinsip ISO 9001:2015 dan implementasinya dalam konteks pendidikan tinggi.</p><p>Sosialisasi ini penting untuk memastikan seluruh civitas akademika memahami dan dapat mengimplementasikan sistem manajemen mutu ISO 9001:2015 dalam aktivitas sehari-hari.</p>',
                'excerpt' => 'Video sosialisasi sistem manajemen mutu ISO 9001:2015 untuk civitas akademika.',
                'type' => 'video',
                'video_url' => 'https://www.youtube.com/watch?v=dQw4w9WgXcQ',
                'is_published' => true,
                'published_at' => Carbon::now()->subDays(60),
                'category_id' => $categories['penjaminan-mutu'] ?? null,
                'user_id' => $user->id,
                'views' => 167,
                'sort_order' => 8
            ]
        ];

        foreach ($videos as $video) {
            Post::create($video);
        }
    }
}