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
        // 1. Quizzes (Kuis Bab & Ujian Akhir Kelas ala Dicoding)
        Schema::create('quizzes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('course_id')->constrained('courses')->cascadeOnDelete();
            $table->foreignId('chapter_id')->nullable()->constrained('chapters')->cascadeOnDelete();
            $table->string('type', 20)->default('chapter'); // 'chapter' atau 'final'
            $table->string('title');
            $table->text('description')->nullable();
            $table->unsignedSmallInteger('time_limit_minutes')->nullable();
            $table->unsignedInteger('total_score')->default(0);
            $table->unsignedInteger('passing_score')->default(70);
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            $table->softDeletes();
        });

        // 2. Quiz Questions (Butir Soal dengan Skor Dinamis 1-20)
        Schema::create('quiz_questions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('quiz_id')->constrained('quizzes')->cascadeOnDelete();
            $table->text('question_text');
            $table->text('explanation')->nullable();
            $table->unsignedTinyInteger('score')->default(10); // Bobot skor 1 - 20
            $table->unsignedSmallInteger('order')->default(1);
            $table->timestamps();
        });

        // 3. Quiz Options (Pilihan Ganda Dinamis 2-5 opsi)
        Schema::create('quiz_options', function (Blueprint $table) {
            $table->id();
            $table->foreignId('question_id')->constrained('quiz_questions')->cascadeOnDelete();
            $table->text('option_text');
            $table->boolean('is_correct')->default(false);
            $table->unsignedTinyInteger('order')->default(1);
            $table->timestamps();
        });

        // 4. Quiz Attempts (Pengerjaan Siswa - Single Attempt / No Retake)
        Schema::create('quiz_attempts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('quiz_id')->constrained('quizzes')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->unsignedInteger('total_earned_score')->default(0);
            $table->unsignedInteger('total_possible_score')->default(0);
            $table->decimal('percentage', 5, 2)->default(0.00);
            $table->boolean('is_passed')->default(false);
            $table->json('answers_data')->nullable();
            $table->timestamp('started_at')->nullable();
            $table->timestamp('submitted_at')->nullable();
            $table->timestamps();

            // Kunci peserta hanya dapat mengerjakan 1 kali (No Retake)
            $table->unique(['quiz_id', 'user_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('quiz_attempts');
        Schema::dropIfExists('quiz_options');
        Schema::dropIfExists('quiz_questions');
        Schema::dropIfExists('quizzes');
    }
};
