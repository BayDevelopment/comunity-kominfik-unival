<?php

use App\Http\Controllers\ActivityLogController;
use App\Http\Controllers\Admin\CertificateController;
use App\Http\Controllers\AnggotaController;
use App\Http\Controllers\CertificateTemplateController;
use App\Http\Controllers\CertificateVerificationController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\JoinController;
use App\Http\Controllers\LayananController;
use App\Http\Controllers\PendaftaranAnggotaController;
use App\Http\Controllers\PendaftaranKerjasamaController;
use App\Http\Controllers\PeriodePendaftaranController;
use App\Http\Controllers\ProjectController;
use App\Http\Controllers\Settings\ProfileController;
use App\Http\Controllers\Settings\SecurityController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

// ---------- ROUTE PUBLIK ----------
Route::get('/', [HomeController::class, 'index'])->name('home');

// Ditambahkan agar middleware 'auth' tahu kemana harus me-redirect user
Route::get('/login', function () {
    return Inertia::render('auth/Login');
})->name('login');

Route::get('/join', [JoinController::class, 'join']);

Route::get('/join/anggota', [JoinController::class, 'anggota'])->name('join.anggota');
Route::post('/join/anggota', [JoinController::class, 'storeAnggota'])
    ->middleware('throttle:5,1')
    ->name('join.anggota.store');

Route::get('/join/kerjasama', [JoinController::class, 'kerjasamaUniversity'])->name('join.kerjasama.university');
Route::post('/join/kerjasama', [JoinController::class, 'storeKerjasama'])
    ->middleware('throttle:5,1')
    ->name('join.kerjasama.university.store');

// Certificate Verification (Public)
Route::get('/sertifikat', [CertificateVerificationController::class, 'index'])
    ->name('sertifikat.index');

Route::post('/sertifikat/cari', [CertificateVerificationController::class, 'search'])
    ->middleware('throttle:5,1')
    ->name('sertifikat.cari');

Route::get('/sertifikat/{verificationCode}/download', [CertificateVerificationController::class, 'download'])
    ->name('sertifikat.download');
    
// ---------- ROUTE PROTECTED (MEMBER / ADMIN) ----------
Route::middleware(['auth', 'verified', 'academy'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])
        ->name('dashboard');

    // Project Routes
    Route::get('/project', [ProjectController::class, 'index'])->name('project');
    Route::get('/project/create', [ProjectController::class, 'create'])->name('project.create');
    Route::post('/project', [ProjectController::class, 'store'])->name('project.store');
    Route::get('/project/{project}', [ProjectController::class, 'show'])->name('project.show');
    Route::get('/project/{project}/edit', [ProjectController::class, 'edit'])->name('project.edit');
    Route::put('/project/{project}', [ProjectController::class, 'update'])->name('project.update');
    Route::delete('/project/{project}', [ProjectController::class, 'destroy'])->name('project.destroy');

    // Anggota routes
    Route::get('/anggota', [AnggotaController::class, 'index'])->name('anggota');
    Route::get('/anggota/create', [AnggotaController::class, 'create'])->name('anggota.create');
    Route::post('/anggota', [AnggotaController::class, 'store'])->name('anggota.store');
    Route::get('/anggota/{anggota}', [AnggotaController::class, 'show'])->name('anggota.show');
    Route::get('/anggota/{anggota}/edit', [AnggotaController::class, 'edit'])->name('anggota.edit');
    Route::put('/anggota/{anggota}', [AnggotaController::class, 'update'])->name('anggota.update');
    Route::delete('/anggota/{anggota}', [AnggotaController::class, 'destroy'])->name('anggota.destroy');

    // Layanan routes
    Route::get('/layanan', [LayananController::class, 'index'])->name('layanan');
    Route::get('/layanan/create', [LayananController::class, 'create'])->name('layanan.create');
    Route::post('/layanan', [LayananController::class, 'store'])->name('layanan.store');
    Route::get('/layanan/{layanan}', [LayananController::class, 'show'])->name('layanan.show');
    Route::get('/layanan/{layanan}/edit', [LayananController::class, 'edit'])->name('layanan.edit');
    Route::put('/layanan/{layanan}', [LayananController::class, 'update'])->name('layanan.update');
    Route::delete('/layanan/{layanan}', [LayananController::class, 'destroy'])->name('layanan.destroy');

    // Periode Pendaftaran routes
    Route::get('/periode-pendaftaran', [PeriodePendaftaranController::class, 'index'])->name('periode-pendaftaran.index');
    Route::get('/periode-pendaftaran/create', [PeriodePendaftaranController::class, 'create'])->name('periode-pendaftaran.create');
    Route::post('/periode-pendaftaran', [PeriodePendaftaranController::class, 'store'])->name('periode-pendaftaran.store');
    Route::get('/periode-pendaftaran/{periode}', [PeriodePendaftaranController::class, 'show'])->name('periode-pendaftaran.show');
    Route::get('/periode-pendaftaran/{periode}/edit', [PeriodePendaftaranController::class, 'edit'])->name('periode-pendaftaran.edit');
    Route::put('/periode-pendaftaran/{periode}', [PeriodePendaftaranController::class, 'update'])->name('periode-pendaftaran.update');
    Route::patch('/periode-pendaftaran/{periode}/toggle-status', [PeriodePendaftaranController::class, 'toggleStatus'])
        ->name('periode-pendaftaran.toggle-status');
    Route::delete('/periode-pendaftaran/{periode}', [PeriodePendaftaranController::class, 'destroy'])->name('periode-pendaftaran.destroy');

    // Pendaftaran Anggota routes
    Route::get('/pendaftaran-anggota', [PendaftaranAnggotaController::class, 'index'])->name('pendaftaran-anggota.index');
    Route::get('/pendaftaran-anggota/create', [PendaftaranAnggotaController::class, 'create'])->name('pendaftaran-anggota.create');
    Route::post('/pendaftaran-anggota', [PendaftaranAnggotaController::class, 'store'])->name('pendaftaran-anggota.store');
    Route::get('/pendaftaran-anggota/{pendaftaranAnggota}', [PendaftaranAnggotaController::class, 'show'])->name('pendaftaran-anggota.show');
    Route::get('/pendaftaran-anggota/{pendaftaranAnggota}/edit', [PendaftaranAnggotaController::class, 'edit'])->name('pendaftaran-anggota.edit');
    Route::put('/pendaftaran-anggota/{pendaftaranAnggota}', [PendaftaranAnggotaController::class, 'update'])->name('pendaftaran-anggota.update');
    Route::patch('/pendaftaran-anggota/{pendaftaranAnggota}/terima', [PendaftaranAnggotaController::class, 'terima'])
        ->name('pendaftaran-anggota.terima');
    Route::patch('/pendaftaran-anggota/{pendaftaranAnggota}/tolak', [PendaftaranAnggotaController::class, 'tolak'])
        ->name('pendaftaran-anggota.tolak');
    Route::delete('/pendaftaran-anggota/{pendaftaranAnggota}', [PendaftaranAnggotaController::class, 'destroy'])->name('pendaftaran-anggota.destroy');

    // Pendaftaran Kerjasama routes
    Route::get('/pendaftaran-kerjasama', [PendaftaranKerjasamaController::class, 'index'])->name('pendaftaran-kerjasama.index');
    Route::get('/pendaftaran-kerjasama/create', [PendaftaranKerjasamaController::class, 'create'])->name('pendaftaran-kerjasama.create');
    Route::post('/pendaftaran-kerjasama', [PendaftaranKerjasamaController::class, 'store'])->name('pendaftaran-kerjasama.store');
    Route::get('/pendaftaran-kerjasama/{pendaftaran}/edit', [PendaftaranKerjasamaController::class, 'edit'])->name('pendaftaran-kerjasama.edit');
    Route::put('/pendaftaran-kerjasama/{pendaftaran}', [PendaftaranKerjasamaController::class, 'update'])->name('pendaftaran-kerjasama.update');
    Route::patch('/pendaftaran-kerjasama/{pendaftaran}/proses', [PendaftaranKerjasamaController::class, 'proses'])->name('pendaftaran-kerjasama.proses');
    Route::patch('/pendaftaran-kerjasama/{pendaftaran}/terima', [PendaftaranKerjasamaController::class, 'terima'])->name('pendaftaran-kerjasama.terima');
    Route::patch('/pendaftaran-kerjasama/{pendaftaran}/tolak', [PendaftaranKerjasamaController::class, 'tolak'])->name('pendaftaran-kerjasama.tolak');
    Route::get('/pendaftaran-kerjasama/{pendaftaran}', [PendaftaranKerjasamaController::class, 'show'])->name('pendaftaran-kerjasama.show');
    Route::delete('/pendaftaran-kerjasama/{pendaftaran}', [PendaftaranKerjasamaController::class, 'destroy'])->name('pendaftaran-kerjasama.destroy');

    // Certificate (Admin)
    Route::get('/certificate', [CertificateController::class, 'index'])->name('certificate.index');
    Route::get('/certificate/create', [CertificateController::class, 'create'])->name('certificate.create');
    Route::post('/certificate', [CertificateController::class, 'store'])->name('certificate.store');
    Route::get('/certificate/{certificate}', [CertificateController::class, 'show'])->name('certificate.show');
    Route::get('/certificate/{certificate}/edit', [CertificateController::class, 'edit'])->name('certificate.edit');
    Route::put('/certificate/{certificate}', [CertificateController::class, 'update'])->name('certificate.update');
    Route::delete('/certificate/{certificate}', [CertificateController::class, 'destroy'])->name('certificate.destroy');

    // certificate-template
    Route::resource('certificate-template', CertificateTemplateController::class)->parameters([
        'certificate-template' => 'certificateTemplate'
    ]);

    // Custom action
    Route::patch('certificate/{certificate}/revoke', [CertificateController::class, 'revoke'])
        ->name('certificate.revoke');

    Route::get('/certificate/{certificate}/download', [CertificateController::class, 'download'])
        ->name('certificate.download');

    // Users
    Route::get('/users', [UserController::class, 'index'])->name('users.index');
    Route::get('/users/create', [UserController::class, 'create'])->name('users.create');
    Route::post('/users', [UserController::class, 'store'])->name('users.store');
    Route::post('/users/{user}/resend-verification', [UserController::class, 'resendVerification'])
        ->name('users.resend-verification');
    Route::get('/users/{user}', [UserController::class, 'show'])->name('users.show');
    Route::get('/users/{user}/edit', [UserController::class, 'edit'])->name('users.edit');
    Route::put('/users/{user}', [UserController::class, 'update'])->name('users.update');
    Route::delete('/users/{user}', [UserController::class, 'destroy'])->name('users.destroy');

    // Settings
    Route::redirect('settings', 'settings/profile');

    // Profile
    Route::get('settings/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('settings/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('settings/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Appearance
    Route::get('settings/appearance', function () {
        return Inertia::render('settings/Appearance');
    })->name('appearance.edit');

    // Activity Log
    Route::delete('/activity-log', [ActivityLogController::class, 'destroyAll'])
        ->name('activity-log.destroy-all');
    Route::get('/activity-log', [ActivityLogController::class, 'index'])->name('activity-log.index');
});

require __DIR__ . '/settings.php';
