<?php

namespace App\Mail;

use App\Models\PendaftaranAnggota;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class PendaftaranAnggotaDiterima extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public PendaftaranAnggota $pendaftaran)
    {
    }

    public function build()
    {
        return $this->subject('Pendaftaran Anggota KOMINFIK Berhasil Dikirim')
            ->markdown('emails.pendaftaran-anggota-diterima');
    }
}
