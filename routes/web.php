<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Admin\UserController as AdminUserController;
use App\Http\Controllers\Admin\KriteriaController as AdminKriteriaController;
use App\Http\Controllers\Admin\SiswaController as AdminSiswaController;
use App\Http\Controllers\Admin\BwmController as AdminBwmController;
use App\Http\Controllers\Admin\WaspasController as AdminWaspasController;
use App\Http\Controllers\Admin\LaporanController as AdminLaporanController;
use App\Http\Controllers\WaliKelas\DashboardController as WaliDashboardController;
use App\Http\Controllers\WaliKelas\PenilaianController as WaliPenilaianController;
use App\Http\Controllers\Kepsek\DashboardController as KepsekDashboardController;
use App\Http\Controllers\Kepsek\VerifikasiController as KepsekVerifikasiController;

// Root redirect
Route::get('/', function () {
    if (Auth::check()) {
        return match (Auth::user()->role) {
            'admin' => redirect()->route('admin.dashboard'),
            'walikelas' => redirect()->route('walikelas.dashboard'),
            'kepsek' => redirect()->route('kepsek.dashboard'),
            default => redirect()->route('login'),
        };
    }
    return redirect()->route('login');
});

// Authentication routes
Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// 1. Modul Administrator
Route::middleware(['auth', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');

    // Manajemen Pengguna
    Route::resource('users', AdminUserController::class);

    // Manajemen Kriteria
    Route::resource('kriteria', AdminKriteriaController::class);

    // Manajemen Siswa
    Route::resource('siswa', AdminSiswaController::class);

    // Kalkulator BWM
    Route::get('/bwm', [AdminBwmController::class, 'index'])->name('bwm.index');
    Route::post('/bwm/calculate', [AdminBwmController::class, 'calculate'])->name('bwm.calculate');
    Route::post('/bwm/apply', [AdminBwmController::class, 'apply'])->name('bwm.apply');

    // Kalkulator WASPAS
    Route::get('/waspas', [AdminWaspasController::class, 'index'])->name('waspas.index');
    Route::post('/waspas/save', [AdminWaspasController::class, 'save'])->name('waspas.save');

    // Cetak Laporan & Ekspor
    Route::get('/laporan', [AdminLaporanController::class, 'index'])->name('laporan.index');
    Route::get('/laporan/per-kelas', [AdminLaporanController::class, 'perKelas'])->name('laporan.per_kelas');
    Route::get('/laporan/per-kelas/cetak', [AdminLaporanController::class, 'cetakPerKelas'])->name('laporan.cetak_per_kelas');
    Route::get('/laporan/cetak', [AdminLaporanController::class, 'cetak'])->name('laporan.cetak');
    Route::get('/laporan/excel', [AdminLaporanController::class, 'exportExcel'])->name('laporan.excel');
});

// 2. Modul Wali Kelas
Route::middleware(['auth', 'role:walikelas'])->prefix('walikelas')->name('walikelas.')->group(function () {
    Route::get('/dashboard', [WaliDashboardController::class, 'index'])->name('dashboard');

    // Input Penilaian & Rekap
    Route::get('/penilaian', [WaliPenilaianController::class, 'index'])->name('penilaian.index');
    Route::get('/penilaian/matriks', [WaliPenilaianController::class, 'matriks'])->name('penilaian.matriks');
    Route::post('/penilaian/matriks', [WaliPenilaianController::class, 'storeMatriks'])->name('penilaian.storeMatriks');
    Route::get('/penilaian/{siswa}/edit', [WaliPenilaianController::class, 'edit'])->name('penilaian.edit');
    Route::post('/penilaian/{siswa}', [WaliPenilaianController::class, 'update'])->name('penilaian.update');
    Route::get('/penilaian/rekap', [WaliPenilaianController::class, 'rekap'])->name('penilaian.rekap');
});

// 3. Modul Kepala Sekolah
Route::middleware(['auth', 'role:kepsek'])->prefix('kepsek')->name('kepsek.')->group(function () {
    Route::get('/dashboard', [KepsekDashboardController::class, 'index'])->name('dashboard');

    // Verifikasi & Persetujuan
    Route::get('/verifikasi', [KepsekVerifikasiController::class, 'index'])->name('verifikasi.index');
    Route::post('/verifikasi/approve', [KepsekVerifikasiController::class, 'approve'])->name('verifikasi.approve');
    Route::get('/verifikasi/sk', [KepsekVerifikasiController::class, 'skResmi'])->name('verifikasi.sk');
});
