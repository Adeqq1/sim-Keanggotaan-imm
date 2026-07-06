<?php

namespace App\Http\Requests;

use App\Models\Arsip;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class ArsipRequest extends FormRequest
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
            'anggota_id' => ['required', 'exists:anggota,id'],
            'nomor_dokumen' => ['nullable', 'string', 'max:255'],
            'judul_dokumen' => ['required', 'string', 'max:255'],
            'kategori_arsip' => ['required', 'string', 'in:'.implode(',', array_keys(Arsip::KATEGORI))],
            'file_arsip' => ['required', 'file', 'mimes:pdf,doc,docx,xls,xlsx,jpg,jpeg,png', 'max:10240'],
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
            'anggota_id.required' => 'Anggota wajib dipilih.',
            'anggota_id.exists' => 'Anggota yang dipilih tidak valid.',
            'judul_dokumen.required' => 'Judul dokumen wajib diisi.',
            'kategori_arsip.required' => 'Kategori wajib dipilih.',
            'kategori_arsip.in' => 'Kategori tidak valid.',
            'file_arsip.required' => 'File dokumen wajib diunggah.',
            'file_arsip.file' => 'File dokumen harus berupa berkas yang valid.',
            'file_arsip.mimes' => 'Format file harus: PDF, DOC, DOCX, XLS, XLSX, JPG, atau PNG.',
            'file_arsip.max' => 'Ukuran file maksimal 10MB.',
        ];
    }
}
