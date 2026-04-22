<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\LogErrorController;
use App\Http\Controllers\MediaController;
use App\Http\Controllers\PermissionController;
use App\Http\Controllers\ProcurementController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ProductionController;
use App\Http\Controllers\ProductVariantController;
use App\Http\Controllers\PurchaseReceiptController;
use App\Http\Controllers\RawMaterialController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\SaleController;
use App\Http\Controllers\ShipmentController;
use App\Http\Controllers\ShipmentReceiptController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\WarehouseController;
use Illuminate\Support\Facades\Route;

Route::get('/phpinfo', function () {
    phpinfo();
});

Route::middleware('auth')->prefix('admin')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->middleware(['auth', 'permission:akses dashboard'])->name('admin.dashboard');
    Route::get('/profile', function () {
        return view('admin.profile');
    })->name('admin.profile');

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

    Route::get('/add-gudang-permintaan-pengiriman', function () {
        return view('admin.add-gudang-permintaan-pengiriman');
    })->name('admin.add-gudang-permintaan-pengiriman');
    Route::get('/gudang-permintaan-pengiriman', function () {
        return view('admin.gudang-permintaan-pengiriman');
    })->name('admin.gudang-permintaan-pengiriman');

    Route::get('/add-pemasaran-permintaan-pengiriman', function () {
        return view('admin.add-pemasaran-permintaan-pengiriman');
    })->name('admin.add-pemasaran-permintaan-pengiriman');

    // Route::get('/add-laporan-penjualan', function () {
    //     return view('admin.add-laporan-penjualan');
    // })->name('admin.add-laporan-penjualan');

    // Route::get('/add-produk', function () {
    //     return view('admin.add-produk');
    // })->name('admin.add-produk');

    Route::get('/add-executive-pengadaan-barang', function () {
        return view('admin.add-executive-pengadaan-barang');
    })->name('admin.add-executive-pengadaan-barang');

    Route::get('/edit-barang-masuk', function () {
        return view('admin.edit-barang-masuk');
    })->name('admin.edit-barang-masuk');

    Route::get('/edit-profile', function () {
        return view('admin.edit-profile');
    })->name('admin.edit-profile');

    Route::get(
        '/add-pilih-produk',
        [ProductionController::class, 'pilihProduk']
    )->name('admin.add-pilih-produk');

    // Laporan Penjualan
    Route::prefix('admin')->middleware(['auth'])->group(function () {
        Route::get('/pemasaran/laporan-penjualan', [SaleController::class, 'index'])
            ->name('admin.pemasaran-laporan-penjualan');
        Route::get('/pemasaran/laporan-penjualan/create', [SaleController::class, 'create'])
            ->name('admin.pemasaran-laporan-penjualan.create');
        Route::post('/pemasaran/laporan-penjualan', [SaleController::class, 'store'])
            ->name('admin.pemasaran-laporan-penjualan.store');
        Route::get('/pemasaran/laporan-penjualan/stocks-by-warehouse', [SaleController::class, 'getStocksByWarehouse'])
            ->name('admin.pemasaran-laporan-penjualan.stocks-by-warehouse');
        Route::get('/pemasaran/laporan-penjualan/{id}/edit', [SaleController::class, 'edit'])
            ->name('admin.pemasaran-laporan-penjualan.edit');
        Route::put('/pemasaran/laporan-penjualan/{id}', [SaleController::class, 'update'])
            ->name('admin.pemasaran-laporan-penjualan.update');
        Route::delete('/pemasaran/laporan-penjualan/{id}', [SaleController::class, 'destroy'])
            ->name('admin.pemasaran-laporan-penjualan.destroy');
        Route::get('/pemasaran/laporan-penjualan/{id}/history-pembayaran', [SaleController::class, 'historyPayment'])
            ->name('admin.pemasaran-laporan-penjualan.history-pembayaran');
        Route::get('/pemasaran/laporan-penjualan/{id}/invoice', [SaleController::class, 'invoice'])
            ->name('admin.pemasaran-laporan-penjualan.invoice');
    });

    // This Point
    Route::name('admin.')
        ->group(function () {

            Route::get('/gudang/laporan-produksi', [ProductionController::class, 'index'])->middleware(['auth', 'permission:baca produksi'])
                ->name('gudang-laporan-produksi');
            Route::get('/gudang/laporan-produksi/tambah/{productVariant}', [ProductionController::class, 'create'])->middleware(['auth', 'permission:tambah produksi'])->name('add-produk');
            Route::get('/gudang/laporan-produksi/pilih-produk', [ProductionController::class, 'pilihProduk'])
                ->name('add-pilih-produk');

            Route::get('/add-produk/{productVariant}', [ProductionController::class, 'create'])
                ->name('add-produk');

            Route::get('/production/materials', [ProductionController::class, 'getMaterialsByWarehouse'])
                ->name('production.materials');

            Route::post('/production/store', [ProductionController::class, 'store'])
                ->name('production.store');

            Route::get('/edit-produk/{productionBatch}', [ProductionController::class, 'edit'])
                ->name('edit-produk');

            Route::put('/edit-produk/{productionBatch}', [ProductionController::class, 'update'])
                ->name('production.update');

            Route::delete('/production/{productionBatch}', [ProductionController::class, 'destroy'])
                ->name('production.delete');
        });

    // Product Variant
    Route::controller(ProductVariantController::class)->group(function () {
        Route::get('/executive-produk-variant', 'index')->middleware(['auth', 'permission:baca produk'])->name('admin.executive-produk-variant');
        Route::get('/add-executive-produk-variant', 'create')->middleware(['auth', 'permission:tambah produk'])
            ->name('admin.add-executive-produk-variant');
        Route::post('/add-executive-produk-variant/store', 'store')->middleware(['auth', 'permission:tambah produk'])
            ->name('admin.add-executive-produk-variant.store');
        Route::get('/executive-produk-variant/{id}/edit', 'edit')->middleware(['auth', 'permission:edit produk|baca produk'])
            ->name('admin.executive-produk-variant.edit');
        Route::put('/executive-produk-variant/{id}', 'update')->middleware(['auth', 'permission:edit produk'])
            ->name('admin.executive-produk-variant.update');
    });

    // Bahan Baku
    Route::controller(RawMaterialController::class)->prefix('raw-materials')->group(function () {
        Route::get('/', 'index')->name('admin.gudang-bahan-baku');
        Route::get('/create', 'create')->name('admin.add-bahan-baku');
        Route::get('/stock', 'stockIndex')->name('admin.gudang-stok-bahan-baku');
        Route::post('/store', 'store')->name('admin.add-bahan-baku.store');
        Route::get('/{id}/edit', 'edit')->name('admin.gudang-bahan-baku.edit');
        Route::put('/{id}', 'update')->name('admin.gudang-bahan-baku.update');
    });

    // Executive Produk
    Route::controller(ProductController::class)->prefix('executive-produk')->group(function () {
        Route::get('/', 'indexExecutive')->middleware(['auth', 'permission:baca produk'])->name('admin.executive-produk');
        Route::get('/create', 'createExecutive')->middleware(['auth', 'permission:tambah produk'])->name('admin.add-executive-produk-baru');
        Route::post('/store', 'storeExecutive')->middleware(['auth', 'permission:tambah produk'])->name('admin.add-executive-produk-baru.store');
        Route::get('/edit/{id}', 'editExecutive')->middleware(['auth', 'permission:edit produk|baca produk'])->name('admin.edit-executive-produk');
        Route::put('/edit/{id}', 'updateExecutive')->middleware(['auth', 'permission:edit produk'])->name('admin.edit-executive-produk.update');
        Route::delete('/delete/{id}', 'destroyExecutive')->middleware(['auth', 'permission:hapus produk'])->name('admin.executive-produk.destroy');
        Route::get('/stock', 'productStock')->middleware(['auth', 'permission:baca produk stok'])->name('product-stocks');
    });

    // Employee
    Route::controller(EmployeeController::class)->prefix('employees')->group(function () {
        Route::get('/', 'index')->middleware(['auth', 'permission:baca karyawan'])->name('employees');
        Route::get('/create', 'create')->middleware(['auth', 'permission:tambah karyawan'])->name('admin.create-employee');
        Route::post('/create', 'store')->middleware(['auth', 'permission:tambah karyawan'])->name('admin.store-employee');
        Route::get('/get-provinces/{countryCode}', 'getProvinces');
        Route::get('/get-cities/{countryCode}', 'getCities');
        Route::get('/edit/{id}', 'edit')->middleware(['auth', 'permission:edit karyawan|baca karyawan'])->name('edit.employee');
        Route::put('/edit/{id}', 'update')->middleware(['auth', 'permission:edit karyawan'])->name('update.employee');
        Route::delete('/delete/{id}', 'destroy')->middleware(['auth', 'permission:hapus karyawan'])->name('delete.employee');
        Route::put('/restore/{id}', 'restore')->name('restore.employee');
    });
    Route::controller(RoleController::class)->prefix('roles')->group(function () {
        Route::get('/', 'index')->middleware(['auth', 'permission:baca role'])->name('roles');
        Route::get('/create', 'create')->middleware(['auth', 'permission:tambah role'])->name('admin.create-role');
        Route::post('/create', 'store')->middleware(['auth', 'permission:tambah role'])->name('admin.store-role');
        Route::get('/edit/{id}', 'edit')->middleware(['auth', 'permission:edit role'])->name('edit.role');
        Route::put('/edit/{id}', 'update')->middleware(['auth', 'permission:edit role'])->name('update.role');
    });
    Route::controller(PermissionController::class)->prefix('permissions')->group(function () {
        Route::get('/', 'index')->middleware(['auth', 'permission:baca izin'])->name('permissions');
        Route::get('/create', 'create')->middleware(['auth', 'permission:tambah izin'])->name('create-permission');
        Route::post('/store', 'store')->name('store-permission');
        Route::get('/edit/{id}', 'edit')->middleware(['auth', 'permission:edit izin'])->name('edit-permission');
        Route::put('/edit/{id}', 'update')->middleware(['auth', 'permission:edit izin'])->name('update-permission');
        Route::delete('/delete/{id}', 'destroy')->middleware(['auth', 'permission:hapus izin'])->name('delete-permission');
    });
    Route::controller(UserController::class)->prefix('users')->group(function () {
        Route::get('/', 'index')->name('users');
        Route::get('/create', 'create')->name('create-user');
        Route::post('/create', 'store')->name('store-user');
        Route::get('/edit/{id}', 'edit')->name('edit-user');
        Route::put('/edit/{id}', 'update')->name('update-user');
        Route::delete('/delete/{id}', 'destroy')->name('delete-user');
        Route::put('/restore/{id}', 'restore')->name('restore-user');
    });

    Route::controller(ProcurementController::class)->prefix('procurements')->group(function () {
        Route::get('/create', 'create')->middleware(['auth', 'permission:tambah pengadaan bahan baku'])->name('create-procurement');
        Route::post('/store', 'store')->middleware(['auth', 'permission:tambah pengadaan bahan baku'])->name('store-procurement');
        Route::get('/', 'index')->middleware(['auth', 'permission:baca pengadaan bahan baku'])->name('procurements');
        Route::get('/edit/{id}', 'edit')->middleware(['auth', 'permission:edit pengadaan bahan baku|baca pengadaan bahan baku'])->name('edit-procurement');
        Route::delete('/delete/{id}', 'destroy')->middleware(['auth', 'permission:hapus pengadaan bahan baku'])->name('delete-procurement');

        Route::put('/edit/{id}', 'update')->name('update-procurement');
    });
    Route::controller(PurchaseReceiptController::class)->prefix('purchase-receipts')->group(function () {
        Route::get('/', 'index')->middleware(['auth', 'permission:baca bahan baku masuk'])->name('purchase-receipts');
        Route::get('/create', 'create')->middleware(['auth', 'permission:tambah bahan baku masuk'])->name('create-purchase-receipt');
        Route::post('/store', 'store')->middleware(['auth', 'permission:tambah bahan baku masuk'])->name('store-purchase-receipt');
        Route::get('/edit/{id}', 'edit')->middleware(['auth', 'permission:edit bahan baku masuk|baca bahan baku masuk'])->name('edit-purchase-receipt');
        Route::put('/edit/{id}', 'update')->middleware(['auth', 'permission:edit bahan baku masuk'])->name('update-purchase-receipt');
        Route::post('/add-media/{id}', 'addMedia')->name('purchase-receipts.add-media');
        Route::delete('/delete/{id}', 'destroy')->middleware(['auth', 'permission:hapus barang masuk'])->name('purchase-receipts.destroy');
    });

    Route::controller(MediaController::class)->prefix('media')->group(function () {
        Route::delete('/delete/{mediaId}', 'delete')->name('media.delete');
    });
    Route::controller(LogErrorController::class)->prefix('log-errors')->group(function () {
        Route::get('/', 'index')->middleware(['auth', 'permission:baca log error'])->name('log-errors');
    });

    Route::get('/procurements/export', [ProcurementController::class, 'export'])
        ->name('procurements.export');
    Route::controller(ShipmentController::class)->prefix('shipments')->group(function () {
        Route::get('/', 'index')->middleware(['auth', 'permission:baca pengiriman produk'])->name('shipments');
        Route::get('/create', 'create')->middleware(['auth', 'permission:tambah pengiriman produk'])->name('create-shipment');
        Route::post('/store', 'store')->middleware(['auth', 'permission:tambah pengiriman produk'])->name('store-shipment');
        Route::get('/edit/{id}', 'edit')->middleware(['auth', 'permission:edit pengiriman produk|baca pengiriman produk'])->name('edit-shipment');
        Route::put('/edit/{id}', 'update')->middleware(['auth', 'permission:edit pengiriman produk'])->name('update-shipment');
        Route::delete('/delete/{id}', 'destroy')->middleware(['auth', 'permission:hapus pengiriman produk'])->name('delete-shipment');
        Route::get('/{id}/items', 'getShipmentItems')->name('shipments.items');
    });

    Route::controller(WarehouseController::class)->prefix('warehouses')->group(function () {
        Route::get('/', 'index')->middleware(['auth', 'permission:baca gudang'])->name('warehouses');
        Route::get('/create', 'create')->middleware(['auth', 'permission:tambah gudang'])->name('create-warehouse');
        Route::post('/create', 'store')->middleware(['auth', 'permission:tambah gudang'])->name('store-warehouse');
        Route::get('/edit/{id}', 'edit')->middleware(['auth', 'permission:edit gudang|baca gudang'])->name('edit-warehouse');
        Route::put('/update/{id}', 'update')->middleware(['auth', 'permission:edit gudang'])->name('update-warehouse');
        Route::get('/cities/{adminCode1}', 'getCities')->name('warehouse-cities');
        Route::delete('/delete/{id}', 'destroy')->middleware(['auth', 'permission:hapus gudang'])->name('delete-warehouse');
    });

    Route::controller(ShipmentReceiptController::class)->prefix('shipment-receipts')->group(function () {
        Route::get('/', 'index')->middleware(['auth', 'permission:baca penerimaan pengiriman produk'])->name('shipment-receipts');
        Route::get('/create', 'create')->middleware(['auth', 'permission:tambah penerimaan pengiriman produk'])->name('create-shipment-receipt');
        Route::post('/store', 'store')->middleware(['auth', 'permission:tambah penerimaan pengiriman produk'])->name('store-shipment-receipt');
        Route::get('/edit/{id}', 'edit')->middleware(['auth', 'permission:edit penerimaan pengiriman produk|baca penerimaan pengiriman produk|edit status penerimaan pengiriman produk'])->name('edit-shipment-receipt');
        Route::put('/edit/{id}', 'update')->middleware(['auth', 'permission:edit penerimaan pengiriman produk'])->name('update-shipment-receipt');
        Route::delete('/delete/{id}', 'destroy')->middleware(['auth', 'permission:hapus penerimaan pengiriman produk'])->name('delete-shipment-receipt');
    });
});
require __DIR__.'/auth.php';
