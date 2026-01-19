<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('admin.pengadaan-barang');
});

Route::prefix('admin')->group(function () {

    Route::get('/dashboard', function () {
        return view('admin.dashboard');
    })->name('admin.dashboard');

    Route::get('/pengadaan-barang', function () {
        return view('admin.pengadaan-barang');
    })->name('admin.pengadaan-barang');

    Route::get('/barang-masuk', function () {
        return view('admin.barang-masuk');
    })->name('admin.barang-masuk');

    Route::get('/laporan-produksi', function () {
        return view('admin.laporan-produksi');
    })->name('admin.laporan-produksi');

    Route::get('/permintaan-pengiriman-gudang', function () {
        return view('admin.permintaan-pengiriman-gudang');
    })->name('admin.permintaan-pengiriman-gudang');

    Route::get('/bahan-baku', function () {
        return view('admin.bahan-baku');
    })->name('admin.bahan-baku');

    Route::get('/permintaan-pengiriman-pemasaran', function () {
        return view('admin.permintaan-pengiriman-pemasaran');
    })->name('admin.permintaan-pengiriman-pemasaran');

    Route::get('/laporan-penjualan-pemasaran', function () {
        return view('admin.laporan-penjualan-pemasaran');
    })->name('admin.laporan-penjualan-pemasaran');
});


