<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('posts', function (Blueprint $table) {
            // Change type column from ENUM to VARCHAR to support any post type
            $table->string('type')->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('posts', function (Blueprint $table) {
            // Revert back to ENUM if needed, but be careful of data loss for new types
            // We use the original enum list from create_posts_table
            // 'berita', 'page', 'gallery', 'video', 'partner'
            $table->enum('type', ['berita', 'page', 'gallery', 'video', 'partner'])->change();
        });
    }
};
