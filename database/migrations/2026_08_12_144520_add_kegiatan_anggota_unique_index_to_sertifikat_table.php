<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $this->deduplicateExistingCertificates();

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

    private function deduplicateExistingCertificates(): void
    {
        $duplicatePairs = DB::table('sertifikat')
            ->select('kegiatan_id', 'anggota_id')
            ->selectRaw('COUNT(*) as total')
            ->groupBy('kegiatan_id', 'anggota_id')
            ->having('total', '>', 1)
            ->get();

        foreach ($duplicatePairs as $pair) {
            $certificates = DB::table('sertifikat')
                ->where('kegiatan_id', $pair->kegiatan_id)
                ->where('anggota_id', $pair->anggota_id)
                ->orderByDesc('created_at')
                ->orderByDesc('id')
                ->get();
            $canonical = $certificates->first(
                fn (object $certificate): bool => Storage::disk('public')->exists($certificate->file_sertifikat),
            ) ?? $certificates->first();
            $duplicateIds = $certificates
                ->reject(fn (object $certificate): bool => (int) $certificate->id === (int) $canonical->id)
                ->pluck('id');

            DB::table('sertifikat')->whereIn('id', $duplicateIds)->delete();

            Log::warning('Duplicate certificate rows consolidated before unique index.', [
                'kegiatan_id' => $pair->kegiatan_id,
                'anggota_id' => $pair->anggota_id,
                'canonical_id' => $canonical->id,
                'removed_ids' => $duplicateIds->values()->all(),
            ]);
        }
    }
};
