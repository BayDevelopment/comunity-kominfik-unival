<?php

use App\Http\Controllers\AnggotaController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\JoinController;
use App\Http\Controllers\LayananController;
use App\Http\Controllers\PeriodePendaftaranController;
use App\Http\Controllers\ProjectController;
use App\Http\Controllers\Settings\ProfileController;
use App\Http\Controllers\Settings\SecurityController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::inertia('/', 'Home')->name('home');

Route::inertia('/project', 'Project')->name('project');
Route::inertia('/anggota', 'Anggota')->name('anggota');
Route::inertia('/layanan', 'Layanan')->name('layanan');
Route::get('/join', [JoinController::class, 'join']);
Route::inertia('/kerjasama', 'Kerjasama')->name('kerjasama');

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

    // Appearance (murni client-side, tidak butuh controller)
    Route::get('settings/appearance', function () {
        return Inertia::render('settings/Appearance');
    })->name('appearance.edit');

    // Activity Log
    Route::get('/activity-log', [\App\Http\Controllers\ActivityLogController::class, 'index'])->name('activity-log.index');
});

require __DIR__ . '/settings.php';
