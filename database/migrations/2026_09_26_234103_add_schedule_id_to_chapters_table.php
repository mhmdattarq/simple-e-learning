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
        Schema::table('chapters', function (Blueprint $table) {
            $table->foreignId('schedule_id')
                ->nullable()
                ->after('course_id')
                ->constrained('course_schedules')
                ->nullOnDelete();

            $table->index(['course_id', 'schedule_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('chapters', function (Blueprint $table) {
            $table->dropForeign(['schedule_id']);
            $table->dropIndex(['course_id', 'schedule_id']);
            $table->dropColumn('schedule_id');
        });
    }
};
