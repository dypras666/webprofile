<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Post;
use App\Models\Category;
use App\Models\User;
use Carbon\Carbon;

class PostSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $user = User::first();
        $categories = Category::all()->keyBy('slug');

        $posts = [
            [
                'title' => 'Selamat Datang di LPM Institut Islam Al-Mujaddid Sabak',
                'slug' => 'selamat-datang-di-lpm-institut-islam-al-mujaddid-sabak',
                'content' => '<p>LPM Institut Islam Al-Mujaddid Sabak adalah lembaga penjaminan mutu yang berkomitmen untuk memastikan kualitas pendidikan tinggi berbasis nilai-nilai Islam. Dengan visi menjadi pusat penjaminan mutu terdepan dalam pengembangan sistem mutu pendidikan yang berlandaskan ajaran Islam, kami terus berinovasi dalam memberikan layanan terbaik bagi institusi.</p><p>Lembaga kami dilengkapi dengan sistem penjaminan mutu modern dan tenaga auditor yang berpengalaman. Kami fokus pada penjaminan mutu di berbagai aspek pendidikan dengan pendekatan Islami yang komprehensif.</p><p>Mari bergabung dengan kami untuk membangun pendidikan berkualitas!</p>',
                'excerpt' => 'LPM Institut Islam Al-Mujaddid Sabak berkomitmen memastikan kualitas pendidikan tinggi berbasis nilai Islam dengan sistem mutu modern dan tenaga auditor berpengalaman.',
                'type' => 'berita',
                'is_slider' => true,
                'is_featured' => true,
                'is_published' => true,
                'published_at' => Carbon::now()->subDays(1),
                'category_id' => $categories['berita-lpm']->id ?? 1,
                'user_id' => $user->id,
                'views' => 150,
                'sort_order' => 1
            ],
            [
                'title' => 'Sistem Penjaminan Mutu Internal Meraih Sertifikasi ISO 9001:2015',
                'slug' => 'sistem-penjaminan-mutu-internal-meraih-sertifikasi-iso-9001-2015',
                'content' => '<p>Kami dengan bangga mengumumkan bahwa Sistem Penjaminan Mutu Internal LPM Institut Islam Al-Mujaddid Sabak telah meraih sertifikasi ISO 9001:2015 dari lembaga sertifikasi internasional. Pencapaian ini merupakan hasil dari kerja keras seluruh tim LPM dalam mengintegrasikan standar mutu internasional dengan nilai-nilai Islam.</p><p>Sertifikasi ini menunjukkan bahwa sistem penjaminan mutu kami telah memenuhi standar internasional dan siap memberikan layanan berkualitas tinggi bagi institusi.</p><p>Dengan sertifikasi ini, sistem penjaminan mutu LPM akan semakin diakui dan memiliki dampak yang lebih luas bagi peningkatan mutu pendidikan.</p>',
                'excerpt' => 'Sistem Penjaminan Mutu Internal LPM Institut Islam Al-Mujaddid Sabak meraih sertifikasi ISO 9001:2015, menunjukkan kualitas sistem mutu yang unggul.',
                'type' => 'berita',
                'is_featured' => true,
                'is_published' => true,
                'published_at' => Carbon::now()->subDays(3),
                'category_id' => $categories['penjaminan-mutu']->id ?? 2,
                'user_id' => $user->id,
                'views' => 89,
                'sort_order' => 2
            ],
            [
                'title' => 'Program Studi Pendidikan Agama Islam Meraih Akreditasi A dari BAN-PT',
                'slug' => 'program-studi-pendidikan-agama-islam-meraih-akreditasi-a-dari-ban-pt',
                'content' => '<p>Dr. Ahmad Santoso, Ketua LPM Institut Islam Al-Mujaddid Sabak, mengumumkan bahwa Program Studi Pendidikan Agama Islam telah berhasil meraih akreditasi A dari BAN-PT. Pencapaian ini merupakan hasil kerja keras tim LPM dalam mempersiapkan dokumen akreditasi dan memastikan pemenuhan standar akreditasi.</p><p>Proses akreditasi ini melibatkan evaluasi menyeluruh terhadap standar pendidikan, penelitian, pengabdian masyarakat, dan tata kelola institusi. Tim asesor BAN-PT memberikan apresiasi tinggi terhadap sistem penjaminan mutu yang telah diterapkan.</p><p>Pencapaian ini semakin memperkuat reputasi Institut Islam Al-Mujaddid Sabak sebagai institusi pendidikan tinggi berkualitas.</p>',
                'excerpt' => 'Program Studi Pendidikan Agama Islam meraih akreditasi A dari BAN-PT, menunjukkan kualitas pendidikan yang unggul.',
                'type' => 'berita',
                'is_published' => true,
                'published_at' => Carbon::now()->subDays(5),
                'category_id' => $categories['akreditasi']->id ?? 3,
                'user_id' => $user->id,
                'views' => 67,
                'sort_order' => 3
            ],
            [
                'title' => 'Tim Auditor LPM Juara 1 Kompetisi Audit Mutu Internal Nasional',
                'slug' => 'tim-auditor-lpm-juara-1-kompetisi-audit-mutu-internal-nasional',
                'content' => '<p>Tim auditor LPM Institut Islam Al-Mujaddid Sabak yang terdiri dari 5 auditor berpengalaman berhasil meraih juara 1 dalam Kompetisi Audit Mutu Internal Nasional 2024. Kompetisi yang diselenggarakan di Jakarta ini diikuti oleh 50 tim dari seluruh Indonesia.</p><p>Metodologi audit yang mereka kembangkan mampu mengintegrasikan nilai-nilai Islam dalam proses audit mutu dengan tingkat efektivitas tinggi. Tim yang dipimpin oleh Dr. Sari Indrawati ini telah mempersiapkan metodologi selama 6 bulan.</p><p>Prestasi ini menambah koleksi penghargaan yang telah diraih LPM Institut Islam Al-Mujaddid Sabak di berbagai kompetisi nasional dan internasional.</p>',
                'excerpt' => 'Tim auditor LPM raih juara 1 kompetisi audit mutu internal nasional, menunjukkan keunggulan dalam audit berbasis Islam.',
                'type' => 'berita',
                'is_featured' => true,
                'is_published' => true,
                'published_at' => Carbon::now()->subDays(7),
                'category_id' => $categories['audit-mutu-internal']->id ?? 4,
                'user_id' => $user->id,
                'views' => 123,
                'sort_order' => 4
            ],
            [
                'title' => 'Workshop Penyusunan Standar Mutu untuk Dosen dan Tenaga Kependidikan',
                'slug' => 'workshop-penyusunan-standar-mutu-untuk-dosen-dan-tenaga-kependidikan',
                'content' => '<p>Tim LPM Institut Islam Al-Mujaddid Sabak melaksanakan workshop penyusunan standar mutu untuk dosen dan tenaga kependidikan. Workshop ini bertujuan membantu civitas akademika memahami dan menerapkan standar mutu dalam kegiatan akademik.</p><p>Selama 3 hari, tim memberikan pelatihan penyusunan standar mutu, implementasi sistem penjaminan mutu, dan evaluasi kinerja. Sebanyak 50 peserta telah berhasil memahami konsep penjaminan mutu secara komprehensif.</p><p>Workshop ini mendapat apresiasi dari peserta dan diharapkan dapat meningkatkan kualitas pendidikan di institusi.</p>',
                'excerpt' => 'LPM laksanakan workshop penyusunan standar mutu untuk 50 dosen dan tenaga kependidikan, meningkatkan pemahaman sistem mutu.',
                'type' => 'berita',
                'is_published' => true,
                'published_at' => Carbon::now()->subDays(10),
                'category_id' => $categories['pelatihan-workshop']->id ?? 6,
                'user_id' => $user->id,
                'views' => 45,
                'sort_order' => 5
            ],
            [
                'title' => 'Kegiatan Monitoring dan Evaluasi Implementasi Sistem Penjaminan Mutu',
                'slug' => 'kegiatan-monitoring-dan-evaluasi-implementasi-sistem-penjaminan-mutu',
                'content' => '<p>LPM Institut Islam Al-Mujaddid Sabak melaksanakan kegiatan monitoring dan evaluasi implementasi sistem penjaminan mutu di seluruh unit kerja. Kegiatan ini bertujuan memastikan efektivitas penerapan sistem mutu dan mengidentifikasi area yang perlu diperbaiki.</p><p>Program monitoring ini mencakup evaluasi standar pendidikan, penelitian, pengabdian masyarakat, dan tata kelola institusi. Tim evaluator melakukan assessment menyeluruh terhadap pencapaian indikator kinerja utama. Hasil evaluasi menunjukkan peningkatan signifikan dalam berbagai aspek mutu.</p><p>Ketua LPM menyatakan bahwa kegiatan ini merupakan bagian integral dari siklus penjaminan mutu untuk memastikan peningkatan berkelanjutan.</p>',
                'excerpt' => 'LPM laksanakan monitoring dan evaluasi sistem penjaminan mutu di seluruh unit kerja untuk memastikan peningkatan berkelanjutan.',
                'type' => 'berita',
                'is_published' => true,
                'published_at' => Carbon::now()->subDays(12),
                'category_id' => $categories['monitoring-evaluasi']->id ?? 7,
                'user_id' => $user->id,
                'views' => 78,
                'sort_order' => 6
            ],
            [
                'title' => 'Penyusunan Dokumen Standar Mutu Pendidikan Berbasis Nilai Islam',
                'slug' => 'penyusunan-dokumen-standar-mutu-pendidikan-berbasis-nilai-islam',
                'content' => '<p>Tim LPM Institut Islam Al-Mujaddid Sabak berhasil menyelesaikan penyusunan dokumen standar mutu pendidikan berbasis nilai Islam. Dokumen ini akan menjadi acuan dalam implementasi sistem penjaminan mutu di seluruh unit kerja institusi.</p><p>Dokumen standar mutu ini mencakup 8 standar nasional pendidikan tinggi yang diintegrasikan dengan nilai-nilai Islam. Setiap standar dilengkapi dengan indikator kinerja yang terukur dan dapat diaudit secara berkala.</p><p>Ketua LPM menyatakan bahwa dokumen ini merupakan inovasi dalam pengembangan standar mutu pendidikan tinggi yang sesuai dengan karakteristik institusi Islam.</p>',
                'excerpt' => 'LPM selesaikan penyusunan dokumen standar mutu pendidikan berbasis nilai Islam sebagai acuan sistem penjaminan mutu.',
                'type' => 'berita',
                'is_published' => true,
                'published_at' => Carbon::now()->subDays(15),
                'category_id' => $categories['standar-mutu']->id ?? 5,
                'user_id' => $user->id,
                'views' => 92,
                'sort_order' => 7
            ],
            [
                'title' => 'Dokumentasi Kegiatan LPM dalam Mendukung Akreditasi Institusi',
                'slug' => 'dokumentasi-kegiatan-lpm-dalam-mendukung-akreditasi-institusi',
                'content' => '<p>LPM Institut Islam Al-Mujaddid Sabak telah mendokumentasikan seluruh kegiatan dalam mendukung proses akreditasi institusi. Dokumentasi ini mencakup foto, video, dan laporan kegiatan yang menunjukkan komitmen institusi terhadap penjaminan mutu.</p><p>Dokumentasi ini meliputi kegiatan audit mutu internal, workshop penjaminan mutu, monitoring dan evaluasi, serta berbagai pelatihan yang telah dilaksanakan. Semua dokumentasi disusun secara sistematis dan dapat diakses oleh seluruh civitas akademika.</p><p>Dengan dokumentasi yang lengkap, diharapkan dapat memperkuat portofolio institusi dalam proses akreditasi dan menunjukkan konsistensi dalam implementasi sistem penjaminan mutu.</p>',
                'excerpt' => 'LPM dokumentasikan seluruh kegiatan penjaminan mutu untuk memperkuat portofolio akreditasi institusi.',
                'type' => 'berita',
                'is_slider' => true,
                'is_published' => true,
                'published_at' => Carbon::now()->subDays(18),
                'category_id' => $categories['dokumentasi']->id ?? 8,
                'user_id' => $user->id,
                'views' => 134,
                'sort_order' => 8
            ]
        ];

        foreach ($posts as $post) {
            Post::create($post);
        }
    }
}