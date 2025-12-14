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
            $table->enum('position', ['top', 'bottom', 'quicklink'])->default('top')->after('type');
            $table->index(['position', 'parent_id', 'sort_order']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('navigation_menus', function (Blueprint $table) {
            $table->dropIndex(['position', 'parent_id', 'sort_order']);
            $table->dropColumn('position');
        });
    }
};
