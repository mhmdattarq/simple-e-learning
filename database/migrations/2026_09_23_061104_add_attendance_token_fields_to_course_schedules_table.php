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
        Schema::table('course_schedules', function (Blueprint $table) {
            $table->string('attendance_token', 10)->nullable()->after('status')->index();
            $table->unsignedSmallInteger('token_validity_minutes')->default(15)->after('attendance_token');
            $table->timestamp('token_expires_at')->nullable()->after('token_validity_minutes');
            $table->boolean('is_attendance_open')->default(false)->after('token_expires_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('course_schedules', function (Blueprint $table) {
            $table->dropIndex(['attendance_token']);
            $table->dropColumn([
                'attendance_token',
                'token_validity_minutes',
                'token_expires_at',
                'is_attendance_open',
            ]);
        });
    }
};
