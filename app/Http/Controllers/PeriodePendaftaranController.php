<?php

namespace App\Http\Controllers;

use App\Models\PeriodePendaftaran;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class PeriodePendaftaranController extends Controller
{
    /**
     * Jumlah data per halaman, dibatasi agar tidak bisa dibesarkan
     * sembarangan lewat query string (mis. ?per_page=999999).
     */
    private const PER_PAGE = 10;
    private const MAX_PER_PAGE = 50;

    public function index(Request $request)
    {

        $filters = $request->validate([
            'search' => ['nullable', 'string', 'max:100'],
            'jenis' => ['nullable', Rule::in(['anggota', 'kerjasama'])],
            'status' => ['nullable', Rule::in(['active', 'nonactive'])],
            'page' => ['nullable', 'integer', 'min:1'],
        ]);

        $query = PeriodePendaftaran::query();

        if (! empty($filters['search'])) {

            $search = str_replace(['\\', '%', '_'], ['\\\\', '\\%', '\\_'], $filters['search']);
            $query->where('nama_periode', 'like', "%{$search}%");
        }

        if (! empty($filters['jenis'])) {
            $query->where('jenis', $filters['jenis']);
        }

        if (! empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        $periodes = $query
            ->latest('created_at')
            ->paginate(self::PER_PAGE)
            ->withQueryString();

        return inertia('periode/index', [
            'periodes' => $periodes,
            'filters' => [
                'search' => $filters['search'] ?? '',
                'jenis' => $filters['jenis'] ?? '',
                'status' => $filters['status'] ?? '',
            ],
        ]);
    }

    public function create()
    {
        return inertia('periode/create');
    }

    public function store(Request $request)
    {
        $validated = $this->validatePeriode($request);

        PeriodePendaftaran::create($validated);

        return redirect()
            ->route('periode-pendaftaran.index')
            ->with('flash', [
                'toast' => [
                    'type' => 'success',
                    'message' => 'Periode pendaftaran berhasil ditambahkan.',
                ],
            ]);
    }

    public function show(PeriodePendaftaran $periode)
    {
        return inertia('periode/view', [
            'periode' => $periode,
        ]);
    }

    public function edit(PeriodePendaftaran $periode)
    {
        return inertia('periode/edit', [
            'periode' => $periode,
        ]);
    }

    public function update(Request $request, PeriodePendaftaran $periode)
    {
        $validated = $this->validatePeriode($request);

        $periode->update($validated);

        return redirect()
            ->route('periode-pendaftaran.index')
            ->with('flash', [
                'toast' => [
                    'type' => 'success',
                    'message' => 'Periode pendaftaran berhasil diperbarui.',
                ],
            ]);
    }


    public function toggleStatus(PeriodePendaftaran $periode)
    {
        $periode->update([
            'status' => $periode->status === 'active' ? 'nonactive' : 'active',
        ]);

        return back()->with('flash', [
            'toast' => [
                'type' => 'success',
                'message' => $periode->status === 'active'
                    ? 'Pendaftaran berhasil dibuka.'
                    : 'Pendaftaran berhasil ditutup.',
            ],
        ]);
    }

    public function destroy(PeriodePendaftaran $periode)
    {
        $periode->delete();

        return redirect()
            ->route('periode-pendaftaran.index')
            ->with('flash', [
                'toast' => [
                    'type' => 'success',
                    'message' => 'Periode pendaftaran berhasil dihapus.',
                ],
            ]);
    }


    private function validatePeriode(Request $request): array
    {

        if ($request->filled('nama_periode')) {
            $request->merge([
                'nama_periode' => trim(strip_tags($request->input('nama_periode'))),
            ]);
        }

        return $request->validate([
            'jenis' => ['required', Rule::in(['anggota', 'kerjasama'])],
            'nama_periode' => ['nullable', 'string', 'max:100'],
            'tanggal_mulai' => ['nullable', 'date'],
            'tanggal_selesai' => ['nullable', 'date', 'after_or_equal:tanggal_mulai'],
            'status' => ['required', Rule::in(['active', 'nonactive'])],
        ], [
            'jenis.required' => 'Jenis periode wajib dipilih.',
            'jenis.in' => 'Jenis periode tidak valid.',
            'nama_periode.max' => 'Nama periode maksimal 100 karakter.',
            'tanggal_mulai.date' => 'Tanggal mulai tidak valid.',
            'tanggal_selesai.date' => 'Tanggal selesai tidak valid.',
            'tanggal_selesai.after_or_equal' => 'Tanggal selesai tidak boleh sebelum tanggal mulai.',
            'status.required' => 'Status wajib dipilih.',
            'status.in' => 'Status tidak valid.',
        ]);
    }
}
