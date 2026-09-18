<?php

namespace Database\Factories;

use App\Models\Kegiatan;
use App\Models\LaporanKegiatan;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<LaporanKegiatan>
 */
class LaporanKegiatanFactory extends Factory
{
    protected $model = LaporanKegiatan::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'kegiatan_id' => Kegiatan::factory(),
            'tujuan' => 'Meningkatkan pemahaman dan kapasitas peserta.',
            'ringkasan' => 'Kegiatan terlaksana dengan tertib dan sesuai rencana.',
            'agenda' => 'Pembukaan, penyampaian materi, diskusi, dan penutup.',
            'narasumber' => 'Ahmad Fauzan',
            'hasil' => 'Peserta memahami materi yang disampaikan.',
            'kendala' => null,
            'tindak_lanjut' => 'Mengadakan evaluasi dan pendampingan lanjutan.',
            'file_lampiran' => 'laporan_kegiatan/contoh.pdf',
        ];
    }
}
