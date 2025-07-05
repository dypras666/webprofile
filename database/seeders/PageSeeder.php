<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Post;
use App\Models\User;
use Carbon\Carbon;

class PageSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get the first admin user
        $user = User::first();

        $pages = [
            [
                'title' => 'Tentang LPM Institut Islam Al-Mujaddid Sabak',
                'slug' => 'tentang-lpm-institut-islam-al-mujaddid-sabak',
                'content' => '<h2>Sejarah dan Visi</h2><p>Lembaga Penjaminan Mutu (LPM) Institut Islam Al-Mujaddid Sabak didirikan sebagai unit yang bertanggung jawab dalam memastikan kualitas pendidikan tinggi yang sesuai dengan standar nasional dan nilai-nilai Islam.</p><h2>Visi</h2><p>Menjadi lembaga penjaminan mutu terdepan dalam mengembangkan sistem mutu pendidikan tinggi berbasis nilai-nilai Islam yang unggul dan berkelanjutan.</p><h2>Misi</h2><ul><li>Mengembangkan sistem penjaminan mutu internal yang efektif</li><li>Melaksanakan audit mutu internal secara berkala</li><li>Memfasilitasi peningkatan mutu akademik dan non-akademik</li><li>Mendukung proses akreditasi institusi dan program studi</li></ul>',
                'excerpt' => 'Mengenal lebih dekat Lembaga Penjaminan Mutu Institut Islam Al-Mujaddid Sabak, visi, misi, dan komitmennya terhadap mutu pendidikan.',
                'type' => 'page',
                'is_published' => true,
                'published_at' => Carbon::now(),
                'category_id' => null,
                'user_id' => $user->id,
                'views' => 245,
                'sort_order' => 1
            ],
            [
                'title' => 'Struktur Organisasi LPM',
                'slug' => 'struktur-organisasi-lpm',
                'content' => '<h2>Struktur Organisasi</h2><p>LPM Institut Islam Al-Mujaddid Sabak memiliki struktur organisasi yang terdiri dari:</p><h3>Ketua LPM</h3><p>Bertanggung jawab dalam memimpin dan mengkoordinasikan seluruh kegiatan penjaminan mutu di institusi.</p><h3>Sekretaris LPM</h3><p>Membantu ketua dalam administrasi dan koordinasi kegiatan LPM.</p><h3>Divisi Audit Mutu Internal</h3><p>Melaksanakan audit mutu internal secara berkala untuk memastikan implementasi sistem mutu.</p><h3>Divisi Standar dan Dokumentasi</h3><p>Mengembangkan standar mutu dan mengelola dokumentasi sistem penjaminan mutu.</p><h3>Divisi Monitoring dan Evaluasi</h3><p>Melakukan monitoring dan evaluasi terhadap implementasi sistem penjaminan mutu.</p>',
                'excerpt' => 'Struktur organisasi Lembaga Penjaminan Mutu Institut Islam Al-Mujaddid Sabak dan tugas masing-masing divisi.',
                'type' => 'page',
                'is_published' => true,
                'published_at' => Carbon::now(),
                'category_id' => null,
                'user_id' => $user->id,
                'views' => 189,
                'sort_order' => 2
            ],
            [
                'title' => 'Standar Mutu Pendidikan',
                'slug' => 'standar-mutu-pendidikan',
                'content' => '<h2>8 Standar Nasional Pendidikan Tinggi</h2><p>LPM Institut Islam Al-Mujaddid Sabak mengacu pada 8 Standar Nasional Pendidikan Tinggi yang diintegrasikan dengan nilai-nilai Islam:</p><h3>1. Standar Kompetensi Lulusan</h3><p>Kriteria minimal tentang kualifikasi kemampuan lulusan yang mencakup sikap, pengetahuan, dan keterampilan yang dinyatakan dalam rumusan capaian pembelajaran lulusan.</p><h3>2. Standar Isi Pembelajaran</h3><p>Kriteria minimal tentang kedalaman dan keluasan materi pembelajaran pada setiap program studi.</p><h3>3. Standar Proses Pembelajaran</h3><p>Kriteria minimal tentang pelaksanaan pembelajaran pada program studi untuk memperoleh capaian pembelajaran lulusan.</p><h3>4. Standar Penilaian Pembelajaran</h3><p>Kriteria minimal tentang penilaian proses dan hasil belajar mahasiswa dalam rangka pemenuhan capaian pembelajaran lulusan.</p><h3>5. Standar Dosen dan Tenaga Kependidikan</h3><p>Kriteria minimal tentang kualifikasi dan kompetensi dosen dan tenaga kependidikan untuk menyelenggarakan pendidikan.</p><h3>6. Standar Sarana dan Prasarana Pembelajaran</h3><p>Kriteria minimal tentang sarana dan prasarana sesuai dengan kebutuhan isi dan proses pembelajaran.</p><h3>7. Standar Pengelolaan Pembelajaran</h3><p>Kriteria minimal tentang perencanaan, pelaksanaan, pengendalian, pemantauan dan evaluasi, serta pelaporan kegiatan pembelajaran.</p><h3>8. Standar Pembiayaan Pembelajaran</h3><p>Kriteria minimal tentang komponen dan besaran biaya investasi dan biaya operasional yang disusun dalam rangka pemenuhan capaian pembelajaran lulusan.</p>',
                'excerpt' => 'Penjelasan lengkap tentang 8 Standar Nasional Pendidikan Tinggi yang diterapkan di Institut Islam Al-Mujaddid Sabak.',
                'type' => 'page',
                'is_published' => true,
                'published_at' => Carbon::now(),
                'category_id' => null,
                'user_id' => $user->id,
                'views' => 312,
                'sort_order' => 3
            ],
            [
                'title' => 'Prosedur Audit Mutu Internal',
                'slug' => 'prosedur-audit-mutu-internal',
                'content' => '<h2>Prosedur Audit Mutu Internal</h2><p>Audit Mutu Internal (AMI) merupakan kegiatan evaluasi sistematis untuk memastikan implementasi sistem penjaminan mutu di Institut Islam Al-Mujaddid Sabak.</p><h3>Tahapan Audit Mutu Internal:</h3><h4>1. Perencanaan Audit</h4><ul><li>Penyusunan jadwal audit tahunan</li><li>Penentuan ruang lingkup audit</li><li>Pembentukan tim auditor</li><li>Penyiapan dokumen audit</li></ul><h4>2. Pelaksanaan Audit</h4><ul><li>Rapat pembukaan audit</li><li>Pengumpulan bukti audit</li><li>Wawancara dengan auditee</li><li>Verifikasi dokumen dan implementasi</li></ul><h4>3. Pelaporan Hasil Audit</h4><ul><li>Penyusunan temuan audit</li><li>Rapat penutupan audit</li><li>Penyampaian laporan audit</li><li>Tindak lanjut perbaikan</li></ul><h4>4. Monitoring Tindak Lanjut</h4><ul><li>Pemantauan implementasi perbaikan</li><li>Verifikasi efektivitas perbaikan</li><li>Penutupan temuan audit</li></ul>',
                'excerpt' => 'Prosedur lengkap pelaksanaan Audit Mutu Internal di Institut Islam Al-Mujaddid Sabak.',
                'type' => 'page',
                'is_published' => true,
                'published_at' => Carbon::now(),
                'category_id' => null,
                'user_id' => $user->id,
                'views' => 278,
                'sort_order' => 4
            ],
            [
                'title' => 'Kontak LPM',
                'slug' => 'kontak-lpm',
                'content' => '<h2>Hubungi Kami</h2><p>Lembaga Penjaminan Mutu Institut Islam Al-Mujaddid Sabak siap melayani dan membantu Anda.</p><h3>Alamat Kantor</h3><p>Gedung Rektorat Lantai 2<br>Institut Islam Al-Mujaddid Sabak<br>Jl. Pendidikan No. 123, Sabak<br>Kalimantan Selatan 70123</p><h3>Kontak</h3><p><strong>Telepon:</strong> (0511) 123-4567<br><strong>Email:</strong> lpm@iimsabak.ac.id<br><strong>Website:</strong> https://lpm.iimsabak.ac.id</p><h3>Jam Operasional</h3><p><strong>Senin - Jumat:</strong> 08:00 - 16:00 WITA<br><strong>Sabtu:</strong> 08:00 - 12:00 WITA<br><strong>Minggu:</strong> Tutup</p><h3>Media Sosial</h3><p>Ikuti kami di media sosial untuk mendapatkan informasi terbaru:<br><strong>Instagram:</strong> @lpm_iimsabak<br><strong>Facebook:</strong> LPM Institut Islam Al-Mujaddid Sabak<br><strong>YouTube:</strong> LPM IIMS Channel</p>',
                'excerpt' => 'Informasi kontak dan alamat Lembaga Penjaminan Mutu Institut Islam Al-Mujaddid Sabak.',
                'type' => 'page',
                'is_published' => true,
                'published_at' => Carbon::now(),
                'category_id' => null,
                'user_id' => $user->id,
                'views' => 156,
                'sort_order' => 5
            ]
        ];

        foreach ($pages as $page) {
            Post::create($page);
        }
    }
}