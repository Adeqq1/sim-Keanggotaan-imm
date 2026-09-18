<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('sertifikat', function (Blueprint $table): void {
            $table->string('tipe_sertifikat', 32)->nullable()->after('file_sertifikat');
            $table->enum('nilai_snapshot', ['A', 'B', 'C', 'D'])->nullable()->after('tipe_sertifikat');
        });
    }

    public function down(): void
    {
        Schema::table('sertifikat', function (Blueprint $table): void {
            $table->dropColumn(['tipe_sertifikat', 'nilai_snapshot']);
        });
    }
};
