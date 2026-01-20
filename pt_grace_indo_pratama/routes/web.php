<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('admin.pengadaan-barang');
});

Route::prefix('admin')->group(function () {

    Route::get('/dashboard', function () {
        return view('admin.dashboard');
    })->name('admin.dashboard');

    Route::get('/gudang-pengadaan-barang', function () {
        return view('admin.gudang-pengadaan-barang');
    })->name('admin.gudang-pengadaan-barang');

    Route::get('/gudang-barang-masuk', function () {
        return view('admin.gudang-barang-masuk');
    })->name('admin.gudang-barang-masuk');

    Route::get('/gudang-laporan-produksi', function () {
        return view('admin.gudang-laporan-produksi');
    })->name('admin.gudang-laporan-produksi');

    Route::get('/gudang-permintaan-pengiriman', function () {
        return view('admin.gudang-permintaan-pengiriman');
    })->name('admin.gudang-permintaan-pengiriman');

    Route::get('/gudang-bahan-baku', function () {
        return view('admin.gudang-bahan-baku');
    })->name('admin.gudang-bahan-baku');

    Route::get('/pemasaran-permintaan-pengiriman', function () {
        return view('admin.pemasaran-permintaan-pengiriman');
    })->name('admin.pemasaran-permintaan-pengiriman');

    Route::get('/pemasaran-laporan-penjualan', function () {
        return view('admin.pemasaran-laporan-penjualan');
    })->name('admin.pemasaran-laporan-penjualan');
});


