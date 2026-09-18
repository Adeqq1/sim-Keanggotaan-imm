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
        Schema::table('pendaftaran', function (Blueprint $table) {
            $table->string('komisariat_id', 100)->nullable();
            $table->unsignedSmallInteger('tahun_daftar')->nullable();
        });

        Schema::table('anggota', function (Blueprint $table) {
            $table->string('komisariat_id', 100)->nullable();
            $table->unsignedSmallInteger('tahun_daftar')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pendaftaran', function (Blueprint $table) {
            $table->dropColumn(['komisariat_id', 'tahun_daftar']);
        });

        Schema::table('anggota', function (Blueprint $table) {
            $table->dropColumn(['komisariat_id', 'tahun_daftar']);
        });
    }
};
