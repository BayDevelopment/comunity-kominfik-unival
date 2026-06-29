<?php

use Illuminate\Support\Facades\Route;

Route::inertia('/', 'Home')->name('home');

Route::inertia('/project', 'Project')->name('project');
Route::inertia('/anggota', 'Anggota')->name('anggota');
Route::inertia('/layanan', 'Layanan')->name('layanan');
Route::inertia('/join', 'Join')->name('join');
Route::inertia('/kerjasama', 'Kerjasama')->name('kerjasama');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::inertia('/dashboard', 'Dashboard')->name('dashboard');
});

require __DIR__.'/settings.php';
// require __DIR__.'/auth.php';
