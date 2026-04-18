<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RawMaterialSeeder extends Seeder
{
    public function run(): void
    {
        $materials = [
            [
                'code' => 'RM-001',
                'name' => 'Urea',
                'unit' => 'kg',
                'status' => 'active',
            ],
            [
                'code' => 'RM-002',
                'name' => 'ZA (Amonium Sulfat)',
                'unit' => 'kg',
                'status' => 'active',
            ],
            [
                'code' => 'RM-003',
                'name' => 'KCL',
                'unit' => 'kg',
                'status' => 'active',
            ],
            [
                'code' => 'RM-004',
                'name' => 'SP-36',
                'unit' => 'kg',
                'status' => 'active',
            ],
            [
                'code' => 'RM-005',
                'name' => 'Dolomit',
                'unit' => 'kg',
                'status' => 'active',
            ],
            [
                'code' => 'RM-006',
                'name' => 'Humic Acid',
                'unit' => 'kg',
                'status' => 'active',
            ],
            [
                'code' => 'RM-007',
                'name' => 'Molase',
                'unit' => 'liter',
                'status' => 'active',
            ],
            [
                'code' => 'RM-008',
                'name' => 'Air',
                'unit' => 'liter',
                'status' => 'active',
            ],
            [
                'code' => 'RM-009',
                'name' => 'Mikroba Organik',
                'unit' => 'liter',
                'status' => 'active',
            ],
            [
                'code' => 'RM-010',
                'name' => 'Batu Fosfat',
                'unit' => 'kg',
                'status' => 'active',
            ],
        ];

        foreach ($materials as $material) {
            DB::table('raw_materials')->insert([
                'code' => $material['code'],
                'name' => $material['name'],
                'unit' => $material['unit'],
                'status' => $material['status'],
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}