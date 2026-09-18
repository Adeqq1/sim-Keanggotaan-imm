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
        $duplicates = DB::table('sertifikat')
            ->select('kegiatan_id', 'anggota_id')
            ->selectRaw('COUNT(*) as total')
            ->groupBy('kegiatan_id', 'anggota_id')
            ->having('total', '>', 1)
            ->get();

        if ($duplicates->isNotEmpty()) {
            $pairs = $duplicates
                ->map(fn (object $duplicate): string => "{$duplicate->kegiatan_id}:{$duplicate->anggota_id}")
                ->implode(', ');

            throw new \RuntimeException(
                "Cannot add certificate uniqueness constraint; duplicate activity/member pairs require manual reconciliation: {$pairs}",
            );
        }

        Schema::table('sertifikat', function (Blueprint $table) {
            $table->unique(
                ['kegiatan_id', 'anggota_id'],
                'sertifikat_kegiatan_anggota_unique',
            );
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('sertifikat', function (Blueprint $table) {
            $table->dropUnique('sertifikat_kegiatan_anggota_unique');
        });
    }

};
