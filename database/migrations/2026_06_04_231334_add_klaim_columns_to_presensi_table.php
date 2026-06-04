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
        Schema::table('presensi', function (Blueprint $table) {
            $table->string('bukti_kehadiran')->nullable()->after('status_kehadiran');
            $table->enum('status_klaim', ['pending', 'disetujui', 'ditolak'])->nullable()->default(null)->after('bukti_kehadiran');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('presensi', function (Blueprint $table) {
            $table->dropColumn(['bukti_kehadiran', 'status_klaim']);
        });
    }
};
