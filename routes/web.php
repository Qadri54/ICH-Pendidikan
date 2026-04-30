<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\Admin\SiswaController;
use App\Http\Controllers\Admin\KelasController;
use App\Http\Controllers\Admin\GuruController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\PendaftaranController;
use App\Http\Controllers\Admin\KeuanganController;
use App\Http\Controllers\Admin\TabunganAdminController;
use App\Http\Controllers\Admin\LaporanController;
use App\Http\Controllers\Admin\PengaturanController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', [DashboardController::class, 'index'])
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// ─── Admin Area ───────────────────────────────────────────────────────────────
Route::middleware(['auth', 'role:Admin,Kepala Sekolah,Kepala Yayasan'])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {

        Route::resource('siswa',       SiswaController::class);
        Route::resource('kelas',       KelasController::class)->except('show');
        Route::resource('guru',        GuruController::class)->except('show');
        Route::resource('user',        UserController::class)->except('show');
        Route::resource('keuangan',    KeuanganController::class)->except('show');
        Route::resource('tabungan',    TabunganAdminController::class)->except(['edit', 'update']);

        Route::get('pendaftaran',          [PendaftaranController::class, 'index'])->name('pendaftaran.index');
        Route::get('pendaftaran/{pendaftaran}', [PendaftaranController::class, 'show'])->name('pendaftaran.show');
        Route::patch('pendaftaran/{pendaftaran}', [PendaftaranController::class, 'update'])->name('pendaftaran.update');

        Route::get('laporan',              [LaporanController::class, 'index'])->name('laporan.index');

        Route::get('pengaturan',           [PengaturanController::class, 'index'])->name('pengaturan.index');
        Route::post('pengaturan',          [PengaturanController::class, 'update'])->name('pengaturan.update');
    });

require __DIR__ . '/auth.php';
