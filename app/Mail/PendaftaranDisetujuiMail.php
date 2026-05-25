<?php

namespace App\Mail;

use App\Models\Pendaftaran;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Log;

class PendaftaranDisetujuiMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    /**
     * Jumlah percobaan ulang jika pengiriman email gagal.
     */
    public int $tries = 3;

    /**
     * Jeda antar retry dalam detik (1 menit, 5 menit, 15 menit).
     *
     * @var array<int, int>
     */
    public array $backoff = [60, 300, 900];

    /**
     * @param  string  $passwordSementaraEncrypted  Password di-enkripsi sebelum masuk queue payload.
     */
    public function __construct(
        public Pendaftaran $pendaftaran,
        public string $passwordSementaraEncrypted,
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Pendaftaran Anggota IMM Disetujui',
        );
    }

    public function content(): Content
    {
        return new Content(
            markdown: 'emails.pendaftaran.disetujui',
            with: [
                'nama' => $this->pendaftaran->nama_lengkap,
                'email' => $this->pendaftaran->email,
                'password' => Crypt::decryptString($this->passwordSementaraEncrypted),
                'loginUrl' => route('login'),
            ],
        );
    }

    /**
     * Dipanggil oleh queue worker setelah semua retry habis dan email tetap gagal.
     */
    public function failed(\Throwable $e): void
    {
        Log::error('Email persetujuan pendaftaran gagal terkirim setelah semua retry', [
            'pendaftaran_id' => $this->pendaftaran->id,
            'email' => $this->pendaftaran->email,
            'exception' => $e::class,
            'message' => $e->getMessage(),
            'trace' => $e->getTraceAsString(),
        ]);
    }
}
