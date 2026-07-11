<?php

use App\Http\Controllers\AnggotaController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\LayananController;
use App\Http\Controllers\ProjectController;
use Illuminate\Support\Facades\Route;

Route::inertia('/', 'Home')->name('home');

Route::inertia('/project', 'Project')->name('project');
Route::inertia('/anggota', 'Anggota')->name('anggota');
Route::inertia('/layanan', 'Layanan')->name('layanan');
Route::inertia('/join', 'Join')->name('join');
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

    // anggota routes
    Route::get('/anggota', [AnggotaController::class, 'index'])->name('anggota');
    Route::get('/anggota/create', [AnggotaController::class, 'create'])->name('anggota.create');
    Route::post('/anggota', [AnggotaController::class, 'store'])->name('anggota.store');
    Route::get('/anggota/{anggota}', [AnggotaController::class, 'show'])->name('anggota.show');
    Route::get('/anggota/{anggota}/edit', [AnggotaController::class, 'edit'])->name('anggota.edit');
    Route::put('/anggota/{anggota}', [AnggotaController::class, 'update'])->name('anggota.update');
    Route::delete('/anggota/{anggota}', [AnggotaController::class, 'destroy'])->name('anggota.destroy');

    // layanan routes
    Route::get('/layanan', [LayananController::class, 'index'])
        ->name('layanan');
    Route::get('/layanan/create', [LayananController::class, 'create'])
        ->name('layanan.create');
    Route::post('/layanan', [LayananController::class, 'store'])
        ->name('layanan.store');
    Route::get('/layanan/{layanan}', [LayananController::class, 'show'])
        ->name('layanan.show');
    Route::get('/layanan/{layanan}/edit', [LayananController::class, 'edit'])
        ->name('layanan.edit');
    Route::put('/layanan/{layanan}', [LayananController::class, 'update'])
        ->name('layanan.update');
    Route::delete('/layanan/{layanan}', [LayananController::class, 'destroy'])
        ->name('layanan.destroy');
});

require __DIR__ . '/settings.php';
// require __DIR__.'/auth.php';
