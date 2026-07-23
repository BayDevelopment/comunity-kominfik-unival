<?php

namespace App\Http\Controllers;

use App\Mail\PendaftaranKerjasamaDiterima;
use App\Models\Kerjasama;
use DomainException;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Inertia\Inertia;
use Inertia\Response;
use RuntimeException;
use Throwable;

class PendaftaranKerjasamaController extends Controller
{
    private const STATUS = [
        'pending',
        'diproses',
        'disetujui',
        'ditolak',
    ];

    private const JENIS_INSTANSI = [
        'kampus',
        'sma',
        'smk',
        'perusahaan',
        'lainnya',
    ];

    /**
     * Menampilkan daftar pendaftaran kerjasama.
     */
    public function index(Request $request): Response
    {
        $filters = $request->validate([
            'search' => ['nullable', 'string', 'max:100'],
            'status' => ['nullable', Rule::in(self::STATUS)],
            'jenis_instansi' => ['nullable', Rule::in(self::JENIS_INSTANSI)],
        ]);

        $search = trim((string) ($filters['search'] ?? ''));
        $status = $filters['status'] ?? null;
        $jenisInstansi = $filters['jenis_instansi'] ?? null;

        $kerjasamas = Kerjasama::query()
            ->when($search !== '', function ($query) use ($search) {
                $query->where(function ($query) use ($search) {
                    $query
                        ->where('nama_instansi', 'like', "%{$search}%")
                        ->orWhere('nama_pic', 'like', "%{$search}%")
                        ->orWhere('email_pic', 'like', "%{$search}%")
                        ->orWhere('jenis_kerjasama', 'like', "%{$search}%");
                });
            })
            ->when(
                $status,
                fn($query, $status) => $query->where('status', $status),
            )
            ->when(
                $jenisInstansi,
                fn($query, $jenisInstansi) => $query->where(
                    'jenis_instansi',
                    $jenisInstansi,
                ),
            )
            ->latest('tanggal_pengajuan')
            ->paginate(10)
            ->withQueryString();

        return Inertia::render('pendaftaran-kerjasama/index', [
            'kerjasamas' => $kerjasamas,
            'filters' => [
                'search' => $search,
                'status' => $status,
                'jenis_instansi' => $jenisInstansi,
            ],
        ]);
    }

    /**
     * Menampilkan formulir pendaftaran kerjasama.
     */
    public function create(): Response
    {
        return Inertia::render('pendaftaran-kerjasama/create');
    }

    /**
     * Menyimpan pendaftaran kerjasama baru.
     */

    public function store(Request $request): RedirectResponse
    {
        $validated = $this->validateKerjasama($request);
        $data = $this->cleanKerjasamaData($validated, includeAdminFields: false);
        $storedFiles = [];

        try {
            if (
                $proposalPath = $this->storeUploadedFile(
                    $request,
                    'file_proposal',
                    'kerjasama/proposal',
                )
            ) {
                $data['file_proposal'] = $proposalPath;
                $storedFiles[] = $proposalPath;
            }

            if (
                $mouPath = $this->storeUploadedFile(
                    $request,
                    'file_mou',
                    'kerjasama/mou',
                )
            ) {
                $data['file_mou'] = $mouPath;
                $storedFiles[] = $mouPath;
            }

            $data['status'] = 'pending';
            $data['tanggal_pengajuan'] = now();

            $pendaftaran = DB::transaction(
                fn() => Kerjasama::query()->create($data),
                3,
            );

            // Kirim email konfirmasi ke PIC — kegagalan kirim email
            // tidak boleh menggagalkan pengajuan yang sudah tersimpan
            try {
                Mail::to($pendaftaran->email_pic)->send(
                    new PendaftaranKerjasamaDiterima($pendaftaran),
                );
            } catch (Throwable $mailException) {
                report($mailException);
            }

            return redirect()
                ->route('pendaftaran-kerjasama.show', [
                    'pendaftaran' => $pendaftaran->getRouteKey(),
                ])
                ->with('flash', [
                    'toast' => [
                        'type' => 'success',
                        'message' => 'Pendaftaran kerjasama berhasil ditambahkan.',
                    ],
                ]);
        } catch (Throwable $exception) {
            Storage::disk('public')->delete($storedFiles);
            report($exception);

            return back()
                ->withInput()
                ->with('flash', [
                    'toast' => [
                        'type' => 'error',
                        'message' => 'Pendaftaran kerjasama gagal disimpan.',
                    ],
                ]);
        }
    }

    /**
     * Menampilkan detail pendaftaran kerjasama.
     */
    public function show(Kerjasama $pendaftaran): Response
    {
        return Inertia::render('pendaftaran-kerjasama/view', [
            'kerjasama' => $pendaftaran,
        ]);
    }

    /**
     * Menampilkan formulir edit pendaftaran kerjasama.
     */
    public function edit(Kerjasama $pendaftaran): Response
    {
        return Inertia::render('pendaftaran-kerjasama/edit', [
            'kerjasama' => $pendaftaran,
        ]);
    }

    /**
     * Memperbarui data, status, dan catatan admin dari halaman edit.
     */
    public function update(
        Request $request,
        Kerjasama $pendaftaran,
    ): RedirectResponse {
        $validated = $this->validateKerjasama($request, isUpdate: true);
        $data = $this->cleanKerjasamaData($validated, includeAdminFields: true);

        $newFiles = [];
        $filesToDelete = [];

        try {
            if (
                $proposalPath = $this->storeUploadedFile(
                    $request,
                    'file_proposal',
                    'kerjasama/proposal',
                )
            ) {
                $data['file_proposal'] = $proposalPath;
                $newFiles[] = $proposalPath;
            }

            if (
                $mouPath = $this->storeUploadedFile(
                    $request,
                    'file_mou',
                    'kerjasama/mou',
                )
            ) {
                $data['file_mou'] = $mouPath;
                $newFiles[] = $mouPath;
            }

            DB::transaction(function () use (
                $request,
                $pendaftaran,
                $data,
                &$filesToDelete,
            ) {
                $lockedPendaftaran = Kerjasama::query()
                    ->whereKey($pendaftaran->getKey())
                    ->lockForUpdate()
                    ->firstOrFail();

                if (
                    array_key_exists('file_proposal', $data)
                    && $lockedPendaftaran->file_proposal
                ) {
                    $filesToDelete[] = $lockedPendaftaran->file_proposal;
                }

                if (
                    array_key_exists('file_mou', $data)
                    && $lockedPendaftaran->file_mou
                ) {
                    $filesToDelete[] = $lockedPendaftaran->file_mou;
                }

                $statusBaru = $data['status'] ?? $lockedPendaftaran->status;
                $statusBerubah = $statusBaru !== $lockedPendaftaran->status;

                if ($statusBerubah) {
                    if ($statusBaru === 'pending') {
                        $data['diproses_oleh'] = null;
                        $data['tanggal_diproses'] = null;
                    } else {
                        $userId = $request->user()?->getAuthIdentifier();

                        abort_if(
                            ! $userId,
                            401,
                            'Anda harus masuk terlebih dahulu.',
                        );

                        $data['diproses_oleh'] = $userId;
                        $data['tanggal_diproses'] = now();
                    }
                }

                if ($statusBaru !== 'ditolak') {
                    $data['catatan_admin'] = $data['catatan_admin'] ?? null;
                }

                $lockedPendaftaran->update($data);
            }, 3);

            Storage::disk('public')->delete($filesToDelete);

            return redirect()
                ->route('pendaftaran-kerjasama.show', [
                    'pendaftaran' => $pendaftaran->getRouteKey(),
                ])
                ->with('flash', [
                    'toast' => [
                        'type' => 'success',
                        'message' => 'Data kerjasama berhasil diperbarui.',
                    ],
                ]);
        } catch (Throwable $exception) {
            Storage::disk('public')->delete($newFiles);
            report($exception);

            return back()
                ->withInput()
                ->with('flash', [
                    'toast' => [
                        'type' => 'error',
                        'message' => 'Data kerjasama gagal diperbarui.',
                    ],
                ]);
        }
    }

    /**
     * Mengubah status pending menjadi diproses.
     */
    public function proses(
        Request $request,
        Kerjasama $pendaftaran,
    ): RedirectResponse {
        return $this->changeStatus(
            request: $request,
            pendaftaran: $pendaftaran,
            allowedStatuses: ['pending'],
            newStatus: 'diproses',
            successMessage: 'Pengajuan kerjasama sedang diproses.',
        );
    }

    /**
     * Menyetujui pengajuan.
     */
    public function terima(
        Request $request,
        Kerjasama $pendaftaran,
    ): RedirectResponse {
        return $this->changeStatus(
            request: $request,
            pendaftaran: $pendaftaran,
            allowedStatuses: ['pending', 'diproses'],
            newStatus: 'disetujui',
            successMessage: 'Pengajuan kerjasama berhasil disetujui.',
            clearAdminNote: true,
        );
    }

    /**
     * Menolak pengajuan dan menyimpan catatan admin.
     */
    public function tolak(
        Request $request,
        Kerjasama $pendaftaran,
    ): RedirectResponse {
        $validated = $request->validate([
            'catatan_admin' => ['nullable', 'string', 'max:2000'],
        ]);

        $catatanAdmin = isset($validated['catatan_admin'])
            ? trim(strip_tags($validated['catatan_admin']))
            : null;

        return $this->changeStatus(
            request: $request,
            pendaftaran: $pendaftaran,
            allowedStatuses: ['pending', 'diproses'],
            newStatus: 'ditolak',
            successMessage: 'Pengajuan kerjasama berhasil ditolak.',
            catatanAdmin: $catatanAdmin !== '' ? $catatanAdmin : null,
        );
    }

    /**
     * Menghapus pendaftaran beserta dokumennya.
     */
    public function destroy(Kerjasama $pendaftaran): RedirectResponse
    {
        $filesToDelete = [];

        try {
            DB::transaction(function () use (
                $pendaftaran,
                &$filesToDelete,
            ) {
                $lockedPendaftaran = Kerjasama::query()
                    ->whereKey($pendaftaran->getKey())
                    ->lockForUpdate()
                    ->firstOrFail();

                $filesToDelete = array_values(array_filter([
                    $lockedPendaftaran->file_proposal,
                    $lockedPendaftaran->file_mou,
                ]));

                $lockedPendaftaran->delete();
            }, 3);

            Storage::disk('public')->delete($filesToDelete);

            return redirect()
                ->route('pendaftaran-kerjasama.index')
                ->with('flash', [
                    'toast' => [
                        'type' => 'success',
                        'message' => 'Pendaftaran kerjasama berhasil dihapus.',
                    ],
                ]);
        } catch (Throwable $exception) {
            report($exception);

            return back()->with('flash', [
                'toast' => [
                    'type' => 'error',
                    'message' => 'Pendaftaran kerjasama gagal dihapus.',
                ],
            ]);
        }
    }

    /**
     * Validasi formulir tambah dan edit.
     */
    private function validateKerjasama(
        Request $request,
        bool $isUpdate = false,
    ): array {
        $rules = [
            'jenis_instansi' => [
                'bail',
                'required',
                Rule::in(self::JENIS_INSTANSI),
            ],
            'nama_instansi' => ['bail', 'required', 'string', 'max:150'],
            'alamat' => ['nullable', 'string', 'max:5000'],
            'nama_pic' => ['bail', 'required', 'string', 'max:100'],
            'jabatan_pic' => ['nullable', 'string', 'max:100'],
            'email_pic' => [
                'bail',
                'required',
                'string',
                'email:rfc',
                'max:100',
            ],
            'no_hp_pic' => [
                'bail',
                'required',
                'string',
                'max:20',
                'regex:/^[0-9+()\-.\s]{8,20}$/',
            ],
            'jenis_kerjasama' => ['nullable', 'string', 'max:150'],
            'deskripsi_kerjasama' => ['nullable', 'string', 'max:10000'],
            'file_proposal' => [
                $isUpdate ? 'sometimes' : 'nullable',
                'nullable',
                'file',
                'mimes:pdf,doc,docx',
                'max:5120',
            ],
            'file_mou' => [
                $isUpdate ? 'sometimes' : 'nullable',
                'nullable',
                'file',
                'mimes:pdf,doc,docx',
                'max:5120',
            ],
        ];

        if ($isUpdate) {
            $rules['status'] = [
                'required',
                Rule::in(self::STATUS),
            ];

            $rules['catatan_admin'] = [
                'nullable',
                'string',
                'max:2000',
            ];
        }

        return $request->validate($rules, [
            'no_hp_pic.regex' =>
            'Nomor HP hanya boleh berisi angka dan karakter +, -, titik, spasi, atau tanda kurung.',
            'file_proposal.mimes' =>
            'Proposal harus berupa PDF, DOC, atau DOCX.',
            'file_mou.mimes' =>
            'MoU harus berupa PDF, DOC, atau DOCX.',
            'file_proposal.max' =>
            'Ukuran proposal maksimal 5 MB.',
            'file_mou.max' =>
            'Ukuran MoU maksimal 5 MB.',
        ]);
    }

    /**
     * Membersihkan data teks.
     */
    private function cleanKerjasamaData(
        array $validated,
        bool $includeAdminFields,
    ): array {
        unset(
            $validated['file_proposal'],
            $validated['file_mou'],
            $validated['diproses_oleh'],
            $validated['tanggal_pengajuan'],
            $validated['tanggal_diproses'],
        );

        if (! $includeAdminFields) {
            unset(
                $validated['status'],
                $validated['catatan_admin'],
            );
        }

        foreach ($validated as $key => $value) {
            if (is_string($value)) {
                $cleanValue = trim(strip_tags($value));
                $validated[$key] = $cleanValue === '' ? null : $cleanValue;
            }
        }

        if (isset($validated['email_pic'])) {
            $validated['email_pic'] = Str::lower(
                $validated['email_pic'],
            );
        }

        return $validated;
    }

    /**
     * Menyimpan dokumen ke disk public.
     */
    private function storeUploadedFile(
        Request $request,
        string $field,
        string $directory,
    ): ?string {
        if (! $request->hasFile($field)) {
            return null;
        }

        $file = $request->file($field);

        if (! $file || ! $file->isValid()) {
            throw new RuntimeException(
                "File {$field} tidak valid atau gagal diunggah.",
            );
        }

        $path = $file->store($directory, 'public');

        if (! $path) {
            throw new RuntimeException(
                "File {$field} gagal disimpan.",
            );
        }

        return $path;
    }

    /**
     * Mengubah status melalui route khusus secara atomik.
     */
    private function changeStatus(
        Request $request,
        Kerjasama $pendaftaran,
        array $allowedStatuses,
        string $newStatus,
        string $successMessage,
        ?string $catatanAdmin = null,
        bool $clearAdminNote = false,
    ): RedirectResponse {
        $userId = $request->user()?->getAuthIdentifier();

        abort_if(
            ! $userId,
            401,
            'Anda harus masuk terlebih dahulu.',
        );

        try {
            DB::transaction(function () use (
                $pendaftaran,
                $allowedStatuses,
                $newStatus,
                $catatanAdmin,
                $clearAdminNote,
                $userId,
            ) {
                $lockedPendaftaran = Kerjasama::query()
                    ->whereKey($pendaftaran->getKey())
                    ->lockForUpdate()
                    ->firstOrFail();

                if (
                    ! in_array(
                        $lockedPendaftaran->status,
                        $allowedStatuses,
                        true,
                    )
                ) {
                    throw new DomainException(
                        'Status pengajuan sudah berubah sehingga tindakan tidak dapat dilakukan.',
                    );
                }

                $lockedPendaftaran->status = $newStatus;
                $lockedPendaftaran->diproses_oleh = $userId;
                $lockedPendaftaran->tanggal_diproses = now();

                if ($newStatus === 'ditolak') {
                    $lockedPendaftaran->catatan_admin = $catatanAdmin;
                }

                if ($clearAdminNote) {
                    $lockedPendaftaran->catatan_admin = null;
                }

                $lockedPendaftaran->save();
            }, 3);

            return back()->with('flash', [
                'toast' => [
                    'type' => 'success',
                    'message' => $successMessage,
                ],
            ]);
        } catch (DomainException $exception) {
            return back()->with('flash', [
                'toast' => [
                    'type' => 'error',
                    'message' => $exception->getMessage(),
                ],
            ]);
        } catch (Throwable $exception) {
            report($exception);

            return back()->with('flash', [
                'toast' => [
                    'type' => 'error',
                    'message' => 'Status pengajuan gagal diperbarui.',
                ],
            ]);
        }
    }
}
