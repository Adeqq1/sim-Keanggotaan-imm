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
        return $this->user()?->role === 'instruktur';
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
            'presensi.*.anggota_id' => ['required', 'integer', 'distinct', 'exists:anggota,id'],
            'presensi.*.status_kehadiran' => ['required', 'in:hadir,izin,alfa'],
        ];
    }
}
