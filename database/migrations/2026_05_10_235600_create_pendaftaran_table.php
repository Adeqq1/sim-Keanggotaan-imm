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
        Schema::create('pendaftaran', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained('users')->onDelete('set null');
            $table->string('nama_lengkap');
            $table->string('email');
            $table->string('tempat_lahir');
            $table->date('tanggal_lahir');
            $table->string('no_telp');
            $table->text('alamat');
            $table->date('tanggal_daftar');
            $table->string('file_persyaratan')->nullable();
            $table->enum('status_validasi', ['pending', 'disetujui', 'ditolak'])->default('pending');
            $table->text('catatan_admin')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pendaftaran');
    }
};
