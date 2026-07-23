<?php

namespace Database\Factories;

use App\Models\Anggota;
use App\Models\Arsip;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Arsip>
 */
class ArsipFactory extends Factory
{
    protected $model = Arsip::class;

    /**
     * Contoh judul dokumen arsip berbahasa Indonesia.
     *
     * @var list<string>
     */
    private const JUDUL_DOKUMEN = [
        'Surat Undangan Rapat Kerja Komisariat',
        'Proposal Kegiatan Darul Arqam Dasar',
        'LPJ Kegiatan Kajian Rutin Mingguan',
        'Surat Keputusan Pengangkatan Pengurus',
        'Laporan Kegiatan Bakti Sosial',
        'Surat Keterangan Aktif Organisasi',
        'Proposal Pelatihan Kepemimpinan Kader',
        'LPJ Seminar Keorganisasian',
        'Surat Masuk dari Pimpinan Cabang',
        'Dokumen Administrasi Keanggotaan',
        'Surat Keluar Undangan Mentoring',
        'Laporan Pertanggungjawaban Program Kerja',
        'Berita Acara Musyawarah Komisariat',
        'Proposal Aksi Sosial Peduli Sesama',
        'Surat Tugas Instruktur Kegiatan',
    ];

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'anggota_id' => Anggota::factory(),
            'nomor_dokumen' => fake()->unique()->numerify('DOC-####'),
            'judul_dokumen' => fake()->randomElement(self::JUDUL_DOKUMEN),
            'kategori_arsip' => fake()->randomElement(array_keys(Arsip::KATEGORI)),
            'file_arsip' => 'arsip/dummy.pdf',
            'tanggal_unggah' => now()->toDateString(),
        ];
    }
}
