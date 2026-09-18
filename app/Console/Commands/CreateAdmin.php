<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Validator;

#[Signature('admin:create')]
#[Description('Buat akun administrator secara interaktif.')]
class CreateAdmin extends Command
{
    public function handle(): int
    {
        $attributes = [
            'name' => $this->ask('Nama admin'),
            'email' => $this->ask('Email admin'),
            'password' => $this->secret('Password admin'),
            'password_confirmation' => $this->secret('Konfirmasi password'),
        ];

        $validator = Validator::make($attributes, [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        if ($validator->fails()) {
            foreach ($validator->errors()->all() as $error) {
                $this->error($error);
            }

            return self::FAILURE;
        }

        $admin = User::create([
            'name' => $attributes['name'],
            'email' => $attributes['email'],
            'password' => $attributes['password'],
            'role' => 'admin',
        ]);

        $this->info("Admin {$admin->email} berhasil dibuat.");

        return self::SUCCESS;
    }
}
