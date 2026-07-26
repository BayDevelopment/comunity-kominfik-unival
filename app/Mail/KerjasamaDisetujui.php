<?php

namespace App\Mail;

use App\Models\Kerjasama;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class KerjasamaDisetujui extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public function __construct(public Kerjasama $kerjasama) {}

    public function build()
    {
        return $this->subject('Pengajuan Kerjasama KOMINFIK Disetujui')
            ->view('emails.kerjasama-disetujui');
    }
}
