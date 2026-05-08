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
use App\Http\Controllers\Admin\PembayaranPendaftaranController;
use App\Http\Controllers\Admin\PengaturanController;
use App\Http\Controllers\Admin\AbsensiSiswaController as AdminAbsensiController;
use App\Http\Controllers\OrangTua\PendaftaranController as OrangTuaPendaftaranController;
use App\Http\Controllers\OrangTua\KeuanganController as OrangTuaKeuanganController;
use App\Http\Controllers\OrangTua\TabunganController as OrangTuaTabunganController;
use App\Http\Controllers\OrangTua\ProfilAnakController;
use App\Http\Controllers\OrangTua\KehadiranController as OrangTuaKehadiranController;
use App\Http\Controllers\Guru\TabunganGuruController;
use App\Http\Controllers\Guru\AbsensiSiswaController as GuruAbsensiController;
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

// ─── Orang Tua Area ────────────────────────────────────────────────────
Route::middleware(['auth', 'role:Orang Tua'])->group(function () {
    Route::get('/beranda', fn() => view('orang-tua.beranda'))->name('beranda');
    Route::get('/kehadiran', [OrangTuaKehadiranController::class, 'index'])->name('kehadiran');
    Route::get('/akademik', fn() => view('orang-tua.placeholder', ['pageTitle' => 'Akademik']))->name('akademik');
    Route::get('/pembayaran', [OrangTuaKeuanganController::class, 'index'])->name('pembayaran');
    Route::post('/pembayaran/pendaftaran/{fee}', [OrangTuaKeuanganController::class, 'storeRegistrationPayment'])->name('pembayaran.pendaftaran');
    Route::post('/pembayaran/spp/{invoice}', [OrangTuaKeuanganController::class, 'storeSppPayment'])->name('pembayaran.spp');
    Route::get('/tabungan', [OrangTuaTabunganController::class, 'index'])->name('tabungan');
    Route::get('/profil-anak', [ProfilAnakController::class, 'index'])->name('profil-anak');
    Route::get('/pengaturan', [ProfileController::class, 'edit'])->name('pengaturan');
    Route::get('/pendaftaran',      [OrangTuaPendaftaranController::class, 'index'])->name('pendaftaran');
    Route::get('/pendaftaran/buat', [OrangTuaPendaftaranController::class, 'create'])->name('pendaftaran.create');
    Route::post('/pendaftaran',     [OrangTuaPendaftaranController::class, 'store'])->name('pendaftaran.store');
});

// ─── Guru Area ───────────────────────────────────────────────────────────────
Route::middleware(['auth', 'role:Guru'])->prefix('guru')->name('guru.')->group(function () {

    Route::get('absensi',       [GuruAbsensiController::class, 'index'])->name('absensi.index');
    Route::post('absensi',      [GuruAbsensiController::class, 'store'])->name('absensi.store');

    Route::prefix('tabungan')->name('tabungan.')->group(function () {
        Route::get('/',                               [TabunganGuruController::class, 'index'])->name('index');
        Route::get('/passbooks/{passbook}',           [TabunganGuruController::class, 'showPassbook'])->name('passbook.show');
        Route::post('/passbooks/{passbook}/deposit',  [TabunganGuruController::class, 'deposit'])->name('passbook.deposit');
        Route::post('/passbooks/{passbook}/withdraw', [TabunganGuruController::class, 'withdraw'])->name('passbook.withdraw');
        Route::get('/{ledger}',                       [TabunganGuruController::class, 'show'])->name('show');
    });
});

// ─── Admin Area ───────────────────────────────────────────────────────────────
Route::middleware(['auth', 'role:Admin,Kepala Sekolah,Kepala Yayasan'])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {

        Route::resource('siswa', SiswaController::class)->except(['create', 'store']);
        Route::resource('kelas', KelasController::class, ['parameters' => ['kelas' => 'kelas']])->except('show');
        Route::resource('guru', GuruController::class)->except('show');
        Route::resource('user', UserController::class)->except('show');
        Route::post('keuangan/generate', [KeuanganController::class, 'generate'])->name('keuangan.generate');
        Route::resource('keuangan', KeuanganController::class)->only(['index', 'edit', 'update', 'destroy']);

        Route::get('tabungan/{tabungan}/passbooks/create',    [TabunganAdminController::class, 'openPassbook'])->name('tabungan.passbook.create');
        Route::post('tabungan/{tabungan}/passbooks',          [TabunganAdminController::class, 'storePassbook'])->name('tabungan.passbook.store');
        Route::get('tabungan/passbooks/{passbook}',           [TabunganAdminController::class, 'showPassbook'])->name('tabungan.passbook.show');
        Route::post('tabungan/passbooks/{passbook}/deposit',  [TabunganAdminController::class, 'deposit'])->name('tabungan.passbook.deposit');
        Route::post('tabungan/passbooks/{passbook}/withdraw', [TabunganAdminController::class, 'withdraw'])->name('tabungan.passbook.withdraw');
        Route::resource('tabungan', TabunganAdminController::class)->except(['edit', 'update']);

        Route::get('absensi',  [AdminAbsensiController::class, 'index'])->name('absensi.index');
        Route::post('absensi', [AdminAbsensiController::class, 'store'])->name('absensi.store');

        Route::get('pendaftaran', [PendaftaranController::class, 'index'])->name('pendaftaran.index');
        Route::get('pendaftaran/{pendaftaran}', [PendaftaranController::class, 'show'])->name('pendaftaran.show');
        Route::patch('pendaftaran/{pendaftaran}', [PendaftaranController::class, 'update'])->name('pendaftaran.update');

        Route::get('pembayaran-pendaftaran', [PembayaranPendaftaranController::class, 'index'])->name('pembayaran-pendaftaran.index');
        Route::post('pembayaran-pendaftaran/{transaksi}/approve', [PembayaranPendaftaranController::class, 'approve'])->name('pembayaran-pendaftaran.approve');
        Route::post('pembayaran-pendaftaran/{transaksi}/reject', [PembayaranPendaftaranController::class, 'reject'])->name('pembayaran-pendaftaran.reject');

        Route::get('laporan', [LaporanController::class, 'index'])->name('laporan.index');

        Route::get('pengaturan', [PengaturanController::class, 'index'])->name('pengaturan.index');
        Route::post('pengaturan', [PengaturanController::class, 'update'])->name('pengaturan.update');
        Route::post('pengaturan/toggle-pendaftaran', [PengaturanController::class, 'togglePendaftaran'])->name('pengaturan.toggle-pendaftaran');
    });

require __DIR__ . '/auth.php';
