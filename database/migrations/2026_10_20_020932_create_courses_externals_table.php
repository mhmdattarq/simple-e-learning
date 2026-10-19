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
        Schema::create('courses_externals', function (Blueprint $table) {
            $table->id();
            $table->foreignId('category_id')->nullable()->constrained('categories')->nullOnDelete();
            $table->string('name', 150);
            $table->string('slug', 160)->unique();
            $table->string('logo', 255)->nullable();
            $table->string('url', 500);
            $table->string('description', 255)->nullable();
            $table->string('type', 20)->default('gratis'); // gratis, berbayar
            $table->string('benefit', 100)->nullable(); // contoh: Sertifikat, Piagam
            $table->string('status', 20)->default('published')->index(); // published, draft, archived
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('courses_externals');
    }
};
