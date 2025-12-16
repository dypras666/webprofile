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
        Schema::table('navigation_menus', function (Blueprint $table) {
            if (!Schema::hasColumn('navigation_menus', 'position')) {
                $table->string('position')->default('top')->after('type');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('navigation_menus', function (Blueprint $table) {
            if (Schema::hasColumn('navigation_menus', 'position')) {
                $table->dropColumn('position');
            }
        });
    }
};
