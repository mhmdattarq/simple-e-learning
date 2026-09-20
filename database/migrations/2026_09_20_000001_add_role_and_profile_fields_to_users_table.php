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
        Schema::table('users', function (Blueprint $table) {
            $table->string('nip', 20)->nullable()->unique()->after('email');
            $table->string('role', 20)->default('peserta')->after('password');
            $table->string('opd_agency')->nullable()->after('role');
            $table->string('position')->nullable()->after('opd_agency');
            $table->string('rank_class')->nullable()->after('position');
            $table->string('phone_number', 20)->nullable()->after('rank_class');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'nip',
                'role',
                'opd_agency',
                'position',
                'rank_class',
                'phone_number',
            ]);
        });
    }
};
