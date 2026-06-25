<?php

namespace App\Http\Requests;

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
            'kategori_arsip' => ['required', 'string', 'max:255'],
            'file_arsip' => ['required', 'file', 'mimes:pdf,xls,xlsx,doc,docx,jpg,jpeg,png', 'max:10240'],
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
            'file_arsip.required' => 'File arsip wajib diunggah.',
            'file_arsip.file' => 'File arsip harus berupa berkas yang valid.',
            'file_arsip.mimes' => 'Format file harus berupa PDF, Excel (xls, xlsx), Word (doc, docx), atau Gambar (jpg, jpeg, png).',
            'file_arsip.max' => 'Ukuran file maksimal adalah 10MB.',
        ];
    }
}
