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
                'title' => 'Selamat Datang di LPPM Institut Islam Al-Mujaddid Sabak',
                'slug' => 'selamat-datang-di-lppm-institut-islam-al-mujaddid-sabak',
                'content' => '<p>LPPM Institut Islam Al-Mujaddid Sabak adalah lembaga penelitian dan pengabdian masyarakat yang berkomitmen untuk mengembangkan ilmu pengetahuan berbasis nilai-nilai Islam. Dengan visi menjadi pusat penelitian terdepan dalam pengembangan ilmu pengetahuan dan teknologi yang berlandaskan ajaran Islam, kami terus berinovasi dalam memberikan kontribusi terbaik bagi masyarakat.</p><p>Lembaga kami dilengkapi dengan fasilitas penelitian modern dan tenaga peneliti yang berpengalaman. Kami fokus pada penelitian di berbagai bidang ilmu dengan pendekatan Islami yang komprehensif.</p><p>Mari bergabung dengan kami untuk membangun peradaban yang gemilang!</p>',
                'excerpt' => 'LPPM Institut Islam Al-Mujaddid Sabak berkomitmen mengembangkan ilmu pengetahuan berbasis nilai Islam dengan fasilitas modern dan tenaga peneliti berpengalaman.',
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
                'title' => 'Program Penelitian Teknologi Islami Meraih Pengakuan Nasional',
                'slug' => 'program-penelitian-teknologi-islami-meraih-pengakuan-nasional',
                'content' => '<p>Kami dengan bangga mengumumkan bahwa Program Penelitian Teknologi Islami LPPM Institut Islam Al-Mujaddid Sabak telah meraih pengakuan nasional dari Kemenristekdikti. Pencapaian ini merupakan hasil dari kerja keras seluruh tim peneliti dalam mengintegrasikan teknologi modern dengan nilai-nilai Islam.</p><p>Pengakuan ini menunjukkan bahwa program penelitian kami telah memenuhi standar nasional penelitian dan siap menghasilkan inovasi yang bermanfaat bagi umat.</p><p>Dengan pengakuan ini, hasil penelitian Program Teknologi Islami akan semakin diakui di dunia akademik dan memiliki dampak yang lebih luas bagi masyarakat.</p>',
                'excerpt' => 'Program Penelitian Teknologi Islami LPPM Institut Islam Al-Mujaddid Sabak meraih pengakuan nasional, menunjukkan kualitas penelitian yang unggul.',
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
                'title' => 'Penelitian LPPM Tentang AI Islami Dipublikasikan di Jurnal Internasional',
                'slug' => 'penelitian-lppm-tentang-ai-islami-dipublikasikan-di-jurnal-internasional',
                'content' => '<p>Dr. Ahmad Santoso, peneliti senior LPPM Institut Islam Al-Mujaddid Sabak, berhasil mempublikasikan penelitiannya tentang Artificial Intelligence berbasis nilai Islam di jurnal internasional bereputasi. Penelitian yang berjudul "Islamic Ethics in AI Development for Smart Islamic City" ini mendapat apresiasi tinggi dari komunitas ilmiah internasional.</p><p>Penelitian ini mengeksplorasi penerapan AI dengan prinsip-prinsip Islam dalam pengembangan kota cerdas, khususnya dalam sistem yang sesuai dengan syariah. Hasil penelitian menunjukkan model AI yang lebih etis dan berkelanjutan.</p><p>Publikasi ini semakin memperkuat reputasi LPPM Institut Islam Al-Mujaddid Sabak di kancah penelitian internasional.</p>',
                'excerpt' => 'Peneliti LPPM berhasil publikasikan penelitian AI Islami di jurnal internasional, memperkuat reputasi lembaga di bidang penelitian.',
                'type' => 'berita',
                'is_published' => true,
                'published_at' => Carbon::now()->subDays(5),
                'category_id' => $categories['penelitian']->id ?? 3,
                'user_id' => $user->id,
                'views' => 67,
                'sort_order' => 3
            ],
            [
                'title' => 'Tim Peneliti LPPM Juara 1 Kompetisi Inovasi Teknologi Islami Nasional',
                'slug' => 'tim-peneliti-lppm-juara-1-kompetisi-inovasi-teknologi-islami-nasional',
                'content' => '<p>Tim peneliti LPPM Institut Islam Al-Mujaddid Sabak yang terdiri dari 5 peneliti muda berhasil meraih juara 1 dalam Kompetisi Inovasi Teknologi Islami Nasional 2024. Kompetisi yang diselenggarakan di Jakarta ini diikuti oleh 50 tim dari seluruh Indonesia.</p><p>Inovasi teknologi yang mereka ciptakan mampu mengintegrasikan nilai-nilai Islam dalam solusi teknologi modern dengan tingkat efektivitas tinggi. Tim yang dipimpin oleh Dr. Sari Indrawati ini telah mempersiapkan penelitian selama 6 bulan.</p><p>Prestasi ini menambah koleksi penghargaan yang telah diraih LPPM Institut Islam Al-Mujaddid Sabak di berbagai kompetisi nasional dan internasional.</p>',
                'excerpt' => 'Tim peneliti LPPM raih juara 1 kompetisi inovasi teknologi Islami nasional, menunjukkan keunggulan dalam penelitian berbasis Islam.',
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
                'title' => 'Program Pengabdian Masyarakat: Pemberdayaan UMKM Syariah Desa Sukamaju',
                'slug' => 'program-pengabdian-masyarakat-pemberdayaan-umkm-syariah-desa-sukamaju',
                'content' => '<p>Tim peneliti LPPM Institut Islam Al-Mujaddid Sabak melaksanakan program pengabdian masyarakat berupa pemberdayaan UMKM berbasis syariah di Desa Sukamaju. Program ini bertujuan membantu pelaku UMKM mengembangkan bisnis sesuai prinsip Islam.</p><p>Selama 3 bulan, tim memberikan pelatihan bisnis syariah, manajemen keuangan Islam, dan pemasaran halal. Sebanyak 25 UMKM telah berhasil menerapkan prinsip syariah dalam operasional bisnisnya.</p><p>Program ini mendapat apresiasi dari pemerintah daerah dan MUI setempat, serta diharapkan dapat direplikasi di desa-desa lainnya.</p>',
                'excerpt' => 'LPPM laksanakan program pemberdayaan UMKM syariah di Desa Sukamaju, membantu 25 UMKM menerapkan prinsip Islam.',
                'type' => 'berita',
                'is_published' => true,
                'published_at' => Carbon::now()->subDays(10),
                'category_id' => $categories['pengabdian-masyarakat']->id ?? 4,
                'user_id' => $user->id,
                'views' => 45,
                'sort_order' => 5
            ],
            [
                'title' => 'Kerjasama dengan Islamic University of Madinah untuk Program Penelitian Bersama',
                'slug' => 'kerjasama-dengan-islamic-university-of-madinah-untuk-program-penelitian-bersama',
                'content' => '<p>LPPM Institut Islam Al-Mujaddid Sabak menandatangani MoU dengan Islamic University of Madinah untuk program penelitian bersama dan pertukaran peneliti. Kerjasama ini membuka peluang bagi peneliti LPPM untuk melakukan riset di Arab Saudi.</p><p>Program kerjasama ini mencakup bidang studi Islam, teknologi Islami, dan pengembangan masyarakat Muslim. Peneliti yang terpilih akan mendapat dukungan penuh dari kedua lembaga. Selain itu, akan ada program publikasi bersama hasil penelitian.</p><p>Direktur LPPM menyatakan bahwa kerjasama ini merupakan langkah strategis untuk meningkatkan kualitas penelitian dan memperluas jaringan akademik internasional.</p>',
                'excerpt' => 'LPPM jalin kerjasama dengan Islamic University of Madinah untuk program penelitian bersama dan pertukaran peneliti.',
                'type' => 'berita',
                'is_published' => true,
                'published_at' => Carbon::now()->subDays(12),
                'category_id' => $categories['kerjasama']->id ?? 7,
                'user_id' => $user->id,
                'views' => 78,
                'sort_order' => 6
            ],
            [
                'title' => 'Alumni Peneliti LPPM Raih Penghargaan Inovator Teknologi Islami Terbaik',
                'slug' => 'alumni-peneliti-lppm-raih-penghargaan-inovator-teknologi-islami-terbaik',
                'content' => '<p>Budi Hartono, alumni peneliti LPPM Institut Islam Al-Mujaddid Sabak, meraih penghargaan Inovator Teknologi Islami Terbaik 2024 dari Kementerian Agama. Penghargaan ini diberikan atas keberhasilannya mengembangkan platform teknologi finansial syariah.</p><p>Platform yang didirikan Budi telah membantu lebih dari 10.000 UMKM Muslim dalam mengakses pembiayaan syariah. Teknologi yang dikembangkannya menggunakan AI untuk analisis risiko sesuai prinsip Islam.</p><p>Budi mengaku bahwa pengalaman penelitiannya di LPPM, khususnya dalam bidang ekonomi Islam dan teknologi, sangat membantu dalam mengembangkan inovasinya.</p>',
                'excerpt' => 'Alumni peneliti LPPM Budi Hartono raih penghargaan Inovator Teknologi Islami Terbaik 2024 dengan platform fintech syariah.',
                'type' => 'berita',
                'is_published' => true,
                'published_at' => Carbon::now()->subDays(15),
                'category_id' => $categories['alumni']->id ?? 6,
                'user_id' => $user->id,
                'views' => 92,
                'sort_order' => 7
            ],
            [
                'title' => 'Fasilitas Laboratorium Penelitian Islami Terbaru untuk LPPM',
                'slug' => 'fasilitas-laboratorium-penelitian-islami-terbaru-untuk-lppm',
                'content' => '<p>LPPM Institut Islam Al-Mujaddid Sabak meresmikan laboratorium penelitian Islami terbaru dengan investasi Rp 5 miliar. Laboratorium ini dilengkapi dengan peralatan canggih untuk mendukung penelitian di bidang sains Islam, teknologi halal, dan inovasi berbasis syariah.</p><p>Fasilitas baru ini mencakup laboratorium analisis halal, laboratorium teknologi Islami, dan laboratorium riset sosial keislaman. Semua peralatan menggunakan teknologi terkini yang memungkinkan penelitian berkualitas internasional dengan pendekatan Islam.</p><p>Dengan fasilitas ini, diharapkan kualitas penelitian LPPM akan semakin meningkat dan mampu memberikan kontribusi nyata bagi pengembangan ilmu pengetahuan Islam.</p>',
                'excerpt' => 'LPPM resmikan laboratorium penelitian Islami senilai Rp 5 miliar dengan peralatan canggih untuk riset berbasis Islam.',
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