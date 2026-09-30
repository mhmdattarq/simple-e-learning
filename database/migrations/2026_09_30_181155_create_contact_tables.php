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
        // 1. Tabel Pengaturan Informasi Kontak
        Schema::create('contact_settings', function (Blueprint $table) {
            $table->id();
            $table->string('office_title')->nullable();
            $table->text('address')->nullable();
            $table->string('whatsapp_number', 30)->nullable();
            $table->string('whatsapp_label')->nullable();
            $table->string('email')->nullable();
            $table->string('service_days', 100)->nullable();
            $table->string('service_hours', 100)->nullable();
            $table->text('maps_embed_url')->nullable();
            $table->text('maps_url')->nullable();
            $table->timestamps();
        });

        // 2. Tabel FAQ (Pertanyaan Populer)
        Schema::create('contact_faqs', function (Blueprint $table) {
            $table->id();
            $table->string('question');
            $table->text('answer');
            $table->unsignedInteger('order')->default(1);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // 3. Tabel Kotak Masuk Pesan dari Pengunjung/Peserta
        Schema::create('contact_messages', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email');
            $table->string('phone', 30)->nullable();
            $table->string('subject');
            $table->text('message');
            $table->string('status', 20)->default('unread'); // unread, read, replied
            $table->text('admin_notes')->nullable();
            $table->timestamp('replied_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('contact_messages');
        Schema::dropIfExists('contact_faqs');
        Schema::dropIfExists('contact_settings');
    }
};
