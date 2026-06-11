<?php

use App\Models\Anggota;
use App\Models\Arsip;
use App\Models\Kegiatan;
use App\Models\Pendaftaran;
use App\Models\Presensi;
use App\Models\Sertifikat;
use App\Models\User;
use Illuminate\Support\Facades\Route;

it('uses Indonesian table names for domain models', function () {
    expect((new Anggota)->getTable())->toBe('anggota')
        ->and((new Pendaftaran)->getTable())->toBe('pendaftaran')
        ->and((new Kegiatan)->getTable())->toBe('kegiatan')
        ->and((new Presensi)->getTable())->toBe('presensi')
        ->and((new Sertifikat)->getTable())->toBe('sertifikat')
        ->and((new Arsip)->getTable())->toBe('arsip');
});

it('keeps English framework naming for auth user model and profile routes', function () {
    expect((new User)->getTable())->toBe('users')
        ->and(Route::has('profile.edit'))->toBeTrue()
        ->and(Route::has('profile.update'))->toBeTrue();
});

it('keeps the pendaftaran validation route stable after controller method rename', function () {
    expect(Route::has('admin.pendaftaran.validate'))->toBeTrue()
        ->and(route('admin.pendaftaran.validate', 1, false))->toBe('/admin/pendaftaran/1/validate');
});
