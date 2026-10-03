<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (class_exists(PermissionRegistrar::class)) {
            app()[PermissionRegistrar::class]->forgetCachedPermissions();
        }

        $perms = [
            'baca history stok bahan baku',
            'baca history stok produk',
        ];

        foreach ($perms as $permName) {
            $permission = Permission::firstOrCreate([
                'name' => $permName,
                'guard_name' => 'web',
            ]);

            // Assign to master role if exists
            $masterRole = Role::where('name', 'master')->where('guard_name', 'web')->first();
            if ($masterRole && !$masterRole->hasPermissionTo($permission)) {
                $masterRole->givePermissionTo($permission);
            }
        }

        // Assign to Kepala Produksi
        $kepalaProduksi = Role::where('name', 'Kepala Produksi')->where('guard_name', 'web')->first();
        if ($kepalaProduksi) {
            $kepalaProduksi->givePermissionTo('baca history stok bahan baku');
            $kepalaProduksi->givePermissionTo('baca history stok produk');
        }

        // Assign to Kepala Pemasaran
        $kepalaPemasaran = Role::where('name', 'Kepala Pemasaran')->where('guard_name', 'web')->first();
        if ($kepalaPemasaran) {
            $kepalaPemasaran->givePermissionTo('baca history stok produk');
        }

        if (class_exists(PermissionRegistrar::class)) {
            app()[PermissionRegistrar::class]->forgetCachedPermissions();
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Permission::whereIn('name', [
            'baca history stok bahan baku',
            'baca history stok produk',
        ])->delete();

        if (class_exists(PermissionRegistrar::class)) {
            app()[PermissionRegistrar::class]->forgetCachedPermissions();
        }
    }
};
