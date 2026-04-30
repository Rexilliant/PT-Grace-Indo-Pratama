<?php

namespace Database\Seeders;

use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class WarehouseSeeder extends Seeder
{
    public function run(): void
    {
        $warehouses = [
            [
                'name' => 'Gudang Produksi Sumatera Utara',
                'province' => 'SUMATERA UTARA',
                'city' => 'KOTA MEDAN',
                'type' => 'produksi',
            ],
            [
                'name' => 'Gudang Pemasaran Sumatera Utara',
                'province' => 'SUMATERA UTARA',
                'city' => 'KOTA MEDAN',
                'type' => 'pemasaran',
            ],
        ];

        foreach ($warehouses as $warehouse) {
            DB::table('warehouses')->updateOrInsert(
                [
                    'name' => $warehouse['name'],
                    'province' => $warehouse['province'],
                    'city' => $warehouse['city'],
                ],
                [
                    'type' => $warehouse['type'], // ✅ ini penting (tadi hilang)
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now(),
                ]
            );
        }
    }
}
