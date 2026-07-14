<?php

use App\Models\Kegiatan;
use App\Models\User;
use Illuminate\Http\UploadedFile;

beforeEach(function () {
    app()->setLocale('id');
});

function payloadPendaftaranDenganFile(array $overrides = []): array
{
    return array_merge([
        'nama_lengkap' => 'Ahmad Kader',
        'email' => 'ahmad.kader@example.com',
        'password' => 'password',
        'password_confirmation' => 'password',
        'role' => 'kader',
        'tempat_lahir' => 'Yogyakarta',
        'tanggal_lahir' => '2000-01-01',
        'no_telp' => '08123456789',
        'alamat' => 'Jl. Contoh No. 1',
    ], $overrides);
}

test('Indonesian catalogs resolve framework messages and readable attributes', function () {
    expect(app()->getLocale())->toBe('id')
        ->and(__('auth.failed'))->toBe('Email atau kata sandi yang Anda masukkan tidak sesuai.')
        ->and(__('passwords.sent'))->toBe('Kami telah mengirimkan tautan pengaturan ulang kata sandi ke email Anda.')
        ->and(__('pagination.previous'))->toBe('&laquo; Sebelumnya')
        ->and(__('validation.required', ['attribute' => __('validation.attributes.nama_lengkap')]))
        ->toBe('nama lengkap wajib diisi.')
        ->and(__('validation.mimes', ['attribute' => __('validation.attributes.file_persyaratan'), 'values' => 'pdf, jpg']))
        ->toBe('file persyaratan harus berupa berkas dengan tipe: pdf, jpg.');
});

test('public registration renders Indonesian file validation feedback', function () {
    $response = $this->from(route('pendaftaran'))->post(
        route('pendaftaran.store'),
        payloadPendaftaranDenganFile([
            'file_persyaratan' => UploadedFile::fake()->create('persyaratan.txt', 10, 'text/plain'),
        ])
    );

    $response
        ->assertRedirect(route('pendaftaran'))
        ->assertSessionHas('errors', function ($errors) {
            return $errors->get('file_persyaratan') === ['file persyaratan harus berupa berkas dengan tipe: pdf, jpg, jpeg, png.'];
        });

    $this->followingRedirects()->post(
        route('pendaftaran.store'),
        payloadPendaftaranDenganFile([
            'email' => 'ahmad.kader.render@example.com',
            'file_persyaratan' => UploadedFile::fake()->create('persyaratan.txt', 10, 'text/plain'),
        ])
    )
        ->assertOk()
        ->assertSee('file persyaratan harus berupa berkas dengan tipe: pdf, jpg, jpeg, png.');

    $this->assertDatabaseMissing('pendaftaran', ['email' => 'ahmad.kader@example.com']);
});

test('public interface renders Indonesian accessibility labels and date names', function () {
    $kegiatan = Kegiatan::factory()->create([
        'tanggal_waktu' => '2026-01-15 10:00:00',
    ]);

    $this->get(route('kegiatan.show', $kegiatan))
        ->assertOk()
        ->assertSee('aria-label="Buka atau tutup navigasi"', false)
        ->assertSee('aria-label="Jejak navigasi"', false)
        ->assertSee('title="Bagikan via WhatsApp"', false)
        ->assertSee('Januari')
        ->assertSee('Seluruh hak cipta dilindungi.');
});

test('admin member list renders Indonesian member actions', function () {
    $admin = User::factory()->admin()->create();

    $this->actingAs($admin)
        ->get(route('admin.anggota.index'))
        ->assertOk()
        ->assertSee('Buat NIA Kosong');
});
