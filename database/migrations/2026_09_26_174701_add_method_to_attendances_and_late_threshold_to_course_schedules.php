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
        Schema::table('attendances', function (Blueprint $table) {
            $table->string('method', 20)->default('token')->after('status'); // token, qr, manual
        });

        Schema::table('course_schedules', function (Blueprint $table) {
            $table->timestamp('token_opened_at')->nullable()->after('attendance_token');
            $table->unsignedSmallInteger('late_threshold_minutes')->default(15)->after('token_validity_minutes');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('attendances', function (Blueprint $table) {
            $table->dropColumn('method');
        });

        Schema::table('course_schedules', function (Blueprint $table) {
            $table->dropColumn(['token_opened_at', 'late_threshold_minutes']);
        });
    }
};
