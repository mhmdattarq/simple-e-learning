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
        Schema::table('courses', function (Blueprint $table) {
            $table->string('slug')->nullable()->unique()->after('title');
        });

        if (Schema::hasTable('courses')) {
            $courses = DB::table('courses')->get();
            foreach ($courses as $c) {
                $slug = Str::slug($c->title) ?: 'course-'.$c->id;
                DB::table('courses')->where('id', $c->id)->update(['slug' => $slug]);
            }
        }

        Schema::table('courses', function (Blueprint $table) {
            $table->dropUnique(['code']);
            $table->dropColumn('code');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('courses', function (Blueprint $table) {
            $table->string('code')->nullable()->unique()->after('id');
            $table->dropColumn('slug');
        });
    }
};
