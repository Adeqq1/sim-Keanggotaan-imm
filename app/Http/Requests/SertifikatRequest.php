<?php

namespace App\Http\Requests;

use App\Models\Anggota;
use App\Models\Kegiatan;
use App\Services\VerifiedAttendance;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class SertifikatRequest extends FormRequest
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
            'anggota_ids' => ['required', 'array'],
            'anggota_ids.*' => ['required', 'exists:anggota,id'],
        ];
    }

    public function withValidator($validator): void
    {
        $validator->after(function ($validator): void {
            $kegiatan = Kegiatan::find($this->input('kegiatan_id'));
            if (! $kegiatan) {
                return;
            }

            $invalid = Anggota::query()
                ->whereIn('id', $this->input('anggota_ids', []))
                ->get()
                ->filter(fn (Anggota $anggota): bool => ! $anggota->status_aktif
                    || $anggota->user?->role !== 'kader'
                    || ! app(VerifiedAttendance::class)->meetsRequirement($kegiatan, $anggota));

            if ($invalid->isNotEmpty()) {
                $validator->errors()->add('anggota_ids', 'Anggota berikut tidak memenuhi syarat: '.$invalid->pluck('nama_lengkap')->implode(', '));
            }
        });
    }
}
