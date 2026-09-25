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
            $table->text('competencies')->nullable()->after('description');
            $table->timestamp('registration_open_at')->nullable()->after('end_date');
            $table->timestamp('registration_close_at')->nullable()->after('registration_open_at');
            $table->foreignId('approved_by')->nullable()->after('created_by')->constrained('users')->nullOnDelete();
            $table->timestamp('approved_at')->nullable()->after('approved_by');
            $table->text('approval_notes')->nullable()->after('approved_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('courses', function (Blueprint $table) {
            $table->dropForeign(['approved_by']);
            $table->dropColumn([
                'competencies',
                'registration_open_at',
                'registration_close_at',
                'approved_by',
                'approved_at',
                'approval_notes',
            ]);
        });
    }
};
