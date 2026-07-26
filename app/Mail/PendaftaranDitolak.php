<?php

namespace App\Mail;

use App\Models\PendaftaranAnggota;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class PendaftaranDitolak extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public function __construct(
        public PendaftaranAnggota $pendaftaran,
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Informasi Hasil Pendaftaran Anggota KOMINFIK',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.pendaftaran-ditolak',
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
