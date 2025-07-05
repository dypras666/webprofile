<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Post;
use App\Models\User;
use App\Models\Category;
use Carbon\Carbon;

class GallerySeeder extends Seeder
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

        $galleries = [
            [
                'title' => 'Kegiatan Audit Mutu Internal 2024',
                'slug' => 'kegiatan-audit-mutu-internal-2024',
                'content' => '<p>Dokumentasi kegiatan Audit Mutu Internal yang dilaksanakan oleh LPM Institut Islam Al-Mujaddid Sabak pada tahun 2024. Kegiatan ini melibatkan seluruh unit kerja di lingkungan institusi untuk memastikan implementasi sistem penjaminan mutu berjalan dengan baik.</p><p>Audit dilaksanakan selama 3 hari dengan melibatkan tim auditor internal yang telah tersertifikasi. Proses audit mencakup verifikasi dokumen, wawancara dengan pimpinan unit, dan observasi langsung implementasi standar mutu.</p>',
                'excerpt' => 'Dokumentasi lengkap kegiatan Audit Mutu Internal 2024 di Institut Islam Al-Mujaddid Sabak.',
                'type' => 'gallery',
                'gallery_images' => json_encode([
                    'images/gallery/ami-2024-1.jpg',
                    'images/gallery/ami-2024-2.jpg',
                    'images/gallery/ami-2024-3.jpg',
                    'images/gallery/ami-2024-4.jpg',
                    'images/gallery/ami-2024-5.jpg',
                    'images/gallery/ami-2024-6.jpg'
                ]),
                'is_published' => true,
                'published_at' => Carbon::now()->subDays(5),
                'category_id' => $categories['audit-mutu-internal'] ?? null,
                'user_id' => $user->id,
                'views' => 187,
                'sort_order' => 1
            ],
            [
                'title' => 'Workshop Penyusunan Standar Mutu',
                'slug' => 'workshop-penyusunan-standar-mutu',
                'content' => '<p>Galeri foto workshop penyusunan standar mutu yang diselenggarakan oleh LPM Institut Islam Al-Mujaddid Sabak. Workshop ini dihadiri oleh perwakilan dari seluruh fakultas dan unit kerja untuk membahas pengembangan standar mutu yang sesuai dengan karakteristik institusi Islam.</p><p>Kegiatan berlangsung selama 2 hari dengan narasumber dari BAN-PT dan praktisi penjaminan mutu dari perguruan tinggi terkemuka. Peserta mendapat pemahaman mendalam tentang penyusunan standar mutu yang efektif.</p>',
                'excerpt' => 'Dokumentasi workshop penyusunan standar mutu pendidikan berbasis nilai Islam.',
                'type' => 'gallery',
                'gallery_images' => json_encode([
                    'images/gallery/workshop-standar-1.jpg',
                    'images/gallery/workshop-standar-2.jpg',
                    'images/gallery/workshop-standar-3.jpg',
                    'images/gallery/workshop-standar-4.jpg',
                    'images/gallery/workshop-standar-5.jpg'
                ]),
                'is_published' => true,
                'published_at' => Carbon::now()->subDays(12),
                'category_id' => $categories['pelatihan-workshop'] ?? null,
                'user_id' => $user->id,
                'views' => 143,
                'sort_order' => 2
            ],
            [
                'title' => 'Sertifikasi ISO 9001:2015',
                'slug' => 'sertifikasi-iso-9001-2015',
                'content' => '<p>Momen bersejarah penyerahan sertifikat ISO 9001:2015 kepada Institut Islam Al-Mujaddid Sabak. Pencapaian ini merupakan hasil kerja keras seluruh civitas akademika dalam mengimplementasikan sistem manajemen mutu yang efektif.</p><p>Proses sertifikasi melibatkan audit eksternal yang ketat dari lembaga sertifikasi internasional. LPM berperan penting dalam mempersiapkan dokumentasi dan memastikan kesiapan institusi menghadapi audit sertifikasi.</p>',
                'excerpt' => 'Dokumentasi penyerahan sertifikat ISO 9001:2015 untuk Institut Islam Al-Mujaddid Sabak.',
                'type' => 'gallery',
                'gallery_images' => json_encode([
                    'images/gallery/iso-sertifikat-1.jpg',
                    'images/gallery/iso-sertifikat-2.jpg',
                    'images/gallery/iso-sertifikat-3.jpg',
                    'images/gallery/iso-sertifikat-4.jpg'
                ]),
                'is_featured' => true,
                'is_published' => true,
                'published_at' => Carbon::now()->subDays(20),
                'category_id' => $categories['penjaminan-mutu'] ?? null,
                'user_id' => $user->id,
                'views' => 298,
                'sort_order' => 3
            ],
            [
                'title' => 'Pelatihan Auditor Internal',
                'slug' => 'pelatihan-auditor-internal',
                'content' => '<p>Galeri kegiatan pelatihan auditor internal yang diselenggarakan untuk meningkatkan kompetensi tim audit mutu internal. Pelatihan ini bertujuan untuk mempersiapkan auditor yang kompeten dalam melaksanakan audit mutu internal sesuai standar ISO 19011.</p><p>Peserta pelatihan terdiri dari dosen dan tenaga kependidikan dari berbagai unit kerja. Materi pelatihan mencakup teknik audit, penyusunan checklist, dan praktik audit langsung.</p>',
                'excerpt' => 'Dokumentasi pelatihan auditor internal untuk meningkatkan kompetensi tim audit mutu.',
                'type' => 'gallery',
                'gallery_images' => json_encode([
                    'images/gallery/pelatihan-auditor-1.jpg',
                    'images/gallery/pelatihan-auditor-2.jpg',
                    'images/gallery/pelatihan-auditor-3.jpg',
                    'images/gallery/pelatihan-auditor-4.jpg',
                    'images/gallery/pelatihan-auditor-5.jpg',
                    'images/gallery/pelatihan-auditor-6.jpg'
                ]),
                'is_published' => true,
                'published_at' => Carbon::now()->subDays(30),
                'category_id' => $categories['pelatihan-workshop'] ?? null,
                'user_id' => $user->id,
                'views' => 165,
                'sort_order' => 4
            ],
            [
                'title' => 'Monitoring dan Evaluasi Program Studi',
                'slug' => 'monitoring-evaluasi-program-studi',
                'content' => '<p>Dokumentasi kegiatan monitoring dan evaluasi yang dilakukan LPM terhadap seluruh program studi di Institut Islam Al-Mujaddid Sabak. Kegiatan ini bertujuan untuk memastikan pencapaian standar mutu dan kesiapan menghadapi akreditasi.</p><p>Tim monitoring melakukan kunjungan ke setiap program studi untuk mengevaluasi implementasi kurikulum, kualitas pembelajaran, dan pencapaian capaian pembelajaran lulusan.</p>',
                'excerpt' => 'Galeri kegiatan monitoring dan evaluasi program studi oleh tim LPM.',
                'type' => 'gallery',
                'gallery_images' => json_encode([
                    'images/gallery/monev-prodi-1.jpg',
                    'images/gallery/monev-prodi-2.jpg',
                    'images/gallery/monev-prodi-3.jpg',
                    'images/gallery/monev-prodi-4.jpg'
                ]),
                'is_published' => true,
                'published_at' => Carbon::now()->subDays(45),
                'category_id' => $categories['monitoring-evaluasi'] ?? null,
                'user_id' => $user->id,
                'views' => 134,
                'sort_order' => 5
            ],
            [
                'title' => 'Rapat Koordinasi LPM',
                'slug' => 'rapat-koordinasi-lpm',
                'content' => '<p>Dokumentasi rapat koordinasi rutin LPM Institut Islam Al-Mujaddid Sabak dengan seluruh unit kerja. Rapat ini membahas progress implementasi sistem penjaminan mutu, kendala yang dihadapi, dan strategi perbaikan ke depan.</p><p>Rapat dihadiri oleh pimpinan institusi, dekan fakultas, ketua program studi, dan kepala unit kerja untuk memastikan sinergi dalam implementasi sistem mutu.</p>',
                'excerpt' => 'Galeri rapat koordinasi LPM dengan seluruh unit kerja di institusi.',
                'type' => 'gallery',
                'gallery_images' => json_encode([
                    'images/gallery/rapat-koordinasi-1.jpg',
                    'images/gallery/rapat-koordinasi-2.jpg',
                    'images/gallery/rapat-koordinasi-3.jpg'
                ]),
                'is_published' => true,
                'published_at' => Carbon::now()->subDays(60),
                'category_id' => $categories['dokumentasi'] ?? null,
                'user_id' => $user->id,
                'views' => 98,
                'sort_order' => 6
            ]
        ];

        foreach ($galleries as $gallery) {
            Post::create($gallery);
        }
    }
}