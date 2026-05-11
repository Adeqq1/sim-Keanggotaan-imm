<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class PresensiRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'kegiatan_id' => ['required', 'exists:kegiatan,id'],
            'presensi' => ['required', 'array'],
            'presensi.*.anggota_id' => ['required', 'exists:anggota,id'],
            'presensi.*.status_kehadiran' => ['required', 'in:hadir,izin,alfa'],
        ];
    }
}
