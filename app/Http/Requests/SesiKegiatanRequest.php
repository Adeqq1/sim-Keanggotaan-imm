<?php

namespace App\Http\Requests;

use App\Models\Kegiatan;
use App\Models\SesiKegiatan;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class SesiKegiatanRequest extends FormRequest
{
    public function authorize(): bool
    {
        $kegiatan = $this->route('kegiatan');
        $sesi = $this->route('sesiKegiatan');

        return in_array($this->user()?->role, ['admin', 'instruktur'], true)
            && $kegiatan instanceof Kegiatan
            && (! $sesi || ($sesi instanceof SesiKegiatan && $sesi->kegiatan_id === $kegiatan->id));
    }

    public function rules(): array
    {
        $kegiatan = $this->route('kegiatan');
        $sesi = $this->route('sesiKegiatan');

        return [
            'urutan' => ['required', 'integer', 'min:1', 'max:65535', Rule::unique('sesi_kegiatan')->where('kegiatan_id', $kegiatan->id)->ignore($sesi?->id)],
            'nama_sesi' => ['required', 'string', 'max:255', Rule::unique('sesi_kegiatan')->where(fn ($query) => $query->where('kegiatan_id', $kegiatan->id)->where('mulai_pada', $this->input('mulai_pada')))->ignore($sesi?->id)],
            'mulai_pada' => ['required', 'date'],
        ];
    }
}
