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
        Schema::table('media', function (Blueprint $table) {
            $table->string('type')->default('file')->after('extension');
        });
        
        // Update existing records to set type based on mime_type
        \DB::statement("UPDATE media SET type = 'image' WHERE mime_type LIKE 'image/%'");
        \DB::statement("UPDATE media SET type = 'video' WHERE mime_type LIKE 'video/%'");
        \DB::statement("UPDATE media SET type = 'audio' WHERE mime_type LIKE 'audio/%'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('media', function (Blueprint $table) {
            $table->dropColumn('type');
        });
    }
};
