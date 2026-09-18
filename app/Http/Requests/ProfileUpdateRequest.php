<?php

namespace App\Http\Requests;

use App\Models\User;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ProfileUpdateRequest extends FormRequest
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
        $anggotaRules = $this->user()->anggota !== null ? ['required'] : ['nullable'];

        return [
            'name' => ['required', 'string', 'max:255'],
            'email' => [
                'required',
                'string',
                'lowercase',
                'email',
                'max:255',
                Rule::unique(User::class)->ignore($this->user()->id),
            ],
            'nama_lengkap' => [...$anggotaRules, 'string', 'max:255'],
            'tempat_lahir' => [...$anggotaRules, 'string', 'max:255'],
            'tanggal_lahir' => [...$anggotaRules, 'date'],
            'alamat' => [...$anggotaRules, 'string'],
            'no_telp' => [...$anggotaRules, 'string', 'max:20'],
            'foto_profil' => [
                'nullable',
                'image',
                'mimes:jpg,jpeg,png',
                'mimetypes:image/jpeg,image/png',
                'max:2048',
                'dimensions:max_width=2048,max_height=2048',
            ],
        ];
    }
}
