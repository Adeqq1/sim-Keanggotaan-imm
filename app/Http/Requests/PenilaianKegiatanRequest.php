<?php

namespace App\Http\Requests;

use App\Models\Anggota;
use App\Models\Kegiatan;
use App\Models\PenilaianKegiatan;
use App\Services\VerifiedAttendance;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class PenilaianKegiatanRequest extends FormRequest
{
    protected $errorBag;

    protected function prepareForValidation(): void
    {
        $anggota = $this->route('anggota');
        $this->errorBag = $anggota instanceof Anggota ? 'penilaian-'.$anggota->id : 'default';
    }

    public function authorize(): bool
    {
        return $this->user()?->role === 'instruktur'
            && $this->route('kegiatan') instanceof Kegiatan
            && $this->route('anggota') instanceof Anggota;
    }

    public function rules(): array
    {
        return ['nilai' => ['required', Rule::in(array_keys(PenilaianKegiatan::NILAI_LABELS))]];
    }

    public function after(): array
    {
        return [function ($validator): void {
            $kegiatan = $this->route('kegiatan');
            $anggota = $this->route('anggota');

            if (! $kegiatan instanceof Kegiatan || $kegiatan->jenis_pelaksanaan !== Kegiatan::MULTI_SESI || $kegiatan->minimum_sesi_terverifikasi < 3) {
                $validator->errors()->add('nilai', 'Kegiatan ini tidak menyediakan penilaian.');
            } elseif (! $anggota instanceof Anggota || ! $anggota->status_aktif || $anggota->user?->role !== 'kader' || ! app(VerifiedAttendance::class)->meetsRequirement($kegiatan, $anggota)) {
                $validator->errors()->add('nilai', 'Anggota tidak memenuhi syarat penilaian.');
            } elseif (! $kegiatan->tahunAngkatans()->where('tahun_daftar', $anggota->tahun_daftar)->exists()) {
                $validator->errors()->add('nilai', 'Anggota tidak termasuk target angkatan kegiatan.');
            }
        }];
    }
}
