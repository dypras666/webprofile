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
        Schema::table('program_studis', function (Blueprint $table) {
            // General Info
            $table->string('code')->nullable()->after('name'); // Kode Prodi
            $table->foreignId('program_head_id')->nullable()->after('code')->constrained('team_members')->nullOnDelete(); // KA PRODI
            $table->string('faculty')->nullable()->after('program_head_id'); // Fakultas
            $table->string('degree')->nullable()->after('faculty'); // Jenjang (S1, D3, etc.)

            // Legal & Contact
            $table->date('establishment_date')->nullable()->after('accreditation'); // Tanggal berdiri
            $table->string('decree_number')->nullable()->after('establishment_date'); // SK penyelenggara
            $table->date('decree_date')->nullable()->after('decree_number'); // Tanggal SK
            $table->string('phone')->nullable()->after('decree_date');
            $table->string('email')->nullable()->after('phone');
            $table->text('address')->nullable()->after('email');

            // Detailed Content (Summernote fields)
            $table->longText('vision')->nullable()->after('description');
            $table->longText('mission')->nullable()->after('vision');
            $table->longText('competence')->nullable()->after('mission');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('program_studis', function (Blueprint $table) {
            $table->dropForeign(['program_head_id']);
            $table->dropColumn([
                'code',
                'program_head_id',
                'faculty',
                'degree',
                'establishment_date',
                'decree_number',
                'decree_date',
                'phone',
                'email',
                'address',
                'vision',
                'mission',
                'competence'
            ]);
        });
    }
};
