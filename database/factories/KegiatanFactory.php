<?php

namespace Database\Factories;

use App\Models\Kegiatan;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Kegiatan>
 */
class KegiatanFactory extends Factory
{
    protected $model = Kegiatan::class;


    /**
     * Contoh judul kegiatan IMM/kampus berbahasa Indonesia.
     *
     * @var list<string>
     */
    private const NAMA_KEGIATAN = [
        'Darul Arqam Dasar (DAD)',
        'Kajian Rutin Mingguan',
        'Rapat Kerja Komisariat',
        'Pelatihan Kepemimpinan Kader',
        'Mentoring Kader Baru',
        'Bakti Sosial Ramadan',
        'Diskusi Ideologi IMM',
        'Seminar Keorganisasian',
        'Latihan Dasar Kepemimpinan',
        'Forum Silaturahmi Kader',
        'Workshop Administrasi Organisasi',
        'Kajian Tafsir Al-Qur\'an',
        'Pelatihan Public Speaking Kader',
        'Aksi Sosial Peduli Sesama',
        'Musyawarah Komisariat',
        'Pelatihan Manajemen Organisasi',
        'Kajian Keislaman dan Kemahasiswaan',
        'Rapat Evaluasi Program Kerja',
    ];

    /**
     * @var list<string>
     */
    private const DESKRIPSI = [
        'Kegiatan perkaderan untuk memperkuat pemahaman keislaman dan keorganisasian kader IMM.',
        'Forum kajian rutin yang membahas isu keislaman, kemahasiswaan, dan keimm-an.',
        'Pelatihan untuk meningkatkan kapasitas kepemimpinan dan manajerial kader.',
        'Program bakti sosial sebagai wujud kepedulian kader terhadap masyarakat sekitar.',
        'Rapat koordinasi untuk merencanakan dan mengevaluasi program kerja komisariat.',
        'Mentoring intensif bagi kader baru agar memahami struktur, nilai, dan budaya organisasi IMM.',
        'Diskusi ideologis untuk memperdalam pemahaman gerakan dan arah perjuangan IMM.',
        'Seminar terbuka mengenai pengembangan organisasi dan peran kader di kampus.',
    ];

    /**
     * @var list<string>
     */
    private const LOKASI = [
        'Aula Universitas',
        'Masjid Kampus',
        'Sekretariat IMM Komisariat',
        'Ruang Rapat Fakultas',
        'Gedung Serbaguna Kampus',
        'Lapangan Kampus',
        'Aula Mahasiswa',
        'Ruang Diskusi Perpustakaan',
    ];

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $kota = fake('id_ID')->city();

        return [
            'nama_kegiatan' => fake()->randomElement(self::NAMA_KEGIATAN),
            'deskripsi' => fake()->randomElement(self::DESKRIPSI),
            'tanggal_waktu' => fake()->dateTimeBetween('+1 day', '+30 days'),
            'lokasi' => fake()->randomElement(self::LOKASI).', '.$kota,
            'jenis_pelaksanaan' => Kegiatan::SATU_SESI,
            'minimum_sesi_terverifikasi' => 1,
        ];
    }

    public function configure(): static
    {
        return $this->afterCreating(function (Kegiatan $kegiatan): void {
            $kegiatan->tahunAngkatans()->firstOrCreate(['tahun_daftar' => now()->year]);
        });
    }

    public function withDefaultSession(): static
    {
        return $this->afterCreating(function (Kegiatan $kegiatan): void {
            $kegiatan->sesiKegiatans()->create([
                    'urutan' => 1,
                    'nama_sesi' => 'Sesi 1',
                    'mulai_pada' => $kegiatan->tanggal_waktu,
            ]);
        });
    }

    /**
     * Indicate that the kegiatan is in the past.
     */
    public function past(): static
    {
        return $this->state(fn (array $attributes) => [
            'tanggal_waktu' => fake()->dateTimeBetween('-30 days', '-1 day'),
        ]);
    }
}
