<?php

namespace App\Mail;

use App\Models\Anggota;
use App\Models\PendaftaranAnggota;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class PendaftaranDiterima extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public function __construct(
        public PendaftaranAnggota $pendaftaran,
        public Anggota $anggota,
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Selamat! Kamu Resmi Menjadi Anggota KOMINFIK',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.pendaftaran-diterima',
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
