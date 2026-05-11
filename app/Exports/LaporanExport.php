<?php

namespace App\Exports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithTitle;

class LaporanExport implements FromCollection, ShouldAutoSize, WithHeadings, WithMapping, WithTitle
{
    /**
     * @param  array<string, mixed>  $headings
     * @param  array<string, string>  $mappingKeys
     */
    public function __construct(
        public Collection $data,
        public string $jenis,
        public array $headings = [],
        public array $mappingKeys = [],
    ) {
        $this->resolveConfig();
    }

    public function collection(): Collection
    {
        return $this->data;
    }

    /**
     * @return array<int, string>
     */
    public function headings(): array
    {
        return $this->headings;
    }

    /**
     * @param  mixed  $row
     * @return array<int, mixed>
     */
    public function map($row): array
    {
        return array_map(fn (string $key) => data_get($row, $key), $this->mappingKeys);
    }

    public function title(): string
    {
        return ucfirst($this->jenis);
    }

    private function resolveConfig(): void
    {
        $configs = [
            'kegiatan' => [
                'headings' => ['ID', 'Nama Kegiatan', 'Deskripsi', 'Tanggal & Waktu', 'Lokasi'],
                'keys' => ['id', 'nama_kegiatan', 'deskripsi', 'tanggal_waktu', 'lokasi'],
            ],
            'anggota' => [
                'headings' => ['ID', 'NIA', 'Nama Lengkap', 'Tempat Lahir', 'Tanggal Lahir', 'Alamat', 'No. Telp', 'Status'],
                'keys' => ['id', 'nia', 'nama_lengkap', 'tempat_lahir', 'tanggal_lahir', 'alamat', 'no_telp', 'status_aktif'],
            ],
            'pendaftaran' => [
                'headings' => ['ID', 'Nama Lengkap', 'Email', 'No. Telp', 'Tanggal Daftar', 'Status'],
                'keys' => ['id', 'nama_lengkap', 'email', 'no_telp', 'tanggal_daftar', 'status_validasi'],
            ],
            'arsip' => [
                'headings' => ['ID', 'Nomor Dokumen', 'Judul', 'Kategori', 'Tanggal Unggah'],
                'keys' => ['id', 'nomor_dokumen', 'judul_dokumen', 'kategori_arsip', 'tanggal_unggah'],
            ],
        ];

        $config = $configs[$this->jenis] ?? ['headings' => ['ID'], 'keys' => ['id']];

        $this->headings = $config['headings'];
        $this->mappingKeys = $config['keys'];
    }
}
