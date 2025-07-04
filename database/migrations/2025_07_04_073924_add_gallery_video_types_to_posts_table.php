<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('posts', function (Blueprint $table) {
            // Modify the type enum to include gallery and video
            $table->enum('type', ['berita', 'page', 'gallery', 'video'])->default('berita')->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('posts', function (Blueprint $table) {
            // Revert back to original enum values
            $table->enum('type', ['berita', 'page'])->default('berita')->change();
        });
    }
};
