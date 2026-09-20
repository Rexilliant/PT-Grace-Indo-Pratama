<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

class ProductSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Matikan constraint foreign key & bersihkan data lama
        Schema::disableForeignKeyConstraints();
        DB::table('product_variants')->truncate();
        DB::table('products')->truncate();
        Schema::enableForeignKeyConstraints();

        $products = [
            [
                'code' => 'PRD-001',
                'name' => 'Pupuk NPK Mutiara',
                'status' => 'aktif',
                'variants' => [
                    ['name' => '1 Kg', 'pack_size' => 1, 'unit' => 'kg', 'price' => 25000],
                    ['name' => '5 Kg', 'pack_size' => 5, 'unit' => 'kg', 'price' => 120000],
                ],
            ],
            [
                'code' => 'PRD-002',
                'name' => 'Pupuk Urea',
                'status' => 'aktif',
                'variants' => [
                    ['name' => '1 Kg', 'pack_size' => 1, 'unit' => 'kg', 'price' => 15000],
                    ['name' => '25 Kg', 'pack_size' => 25, 'unit' => 'kg', 'price' => 350000],
                ],
            ],
            [
                'code' => 'PRD-003',
                'name' => 'Pupuk Organik Cair',
                'status' => 'aktif',
                'variants' => [
                    ['name' => '500 ml', 'pack_size' => 500, 'unit' => 'ml', 'price' => 20000],
                    ['name' => '1 Liter', 'pack_size' => 1000, 'unit' => 'ml', 'price' => 35000],
                ],
            ],
            [
                'code' => 'PRD-004',
                'name' => 'Pupuk Kandang',
                'status' => 'aktif',
                'variants' => [
                    ['name' => '10 Kg', 'pack_size' => 10, 'unit' => 'kg', 'price' => 30000],
                    ['name' => '50 Kg', 'pack_size' => 50, 'unit' => 'kg', 'price' => 120000],
                ],
            ],
            [
                'code' => 'PRD-005',
                'name' => 'Pupuk ZA',
                'status' => 'aktif',
                'variants' => [
                    ['name' => '1 Kg', 'pack_size' => 1, 'unit' => 'kg', 'price' => 12000],
                    ['name' => '25 Kg', 'pack_size' => 25, 'unit' => 'kg', 'price' => 280000],
                ],
            ],
        ];

        // 2. Jalankan transaksi database
        DB::transaction(function () use ($products) {
            $now = now();

            foreach ($products as $product) {
                // Insert ke tabel products tanpa kolom description
                $productId = DB::table('products')->insertGetId([
                    'code' => $product['code'],
                    'name' => $product['name'],
                    'status' => $product['status'],
                    'created_at' => $now,
                    'updated_at' => $now,
                ]);

                foreach ($product['variants'] as $variant) {
                    // Format SKU rapi & konsisten (Contoh: SKU-PRD-001-1KG)
                    $skuSuffix = Str::upper($variant['pack_size'] . $variant['unit']);
                    $sku = "SKU-{$product['code']}-{$skuSuffix}";

                    DB::table('product_variants')->insert([
                        'product_id' => $productId,
                        'sku' => $sku,
                        'name' => $product['name'] . ' - ' . $variant['name'],
                        'pack_size' => $variant['pack_size'],
                        'unit' => $variant['unit'],
                        'price' => $variant['price'],
                        'status' => 'aktif',
                        'created_at' => $now,
                        'updated_at' => $now,
                    ]);
                }
            }
        });
    }
}