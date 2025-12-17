<?php

namespace App\Console\Commands;

use App\Models\Post;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class MigrateLegacyPosts extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:migrate-legacy-posts';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Migrate legacy posts from SQL dump file';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $sqlPath = base_path('iimssite_db (2).sql');

        if (!File::exists($sqlPath)) {
            $this->error("SQL file not found at: $sqlPath");
            return;
        }

        $this->info('Reading SQL file...');
        $content = File::get($sqlPath);

        // Extract INSERT statements for ci_post
        // Pattern matches: INSERT INTO `ci_post` (...) VALUES ... ;
        preg_match_all('/INSERT INTO `ci_post` .*? VALUES\s+(.*?);/s', $content, $matches);

        if (empty($matches[1])) {
            $this->error('No INSERT statements found for ci_post table.');
            return;
        }

        $count = 0;
        $skipped = 0;

        foreach ($matches[1] as $valuesBlock) {
            // Split multiple value sets: (val1, val2), (val3, val4)
            // This regex tries to match balanced parentheses for each row
            preg_match_all('/\((?:[^)(]+|(?R))*+\)/', $valuesBlock, $rows);

            foreach ($rows[0] as $row) {
                try {
                    // Parse values roughly
                    // Remove outer parens
                    $inner = substr($row, 1, -1);

                    // Split by comma, respecting quotes. 
                    // This is a naive split, might break on commas inside quotes. 
                    // For better robustness, we can use strA_getcsv-like approach if needed, 
                    // but simple SQL dumps often quote strings with single quotes.
                    $values = str_getcsv($inner, ',', "'");

                    // SQL Dump Columns based on inspection:
                    // (`id`, `post_slug`, `post_judul`, `post_isi`, `post_gambar`, `post_kategori`, `post_komentar`, `post_status`, `post_tanggal`, `post_jenis`, `post_views`, `slider`, `created_at`, `updated_at`, `username`)

                    // Count index mapping (0-indexed):
                    // 0: id
                    // 1: post_slug
                    // 2: post_judul
                    // 3: post_isi
                    // 4: post_gambar
                    // 5: post_kategori
                    // 6: post_komentar
                    // 7: post_status
                    // 8: post_tanggal
                    // 9: post_jenis
                    // 10: post_views
                    // 11: slider
                    // 12: created_at
                    // 13: updated_at
                    // 14: username

                    if (count($values) < 14) {
                        $this->warn("Skipping row due to insufficient columns: " . substr($inner, 0, 50) . "...");
                        $skipped++;
                        continue;
                    }

                    $slug = trim($values[1]);
                    $title = trim($values[2]);
                    $content = $values[3];
                    $image = $values[4] === 'NULL' ? null : $values[4];
                    // $category = $values[5]; // Skipping for now
                    $isPublished = $values[7] == 1; // Assuming 1 is active
                    $publishedAt = $values[8] === 'NULL' ? now() : $values[8];
                    $type = trim($values[9]) === 'post' ? 'berita' : 'halaman';
                    $isSlider = $values[11] == 1;
                    $createdAt = $values[12] === 'NULL' ? now() : $values[12];
                    $updatedAt = $values[13] === 'NULL' ? now() : $values[13];

                    // Clean up HTML entities in title if necessary
                    $title = html_entity_decode($title);

                    Post::updateOrCreate(
                        ['slug' => $slug],
                        [
                            'title' => $title,
                            'content' => $content,
                            'featured_image' => $image,
                            'type' => $type,
                            'is_published' => $isPublished,
                            'published_at' => $publishedAt,
                            'is_slider' => $isSlider,
                            'user_id' => \App\Models\User::first()->id ?? 1, // Use first user or fallback
                            'created_at' => $createdAt,
                            'updated_at' => $updatedAt,
                            // Set defaults for others
                            'views' => $values[10] ?? 0,
                        ]
                    );

                    $count++;
                    $this->output->write('.');

                } catch (\Exception $e) {
                    $this->error("\nError processing row: " . $e->getMessage());
                    $skipped++;
                }
            }
        }

        $this->newLine();
        $this->info("Migration completed.");
        $this->info("Processed: $count posts");
        $this->info("Skipped/Failed: $skipped posts");
    }
}
