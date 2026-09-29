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
        // 1. Lepas index & kolom schedule_id dari tabel chapters
        if (Schema::hasTable('chapters') && Schema::hasColumn('chapters', 'schedule_id')) {
            try {
                Schema::table('chapters', function (Blueprint $table) {
                    $table->dropIndex(['course_id', 'schedule_id']);
                });
            } catch (Throwable $e) {
            }

            Schema::table('chapters', function (Blueprint $table) {
                if (DB::getDriverName() === 'sqlite') {
                    $table->dropForeign(['schedule_id']);
                } else {
                    $fk = DB::select("SELECT CONSTRAINT_NAME FROM information_schema.TABLE_CONSTRAINTS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'chapters' AND CONSTRAINT_NAME = 'chapters_schedule_id_foreign'");
                    if (! empty($fk)) {
                        $table->dropForeign(['schedule_id']);
                    }
                }
                $table->dropColumn('schedule_id');
            });
        }

        // 2. Drop tabel legacy attendances & course_schedules
        Schema::dropIfExists('attendances');
        Schema::dropIfExists('course_schedules');

        // 3. Bersihkan kolom approval berjenjang dari tabel courses
        if (Schema::hasTable('courses')) {
            Schema::table('courses', function (Blueprint $table) {
                if (Schema::hasColumn('courses', 'approved_by')) {
                    $table->dropForeign(['approved_by']);
                    $table->dropColumn('approved_by');
                }
                if (Schema::hasColumn('courses', 'approved_at')) {
                    $table->dropColumn('approved_at');
                }
                if (Schema::hasColumn('courses', 'approval_notes')) {
                    $table->dropColumn('approval_notes');
                }
            });
        }

        // 4. Bersihkan kolom verifikasi manual dan surat rekomendasi dari tabel course_user
        if (Schema::hasTable('course_user')) {
            Schema::table('course_user', function (Blueprint $table) {
                if (Schema::hasColumn('course_user', 'verified_by')) {
                    $table->dropForeign(['verified_by']);
                    $table->dropColumn('verified_by');
                }
                if (Schema::hasColumn('course_user', 'recommendation_letter_path')) {
                    $table->dropColumn('recommendation_letter_path');
                }
                if (Schema::hasColumn('course_user', 'verified_at')) {
                    $table->dropColumn('verified_at');
                }
                if (Schema::hasColumn('course_user', 'verification_notes')) {
                    $table->dropColumn('verification_notes');
                }
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Rollback courses approval columns
        if (Schema::hasTable('courses')) {
            Schema::table('courses', function (Blueprint $table) {
                $table->foreignId('approved_by')->nullable()->after('created_by')->constrained('users')->nullOnDelete();
                $table->timestamp('approved_at')->nullable()->after('approved_by');
                $table->text('approval_notes')->nullable()->after('approved_at');
            });
        }

        // Rollback course_user verification columns
        if (Schema::hasTable('course_user')) {
            Schema::table('course_user', function (Blueprint $table) {
                $table->string('recommendation_letter_path')->nullable()->after('status');
                $table->foreignId('verified_by')->nullable()->after('notes')->constrained('users')->nullOnDelete();
                $table->timestamp('verified_at')->nullable()->after('verified_by');
                $table->text('verification_notes')->nullable()->after('verified_at');
            });
        }
    }
};
