<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\BarangController;
use App\Http\Controllers\BarangKeluarController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\HistoriController;
use App\Http\Controllers\KonfigurasiController;
use App\Http\Controllers\LaporanController;
use Illuminate\Support\Facades\Route;

Route::middleware('guest')->group(function () {
    Route::get('login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('login', [AuthController::class, 'login'])->name('login.proses');
});

Route::post('logout', [AuthController::class, 'logout'])->name('logout')->middleware('auth');

Route::middleware('auth')->group(function () {
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

    // Barang keluar (user penjual & admin)
    Route::get('barang-keluar', [BarangKeluarController::class, 'index'])->name('keluar.index');
    Route::post('barang-keluar', [BarangKeluarController::class, 'store'])->name('keluar.store');
    Route::post('barang-keluar/{barangKeluar}/kembali', [BarangKeluarController::class, 'kembali'])->name('keluar.kembali');
    Route::post('barang-keluar/{barangKeluar}/jual', [BarangKeluarController::class, 'jual'])->name('keluar.jual');

    // Khusus admin
    Route::middleware('admin')->group(function () {
        Route::resource('barang', BarangController::class)->except(['show']);
        Route::get('barang/kode', [BarangController::class, 'kodeOtomatis'])->name('barang.kode');
        Route::get('barang/{barang}', [BarangController::class, 'show'])->name('barang.show');

        Route::get('konfigurasi', [KonfigurasiController::class, 'index'])->name('konfigurasi.index');
        Route::post('konfigurasi', [KonfigurasiController::class, 'store'])->name('konfigurasi.store');
        Route::put('konfigurasi/{konfigurasi}', [KonfigurasiController::class, 'update'])->name('konfigurasi.update');
        Route::delete('konfigurasi/{konfigurasi}', [KonfigurasiController::class, 'destroy'])->name('konfigurasi.destroy');

        Route::get('histori', [HistoriController::class, 'index'])->name('histori.index');

        Route::name('laporan.')->prefix('laporan')->group(function () {
            Route::get('gudang', [LaporanController::class, 'gudang'])->name('gudang');
            Route::get('baru', [LaporanController::class, 'baru'])->name('baru');
            Route::get('lama', [LaporanController::class, 'lama'])->name('lama');
            Route::get('terjual', [LaporanController::class, 'terjual'])->name('terjual');
        });
    });
});
