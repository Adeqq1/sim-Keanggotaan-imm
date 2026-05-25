<?php

use App\Mail\PendaftaranDisetujuiMail;
use App\Mail\PendaftaranDitolakMail;
use App\Models\Pendaftaran;
use App\Models\User;
use Illuminate\Support\Facades\Mail;

beforeEach(function () {
    $this->admin = User::factory()->admin()->create();
    $this->pendaftaran = Pendaftaran::factory()->create([
        'email' => 'calon@example.com',
        'status_validasi' => 'pending',
    ]);
});

it('mengirim email persetujuan saat pendaftaran disetujui', function () {
    Mail::fake();

    $this->actingAs($this->admin)
        ->post(route('admin.pendaftaran.validate', $this->pendaftaran->id), [
            'status' => 'disetujui',
        ])
        ->assertRedirect(route('admin.pendaftaran.index'));

    Mail::assertQueued(PendaftaranDisetujuiMail::class, function (PendaftaranDisetujuiMail $mail) {
        return $mail->hasTo('calon@example.com');
    });
});

it('mengirim email penolakan saat pendaftaran ditolak', function () {
    Mail::fake();

    $this->actingAs($this->admin)
        ->post(route('admin.pendaftaran.validate', $this->pendaftaran->id), [
            'status' => 'ditolak',
            'catatan_admin' => 'Berkas tidak lengkap.',
        ])
        ->assertRedirect(route('admin.pendaftaran.index'));

    Mail::assertQueued(PendaftaranDitolakMail::class, function (PendaftaranDitolakMail $mail) {
        return $mail->hasTo('calon@example.com')
            && $mail->pendaftaran->catatan_admin === 'Berkas tidak lengkap.';
    });
});

it('tidak mengirim email disetujui saat pendaftaran ditolak', function () {
    Mail::fake();

    $this->actingAs($this->admin)
        ->post(route('admin.pendaftaran.validate', $this->pendaftaran->id), [
            'status' => 'ditolak',
            'catatan_admin' => 'Data tidak valid.',
        ]);

    Mail::assertNotQueued(PendaftaranDisetujuiMail::class);
});

it('tidak mengirim email ditolak saat pendaftaran disetujui', function () {
    Mail::fake();

    $this->actingAs($this->admin)
        ->post(route('admin.pendaftaran.validate', $this->pendaftaran->id), [
            'status' => 'disetujui',
        ]);

    Mail::assertNotQueued(PendaftaranDitolakMail::class);
});
