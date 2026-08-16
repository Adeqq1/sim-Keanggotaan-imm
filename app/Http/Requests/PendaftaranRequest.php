<?php

namespace App\Http\Requests;

use App\Enums\RoleEnum;
use App\Models\Pendaftaran;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules;

class PendaftaranRequest extends FormRequest
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
        $komisariatIds = array_keys(Pendaftaran::KOMISARIAT);

        return [
            'nama_lengkap' => ['required', 'string', 'max:255'],
            'email' => [
                'required',
                'string',
                'email',
                'max:255',
                Rule::unique('users', 'email'),
                Rule::unique('pendaftaran', 'email')->where('status_validasi', 'pending'),
            ],
            'password' => ['required', 'string', 'confirmed', Rules\Password::defaults()],
            'role' => ['required', Rule::enum(RoleEnum::class)->except(RoleEnum::ADMIN)],
            'komisariat_id' => [
                'exclude_unless:role,kader',
                'required',
                'string',
                Rule::in($komisariatIds),
            ],
            'tahun_daftar' => [
                'required',
                'integer',
                'digits:4',
                'between:2016,'.now()->year,
            ],
            'jenis_dokumen_identitas' => ['required', Rule::in(array_keys(Pendaftaran::JENIS_DOKUMEN_IDENTITAS))],
            'tempat_lahir' => ['required', 'string', 'max:255'],
            'tanggal_lahir' => ['required', 'date'],
            'no_telp' => ['required', 'string', 'max:20'],
            'alamat' => ['required', 'string'],
            'file_persyaratan' => ['required', 'file', 'extensions:pdf,jpg,jpeg,png', 'mimes:pdf,jpg,jpeg,png', 'max:2048'],
        ];
    }
}
