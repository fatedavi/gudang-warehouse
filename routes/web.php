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

    // Data Barang (index & show terbuka untuk semua; kelola khusus admin)
    Route::get('barang', [BarangController::class, 'index'])->name('barang.index');
    Route::get('barang/create', [BarangController::class, 'create'])->name('barang.create')->middleware('admin');
    Route::get('barang/kode', [BarangController::class, 'kodeOtomatis'])->name('barang.kode')->middleware('admin');
    Route::post('barang', [BarangController::class, 'store'])->name('barang.store')->middleware('admin');
    Route::get('barang/{barang}', [BarangController::class, 'show'])->name('barang.show');
    Route::get('barang/{barang}/edit', [BarangController::class, 'edit'])->name('barang.edit')->middleware('admin');
    Route::put('barang/{barang}', [BarangController::class, 'update'])->name('barang.update')->middleware('admin');
    Route::delete('barang/{barang}', [BarangController::class, 'destroy'])->name('barang.destroy')->middleware('admin');

    // Khusus admin
    Route::middleware('admin')->group(function () {

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
            Route::get('gudang/pdf', [LaporanController::class, 'pdfGudang'])->name('gudang.pdf');
            Route::get('baru/pdf', [LaporanController::class, 'pdfBaru'])->name('baru.pdf');
            Route::get('lama/pdf', [LaporanController::class, 'pdfLama'])->name('lama.pdf');
            Route::get('terjual/pdf', [LaporanController::class, 'pdfTerjual'])->name('terjual.pdf');
        });
    });
});
