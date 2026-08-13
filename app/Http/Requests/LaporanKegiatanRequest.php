<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class LaporanKegiatanRequest extends FormRequest
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
            'tujuan' => ['required', 'string', 'max:16000'],
            'ringkasan' => ['required', 'string', 'max:16000'],
            'agenda' => ['required', 'string', 'max:16000'],
            'narasumber' => ['nullable', 'string', 'max:16000'],
            'hasil' => ['required', 'string', 'max:16000'],
            'kendala' => ['nullable', 'string', 'max:16000'],
            'tindak_lanjut' => ['nullable', 'string', 'max:16000'],
            'file_lampiran' => ['nullable', 'file', 'mimes:pdf,doc,docx,ppt,pptx,xls,xlsx,jpg,jpeg,png', 'max:2048'],
        ];
    }
}
