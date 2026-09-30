<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('contact_settings', function (Blueprint $table) {
            $table->string('office_title')->nullable()->default(null)->change();
            $table->string('whatsapp_label')->nullable()->default(null)->change();
            $table->string('service_days', 100)->nullable()->default(null)->change();
            $table->string('service_hours', 100)->nullable()->default(null)->change();
        });

        // Kosongkan value awal agar halaman form pengaturan kontak hanya menampilkan placeholder
        if (Schema::hasTable('contact_settings')) {
            DB::table('contact_settings')->update([
                'office_title' => null,
                'address' => null,
                'whatsapp_number' => null,
                'whatsapp_label' => null,
                'email' => null,
                'service_days' => null,
                'service_hours' => null,
                'maps_embed_url' => null,
                'maps_url' => null,
            ]);
        }

        // Kosongkan daftar FAQ awal
        if (Schema::hasTable('contact_faqs')) {
            DB::table('contact_faqs')->truncate();
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('contact_settings', function (Blueprint $table) {
            $table->string('office_title')->default('Kantor BKPSDM')->change();
            $table->string('whatsapp_label')->default('Layanan WhatsApp')->change();
            $table->string('service_days', 100)->default('Senin – Jumat')->change();
            $table->string('service_hours', 100)->default('08.00 – 16.30 WIB')->change();
        });
    }
};
