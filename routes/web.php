<?php

use App\Http\Controllers\AnggotaController;
use App\Http\Controllers\ArsipController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\EktaController;
use App\Http\Controllers\KegiatanController;
use App\Http\Controllers\LaporanController;
use App\Http\Controllers\PendaftaranController;
use App\Http\Controllers\PresensiController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\RiwayatKeaktifanController;
use App\Http\Controllers\SertifikatController;
use App\Http\Controllers\ValidasiPendaftaranController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/pendaftaran', [PendaftaranController::class, 'create'])->name('pendaftaran');
Route::post('/pendaftaran', [PendaftaranController::class, 'store'])->name('pendaftaran.store');
Route::get('/pendaftaran/sukses', [PendaftaranController::class, 'success'])->name('pendaftaran.success');

// Admin Routes
Route::middleware(['auth', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'adminDashboard'])->name('dashboard');

    // Modul Pendaftaran
    Route::get('/pendaftaran', [ValidasiPendaftaranController::class, 'index'])->name('pendaftaran.index');
    Route::get('/pendaftaran/{id}', [ValidasiPendaftaranController::class, 'show'])->name('pendaftaran.show');
    Route::post('/pendaftaran/{id}/validate', [ValidasiPendaftaranController::class, 'validatePendaftaran'])->name('pendaftaran.validate');

    // Modul Anggota
    Route::resource('anggota', AnggotaController::class);

    // Modul Kegiatan
    Route::resource('kegiatan', KegiatanController::class);

    // Modul Presensi
    Route::get('/presensi/{kegiatan}', [PresensiController::class, 'create'])->name('presensi.show');
    Route::post('/presensi/{kegiatan}', [PresensiController::class, 'store'])->name('presensi.store');

    // Modul Arsip
    Route::resource('arsip', ArsipController::class)->except(['create', 'edit', 'update', 'show']);
    Route::get('/arsip/{arsip}/download', [ArsipController::class, 'download'])->name('arsip.download');

    // Modul Sertifikat
    Route::get('/sertifikat', [SertifikatController::class, 'index'])->name('sertifikat.index');
    Route::get('/sertifikat/create', [SertifikatController::class, 'create'])->name('sertifikat.create');
    Route::post('/sertifikat/generate', [SertifikatController::class, 'generate'])->name('sertifikat.generate');
    Route::get('/sertifikat/{sertifikat}/download', [SertifikatController::class, 'download'])->name('sertifikat.download');

    // Modul Laporan
    Route::get('/laporan', [LaporanController::class, 'index'])->name('laporan.index');
    Route::post('/laporan/export-pdf', [LaporanController::class, 'exportPdf'])->name('laporan.exportPdf');
    Route::post('/laporan/export-excel', [LaporanController::class, 'exportExcel'])->name('laporan.exportExcel');
});

// Kader Routes
Route::middleware(['auth', 'role:kader'])->prefix('kader')->name('kader.')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'kaderDashboard'])->name('dashboard');

    // Modul E-KTA
    Route::get('/ekta', [EktaController::class, 'show'])->name('ekta');
    Route::get('/ekta/download', [EktaController::class, 'download'])->name('ekta.download');

    // Modul Sertifikat
    Route::get('/sertifikat', [SertifikatController::class, 'mySertifikat'])->name('sertifikat.index');
    Route::get('/sertifikat/{sertifikat}/download', [SertifikatController::class, 'download'])->name('sertifikat.download');

    // Modul Riwayat
    Route::get('/riwayat', [RiwayatKeaktifanController::class, 'index'])->name('riwayat.index');

    // Modul Arsip
    Route::get('/arsip', [ArsipController::class, 'kaderIndex'])->name('arsip.index');
    Route::post('/arsip', [ArsipController::class, 'store'])->name('arsip.store');
    Route::get('/arsip/{arsip}/download', [ArsipController::class, 'download'])->name('arsip.download');
});

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
