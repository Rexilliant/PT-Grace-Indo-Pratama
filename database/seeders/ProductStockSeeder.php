<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ProductStockSeeder extends Seeder
{
    public function run(): void
    {
        $variants = DB::table('product_variants')->get();

        foreach ($variants as $variant) {

            // Warehouse 1
            DB::table('product_stocks')->insert([
                'product_variant_id' => $variant->id,
                'warehouse_id' => 1,
                'stock' => rand(50, 200),
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            // Warehouse 2
            DB::table('product_stocks')->insert([
                'product_variant_id' => $variant->id,
                'warehouse_id' => 2,
                'stock' => rand(30, 150),
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}
