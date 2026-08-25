<?php

namespace App\Http\Requests;

use App\Models\Anggota;
use App\Models\Kegiatan;
use App\Models\Sertifikat;
use App\Services\CertificateEligibility;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class SertifikatRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user()?->role === 'admin';
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
            'anggota_ids' => ['required', 'array', 'min:1'],
            'anggota_ids.*' => ['required', 'integer', 'distinct', 'exists:anggota,id'],
        ];
    }

    public function withValidator($validator): void
    {
        $validator->after(function ($validator): void {
            if ($validator->errors()->isNotEmpty()) {
                return;
            }

            $kegiatan = Kegiatan::find($this->integer('kegiatan_id'));
            if (! $kegiatan) return;

            $invalid = Anggota::query()
                ->with('user')
                ->whereIn('id', $this->input('anggota_ids'))
                ->get();

            $eligibility = app(CertificateEligibility::class);
            $invalid = $invalid->filter(fn (Anggota $anggota): bool => ! $eligibility->eligible($kegiatan, $anggota)
                || Sertifikat::where('kegiatan_id', $kegiatan->id)->where('anggota_id', $anggota->id)->exists());

            if ($invalid->isNotEmpty()) {
                $validator->errors()->add('anggota_ids', 'Anggota berikut tidak memenuhi syarat: '.$invalid->pluck('nama_lengkap')->implode(', '));
            }
        });
    }
}
