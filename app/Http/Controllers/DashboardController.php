<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class DashboardController extends Controller
{
     public function index(): Response
    {
        // TODO:
        // Nanti diganti dengan query database
        // Contoh:
        // Project::count()
        // Kerjasama::count()
        // Layanan::count()
        // Member::count()

        return Inertia::render('Dashboard', [
            'stats' => [
                'project'   => 0,
                'kerjasama' => 0,
                'layanan'   => 0,
                'member'    => 0,
            ],

            // Data grafik
            'chart' => [
                'labels' => [],
                'data' => [],
            ],

            // Aktivitas terbaru
            'activities' => [],
        ]);
    }
}
