<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\NotificationController;
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
use App\Http\Controllers\Admin\AbsensiGuruController as AdminAbsensiGuruController;
use App\Http\Controllers\Admin\RaportController as AdminRaportController;
use App\Http\Controllers\OrangTua\PendaftaranController as OrangTuaPendaftaranController;
use App\Http\Controllers\OrangTua\KeuanganController as OrangTuaKeuanganController;
use App\Http\Controllers\OrangTua\TabunganController as OrangTuaTabunganController;
use App\Http\Controllers\OrangTua\ProfilAnakController;
use App\Http\Controllers\OrangTua\KehadiranController as OrangTuaKehadiranController;
use App\Http\Controllers\OrangTua\AkademikController as OrangTuaAkademikController;
use App\Http\Controllers\OrangTua\BerandaController as OrangTuaBerandaController;
use App\Http\Controllers\Guru\TabunganGuruController;
use App\Http\Controllers\Guru\AbsensiSiswaController as GuruAbsensiController;
use App\Http\Controllers\Guru\AbsensiGuruController as GuruAbsensiGuruController;
use App\Http\Controllers\Guru\RaportController as GuruRaportController;
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

    Route::get('/notifications',               [NotificationController::class, 'index'])->name('notifications.index');
    Route::post('/notifications/{id}/read',   [NotificationController::class, 'markAsRead'])->name('notifications.read');
    Route::delete('/notifications/{id}',      [NotificationController::class, 'destroy'])->name('notifications.delete');
    Route::post('/notifications/read-all',    [NotificationController::class, 'markAllAsRead'])->name('notifications.read-all');
});

// ─── Orang Tua Area ────────────────────────────────────────────────────
Route::middleware(['auth', 'role:Orang Tua'])->group(function () {
    Route::get('/beranda', [OrangTuaBerandaController::class, 'index'])->name('beranda');
    Route::get('/kehadiran', [OrangTuaKehadiranController::class, 'index'])->name('kehadiran');
    Route::get('/akademik', [OrangTuaAkademikController::class, 'index'])->name('akademik');
    Route::get('/raport/{id}/download', [OrangTuaAkademikController::class, 'download'])->name('raport.download');
    Route::get('/pembayaran',             [OrangTuaKeuanganController::class, 'index'])->name('pembayaran');
    Route::get('/pembayaran/pendaftaran', [OrangTuaKeuanganController::class, 'pendaftaran'])->name('pembayaran.pendaftaran.index');
    Route::get('/pembayaran/spp',         [OrangTuaKeuanganController::class, 'spp'])->name('pembayaran.spp.index');
    Route::post('/pembayaran/pendaftaran/{fee}', [OrangTuaKeuanganController::class, 'storeRegistrationPayment'])->name('pembayaran.pendaftaran');
    Route::post('/pembayaran/spp/{invoice}', [OrangTuaKeuanganController::class, 'storeSppPayment'])->name('pembayaran.spp');
    Route::get('/tabungan', [OrangTuaTabunganController::class, 'index'])->name('tabungan');
    Route::get('/profil-anak', [ProfilAnakController::class, 'index'])->name('profil-anak');
    Route::get('/pengaturan', [ProfileController::class, 'edit'])->name('pengaturan');
    Route::get('/pendaftaran',      [OrangTuaPendaftaranController::class, 'index'])->name('pendaftaran');
    Route::get('/pendaftaran/buat', [OrangTuaPendaftaranController::class, 'create'])->name('pendaftaran.create');
    Route::post('/pendaftaran',     [OrangTuaPendaftaranController::class, 'store'])->name('pendaftaran.store');
});

// ─── Absensi Guru (Guru TK + Guru Ngaji) ─────────────────────────────────────
Route::middleware(['auth', 'role:Guru,Guru Ngaji'])->prefix('guru')->name('guru.')->group(function () {
    Route::get('absensi-guru',              [GuruAbsensiGuruController::class, 'index'])->name('absensi-guru.index');
    Route::post('absensi-guru/checkin',     [GuruAbsensiGuruController::class, 'checkIn'])->name('absensi-guru.checkin');
    Route::post('absensi-guru/checkout',    [GuruAbsensiGuruController::class, 'checkOut'])->name('absensi-guru.checkout');
    Route::post('absensi-guru/izin-sakit',  [GuruAbsensiGuruController::class, 'izinSakit'])->name('absensi-guru.izin-sakit');
});

// ─── Guru Area ───────────────────────────────────────────────────────────────
Route::middleware(['auth', 'role:Guru'])->prefix('guru')->name('guru.')->group(function () {

    Route::get('absensi',       [GuruAbsensiController::class, 'index'])->name('absensi.index');
    Route::post('absensi',      [GuruAbsensiController::class, 'store'])->name('absensi.store');

    // Raport siswa (wali kelas)
    Route::get('raport',                                  [GuruRaportController::class, 'index'])->name('raport.index');
    Route::get('raport/create',                           [GuruRaportController::class, 'create'])->name('raport.create');
    Route::post('raport',                                 [GuruRaportController::class, 'store'])->name('raport.store');
    Route::get('raport/{id}/edit',                        [GuruRaportController::class, 'edit'])->name('raport.edit');
    Route::post('raport/{id}/narrative',                  [GuruRaportController::class, 'updateNarrative'])->name('raport.narrative');
    Route::post('raport/{id}/checklist',                  [GuruRaportController::class, 'updateChecklist'])->name('raport.checklist');
    Route::post('raport/{id}/physical',                   [GuruRaportController::class, 'updatePhysical'])->name('raport.physical');
    Route::post('raport/{id}/submit',                     [GuruRaportController::class, 'submit'])->name('raport.submit');
    Route::post('raport/{id}/photo',                      [GuruRaportController::class, 'uploadPhoto'])->name('raport.photo.store');
    Route::delete('raport/photo/{photoId}',               [GuruRaportController::class, 'deletePhoto'])->name('raport.photo.destroy');

    Route::prefix('tabungan')->name('tabungan.')->group(function () {
        Route::get('/',                               [TabunganGuruController::class, 'index'])->name('index');
        Route::get('/passbooks/{passbook}',           [TabunganGuruController::class, 'showPassbook'])->name('passbook.show');
        Route::post('/passbooks/{passbook}/deposit',  [TabunganGuruController::class, 'deposit'])->name('passbook.deposit');
        Route::post('/passbooks/{passbook}/withdraw', [TabunganGuruController::class, 'withdraw'])->name('passbook.withdraw');
        Route::get('/{ledger}',                       [TabunganGuruController::class, 'show'])->name('show');
    });
});

// ─── Admin Area ───────────────────────────────────────────────────────────────
// Outer group: semua role admin dapat mengakses (read-only untuk Kepsek & Kepala Yayasan)
Route::middleware(['auth', 'role:Admin,Kepala Sekolah,Kepala Yayasan'])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {

        // ── Read-only: diakses semua role admin ──────────────────────────────
        Route::resource('siswa',    SiswaController::class)->only(['index', 'show']);
        Route::resource('kelas',    KelasController::class, ['parameters' => ['kelas' => 'kelas']])->only(['index']);
        Route::resource('guru',     GuruController::class)->only(['index']);
        Route::resource('user',     UserController::class)->only(['index']);
        Route::resource('keuangan', KeuanganController::class)->only(['index']);
        Route::resource('tabungan', TabunganAdminController::class)->only(['index', 'show']);
        Route::get('tabungan/passbooks/{passbook}', [TabunganAdminController::class, 'showPassbook'])->name('tabungan.passbook.show');

        Route::get('absensi/recap', [AdminAbsensiController::class,     'recap'])->name('absensi.recap');
        Route::get('absensi',      [AdminAbsensiController::class,    'index'])->name('absensi.index');
        Route::get('absensi-guru/recap', [AdminAbsensiGuruController::class, 'recap'])->name('absensi-guru.recap');
        Route::get('absensi-guru',       [AdminAbsensiGuruController::class, 'index'])->name('absensi-guru.index');

        Route::get('raport',           [AdminRaportController::class, 'index'])->name('raport.index');
        Route::get('raport/{id}/edit', [AdminRaportController::class, 'edit'])->name('raport.edit');

        Route::get('pendaftaran',                  [PendaftaranController::class, 'index'])->name('pendaftaran.index');
        Route::get('pendaftaran/{pendaftaran}',    [PendaftaranController::class, 'show'])->name('pendaftaran.show');
        Route::get('pembayaran-pendaftaran',       [PembayaranPendaftaranController::class, 'index'])->name('pembayaran-pendaftaran.index');

        Route::get('laporan/export/keuangan-pdf',      [LaporanController::class, 'exportKeuanganPdf'])->name('laporan.export.keuangan-pdf');
        Route::get('laporan/export/keuangan-csv',      [LaporanController::class, 'exportKeuanganCsv'])->name('laporan.export.keuangan-csv');
        Route::get('laporan/export/absensi-siswa-pdf',  [LaporanController::class, 'exportAbsensiSiswaPdf'])->name('laporan.export.absensi-siswa-pdf');
        Route::get('laporan/export/absensi-siswa-csv',  [LaporanController::class, 'exportAbsensiSiswaCsv'])->name('laporan.export.absensi-siswa-csv');
        Route::get('laporan/export/absensi-guru-pdf',   [LaporanController::class, 'exportAbsensiGuruPdf'])->name('laporan.export.absensi-guru-pdf');
        Route::get('laporan/export/absensi-guru-csv',   [LaporanController::class, 'exportAbsensiGuruCsv'])->name('laporan.export.absensi-guru-csv');
        Route::get('laporan',    [LaporanController::class,    'index'])->name('laporan.index');
        Route::get('pengaturan', [PengaturanController::class, 'index'])->name('pengaturan.index');

        // ── Write: hanya Admin ───────────────────────────────────────────────
        Route::middleware('role:Admin')->group(function () {

            Route::resource('siswa',    SiswaController::class)->only(['create', 'store', 'edit', 'update', 'destroy']);
            Route::resource('kelas',    KelasController::class, ['parameters' => ['kelas' => 'kelas']])->only(['create', 'store', 'edit', 'update', 'destroy']);
            Route::resource('guru',     GuruController::class)->only(['create', 'store', 'edit', 'update', 'destroy']);
            Route::resource('user',     UserController::class)->only(['create', 'store', 'edit', 'update', 'destroy']);
            Route::post('keuangan/generate', [KeuanganController::class, 'generate'])->name('keuangan.generate');
            Route::resource('keuangan', KeuanganController::class)->only(['create', 'store', 'edit', 'update', 'destroy']);

            Route::get('tabungan/{tabungan}/passbooks/create',    [TabunganAdminController::class, 'openPassbook'])->name('tabungan.passbook.create');
            Route::post('tabungan/{tabungan}/passbooks',          [TabunganAdminController::class, 'storePassbook'])->name('tabungan.passbook.store');
            Route::post('tabungan/passbooks/{passbook}/deposit',  [TabunganAdminController::class, 'deposit'])->name('tabungan.passbook.deposit');
            Route::post('tabungan/passbooks/{passbook}/withdraw', [TabunganAdminController::class, 'withdraw'])->name('tabungan.passbook.withdraw');
            Route::resource('tabungan', TabunganAdminController::class)->only(['create', 'store', 'destroy']);

            Route::post('absensi',     [AdminAbsensiController::class,    'store'])->name('absensi.store');
            Route::post('absensi-guru',[AdminAbsensiGuruController::class, 'store'])->name('absensi-guru.store');

            Route::get('raport/create',          [AdminRaportController::class, 'create'])->name('raport.create');
            Route::post('raport',                [AdminRaportController::class, 'store'])->name('raport.store');
            Route::post('raport/{id}/narrative', [AdminRaportController::class, 'updateNarrative'])->name('raport.narrative');
            Route::post('raport/{id}/checklist', [AdminRaportController::class, 'updateChecklist'])->name('raport.checklist');
            Route::post('raport/{id}/physical',  [AdminRaportController::class, 'updatePhysical'])->name('raport.physical');
            Route::post('raport/{id}/submit',    [AdminRaportController::class, 'submit'])->name('raport.submit');
            Route::post('raport/{id}/approve',   [AdminRaportController::class, 'approve'])->name('raport.approve');
            Route::post('raport/{id}/photo',     [AdminRaportController::class, 'uploadPhoto'])->name('raport.photo.store');
            Route::delete('raport/photo/{photoId}', [AdminRaportController::class, 'deletePhoto'])->name('raport.photo.destroy');
            Route::delete('raport/{id}',         [AdminRaportController::class, 'destroy'])->name('raport.destroy');

            Route::patch('pendaftaran/{pendaftaran}', [PendaftaranController::class, 'update'])->name('pendaftaran.update');

            Route::post('pembayaran-pendaftaran/{transaksi}/approve', [PembayaranPendaftaranController::class, 'approve'])->name('pembayaran-pendaftaran.approve');
            Route::post('pembayaran-pendaftaran/{transaksi}/reject',  [PembayaranPendaftaranController::class, 'reject'])->name('pembayaran-pendaftaran.reject');

            Route::post('pengaturan',                              [PengaturanController::class, 'update'])->name('pengaturan.update');
            Route::post('pengaturan/toggle-pendaftaran',           [PengaturanController::class, 'togglePendaftaran'])->name('pengaturan.toggle-pendaftaran');
            Route::post('pengaturan/semester',                     [PengaturanController::class, 'storeSemester'])->name('pengaturan.semester.store');
            Route::post('pengaturan/semester/{semester}/activate', [PengaturanController::class, 'activateSemester'])->name('pengaturan.semester.activate');
            Route::delete('pengaturan/semester/{semester}',        [PengaturanController::class, 'destroySemester'])->name('pengaturan.semester.destroy');
        });
    });

require __DIR__ . '/auth.php';
