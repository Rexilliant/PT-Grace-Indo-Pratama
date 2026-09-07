<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class RolePermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Bersihkan cache Spatie Permission jika menggunakan package Spatie
        if (class_exists(\Spatie\Permission\PermissionRegistrar::class)) {
            app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();
        }

        Schema::disableForeignKeyConstraints();

        DB::table('role_has_permissions')->truncate();
        DB::table('permissions')->truncate();
        DB::table('roles')->truncate();

        // 1. Seed Roles
        $roles = [
            ['id' => 1, 'name' => 'master', 'guard_name' => 'web', 'created_at' => '2026-05-24 13:18:04', 'updated_at' => '2026-05-24 13:18:04'],
            ['id' => 3, 'name' => 'Kepala Pemasaran', 'guard_name' => 'web', 'created_at' => '2026-07-31 21:18:04', 'updated_at' => '2026-07-31 21:18:04'],
            ['id' => 4, 'name' => 'Kepala Produksi', 'guard_name' => 'web', 'created_at' => '2026-07-31 21:19:59', 'updated_at' => '2026-07-31 21:19:59'],
        ];
        DB::table('roles')->insert($roles);

        // 2. Seed Permissions
        $permissionNames = [
            75 => 'akses dashboard',
            76 => 'tambah gudang',
            77 => 'edit gudang',
            78 => 'hapus gudang',
            79 => 'baca gudang',
            80 => 'export gudang',
            81 => 'tambah pengadaan bahan baku',
            82 => 'edit pengadaan bahan baku',
            83 => 'edit status pengadaan bahan baku',
            84 => 'hapus pengadaan bahan baku',
            85 => 'baca pengadaan bahan baku',
            86 => 'export pengadaan bahan baku',
            87 => 'tambah bahan baku',
            88 => 'edit bahan baku',
            89 => 'hapus bahan baku',
            90 => 'baca bahan baku',
            91 => 'baca stok bahan baku',
            92 => 'export bahan baku',
            93 => 'tambah bahan baku masuk',
            94 => 'edit bahan baku masuk',
            95 => 'hapus bahan baku masuk',
            96 => 'baca bahan baku masuk',
            97 => 'export bahan baku masuk',
            98 => 'tambah produksi',
            99 => 'edit produksi',
            100 => 'hapus produksi',
            101 => 'baca produksi',
            102 => 'export produksi',
            103 => 'tambah pengiriman produk',
            104 => 'edit pengiriman produk',
            105 => 'edit status pengiriman produk',
            106 => 'edit status dikirim pengiriman produk',
            107 => 'hapus pengiriman produk',
            108 => 'baca pengiriman produk',
            109 => 'export pengiriman produk',
            110 => 'tambah penerimaan pengiriman produk',
            111 => 'edit penerimaan pengiriman produk',
            112 => 'edit status penerimaan pengiriman produk',
            113 => 'hapus penerimaan pengiriman produk',
            114 => 'baca penerimaan pengiriman produk',
            115 => 'export penerimaan pengiriman produk',
            116 => 'tambah penjualan',
            117 => 'edit penjualan',
            118 => 'hapus penjualan',
            119 => 'baca penjualan',
            120 => 'export penjualan',
            121 => 'tambah karyawan',
            122 => 'edit karyawan',
            123 => 'hapus karyawan',
            124 => 'baca karyawan',
            125 => 'tambah akun',
            126 => 'edit akun',
            127 => 'hapus akun',
            128 => 'baca akun',
            129 => 'tambah produk',
            130 => 'edit produk',
            131 => 'hapus produk',
            132 => 'baca produk',
            133 => 'export produk',
            134 => 'tambah produk varian',
            135 => 'edit produk varian',
            136 => 'hapus produk varian',
            137 => 'baca produk varian',
            138 => 'export produk varian',
            139 => 'baca produk stok',
            140 => 'export produk stok',
            141 => 'tambah role',
            142 => 'edit role',
            143 => 'hapus role',
            144 => 'baca role',
            145 => 'tambah izin',
            146 => 'edit izin',
            147 => 'hapus izin',
            148 => 'baca izin',
            149 => 'baca log error',
            150 => 'baca history aktivitas',
            153 => 'tambah bahan baku masuk gudang sendiri',
            154 => 'edit bahan baku masuk gudang sendiri',
            155 => 'hapus bahan baku masuk gudang sendiri',
            156 => 'baca bahan baku masuk gudang sendiri',
            157 => 'export bahan baku masuk gudang sendiri',
            158 => 'edit bahan baku masuk sendiri',
            159 => 'hapus bahan baku masuk sendiri',
        ];

        $permissions = [];
        foreach ($permissionNames as $id => $name) {
            $permissions[] = [
                'id' => $id,
                'name' => $name,
                'guard_name' => 'web',
                'created_at' => '2026-09-05 10:57:34',
                'updated_at' => '2026-09-05 10:57:34',
            ];
        }
        DB::table('permissions')->insert($permissions);

        // 3. Seed Role Has Permissions
        $rolePermissionsMap = [
            // Role ID 1 (master): permission 75-150 dan 153-159
            1 => array_merge(range(75, 150), [153, 154, 155, 156, 157, 158, 159]),

            // Role ID 3 (Kepala Pemasaran)
            3 => [79, 103, 104, 107, 108, 110, 111, 113, 114, 116, 117, 118, 119, 132, 137, 139],

            // Role ID 4 (Kepala Produksi)
            4 => [79, 81, 82, 84, 85, 87, 88, 89, 90, 91, 98, 99, 100, 101, 104, 105, 106, 108, 112, 114, 132, 137, 139, 153, 156, 157, 158, 159],
        ];

        $roleHasPermissions = [];
        foreach ($rolePermissionsMap as $roleId => $permIds) {
            foreach ($permIds as $permId) {
                $roleHasPermissions[] = [
                    'permission_id' => $permId,
                    'role_id' => $roleId,
                ];
            }
        }
        DB::table('role_has_permissions')->insert($roleHasPermissions);

        Schema::enableForeignKeyConstraints();
    }
}