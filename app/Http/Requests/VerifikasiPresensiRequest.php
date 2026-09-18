<?php

namespace App\Http\Requests;

use App\Models\Kegiatan;
use App\Models\Presensi;
use App\Models\SesiKegiatan;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class VerifikasiPresensiRequest extends FormRequest
{
    public function authorize(): bool
    {
        $kegiatan = $this->route('kegiatan');
        $sesi = $this->route('sesiKegiatan');
        $presensi = $this->route('presensi');

        return $this->user()?->role === 'instruktur'
            && $kegiatan instanceof Kegiatan
            && $sesi instanceof SesiKegiatan && $sesi->kegiatan_id === $kegiatan->id
            && $presensi instanceof Presensi && $presensi->sesi_kegiatan_id === $sesi->id && $presensi->kegiatan_id === $kegiatan->id;
    }

    public function rules(): array
    {
        return ['status_verifikasi' => ['required', Rule::in(['pending', 'terverifikasi', 'ditolak'])]];
    }
}
