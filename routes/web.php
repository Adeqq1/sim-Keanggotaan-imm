<?php

use App\Http\Controllers\AnggotaController;
use App\Http\Controllers\ArsipController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\EktaController;
use App\Http\Controllers\KegiatanController;
use App\Http\Controllers\LandingController;
use App\Http\Controllers\LaporanController;
use App\Http\Controllers\LaporanKegiatanController;
use App\Http\Controllers\MateriKegiatanController;
use App\Http\Controllers\PendaftaranController;
use App\Http\Controllers\PresensiController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\RiwayatKeaktifanController;
use App\Http\Controllers\SertifikatController;
use App\Http\Controllers\SesiKegiatanController;
use App\Http\Controllers\ValidasiPendaftaranController;
use Illuminate\Support\Facades\Route;

Route::get('/', [LandingController::class, 'index'])->name('landing');
Route::get('/kegiatan/{kegiatan}', [LandingController::class, 'show'])->name('kegiatan.show');

Route::get('/pendaftaran', [PendaftaranController::class, 'create'])->name('pendaftaran');
Route::post('/pendaftaran', [PendaftaranController::class, 'store'])
    ->middleware('throttle:pendaftaran')
    ->name('pendaftaran.store');
Route::get('/pendaftaran/sukses', [PendaftaranController::class, 'success'])->name('pendaftaran.success');

// Admin Routes
Route::middleware(['auth'])->prefix('admin')->name('admin.')->group(function () {
    // Admin Only
    Route::middleware('role:admin')->group(function () {
        Route::get('/dashboard', [DashboardController::class, 'adminDashboard'])->name('dashboard');

        // Modul Pendaftaran
        Route::get('/pendaftaran', [ValidasiPendaftaranController::class, 'index'])->name('pendaftaran.index');
        Route::get('/pendaftaran/{id}', [ValidasiPendaftaranController::class, 'show'])->name('pendaftaran.show');
        Route::get('/pendaftaran/{pendaftaran}/dokumen-identitas', [ValidasiPendaftaranController::class, 'downloadDokumenIdentitas'])->name('pendaftaran.document.download');
        Route::get('/pendaftaran/{pendaftaran}/dokumen-identitas/preview', [ValidasiPendaftaranController::class, 'previewDokumenIdentitas'])->name('pendaftaran.document.preview');
        Route::post('/pendaftaran/{id}/validate', [ValidasiPendaftaranController::class, 'prosesValidasiPendaftaran'])->name('pendaftaran.validate');

        // Modul Anggota
        Route::post('/anggota/generate-nia-bulk', [AnggotaController::class, 'generateBulkNia'])->name('anggota.generate-nia-bulk');
        Route::resource('anggota', AnggotaController::class)->parameters(['anggota' => 'anggota']);
        Route::post('/anggota/{anggota}/generate-nia', [AnggotaController::class, 'generateNia'])->name('anggota.generate-nia');

        // Modul Arsip (admin hanya bisa lihat, unduh, dan hapus; upload hanya oleh kader)
        Route::resource('arsip', ArsipController::class)->only(['index', 'destroy']);
        Route::get('/arsip/{arsip}/download', [ArsipController::class, 'download'])->name('arsip.download');

        // Modul Sertifikat
        Route::get('/sertifikat', [SertifikatController::class, 'index'])->name('sertifikat.index');
        Route::get('/sertifikat/create', [SertifikatController::class, 'create'])->name('sertifikat.create');
        Route::get('/sertifikat/settings', [SertifikatController::class, 'settings'])->name('sertifikat.settings');
        Route::post('/sertifikat/settings', [SertifikatController::class, 'updateSettings'])->name('sertifikat.settings.update');
        Route::post('/sertifikat/generate', [SertifikatController::class, 'generate'])->name('sertifikat.generate');
        Route::get('/sertifikat/{sertifikat}/download', [SertifikatController::class, 'download'])->name('sertifikat.download');

        // Modul Laporan
        Route::get('/laporan', [LaporanController::class, 'index'])->name('laporan.index');
        Route::post('/laporan/export-pdf', [LaporanController::class, 'exportPdf'])->name('laporan.exportPdf');
        Route::post('/laporan/export-excel', [LaporanController::class, 'exportExcel'])->name('laporan.exportExcel');

        Route::get('/materi-kegiatan', [MateriKegiatanController::class, 'adminIndex'])->name('materi-kegiatan.index');

        Route::get('/laporan-kegiatan', [LaporanKegiatanController::class, 'index'])->name('laporan-kegiatan.index');
        Route::get('/kegiatan/{kegiatan}/laporan-kegiatan/create', [LaporanKegiatanController::class, 'create'])->name('kegiatan.laporan-kegiatan.create');
        Route::post('/kegiatan/{kegiatan}/laporan-kegiatan', [LaporanKegiatanController::class, 'store'])->name('kegiatan.laporan-kegiatan.store');
        Route::get('/laporan-kegiatan/{laporanKegiatan}', [LaporanKegiatanController::class, 'show'])->name('laporan-kegiatan.show');
        Route::get('/laporan-kegiatan/{laporanKegiatan}/edit', [LaporanKegiatanController::class, 'edit'])->name('laporan-kegiatan.edit');
        Route::match(['put', 'patch'], '/laporan-kegiatan/{laporanKegiatan}', [LaporanKegiatanController::class, 'update'])->name('laporan-kegiatan.update');
        Route::delete('/laporan-kegiatan/{laporanKegiatan}', [LaporanKegiatanController::class, 'destroy'])->name('laporan-kegiatan.destroy');
        Route::get('/laporan-kegiatan/{laporanKegiatan}/lampiran', [LaporanKegiatanController::class, 'downloadLampiran'])->name('laporan-kegiatan.lampiran.download');
    });

    // Admin & Instruktur Shared
    Route::middleware('role:admin,instruktur')->group(function () {
        // Modul Kegiatan
        Route::resource('kegiatan', KegiatanController::class);

        // Modul Presensi
        Route::get('/presensi', [PresensiController::class, 'index'])->name('presensi.index');
        Route::get('/presensi/{kegiatan}', [PresensiController::class, 'create'])->name('presensi.show');
        Route::get('/presensi/{kegiatan}/sesi/{sesiKegiatan}', [PresensiController::class, 'showSession'])->name('presensi.sesi.show')->scopeBindings();
        Route::get('/kegiatan/{kegiatan}/sesi', [SesiKegiatanController::class, 'index'])->name('kegiatan.sesi.index');
        Route::post('/kegiatan/{kegiatan}/sesi', [SesiKegiatanController::class, 'store'])->name('kegiatan.sesi.store');
        Route::patch('/kegiatan/{kegiatan}/sesi/{sesiKegiatan}', [SesiKegiatanController::class, 'update'])->name('kegiatan.sesi.update')->scopeBindings();
        Route::delete('/kegiatan/{kegiatan}/sesi/{sesiKegiatan}', [SesiKegiatanController::class, 'destroy'])->name('kegiatan.sesi.destroy')->scopeBindings();
    });

    // Hanya instruktur yang dapat mencatat presensi.
    Route::middleware('role:instruktur')->group(function () {
        Route::get('/laporan-kegiatan/{laporanKegiatan}/download', [LaporanKegiatanController::class, 'downloadPdf'])->name('laporan-kegiatan.download');
        Route::post('/presensi/{kegiatan}/sesi/{sesiKegiatan}', [PresensiController::class, 'store'])->name('presensi.store')->scopeBindings();
        Route::patch('/presensi/{kegiatan}/sesi/{sesiKegiatan}/{presensi}/verifikasi', [PresensiController::class, 'updateVerification'])->name('presensi.verifikasi.update')->scopeBindings();
        Route::resource('kegiatan.materi-kegiatan', MateriKegiatanController::class)
            ->except('show')
            ->scoped();
    });
});

// Kader Routes
Route::middleware(['auth', 'role:kader'])->prefix('kader')->name('kader.')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'kaderDashboard'])->name('dashboard');

    // Modul E-KTA
    Route::get('/ekta', [EktaController::class, 'show'])->name('ekta');
    Route::get('/ekta/download', [EktaController::class, 'download'])->name('ekta.download');

    // Modul Sertifikat
    Route::get('/sertifikat', [SertifikatController::class, 'mySertifikat'])->name('sertifikat.index');
    Route::post('/sertifikat/{presensi}/klaim', [SertifikatController::class, 'klaim'])->name('sertifikat.klaim');
    Route::get('/sertifikat/{sertifikat}/download', [SertifikatController::class, 'download'])->name('sertifikat.download');

    // Modul Riwayat
    Route::get('/riwayat', [RiwayatKeaktifanController::class, 'index'])->name('riwayat.index');

    // Modul Arsip
    Route::get('/arsip', [ArsipController::class, 'kaderIndex'])->name('arsip.index');
    Route::get('/arsip/create', [ArsipController::class, 'kaderCreate'])->name('arsip.create');
    Route::post('/arsip', [ArsipController::class, 'kaderStore'])->name('arsip.store');
    Route::get('/arsip/{arsip}/download', [ArsipController::class, 'kaderDownload'])->name('arsip.download');

    // Modul Materi Kegiatan
    Route::get('/materi', [MateriKegiatanController::class, 'kaderIndex'])->name('materi.index');
    Route::get('/materi/tersimpan', [MateriKegiatanController::class, 'savedIndex'])->name('materi.saved.index');
    Route::post('/materi/{materi_kegiatan}/simpan', [MateriKegiatanController::class, 'save'])->name('materi.save');
    Route::get('/materi/{materi_kegiatan}/unduh', [MateriKegiatanController::class, 'download'])->name('materi.download');
});

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
