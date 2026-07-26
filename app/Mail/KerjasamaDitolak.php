<?php

namespace App\Mail;

use App\Models\Kerjasama;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class KerjasamaDitolak extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public function __construct(public Kerjasama $kerjasama) {}

    public function build()
    {
        return $this->subject('Informasi Hasil Pengajuan Kerjasama KOMINFIK')
            ->view('emails.kerjasama-ditolak');
    }
}
