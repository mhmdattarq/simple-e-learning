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
        if (Schema::hasColumn('users', 'nip')) {
            try {
                Schema::table('users', function (Blueprint $table) {
                    $table->dropUnique(['nip']);
                });
            } catch (Throwable $e) {
                // Ignore if unique index was already dropped or absent
            }
        }

        Schema::table('users', function (Blueprint $table) {
            $columnsToDrop = [];
            foreach (['nip', 'opd_agency', 'position', 'rank_class'] as $column) {
                if (Schema::hasColumn('users', $column)) {
                    $columnsToDrop[] = $column;
                }
            }

            if (! empty($columnsToDrop)) {
                $table->dropColumn($columnsToDrop);
            }

            if (! Schema::hasColumn('users', 'phone_number')) {
                $table->string('phone_number', 25)->nullable()->after('email');
            }

            if (! Schema::hasColumn('users', 'address')) {
                $table->text('address')->nullable()->after('phone_number');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (Schema::hasColumn('users', 'address')) {
                $table->dropColumn('address');
            }

            if (! Schema::hasColumn('users', 'nip')) {
                $table->string('nip', 20)->nullable()->unique()->after('email');
            }

            if (! Schema::hasColumn('users', 'opd_agency')) {
                $table->string('opd_agency')->nullable()->after('role');
            }

            if (! Schema::hasColumn('users', 'position')) {
                $table->string('position')->nullable()->after('opd_agency');
            }

            if (! Schema::hasColumn('users', 'rank_class')) {
                $table->string('rank_class')->nullable()->after('position');
            }
        });
    }
};
