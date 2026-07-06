<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        DB::table('arsip')->where('kategori_arsip', 'laporan')->update(['kategori_arsip' => 'lpj']);
        DB::table('arsip')->whereIn('kategori_arsip', ['surat', 'foto'])->update(['kategori_arsip' => 'lainnya']);

        DB::table('arsip')
            ->whereNotNull('file_arsip')
            ->orderBy('id')
            ->each(function (object $arsip): void {
                $path = ltrim($arsip->file_arsip, '/');

                if (Storage::disk('public')->exists($path) && ! Storage::disk('local')->exists($path)) {
                    Storage::disk('local')->put($path, Storage::disk('public')->get($path));
                }

                if (Storage::disk('local')->exists($path)) {
                    Storage::disk('public')->delete($path);
                }
            });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::table('arsip')->where('kategori_arsip', 'lpj')->update(['kategori_arsip' => 'laporan']);

        DB::table('arsip')
            ->whereNotNull('file_arsip')
            ->orderBy('id')
            ->each(function (object $arsip): void {
                $path = ltrim($arsip->file_arsip, '/');

                if (Storage::disk('local')->exists($path) && ! Storage::disk('public')->exists($path)) {
                    Storage::disk('public')->put($path, Storage::disk('local')->get($path));
                }
            });
    }
};
