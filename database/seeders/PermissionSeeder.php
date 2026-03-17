<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class PermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('permissions')->insert([
            ['name' => 'access dashboard', 'guard_name' => 'web'],
            ['name' => 'create-procurements', 'guard_name' => 'web'],
            ['name' => 'update-procurements', 'guard_name' => 'web'],
            ['name' => 'create-purchase-receipts', 'guard_name' => 'web'],
            ['name' => 'update-purchase-receipts', 'guard_name' => 'web'],
            ['name' => 'read-purchase-receipts', 'guard_name' => 'web'],
            ['name' => 'read-procurements', 'guard_name' => 'web'],
            ['name' => 'show-purchase-receipts', 'guard_name' => 'web'],
            ['name' => 'show-procurements', 'guard_name' => 'web'],
            ['name' => 'create-productions', 'guard_name' => 'web'],
            ['name' => 'read-productions', 'guard_name' => 'web'],
            ['name' => 'show-productions', 'guard_name' => 'web'],
            ['name' => 'update-productions', 'guard_name' => 'web'],
            ['name' => 'delete-productions', 'guard_name' => 'web'],
            ['name' => 'create-shippments', 'guard_name' => 'web'],
            ['name' => 'read-shippments', 'guard_name' => 'web'],
            ['name' => 'update-shippments', 'guard_name' => 'web'],
            ['name' => 'show-shippments', 'guard_name' => 'web'],
            ['name' => 'delete-shippments', 'guard_name' => 'web'],
            ['name' => 'read-products', 'guard_name' => 'web'],
            ['name' => 'update-products', 'guard_name' => 'web'],
            ['name' => 'create-products', 'guard_name' => 'web'],
            ['name' => 'read-product-variants', 'guard_name' => 'web'],
            ['name' => 'create-product-variants', 'guard_name' => 'web'],
            ['name' => 'update-product-variants', 'guard_name' => 'web'],
            ['name' => 'read-permissions', 'guard_name' => 'web'],
            ['name' => 'create-permissions', 'guard_name' => 'web'],
            ['name' => 'update-permissions', 'guard_name' => 'web'],
            ['name' => 'delete-permissions', 'guard_name' => 'web'],
            ['name' => 'create-roles', 'guard_name' => 'web'],
            ['name' => 'read-roles', 'guard_name' => 'web'],
            ['name' => 'show-roles', 'guard_name' => 'web'],
            ['name' => 'update-roles', 'guard_name' => 'web'],
            ['name' => 'delete-roles', 'guard_name' => 'web'],
            ['name' => 'read-logs', 'guard_name' => 'web'],
            ['name' => 'read-employees', 'guard_name' => 'web'],
            ['name' => 'show-employees', 'guard_name' => 'web'],
            ['name' => 'create-employees', 'guard_name' => 'web'],
            ['name' => 'update-employees', 'guard_name' => 'web'],
            ['name' => 'delete-employees', 'guard_name' => 'web'],
            ['name' => 'read-users', 'guard_name' => 'web'],
            ['name' => 'show-users', 'guard_name' => 'web'],
            ['name' => 'create-users', 'guard_name' => 'web'],
            ['name' => 'update-users', 'guard_name' => 'web'],
            ['name' => 'delete-users', 'guard_name' => 'web'],
            ['name' => 'read-raw-materials', 'guard_name' => 'web'],
            ['name' => 'show-raw-materials', 'guard_name' => 'web'],
            ['name' => 'create-raw-materials', 'guard_name' => 'web'],
            ['name' => 'update-raw-materials', 'guard_name' => 'web'],
            ['name' => 'delete-raw-materials', 'guard_name' => 'web'],
            ['name' => 'show-stock-raw-materials', 'guard_name' => 'web'],
        ]);
    }
}
