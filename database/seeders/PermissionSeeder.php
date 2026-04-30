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

            // Pengadaan Bahan Baku
            'tambah pengadaan bahan baku',
            'edit pengadaan bahan baku',
            'edit status pengadaan bahan baku',
            'hapus pengadaan bahan baku',
            'baca pengadaan bahan baku',

            // Bahan Baku
            'tambah bahan baku',
            'edit bahan baku',
            'hapus bahan baku',
            'baca bahan baku',
            'baca stok bahan baku',

            // Bahan Baku Masuk
            'tambah bahan baku masuk',
            'edit bahan baku masuk',
            'hapus bahan baku masuk',
            'baca bahan baku masuk',

            // Produksi
            'tambah produksi',
            'edit produksi',
            'hapus produksi',
            'baca produksi',

            // Pengiriman Produk
            'tambah pengiriman produk',
            'edit pengiriman produk',
            'edit status pengiriman produk',
            'edit status dikirim pengiriman produk',
            'hapus pengiriman produk',
            'baca pengiriman produk',

            // Penerimaan Pengiriman Produk
            'tambah penerimaan pengiriman produk',
            'edit penerimaan pengiriman produk',
            'edit status penerimaan pengiriman produk',
            'hapus penerimaan pengiriman produk',
            'baca penerimaan pengiriman produk',

            // Penjualan
            'tambah penjualan',
            'edit penjualan',
            'hapus penjualan',
            'baca penjualan',

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

            // Produk Varian
            'tambah produk varian',
            'edit produk varian',
            'hapus produk varian',
            'baca produk varian',

            // Stok Produk
            'baca produk stok',

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
