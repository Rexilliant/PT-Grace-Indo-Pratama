<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RawMaterialStockSeeder extends Seeder
{
    public function run(): void
    {
        $rawMaterials = DB::table('raw_materials')->get();

        foreach ($rawMaterials as $material) {
            DB::table('raw_material_stocks')->insert([
                'raw_material_id' => $material->id,
                'warehouse_id' => 1,
                'stock' => $material->unit === 'kg' ? rand(500, 2000) : rand(200, 1000),
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            DB::table('raw_material_stocks')->insert([
                'raw_material_id' => $material->id,
                'warehouse_id' => 2,
                'stock' => $material->unit === 'kg' ? rand(200, 1000) : rand(100, 500),
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}
