<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        $lengthFunction = DB::getDriverName() === 'sqlite' ? 'LENGTH' : 'CHAR_LENGTH';

        if (DB::table('pendaftaran')->whereRaw("{$lengthFunction}(no_telp) > 20")->exists()) {
            throw new RuntimeException('Nomor telepon pendaftaran lebih dari 20 karakter harus diselesaikan sebelum kolom diubah.');
        }

        Schema::table('pendaftaran', function (Blueprint $table) {
            $table->string('no_telp', 20)->change();
            $table->index(['status_validasi', 'created_at'], 'pendaftaran_status_created_index');
        });
    }

    public function down(): void
    {
        Schema::table('pendaftaran', function (Blueprint $table) {
            $table->dropIndex('pendaftaran_status_created_index');
            $table->string('no_telp')->change();
        });
    }
};
