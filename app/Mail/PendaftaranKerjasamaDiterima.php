<?php

namespace App\Mail;

use App\Models\Kerjasama;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class PendaftaranKerjasamaDiterima extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public Kerjasama $kerjasama) {}

    public function build()
    {
        return $this->subject('Pengajuan Kerjasama KOMINFIK Berhasil Dikirim')
            ->view('emails.pendaftaran-kerjasama-diterima');
    }
}
