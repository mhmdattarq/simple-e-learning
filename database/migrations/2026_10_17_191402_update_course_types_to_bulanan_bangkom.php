<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1. Perbarui nilai data yang ada di tabel courses
        DB::table('courses')->where('type', 'batch')->update(['type' => 'bulanan']);
        DB::table('courses')->where('type', 'permanent')->update(['type' => 'bangkom']);
        DB::table('courses')->whereIn('type', ['paid', 'berbayar'])->update(['type' => 'bangkom_external']);

        // 2. Ubah nilai default kolom type menjadi 'bangkom'
        Schema::table('courses', function (Blueprint $table) {
            $table->string('type')->default('bangkom')->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Revert nilai data ke jenis lama
        DB::table('courses')->where('type', 'bulanan')->update(['type' => 'batch']);
        DB::table('courses')->where('type', 'bangkom')->update(['type' => 'permanent']);
        DB::table('courses')->where('type', 'bangkom_external')->update(['type' => 'paid']);

        // Kembalikan default kolom type menjadi 'permanent'
        Schema::table('courses', function (Blueprint $table) {
            $table->string('type')->default('permanent')->change();
        });
    }
};
