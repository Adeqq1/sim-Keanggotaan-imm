<?php

namespace App\Mail;

use App\Models\Pendaftaran;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class PendaftaranDisetujuiMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public function __construct(
        public Pendaftaran $pendaftaran,
        public string $passwordSementara,
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
                'password' => $this->passwordSementara,
                'loginUrl' => route('login'),
            ],
        );
    }
}
