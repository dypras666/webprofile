<?php

namespace Database\Seeders;

use App\Models\Download;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class DownloadSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get admin user
        $adminUser = User::where('email', 'admin@iimsabak.ac.id')->first();
        if (!$adminUser) {
            $adminUser = User::first();
        }
        
        // Create downloads directory if not exists
        if (!Storage::disk('public')->exists('downloads')) {
            Storage::disk('public')->makeDirectory('downloads');
        }
        
        // Sample downloads data
        $downloads = [
            [
                'title' => 'Panduan Sistem Penjaminan Mutu Internal',
                'description' => 'Dokumen panduan lengkap untuk implementasi Sistem Penjaminan Mutu Internal (SPMI) di Institut Islam Al-Mujaddid Sabak.',
                'file_name' => 'panduan-spmi-2024.pdf',
                'category' => 'Panduan',
                'is_public' => true,
                'password' => null,
                'sort_order' => 1,
            ],
            [
                'title' => 'Standar Mutu Pendidikan Tinggi',
                'description' => 'Dokumen standar mutu pendidikan tinggi yang menjadi acuan dalam penyelenggaraan pendidikan di IIMS.',
                'file_name' => 'standar-mutu-pendidikan.pdf',
                'category' => 'Standar',
                'is_public' => true,
                'password' => null,
                'sort_order' => 2,
            ],
            [
                'title' => 'Formulir Audit Mutu Internal',
                'description' => 'Kumpulan formulir yang digunakan dalam proses audit mutu internal.',
                'file_name' => 'formulir-ami.zip',
                'category' => 'Formulir',
                'is_public' => false,
                'password' => 'ami2024',
                'sort_order' => 3,
            ],
            [
                'title' => 'Laporan Audit Mutu Internal 2024',
                'description' => 'Laporan hasil audit mutu internal tahun 2024 untuk semua program studi.',
                'file_name' => 'laporan-ami-2024.pdf',
                'category' => 'Laporan',
                'is_public' => false,
                'password' => 'laporan2024',
                'sort_order' => 4,
            ],
            [
                'title' => 'Template Dokumen Mutu',
                'description' => 'Template dokumen mutu yang dapat digunakan oleh unit kerja dalam penyusunan dokumen.',
                'file_name' => 'template-dokumen-mutu.docx',
                'category' => 'Template',
                'is_public' => true,
                'password' => null,
                'sort_order' => 5,
            ],
            [
                'title' => 'Prosedur Operasional Standar (POS)',
                'description' => 'Kumpulan Prosedur Operasional Standar untuk berbagai kegiatan akademik dan non-akademik.',
                'file_name' => 'pos-lengkap.pdf',
                'category' => 'Prosedur',
                'is_public' => true,
                'password' => null,
                'sort_order' => 6,
            ],
            [
                'title' => 'Instrumen Evaluasi Diri',
                'description' => 'Instrumen untuk melakukan evaluasi diri unit kerja dalam rangka penjaminan mutu.',
                'file_name' => 'instrumen-evaluasi-diri.xlsx',
                'category' => 'Instrumen',
                'is_public' => false,
                'password' => null,
                'sort_order' => 7,
            ],
            [
                'title' => 'Materi Pelatihan ISO 9001:2015',
                'description' => 'Materi pelatihan tentang implementasi ISO 9001:2015 dalam sistem manajemen mutu.',
                'file_name' => 'materi-iso-9001.pptx',
                'category' => 'Pelatihan',
                'is_public' => true,
                'password' => null,
                'sort_order' => 8,
            ],
            [
                'title' => 'Dokumen Akreditasi Program Studi',
                'description' => 'Dokumen pendukung akreditasi program studi yang telah mendapat akreditasi A.',
                'file_name' => 'dokumen-akreditasi-prodi.zip',
                'category' => 'Akreditasi',
                'is_public' => false,
                'password' => 'akreditasi2024',
                'sort_order' => 9,
            ],
            [
                'title' => 'Panduan Monitoring dan Evaluasi',
                'description' => 'Panduan pelaksanaan monitoring dan evaluasi kegiatan penjaminan mutu.',
                'file_name' => 'panduan-monev.pdf',
                'category' => 'Panduan',
                'is_public' => true,
                'password' => null,
                'sort_order' => 10,
            ],
        ];
        
        foreach ($downloads as $downloadData) {
            // Create dummy file content
            $fileContent = $this->generateDummyFileContent($downloadData['file_name']);
            $fileExtension = pathinfo($downloadData['file_name'], PATHINFO_EXTENSION);
            $filePath = 'downloads/' . Str::uuid() . '.' . $fileExtension;
            
            // Store dummy file
            Storage::disk('public')->put($filePath, $fileContent);
            
            // Get file size
            $fileSize = Storage::disk('public')->size($filePath);
            
            // Determine file type
            $fileType = $this->getFileType($fileExtension);
            
            Download::create([
                'title' => $downloadData['title'],
                'description' => $downloadData['description'],
                'file_name' => $downloadData['file_name'],
                'file_path' => $filePath,
                'file_type' => $fileType,
                'file_size' => $fileSize,
                'is_public' => $downloadData['is_public'],
                'password' => $downloadData['password'],
                'category' => $downloadData['category'],
                'download_count' => rand(0, 50),
                'is_active' => true,
                'sort_order' => $downloadData['sort_order'],
                'user_id' => $adminUser->id,
            ]);
        }
    }
    
    /**
     * Generate dummy file content based on file type
     */
    private function generateDummyFileContent($fileName)
    {
        $extension = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
        
        switch ($extension) {
            case 'pdf':
                return '%PDF-1.4\n1 0 obj\n<<\n/Type /Catalog\n/Pages 2 0 R\n>>\nendobj\n\n2 0 obj\n<<\n/Type /Pages\n/Kids [3 0 R]\n/Count 1\n>>\nendobj\n\n3 0 obj\n<<\n/Type /Page\n/Parent 2 0 R\n/MediaBox [0 0 612 792]\n>>\nendobj\n\nxref\n0 4\n0000000000 65535 f \n0000000009 00000 n \n0000000074 00000 n \n0000000120 00000 n \ntrailer\n<<\n/Size 4\n/Root 1 0 R\n>>\nstartxref\n179\n%%EOF';
            
            case 'docx':
            case 'xlsx':
            case 'pptx':
                return 'PK' . str_repeat('\x00', 100) . 'Dummy Office Document Content';
            
            case 'zip':
                return 'PK' . str_repeat('\x00', 50) . 'Dummy ZIP Archive Content';
            
            case 'txt':
                return "Ini adalah file contoh untuk download area.\n\nFile: {$fileName}\nDibuat oleh: LPM Institut Islam Al-Mujaddid Sabak\nTanggal: " . date('Y-m-d H:i:s');
            
            default:
                return "Dummy content for {$fileName}\nGenerated at: " . date('Y-m-d H:i:s');
        }
    }
    
    /**
     * Get MIME type based on file extension
     */
    private function getFileType($extension)
    {
        $mimeTypes = [
            'pdf' => 'application/pdf',
            'doc' => 'application/msword',
            'docx' => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
            'xls' => 'application/vnd.ms-excel',
            'xlsx' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'ppt' => 'application/vnd.ms-powerpoint',
            'pptx' => 'application/vnd.openxmlformats-officedocument.presentationml.presentation',
            'zip' => 'application/zip',
            'rar' => 'application/x-rar-compressed',
            'txt' => 'text/plain',
            'jpg' => 'image/jpeg',
            'jpeg' => 'image/jpeg',
            'png' => 'image/png',
            'gif' => 'image/gif',
        ];
        
        return $mimeTypes[strtolower($extension)] ?? 'application/octet-stream';
    }
}