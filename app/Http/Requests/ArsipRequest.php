<?php

namespace App\Http\Requests;

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
            'kategori_arsip' => ['required', 'string', 'max:255'],
            'file_arsip' => ['required', 'file', 'mimes:pdf,doc,docx,jpg,jpeg,png', 'max:5120'],
            'tanggal_unggah' => ['required', 'date'],
        ];
    }
}
