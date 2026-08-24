<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use App\Models\Kegiatan;
use Illuminate\Validation\Rule;

class KegiatanRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return in_array($this->user()?->role, ['admin', 'instruktur'], true);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'nama_kegiatan' => ['required', 'string', 'max:255'],
            'deskripsi' => ['nullable', 'string'],
            'tanggal_waktu' => ['required', 'date'],
            'lokasi' => ['required', 'string', 'max:255'],
            'thumbnail' => ['nullable', 'image', 'mimes:jpeg,png,jpg', 'max:2048'],
            'jenis_pelaksanaan' => ['required', Rule::in([Kegiatan::SATU_SESI, Kegiatan::MULTI_SESI])],
            'minimum_sesi_terverifikasi' => ['required', 'integer', 'min:1', 'max:255'],
        ];
    }

    protected function prepareForValidation(): void
    {
        if ($this->input('jenis_pelaksanaan') === Kegiatan::SATU_SESI) {
            $this->merge(['minimum_sesi_terverifikasi' => 1]);
        }
    }

    public function withValidator($validator): void
    {
        $validator->after(function ($validator): void {
            if ($this->input('jenis_pelaksanaan') === Kegiatan::MULTI_SESI && (int) $this->input('minimum_sesi_terverifikasi') < 3) {
                $validator->errors()->add('minimum_sesi_terverifikasi', 'Ambang multi-sesi minimal 3.');
            }
        });
    }
}
