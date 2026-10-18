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
        Schema::table('courses', function (Blueprint $table) {
            $table->string('jam_pelajaran', 50)->default('24')->comment('Estimasi bobot jam pelajaran pelatihan (bisa angka, koma, desimal, atau teks)')->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('courses', function (Blueprint $table) {
            $table->unsignedInteger('jam_pelajaran')->default(24)->comment('Estimasi bobot jam pelajaran pelatihan (1 JP = 45 menit)')->change();
        });
    }
};
