<?php

namespace App\Mail;

use App\Models\PendaftaranAnggota;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
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
            ->view('emails.pendaftaran-anggota-diterima');
    }
}
