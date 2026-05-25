<?php

namespace App\Mail;

use App\Models\Pendaftaran;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class PendaftaranDitolakMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public function __construct(
        public Pendaftaran $pendaftaran,
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Pendaftaran Anggota IMM Ditolak',
        );
    }

    public function content(): Content
    {
        return new Content(
            markdown: 'emails.pendaftaran.ditolak',
            with: [
                'nama' => $this->pendaftaran->nama_lengkap,
                'catatan' => $this->pendaftaran->catatan_admin,
                'pendaftaranUrl' => route('pendaftaran'),
            ],
        );
    }
}
