<?php

use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\PermissionController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\RoleController;
use Illuminate\Support\Facades\Route;

// Route::get('/', function () {
//     return view('welcome');
// });

// Route::get('/dashboard', function () {
//     return view('dashboard');
// })->middleware(['auth', 'verified'])->name('dashboard');

// Route::middleware('auth')->group(function () {
//     Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
//     Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
//     Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
// });

Route::middleware('auth')->prefix('admin')->group(function () {
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

    Route::get('/executive-produk', function () {
        return view('admin.executive-produk');
    })->name('admin.executive-produk');

    Route::get('/executive-pengadaan-barang', function () {
        return view('admin.executive-pengadaan-barang');
    })->name('admin.executive-pengadaan-barang');

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

    Route::get('/add-laporan-penjualan', function () {
        return view('admin.add-laporan-penjualan');
    })->name('admin.add-laporan-penjualan');

    Route::get('/add-pilih-produk', function () {
        return view('admin.add-pilih-produk');
    })->name('admin.add-pilih-produk');

    Route::get('/add-produk', function () {
        return view('admin.add-produk');
    })->name('admin.add-produk');

    Route::get('/add-executive-produk-baru', function () {
        return view('admin.add-executive-produk-baru');
    })->name('admin.add-executive-produk-baru');

    Route::get('/add-executive-pengadaan-barang', function () {
        return view('admin.add-executive-pengadaan-barang');
    })->name('admin.add-executive-pengadaan-barang');

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

    Route::get('/edit-executive-produk', function () {
        return view('admin.edit-executive-produk');
    })->name('admin.edit-executive-produk');

    // Employee
    Route::controller(EmployeeController::class)->prefix('employees')->group(function () {
        Route::get('/create', 'create')->name('admin.create-emplyee');
    });
    Route::controller(RoleController::class)->prefix('roles')->group(function () {
        Route::get('/create', 'create')->name('admin.create-role');
        Route::post('/create', 'store')->name('admin.store-role');
        Route::get('/edit/{id}', 'edit')->name('edit.role');
        Route::put('/edit/{id}', 'update')->name('update.role');
    });
    Route::controller(PermissionController::class)->prefix('permissions')->group(function () {
        Route::get('/create', 'create')->name('create-permission');
        Route::post('/store', 'store')->name('store-permission');
    });
});
require __DIR__.'/auth.php';
