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

        if (DB::table('anggota')->select('user_id')->groupBy('user_id')->havingRaw('COUNT(*) > 1')->exists()) {
            throw new RuntimeException('Duplikasi user_id pada anggota harus diselesaikan sebelum unique index ditambahkan.');
        }

        if (DB::table('anggota')->whereNotNull('nia')->whereRaw("{$lengthFunction}(nia) != 8")->exists()) {
            throw new RuntimeException('NIA di luar panjang 8 karakter harus diselesaikan sebelum kolom diubah.');
        }

        if (DB::table('anggota')->whereRaw("{$lengthFunction}(no_telp) > 20")->exists()) {
            throw new RuntimeException('Nomor telepon anggota lebih dari 20 karakter harus diselesaikan sebelum kolom diubah.');
        }

        // Changing nia may rebuild and lock this table; deploy in a maintenance window for large datasets.
        Schema::table('anggota', function (Blueprint $table) {
            $table->unique('user_id', 'anggota_user_id_unique');
            $table->char('nia', 8)->nullable()->change();
            $table->string('no_telp', 20)->change();
            $table->index(['status_aktif', 'tahun_daftar'], 'anggota_status_tahun_index');
        });
    }

    public function down(): void
    {
        Schema::table('anggota', function (Blueprint $table) {
            $table->dropIndex('anggota_status_tahun_index');
            $table->dropUnique('anggota_user_id_unique');
            $table->string('no_telp')->change();
            $table->string('nia')->nullable()->change();
        });
    }
};
