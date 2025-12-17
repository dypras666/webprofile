<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up()
    {
        DB::statement("ALTER TABLE navigation_menus MODIFY COLUMN position ENUM('top', 'bottom', 'quicklink', 'footer_2') NOT NULL DEFAULT 'top'");
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        // Revert ensuring no data loss if possible, though 'footer_2' values would be problematic. 
        // For safety in dev environment, we might just leave it or revert to string if needed.
        // Or revert to original enum.
        DB::statement("ALTER TABLE navigation_menus MODIFY COLUMN position ENUM('top', 'bottom', 'quicklink') NOT NULL DEFAULT 'top'");
    }
};
