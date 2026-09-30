<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('quizzes', function (Blueprint $table) {
            $table->string('slug')->nullable()->unique()->after('title');
        });

        if (Schema::hasTable('quizzes')) {
            $quizzes = DB::table('quizzes')->get();
            foreach ($quizzes as $q) {
                $baseSlug = Str::slug($q->title) ?: 'evaluasi-'.$q->id;
                $slug = $baseSlug;
                $count = 1;
                while (DB::table('quizzes')->where('slug', $slug)->where('id', '!=', $q->id)->exists()) {
                    $slug = $baseSlug.'-'.$count++;
                }
                DB::table('quizzes')->where('id', $q->id)->update(['slug' => $slug]);
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('quizzes', function (Blueprint $table) {
            $table->dropColumn('slug');
        });
    }
};
