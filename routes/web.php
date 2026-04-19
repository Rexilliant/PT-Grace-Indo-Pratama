<?php

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
    Route::get('/dashboard', function () {
        return view('admin.dashboard');
    })->name('admin.dashboard');

    Route::get('/gudang-pengadaan-barang', function () {
        return view('admin.gudang-pengadaan-barang');
    })->name('admin.gudang-pengadaan-barang');

    Route::get('/gudang-barang-masuk', function () {
        return view('admin.gudang-barang-masuk');
    })->name('admin.gudang-barang-masuk');

    // Route::get('/gudang-laporan-produksi', function () {
    //     return view('admin.gudang-laporan-produksi');
    // })->name('admin.gudang-laporan-produksi');

    Route::get('/gudang-permintaan-pengiriman', function () {
        return view('admin.gudang-permintaan-pengiriman');
    })->name('admin.gudang-permintaan-pengiriman');
    Route::get('/pemasaran-permintaan-pengiriman', function () {
        return view('admin.pemasaran-permintaan-pengiriman');
    })->name('admin.pemasaran-permintaan-pengiriman');

    // Route::get('/pemasaran-laporan-penjualan', function () {
    //     return view('admin.pemasaran-laporan-penjualan');
    // })->name('admin.pemasaran-laporan-penjualan');

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

    Route::get('/add-pemasaran-permintaan-pengiriman', function () {
        return view('admin.add-pemasaran-permintaan-pengiriman');
    })->name('admin.add-pemasaran-permintaan-pengiriman');

    // Route::get('/add-laporan-penjualan', function () {
    //     return view('admin.add-laporan-penjualan');
    // })->name('admin.add-laporan-penjualan');

    // Route::get('/add-produk', function () {
    //     return view('admin.add-produk');
    // })->name('admin.add-produk');

    Route::get('/admin/gudang/laporan-produksi/tambah/{productVariant}', [ProductionController::class, 'create'])
        ->name('admin.add-produk');

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
    Route::prefix('admin')->name('admin.')->group(function () {
        Route::get('/gudang/laporan-produksi', [ProductionController::class, 'index'])
            ->name('gudang-laporan-produksi');
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
        Route::get('/executive-produk-variant', 'index')
            ->name('admin.executive-produk-variant');
        Route::get('/add-executive-produk-variant', 'create')
            ->name('admin.add-executive-produk-variant');
        Route::post('/add-executive-produk-variant/store', 'store')
            ->name('admin.add-executive-produk-variant.store');
        Route::get('/executive-produk-variant/{id}/edit', 'edit')
            ->name('admin.executive-produk-variant.edit');
        Route::put('/executive-produk-variant/{id}', 'update')
            ->name('admin.executive-produk-variant.update');
    });

    // Bahan Baku
    Route::controller(RawMaterialController::class)->prefix('raw-materials')->middleware(['auth', 'role:admin|admin_gudang'])->group(function () {
        Route::get('/', 'index')->name('admin.gudang-bahan-baku');
        Route::get('/create', 'create')->name('admin.add-bahan-baku');
        Route::get('/stock', 'stockIndex')->name('admin.gudang-stok-bahan-baku');
        Route::post('/store', 'store')->name('admin.add-bahan-baku.store');
        Route::get('/{id}/edit', 'edit')->name('admin.gudang-bahan-baku.edit');
        Route::put('/{id}', 'update')->name('admin.gudang-bahan-baku.update');
    });

    // Executive Produk
    Route::controller(ProductController::class)->prefix('executive-produk')->group(function () {
        Route::get('/', 'indexExecutive')->name('admin.executive-produk');
        Route::get('/create', 'createExecutive')->name('admin.add-executive-produk-baru');
        Route::post('/store', 'storeExecutive')->name('admin.add-executive-produk-baru.store');
        Route::get('/edit/{id}', 'editExecutive')->name('admin.edit-executive-produk');
        Route::put('/edit/{id}', 'updateExecutive')->name('admin.edit-executive-produk.update');
        Route::delete('/delete/{id}', 'destroyExecutive')->name('admin.executive-produk.destroy');
        Route::get('/stock', 'productStock')->name('product-stocks');
    });

    // Employee
    Route::controller(EmployeeController::class)->middleware(['auth', 'role:admin|executive'])->prefix('employees')->group(function () {
        Route::get('/', 'index')->name('employees');
        Route::get('/create', 'create')->name('admin.create-employee');
        Route::post('/create', 'store')->name('admin.store-employee');
        Route::get('/get-provinces/{countryCode}', 'getProvinces');
        Route::get('/get-cities/{countryCode}', 'getCities');
        Route::get('/edit/{id}', 'edit')->name('edit.employee');
        Route::put('/edit/{id}', 'update')->name('update.employee');
        Route::delete('/delete/{id}', 'destroy')->name('delete.employee');
        Route::put('/restore/{id}', 'restore')->name('restore.employee');
    });

    Route::controller(RoleController::class)->prefix('roles')->middleware(['auth', 'role:admin'])->group(function () {
        Route::get('/', 'index')->name('roles');
        Route::get('/create', 'create')->name('admin.create-role');
        Route::post('/create', 'store')->name('admin.store-role');
        Route::get('/edit/{id}', 'edit')->name('edit.role');
        Route::put('/edit/{id}', 'update')->name('update.role');
    });

    Route::controller(PermissionController::class)->prefix('permissions')->middleware(['auth', 'role:admin'])->group(function () {
        Route::get('/', 'index')->name('permissions');
        Route::get('/create', 'create')->name('create-permission');
        Route::post('/store', 'store')->name('store-permission');
        Route::get('/edit/{id}', 'edit')->name('edit-permission');
        Route::put('/edit/{id}', 'update')->name('update-permission');
        Route::delete('/delete/{id}', 'destroy')->name('delete-permission');
    });

    Route::controller(UserController::class)->prefix('users')->middleware(['auth', 'role:admin'])->group(function () {
        Route::get('/', 'index')->name('users');
        Route::get('/create', 'create')->name('create-user');
        Route::post('/create', 'store')->name('store-user');
        Route::get('/edit/{id}', 'edit')->name('edit-user');
        Route::put('/edit/{id}', 'update')->name('update-user');
        Route::delete('/delete/{id}', 'destroy')->name('delete-user');
        Route::put('/restore/{id}', 'restore')->name('restore-user');
    });

    Route::controller(ProcurementController::class)->prefix('procurements')->group(function () {
        Route::get('/create', 'create')->middleware(['auth', 'permission:create-procurements'])->name('create-procurement');
        Route::post('/store', 'store')->middleware(['auth', 'permission:create-procurements'])->name('store-procurement');
        Route::get('/', 'index')->middleware(['auth', 'permission:read-procurements'])->name('procurements');
        Route::get('/edit/{id}', 'edit')->middleware(['auth', 'permission:show-procurements'])->name('edit-procurement');
        Route::delete('/delete/{id}', 'destroy')->name('delete-procurement');

        Route::put('/edit/{id}', 'update')->name('update-procurement');
    });

    Route::controller(PurchaseReceiptController::class)->prefix('purchase-receipts')->middleware(['auth', 'role:admin|executive|admin_gudang'])->group(function () {
        Route::get('/', 'index')->middleware(['auth', 'permission:read-purchase-receipts'])->name('purchase-receipts');
        Route::get('/create', 'create')->middleware(['auth', 'permission:create-purchase-receipts'])->name('create-purchase-receipt');
        Route::post('/store', 'store')->name('store-purchase-receipt');
        Route::get('/edit/{id}', 'edit')->middleware(['auth', 'permission:show-purchase-receipts'])->name('edit-barang-masuk');
        Route::put('/edit/{id}', 'update')->middleware(['auth', 'permission:update-purchase-receipts'])->name('update-barang-masuk');
        Route::post('/add-media/{id}', 'addMedia')->name('purchase-receipts.add-media');
        Route::delete('/delete/{id}', 'destroy')->name('purchase-receipts.destroy');
    });

    Route::controller(MediaController::class)->prefix('media')->group(function () {
        Route::delete('/delete/{mediaId}', 'delete')->name('media.delete');
    });

    Route::controller(LogErrorController::class)->middleware(['auth', 'role:admin'])->prefix('log-errors')->group(function () {
        Route::get('/', 'index')->name('log-errors');
    });

    Route::get('/procurements/export', [ProcurementController::class, 'export'])
        ->name('procurements.export');

    Route::controller(ShipmentController::class)->prefix('shipments')->middleware(['auth', 'role:admin|executive|admin_pemasaran'])->group(function () {
        Route::get('/', 'index')->middleware(['auth', 'permission:read-shipments'])->name('shipments');
        Route::get('/create', 'create')->middleware(['auth', 'permission:create-shipments'])->name('create-shipment');
        Route::post('/store', 'store')->middleware(['auth', 'permission:create-shipments'])->name('store-shipment');
        Route::get('/edit/{id}', 'edit')->middleware(['auth', 'permission:show-shipments'])->name('edit-shipment');
        Route::put('/edit/{id}', 'update')->middleware(['auth', 'permission:update-shipments'])->name('update-shipment');
        Route::delete('/delete/{id}', 'destroy')->name('delete-shipment');
        Route::get('/{id}/items', 'getShipmentItems')->name('shipments.items');
    });

    Route::controller(WarehouseController::class)->prefix('warehouses')->group(function () {
        Route::get('/', 'index')->name('warehouses');
        Route::get('/create', 'create')->name('create-warehouse');
        Route::post('/create', 'store')->name('store-warehouse');
        Route::get('/edit/{id}', 'edit')->name('edit-warehouse');
        Route::put('/update/{id}', 'update')->name('update-warehouse');
        Route::get('/cities/{adminCode1}', 'getCities')->name('warehouse-cities');
        Route::delete('/delete/{id}', 'destroy')->name('delete-warehouse');
    });

    Route::controller(ShipmentReceiptController::class)->prefix('shipment-receipts')->group(function () {
        Route::get('/', 'index')->name('shipment-receipts');
        Route::get('/create', 'create')->name('create-shipment-receipt');
        Route::post('/store', 'store')->name('store-shipment-receipt');
        Route::get('/edit/{id}', 'edit')->name('edit-shipment-receipt');
        Route::put('/edit/{id}', 'update')->name('update-shipment-receipt');
        Route::delete('/delete/{id}', 'destroy')->name('delete-shipment-receipt');
    });
});
require __DIR__ . '/auth.php';
