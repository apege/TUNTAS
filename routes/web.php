<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\DashboardController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    if (Auth::check()) {
        $peran = Auth::user()->peran;

        return match ($peran) {
            'admin_rt' => redirect()->route('admin-rt.dashboard'),
            'admin_rw' => redirect()->route('admin-rw.dashboard'),
            'petugas' => redirect()->route('petugas.dashboard'),
            default => redirect()->route('warga.dashboard'),
        };
    }

    return redirect()->route('login');
});

// Autentikasi
Route::middleware('guest')->group(function () {
    Route::get('/login', [LoginController::class, 'create'])->name('login');
    Route::post('/login', [LoginController::class, 'store'])->name('login.store');
});

Route::post('/logout', [LoginController::class, 'destroy'])
    ->middleware('auth')
    ->name('logout');

// Workspace Dashboard per Peran (PRD Section 08)
Route::middleware('auth')->group(function () {
    Route::get('/dashboard', function () {
        $peran = Auth::user()->peran;

        return match ($peran) {
            'admin_rt' => redirect()->route('admin-rt.dashboard'),
            'admin_rw' => redirect()->route('admin-rw.dashboard'),
            'petugas' => redirect()->route('petugas.dashboard'),
            default => redirect()->route('warga.dashboard'),
        };
    })->name('dashboard');

    Route::get('/warga/dashboard', [DashboardController::class, 'warga'])->name('warga.dashboard');
    Route::get('/admin-rt/dashboard', [DashboardController::class, 'adminRt'])->name('admin-rt.dashboard');
    Route::get('/admin-rw/dashboard', [DashboardController::class, 'adminRw'])->name('admin-rw.dashboard');
    Route::get('/petugas/dashboard', [DashboardController::class, 'petugas'])->name('petugas.dashboard');
});
