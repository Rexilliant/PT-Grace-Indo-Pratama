<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\RegionController;

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

    Route::get('/profile', function () {
        return view('admin.profile');
    })->name('admin.profile');

    // Add
    Route::get('/add-gudang-pengadaan-barang', function () {
        return view('admin.add-gudang-pengadaan-barang');
    })->name('admin.add-gudang-pengadaan-barang');

    Route::get('/add-barang-masuk', function () {
        return view('admin.add-barang-masuk');
    })->name('admin.add-barang-masuk');

    Route::get('/add-bahan-baku', function () {
        return view('admin.add-bahan-baku');
    })->name('admin.add-bahan-baku');

    Route::get('/add-gudang-permintaan-pengiriman', function () {
        return view('admin.add-gudang-permintaan-pengiriman');
    })->name('admin.add-gudang-permintaan-pengiriman');

    Route::get('/add-pemasaran-permintaan-pengiriman', function () {
        return view('admin.add-pemasaran-permintaan-pengiriman');
    })->name('admin.add-pemasaran-permintaan-pengiriman');

    Route::get('/add-pilih-produk', function () {
        return view('admin.add-pilih-produk');
    })->name('admin.add-pilih-produk');

    Route::get('/add-produk', function () {
        return view('admin.add-produk');
    })->name('admin.add-produk');

    // Edit
    Route::get('/edit-bahan-baku', function () {
        return view('admin.edit-bahan-baku');
    })->name('admin.edit-bahan-baku');

    Route::get('/edit-barang-masuk', function () {
        return view('admin.edit-barang-masuk');
    })->name('admin.edit-barang-masuk');

    Route::get('/edit-profile', function () {
        return view('admin.edit-profile');
    })->name('admin.edit-profile');

    Route::get('/edit-produk', function () {
        return view('admin.edit-produk');
    })->name('admin.edit-produk');
});
