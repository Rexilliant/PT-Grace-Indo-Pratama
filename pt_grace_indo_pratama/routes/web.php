<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('admin.pengadaan-barang');
});

Route::prefix('admin')->group(function () {
    Route::get('/dashboard', function () {
        return view('admin.dashboard');
    });

    Route::get('/pengadaan-barang', function () {
        return view('admin.pengadaan-barang');
    });
});

