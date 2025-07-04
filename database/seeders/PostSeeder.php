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
                'title' => 'Selamat Datang di Universitas Maju Mundur',
                'slug' => 'selamat-datang-di-universitas-maju-mundur',
                'content' => '<p>Universitas Maju Mundur adalah institusi pendidikan tinggi yang berkomitmen untuk menghasilkan lulusan berkualitas dan berdaya saing global. Dengan visi menjadi universitas terdepan dalam pengembangan ilmu pengetahuan dan teknologi, kami terus berinovasi dalam memberikan pendidikan terbaik.</p><p>Kampus kami dilengkapi dengan fasilitas modern dan tenaga pengajar yang berpengalaman. Kami menawarkan berbagai program studi dari jenjang sarjana hingga doktor di berbagai bidang ilmu.</p><p>Mari bergabung dengan kami untuk meraih masa depan yang gemilang!</p>',
                'excerpt' => 'Universitas Maju Mundur berkomitmen menghasilkan lulusan berkualitas dengan fasilitas modern dan tenaga pengajar berpengalaman.',
                'type' => 'berita',
                'is_slider' => true,
                'is_featured' => true,
                'is_published' => true,
                'published_at' => Carbon::now()->subDays(1),
                'category_id' => $categories['berita-kampus']->id ?? 1,
                'user_id' => $user->id,
                'views' => 150,
                'sort_order' => 1
            ],
            [
                'title' => 'Program Studi Teknik Informatika Meraih Akreditasi A',
                'slug' => 'program-studi-teknik-informatika-meraih-akreditasi-a',
                'content' => '<p>Kami dengan bangga mengumumkan bahwa Program Studi Teknik Informatika Universitas Maju Mundur telah meraih akreditasi A dari BAN-PT. Pencapaian ini merupakan hasil dari kerja keras seluruh civitas akademika dalam meningkatkan kualitas pendidikan.</p><p>Akreditasi A ini menunjukkan bahwa program studi kami telah memenuhi standar nasional pendidikan tinggi dan siap menghasilkan lulusan yang kompeten di bidang teknologi informasi.</p><p>Dengan akreditasi ini, lulusan Program Studi Teknik Informatika akan semakin diakui di dunia kerja dan memiliki peluang karir yang lebih luas.</p>',
                'excerpt' => 'Program Studi Teknik Informatika Universitas Maju Mundur meraih akreditasi A dari BAN-PT, menunjukkan kualitas pendidikan yang unggul.',
                'type' => 'berita',
                'is_featured' => true,
                'is_published' => true,
                'published_at' => Carbon::now()->subDays(3),
                'category_id' => $categories['akademik']->id ?? 2,
                'user_id' => $user->id,
                'views' => 89,
                'sort_order' => 2
            ],
            [
                'title' => 'Penelitian Dosen UMM Tentang AI Dipublikasikan di Jurnal Internasional',
                'slug' => 'penelitian-dosen-umm-tentang-ai-dipublikasikan-di-jurnal-internasional',
                'content' => '<p>Dr. Ahmad Santoso, dosen Fakultas Teknik Universitas Maju Mundur, berhasil mempublikasikan penelitiannya tentang Artificial Intelligence di jurnal internasional bereputasi. Penelitian yang berjudul "Machine Learning Applications in Smart City Development" ini mendapat apresiasi tinggi dari komunitas ilmiah internasional.</p><p>Penelitian ini mengeksplorasi penerapan machine learning dalam pengembangan smart city, khususnya dalam optimalisasi sistem transportasi dan manajemen energi. Hasil penelitian menunjukkan peningkatan efisiensi hingga 30% dalam sistem yang diuji.</p><p>Publikasi ini semakin memperkuat reputasi Universitas Maju Mundur di kancah penelitian internasional.</p>',
                'excerpt' => 'Dosen UMM berhasil publikasikan penelitian AI di jurnal internasional, memperkuat reputasi universitas di bidang penelitian.',
                'type' => 'berita',
                'is_published' => true,
                'published_at' => Carbon::now()->subDays(5),
                'category_id' => $categories['penelitian']->id ?? 3,
                'user_id' => $user->id,
                'views' => 67,
                'sort_order' => 3
            ],
            [
                'title' => 'Mahasiswa UMM Juara 1 Kompetisi Robotika Nasional',
                'slug' => 'mahasiswa-umm-juara-1-kompetisi-robotika-nasional',
                'content' => '<p>Tim robotika Universitas Maju Mundur yang terdiri dari 5 mahasiswa Teknik Elektro berhasil meraih juara 1 dalam Kompetisi Robotika Nasional 2024. Kompetisi yang diselenggarakan di Jakarta ini diikuti oleh 50 tim dari seluruh Indonesia.</p><p>Robot yang mereka ciptakan mampu menyelesaikan berbagai tantangan dengan tingkat akurasi tinggi. Tim yang dibimbing oleh Dr. Sari Indrawati ini telah mempersiapkan diri selama 6 bulan untuk kompetisi tersebut.</p><p>Prestasi ini menambah koleksi penghargaan yang telah diraih mahasiswa Universitas Maju Mundur di berbagai kompetisi nasional dan internasional.</p>',
                'excerpt' => 'Tim robotika UMM raih juara 1 kompetisi nasional, menunjukkan keunggulan mahasiswa di bidang teknologi.',
                'type' => 'berita',
                'is_featured' => true,
                'is_published' => true,
                'published_at' => Carbon::now()->subDays(7),
                'category_id' => $categories['kemahasiswaan']->id ?? 5,
                'user_id' => $user->id,
                'views' => 123,
                'sort_order' => 4
            ],
            [
                'title' => 'Program Pengabdian Masyarakat: Digitalisasi UMKM Desa Sukamaju',
                'slug' => 'program-pengabdian-masyarakat-digitalisasi-umkm-desa-sukamaju',
                'content' => '<p>Tim dosen dan mahasiswa Universitas Maju Mundur melaksanakan program pengabdian masyarakat berupa digitalisasi UMKM di Desa Sukamaju. Program ini bertujuan membantu pelaku UMKM memasarkan produknya secara online.</p><p>Selama 3 bulan, tim memberikan pelatihan pembuatan website, penggunaan media sosial untuk pemasaran, dan manajemen keuangan digital. Sebanyak 25 UMKM telah berhasil memiliki platform digital untuk memasarkan produknya.</p><p>Program ini mendapat apresiasi dari pemerintah daerah dan diharapkan dapat direplikasi di desa-desa lainnya.</p>',
                'excerpt' => 'UMM laksanakan program digitalisasi UMKM di Desa Sukamaju, membantu 25 UMKM go digital.',
                'type' => 'berita',
                'is_published' => true,
                'published_at' => Carbon::now()->subDays(10),
                'category_id' => $categories['pengabdian-masyarakat']->id ?? 4,
                'user_id' => $user->id,
                'views' => 45,
                'sort_order' => 5
            ],
            [
                'title' => 'Kerjasama dengan Universitas Tokyo untuk Program Pertukaran Mahasiswa',
                'slug' => 'kerjasama-dengan-universitas-tokyo-untuk-program-pertukaran-mahasiswa',
                'content' => '<p>Universitas Maju Mundur menandatangani MoU dengan Universitas Tokyo untuk program pertukaran mahasiswa dan dosen. Kerjasama ini membuka peluang bagi mahasiswa UMM untuk belajar di Jepang selama satu semester.</p><p>Program pertukaran ini mencakup bidang teknologi, sains, dan budaya. Mahasiswa yang terpilih akan mendapat beasiswa penuh dari kedua universitas. Selain itu, akan ada program penelitian bersama antara dosen dari kedua institusi.</p><p>Rektor UMM menyatakan bahwa kerjasama ini merupakan langkah strategis untuk meningkatkan kualitas pendidikan dan memperluas wawasan global mahasiswa.</p>',
                'excerpt' => 'UMM jalin kerjasama dengan Universitas Tokyo untuk program pertukaran mahasiswa dan penelitian bersama.',
                'type' => 'berita',
                'is_published' => true,
                'published_at' => Carbon::now()->subDays(12),
                'category_id' => $categories['kerjasama']->id ?? 7,
                'user_id' => $user->id,
                'views' => 78,
                'sort_order' => 6
            ],
            [
                'title' => 'Alumni UMM Raih Penghargaan Entrepreneur Muda Terbaik',
                'slug' => 'alumni-umm-raih-penghargaan-entrepreneur-muda-terbaik',
                'content' => '<p>Budi Hartono, alumni Fakultas Ekonomi Universitas Maju Mundur angkatan 2018, meraih penghargaan Entrepreneur Muda Terbaik 2024 dari Kementerian Koperasi dan UKM. Penghargaan ini diberikan atas keberhasilannya mengembangkan startup teknologi finansial.</p><p>Startup yang didirikan Budi telah membantu lebih dari 10.000 UMKM dalam mengakses pembiayaan. Platform yang dikembangkannya menggunakan teknologi AI untuk analisis risiko kredit mikro.</p><p>Budi mengaku bahwa pendidikan yang diterimanya di UMM, khususnya mata kuliah kewirausahaan dan teknologi, sangat membantu dalam mengembangkan bisnisnya.</p>',
                'excerpt' => 'Alumni UMM Budi Hartono raih penghargaan Entrepreneur Muda Terbaik 2024 dengan startup fintech-nya.',
                'type' => 'berita',
                'is_published' => true,
                'published_at' => Carbon::now()->subDays(15),
                'category_id' => $categories['alumni']->id ?? 6,
                'user_id' => $user->id,
                'views' => 92,
                'sort_order' => 7
            ],
            [
                'title' => 'Fasilitas Laboratorium Baru untuk Fakultas Sains',
                'slug' => 'fasilitas-laboratorium-baru-untuk-fakultas-sains',
                'content' => '<p>Universitas Maju Mundur meresmikan laboratorium baru untuk Fakultas Sains dengan investasi Rp 5 miliar. Laboratorium ini dilengkapi dengan peralatan canggih untuk mendukung penelitian di bidang kimia, biologi, dan fisika.</p><p>Fasilitas baru ini mencakup laboratorium analitik, laboratorium mikrobiologi, dan laboratorium fisika material. Semua peralatan menggunakan teknologi terkini yang memungkinkan penelitian berkualitas internasional.</p><p>Dengan fasilitas ini, diharapkan kualitas penelitian dan pembelajaran di Fakultas Sains akan semakin meningkat dan mampu bersaing di tingkat internasional.</p>',
                'excerpt' => 'UMM resmikan laboratorium baru senilai Rp 5 miliar untuk Fakultas Sains dengan peralatan canggih.',
                'type' => 'berita',
                'is_slider' => true,
                'is_published' => true,
                'published_at' => Carbon::now()->subDays(18),
                'category_id' => $categories['berita-kampus']->id ?? 1,
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