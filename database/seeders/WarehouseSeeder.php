<?php

namespace Database\Seeders;

use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class WarehouseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $warehouses = [
            ['name' => 'Gudang Produksi Sumatera Utara', 'province' => 'Sumatera Utara', 'city' => 'Medan', 'responsible_id' => 1],
            ['name' => 'Gudang Pemasaran Sumatera Utara', 'province' => 'Sumatera Utara', 'city' => 'Medan', 'responsible_id' => 1],
        ];

        foreach ($warehouses as $warehouse) {
            DB::table('warehouses')->updateOrInsert(
                [
                    'name' => $warehouse['name'],
                    'province' => $warehouse['province'],
                    'city' => $warehouse['city'],
                    'responsible_id' => $warehouse['responsible_id'],
                ],
                [
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now(),
                ]
            );
        }
    }
}
