<?php

namespace App\Http\Requests;

use App\Enums\RoleEnum;
use App\Models\Pendaftaran;
use App\Models\User;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ValidasiPendaftaranRequest extends FormRequest
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
            'status' => ['required', Rule::in(['disetujui', 'ditolak'])],
            'role' => ['required_if:status,disetujui', Rule::enum(RoleEnum::class)->except(RoleEnum::ADMIN)],
            'catatan_admin' => ['required_if:status,ditolak', 'nullable', 'string'],
        ];
    }

    /**
     * Configure the validator instance.
     */
    public function after(): array
    {
        return [
            function ($validator): void {
                $pendaftaran = $this->route('id') ? Pendaftaran::find($this->route('id')) : null;

                if (! $pendaftaran) {
                    return;
                }

                if ($pendaftaran->status_validasi !== 'pending') {
                    $validator->errors()->add('status', 'Pendaftaran ini sudah diproses.');

                    return;
                }

                if ($this->input('status') !== 'disetujui') {
                    return;
                }

                if (User::where('email', $pendaftaran->email)->exists()) {
                    $validator->errors()->add('email', 'Email pendaftar sudah terdaftar sebagai pengguna.');
                }
            },
        ];
    }
}
