<?php

namespace App\Http\Controllers;

use App\Models\Anggota;
use App\Models\Kerjasama;
use App\Models\Layanan;
use App\Models\PendaftaranAnggota;
use App\Models\Project;
use Illuminate\Support\Carbon;
use Carbon\CarbonInterface;
use Inertia\Inertia;
use Inertia\Response;

class DashboardController extends Controller
{
    public function index(): Response
    {
        return Inertia::render('Dashboard', [
            'stats' => $this->getStats(),
            'chart' => $this->getChartData(),
            'activities' => $this->getRecentActivities(),
        ]);
    }

    /**
     * Ringkasan angka total tiap modul (untuk 4 kartu di atas).
     * Total di sini mencakup SEMUA status (bukan cuma yang aktif/disetujui),
     * karena ini dashboard internal admin, bukan halaman publik.
     */
    private function getStats(): array
    {
        return [
            'project'   => Project::count(),
            'kerjasama' => Kerjasama::count(),
            'layanan'   => Layanan::count(),
            'member'    => Anggota::count(),
        ];
    }

    /**
     * Data grafik: jumlah project baru per bulan, 6 bulan terakhir.
     */
    private function getChartData(): array
    {
        $months = collect(range(5, 0))->map(function (int $i) {
            return now()->subMonths($i);
        });

        $labels = $months->map(fn (CarbonInterface $date) => $date->format('M Y'))->toArray();

        $data = $months->map(function (CarbonInterface $date) {
            return Project::whereYear('created_at', $date->year)
                ->whereMonth('created_at', $date->month)
                ->count();
        })->toArray();

        return [
            'labels' => $labels,
            'data' => $data,
        ];
    }

    /**
     * Aktivitas terbaru, digabung dari beberapa tabel (project, kerjasama,
     * pendaftaran anggota), diurutkan dari yang paling baru, diambil 5 teratas.
     */
    private function getRecentActivities(): array
    {
        $projectActivities = Project::latest()->take(5)->get()->map(function (Project $item) {
            return [
                'type' => 'project',
                'title' => 'Project baru: ' . $item->nama,
                'description' => $item->klien ? 'Klien: ' . $item->klien : 'Status: ' . ucfirst($item->status),
                'created_at' => $item->created_at,
            ];
        });

        $kerjasamaActivities = Kerjasama::latest()->take(5)->get()->map(function (Kerjasama $item) {
            return [
                'type' => 'kerjasama',
                'title' => 'Pengajuan kerjasama: ' . $item->nama_instansi,
                'description' => 'Status: ' . ucfirst($item->status),
                'created_at' => $item->created_at,
            ];
        });

        $anggotaActivities = PendaftaranAnggota::latest()->take(5)->get()->map(function (PendaftaranAnggota $item) {
            return [
                'type' => 'anggota',
                'title' => 'Pendaftaran anggota: ' . $item->nama,
                'description' => 'Status: ' . ucfirst($item->status),
                'created_at' => $item->created_at,
            ];
        });

        return $projectActivities
            ->concat($kerjasamaActivities)
            ->concat($anggotaActivities)
            ->sortByDesc('created_at')
            ->take(5)
            ->map(function (array $item) {
                $item['time'] = Carbon::parse($item['created_at'])->diffForHumans();
                unset($item['created_at']);

                return $item;
            })
            ->values()
            ->toArray();
    }
}
