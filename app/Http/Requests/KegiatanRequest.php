<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use App\Models\Kegiatan;
use Illuminate\Validation\Rule;
use Carbon\Carbon;
use Carbon\Exceptions\InvalidFormatException;

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
        $existing = $this->route('kegiatan');

        return [
            'nama_kegiatan' => ['required', 'string', 'max:255'],
            'deskripsi' => ['nullable', 'string'],
            'tanggal_kegiatan' => ['required', 'date_format:Y-m-d'],
            'waktu_mulai' => ['required', 'date_format:H:i'],
            'tanggal_waktu' => [
                'required',
                 'date',
                 function (string $attribute, mixed $value, \Closure $fail) use ($existing): void {
                     try {
                         $date = Carbon::parse($value);
                     } catch (InvalidFormatException) {
                         return;
                     }

                     if ($existing && $existing->tanggal_waktu?->format('Y-m-d H:i') === $date->format('Y-m-d H:i')) {
                         return;
                     }

                     if ($date->lessThanOrEqualTo(now())) {
                        $fail('Jadwal kegiatan harus berada di masa depan.');
                    }
                },
            ],
            'lokasi' => ['required', 'string', 'max:255'],
            'thumbnail' => ['nullable', 'image', 'mimes:jpeg,png,jpg', 'max:2048'],
            'jenis_pelaksanaan' => ['required', Rule::in([Kegiatan::SATU_SESI, Kegiatan::MULTI_SESI])],
            'minimum_sesi_terverifikasi' => ['required', 'integer', 'min:1', 'max:255'],
            'tahun_angkatan' => ['required', 'array', 'min:1'],
            'tahun_angkatan.*' => ['required', 'integer', 'distinct', 'between:2016,'.now()->year],
        ];
    }

    protected function prepareForValidation(): void
    {
        if ($this->filled('tanggal_waktu') && ! $this->filled('tanggal_kegiatan')) {
            try {
                $legacyDate = Carbon::parse($this->input('tanggal_waktu'));
                $this->merge([
                    'tanggal_kegiatan' => $legacyDate->format('Y-m-d'),
                    'waktu_mulai' => $legacyDate->format('H:i'),
                ]);
            } catch (InvalidFormatException) {
                // Let the date rule return the normal validation error.
            }
        }

        if ($this->filled('tanggal_kegiatan') && $this->filled('waktu_mulai')) {
            $this->merge([
                'tanggal_waktu' => $this->input('tanggal_kegiatan').' '.$this->input('waktu_mulai'),
            ]);
        }

        if ($this->input('jenis_pelaksanaan') === Kegiatan::SATU_SESI) {
            $this->merge(['minimum_sesi_terverifikasi' => 1]);
        }
    }

    public function withValidator($validator): void
    {
        $validator->after(function ($validator): void {
            if ($this->boolean('thumbnail_selected') && ! $this->hasFile('thumbnail')) {
                $validator->errors()->add('thumbnail', 'Thumbnail gagal diterima. Pilih ulang file JPG atau PNG maksimal 2MB.');
            }

            if ($this->input('jenis_pelaksanaan') === Kegiatan::MULTI_SESI && (int) $this->input('minimum_sesi_terverifikasi') < 3) {
                $validator->errors()->add('minimum_sesi_terverifikasi', 'Ambang multi-sesi minimal 3.');
            }
        });
    }
}
