<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\PembayaranController;
use App\Http\Controllers\WargaController;
use App\Http\Controllers\PengeluaranController;
use App\Http\Controllers\SettingController;

// ─── Public Routes (tanpa login) ───────────────────────────────────────────
Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

// Pembayaran: halaman bayar iuran (bisa diakses warga tanpa login)
Route::get('/pembayaran/{pembayaran}/bayar', [PembayaranController::class, 'bayar'])->name('pembayaran.bayar');
Route::get('/pembayaran/bayar-multi', [PembayaranController::class, 'bayarMulti'])->name('pembayaran.bayarMulti');

// Pembayaran publik (warga mandiri)
Route::post('/pembayaran/mandiri', [PembayaranController::class, 'publicStore'])->name('pembayaran.publicStore');
Route::get('/pembayaran/status/{warga}/{year}', [PembayaranController::class, 'getStatus'])->name('pembayaran.status');

// Doitpay QRIS Routes (publik)
Route::post('/doitpay/callback', [\App\Http\Controllers\DoitpayController::class, 'callback'])->name('doitpay.callback');
Route::get('/doitpay/callback', function () {
    return response()->json(['status' => 'ok', 'message' => 'Doitpay callback endpoint is active'], 200);
});
Route::post('/doitpay/generate-qris', [\App\Http\Controllers\DoitpayController::class, 'generateQris'])->name('doitpay.generate');
Route::post('/doitpay/check-status', [\App\Http\Controllers\DoitpayController::class, 'checkStatus'])->name('doitpay.checkStatus');

// ─── Auth Routes ────────────────────────────────────────────────────────────
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.post');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// ─── Admin Routes (harus login sebagai admin) ───────────────────────────────
Route::middleware(['auth', 'admin'])->group(function () {

    // Warga
    Route::get('/warga', [WargaController::class, 'index'])->name('warga.index');
    Route::post('/warga', [WargaController::class, 'store'])->name('warga.store');
    Route::get('/warga/{warga}', [WargaController::class, 'show'])->name('warga.show');
    Route::put('/warga/{warga}', [WargaController::class, 'update'])->name('warga.update');
    Route::delete('/warga/{warga}', [WargaController::class, 'destroy'])->name('warga.destroy');

    // Pembayaran (kelola)
    Route::get('/pembayaran', [PembayaranController::class, 'index'])->name('pembayaran.index');
    Route::post('/pembayaran', [PembayaranController::class, 'store'])->name('pembayaran.store');
    Route::put('/pembayaran/{pembayaran}', [PembayaranController::class, 'update'])->name('pembayaran.update');
    Route::delete('/pembayaran/{pembayaran}', [PembayaranController::class, 'destroy'])->name('pembayaran.destroy');
    Route::post('/pembayaran/{pembayaran}/manual', [PembayaranController::class, 'bayarManual'])->name('pembayaran.manual');
    Route::post('/pembayaran/bulk-delete', [PembayaranController::class, 'bulkDelete'])->name('pembayaran.bulkDelete');
    Route::post('/pembayaran/bulk-update-status', [PembayaranController::class, 'bulkUpdateStatus'])->name('pembayaran.bulkUpdateStatus');
    Route::post('/pembayaran/bulk-update', [PembayaranController::class, 'bulkUpdate'])->name('pembayaran.bulkUpdate');
    Route::post('/pembayaran/bulk-bayar-qris', [PembayaranController::class, 'bulkBayarQris'])->name('pembayaran.bulkBayarQris');
    Route::get('/pembayaran/laporan', [PembayaranController::class, 'exportLaporan'])->name('pembayaran.laporan');

    // Pengeluaran & Kategori
    Route::get('/pengeluaran', [PengeluaranController::class, 'index'])->name('pengeluaran.index');
    Route::post('/pengeluaran', [PengeluaranController::class, 'store'])->name('pengeluaran.store');
    Route::delete('/pengeluaran/{pengeluaran}', [PengeluaranController::class, 'destroy'])->name('pengeluaran.destroy');
    Route::post('/kategori-pengeluaran', [\App\Http\Controllers\KategoriPengeluaranController::class, 'store'])->name('kategori.store');

    // Pemasukan Lain
    Route::get('/pemasukan-lain', [\App\Http\Controllers\PemasukanLainController::class, 'index'])->name('pemasukan_lain.index');
    Route::post('/pemasukan-lain', [\App\Http\Controllers\PemasukanLainController::class, 'store'])->name('pemasukan_lain.store');
    Route::put('/pemasukan-lain/{pemasukanLain}', [\App\Http\Controllers\PemasukanLainController::class, 'update'])->name('pemasukan_lain.update');
    Route::delete('/pemasukan-lain/{pemasukanLain}', [\App\Http\Controllers\PemasukanLainController::class, 'destroy'])->name('pemasukan_lain.destroy');

    // Pengaturan
    Route::get('/settings', [SettingController::class, 'index'])->name('settings.index');
    Route::post('/settings', [SettingController::class, 'update'])->name('settings.update');

    // Manajemen Admin / User
    Route::get('/users', [\App\Http\Controllers\UserController::class, 'index'])->name('users.index');
    Route::post('/users', [\App\Http\Controllers\UserController::class, 'store'])->name('users.store');
    Route::delete('/users/{user}', [\App\Http\Controllers\UserController::class, 'destroy'])->name('users.destroy');
});
