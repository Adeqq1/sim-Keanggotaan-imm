<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class MateriKegiatanRequest extends FormRequest
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
            'judul' => ['required', 'string', 'max:255'],
            'deskripsi' => ['required', 'string'],
            'file_materi' => [$this->isMethod('POST') ? 'required' : 'nullable', 'file', 'mimes:pdf,doc,docx,ppt,pptx,xls,xlsx', 'max:2048'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'judul.required' => 'Judul materi wajib diisi.',
            'deskripsi.required' => 'Deskripsi materi wajib diisi.',
            'file_materi.required' => 'File materi wajib diunggah.',
            'file_materi.file' => 'File materi harus berupa berkas yang valid.',
            'file_materi.mimes' => 'Format file harus PDF, DOC, DOCX, PPT, PPTX, XLS, atau XLSX.',
            'file_materi.max' => 'Ukuran file maksimal adalah 2MB.',
        ];
    }
}
