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
        Schema::create('courses', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique();
            $table->string('title');
            $table->foreignId('category_id')->constrained('categories')->cascadeOnDelete();
            $table->string('type')->default('permanent'); // permanent, batch
            $table->date('start_date')->nullable();
            $table->date('end_date')->nullable();
            $table->string('method')->default('daring'); // luring, daring, hybrid
            $table->string('location')->nullable();
            $table->unsignedInteger('quota')->default(40);
            $table->string('target_audience')->nullable();
            $table->string('budget_source')->nullable();
            $table->text('description')->nullable();
            $table->string('thumbnail')->nullable();
            $table->string('tor_file')->nullable();
            $table->string('status')->default('draft'); // draft, published, archived
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('courses');
    }
};
