<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use App\Models\Kegiatan;
use App\Models\SesiKegiatan;

class PresensiRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        $kegiatan = $this->route('kegiatan');
        $sesi = $this->route('sesiKegiatan');

        return $this->user()?->role === 'instruktur'
            && $kegiatan instanceof Kegiatan
            && $sesi instanceof SesiKegiatan
            && $sesi->kegiatan_id === $kegiatan->id
            && $kegiatan->jenis_pelaksanaan !== Kegiatan::BELUM_DITETAPKAN;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'presensi' => ['required', 'array', 'min:1'],
            'presensi.*' => ['required', 'array'],
            'presensi.*.anggota_id' => [
                'required',
                'integer',
                'distinct',
                Rule::exists('anggota', 'id')->where('status_aktif', true),
            ],
            'presensi.*.status_kehadiran' => ['required', 'in:hadir,izin,alfa'],
        ];
    }
}
