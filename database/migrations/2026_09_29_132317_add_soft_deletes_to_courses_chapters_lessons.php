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
        if (Schema::hasTable('courses') && ! Schema::hasColumn('courses', 'deleted_at')) {
            Schema::table('courses', function (Blueprint $table) {
                $table->softDeletes();
            });
        }

        if (Schema::hasTable('chapters') && ! Schema::hasColumn('chapters', 'deleted_at')) {
            Schema::table('chapters', function (Blueprint $table) {
                $table->softDeletes();
            });
        }

        if (Schema::hasTable('lessons') && ! Schema::hasColumn('lessons', 'deleted_at')) {
            Schema::table('lessons', function (Blueprint $table) {
                $table->softDeletes();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('lessons') && Schema::hasColumn('lessons', 'deleted_at')) {
            Schema::table('lessons', function (Blueprint $table) {
                $table->dropSoftDeletes();
            });
        }

        if (Schema::hasTable('chapters') && Schema::hasColumn('chapters', 'deleted_at')) {
            Schema::table('chapters', function (Blueprint $table) {
                $table->dropSoftDeletes();
            });
        }

        if (Schema::hasTable('courses') && Schema::hasColumn('courses', 'deleted_at')) {
            Schema::table('courses', function (Blueprint $table) {
                $table->dropSoftDeletes();
            });
        }
    }
};
