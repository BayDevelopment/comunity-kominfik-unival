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
    Route::get('/anggota', [AnggotaController::class, 'index'])
        ->name('anggota');

    // layanan routes
    Route::get('/layanan', [LayananController::class, 'index'])
        ->name('layanan');
});

require __DIR__ . '/settings.php';
// require __DIR__.'/auth.php';
