<?php

namespace App\Http\Controllers;

use App\Models\Anggota;
use App\Models\Kerjasama;
use App\Models\Layanan;
use App\Models\Project;
use Inertia\Inertia;

class HomeController extends Controller
{
    public function index()
    {
        $stats = [
            // Jumlah project yang sudah/sedang berjalan (tidak termasuk yang dibatalkan)
            'project' => Project::whereIn('status', ['aktif', 'selesai', 'ditunda'])->count(),

            // Jumlah anggota yang statusnya masih aktif
            'anggota' => Anggota::where('status', 'aktif')->count(),

            // Jumlah layanan yang statusnya aktif
            'layanan' => Layanan::where('status', 'aktif')->count(),

            // Jumlah mitra kerjasama yang sudah disetujui (dipakai di kartu kecil hero)
            'mitra' => Kerjasama::where('status', 'disetujui')->count(),
        ];

        $projects = Project::whereIn('status', ['aktif', 'selesai'])
            ->orderByDesc('created_at')
            ->take(6)
            ->get();

        $layanans = Layanan::where('status', 'aktif')
            ->orderByDesc('created_at')
            ->get();

        // Daftar anggota aktif untuk carousel di halaman utama
        $anggotas = Anggota::where('status', 'aktif')
            ->orderByDesc('created_at')
            ->get();

        return Inertia::render('Home', [
            'stats' => $stats,
            'projects' => $projects,
            'layanans' => $layanans,
            'anggotas' => $anggotas,
        ]);
    }
}
