<?php

namespace App\Http\Controllers;

use App\Models\PeriodePendaftaran;
use Illuminate\Http\Request;
use Inertia\Inertia;

class JoinController extends Controller
{
   public function join()
    {
        // Ambil semua data periode tanpa terkecuali, diurutkan dari yang terbaru
        $periodes = PeriodePendaftaran::orderBy('created_at', 'desc')->get();

        return Inertia::render('Join', [
            'periodes' => $periodes,
        ]);
    }
}
