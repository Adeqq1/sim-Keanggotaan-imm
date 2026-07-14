<?php

namespace App\Http\Requests;

use App\Models\Arsip;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class KaderArsipRequest extends FormRequest
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
            'nomor_dokumen' => ['nullable', 'string', 'max:255'],
            'judul_dokumen' => ['required', 'string', 'max:255'],
            'kategori_arsip' => ['required', 'string', 'in:'.implode(',', array_keys(Arsip::KATEGORI))],
            'file_arsip' => ['required', 'file', 'mimes:pdf,doc,docx,xls,xlsx', 'max:5120'],
        ];
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'judul_dokumen.required' => 'Judul dokumen wajib diisi.',
            'kategori_arsip.required' => 'Kategori wajib dipilih.',
            'kategori_arsip.in' => 'Kategori tidak valid.',
            'file_arsip.required' => 'File arsip wajib diunggah.',
            'file_arsip.file' => 'File arsip harus berupa berkas yang valid.',
            'file_arsip.mimes' => 'Format file harus: PDF, DOC, DOCX, XLS, atau XLSX.',
            'file_arsip.max' => 'Ukuran file maksimal adalah 5MB.',
        ];
    }
}
