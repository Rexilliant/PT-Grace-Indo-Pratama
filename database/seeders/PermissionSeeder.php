<?php

namespace Database\Seeders;

use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\PermissionRegistrar;

class PermissionSeeder extends Seeder
{
    public function run(): void
    {
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        $now = Carbon::now();

        $permissions = [
            // Dashboard
            'akses dashboard',

            // Gudang
            'tambah gudang',
            'edit gudang',
            'hapus gudang',
            'baca gudang',
            'export gudang',

            // Pengadaan Bahan Baku
            'tambah pengadaan bahan baku',
            'edit pengadaan bahan baku',
            'edit status pengadaan bahan baku',
            'hapus pengadaan bahan baku',
            'baca pengadaan bahan baku',
            'export pengadaan bahan baku',

            // Bahan Baku
            'tambah bahan baku',
            'edit bahan baku',
            'hapus bahan baku',
            'baca bahan baku',
            'baca stok bahan baku',
            'export bahan baku',

            // Bahan Baku Masuk
            'tambah bahan baku masuk',
            'tambah bahan baku masuk gudang sendiri',
            'edit bahan baku masuk',
            'edit bahan baku masuk gudang sendiri',
            'edit bahan baku masuk sendiri',
            'hapus bahan baku masuk',
            'hapus bahan baku masuk gudang sendiri',
            'hapus bahan baku masuk sendiri',
            'baca bahan baku masuk',
            'baca bahan baku masuk gudang sendiri',
            'export bahan baku masuk',
            'export bahan baku masuk gudang sendiri',

            // Produksi
            'tambah produksi',
            'edit produksi',
            'hapus produksi',
            'baca produksi',
            'export produksi',

            // Pengiriman Produk
            'tambah pengiriman produk',
            'edit pengiriman produk',
            'edit status pengiriman produk',
            'edit status dikirim pengiriman produk',
            'hapus pengiriman produk',
            'baca pengiriman produk',
            'export pengiriman produk',

            // Penerimaan Pengiriman Produk
            'tambah penerimaan pengiriman produk',
            'edit penerimaan pengiriman produk',
            'edit status penerimaan pengiriman produk',
            'hapus penerimaan pengiriman produk',
            'baca penerimaan pengiriman produk',
            'export penerimaan pengiriman produk',

            // Penjualan
            'tambah penjualan',
            'edit penjualan',
            'hapus penjualan',
            'baca penjualan',
            'export penjualan',

            // Karyawan
            'tambah karyawan',
            'edit karyawan',
            'hapus karyawan',
            'baca karyawan',

            // Akun
            'tambah akun',
            'edit akun',
            'hapus akun',
            'baca akun',

            // Produk
            'tambah produk',
            'edit produk',
            'hapus produk',
            'baca produk',
            'export produk',

            // Produk Varian
            'tambah produk varian',
            'edit produk varian',
            'hapus produk varian',
            'baca produk varian',
            'export produk varian',

            // Stok Produk
            'baca produk stok',
            'export produk stok',

            // Role
            'tambah role',
            'edit role',
            'hapus role',
            'baca role',

            // Izin / Permission
            'tambah izin',
            'edit izin',
            'hapus izin',
            'baca izin',

            // Lainnya
            'baca log error',
            'baca history aktivitas',
        ];

        foreach ($permissions as $permission) {
            Permission::updateOrCreate(
                [
                    'name' => $permission,
                    'guard_name' => 'web',
                ],
                [
                    'updated_at' => $now,
                    'created_at' => $now,
                ]
            );
        }

        app()[PermissionRegistrar::class]->forgetCachedPermissions();
    }
}
