<?php

namespace Database\Seeders;

use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SaleSeeder extends Seeder
{
    public function run(): void
    {
        $productStocks = DB::table('product_stocks')->get();
        $warehouses = DB::table('warehouses')->get();
        $users = DB::table('users')->get();

        if ($productStocks->isEmpty()) {
            $this->command->warn('Product stock kosong. Jalankan ProductStockSeeder terlebih dahulu.');

            return;
        }

        if ($warehouses->isEmpty()) {
            $this->command->warn('Warehouse kosong. Jalankan WarehouseSeeder terlebih dahulu.');

            return;
        }

        if ($users->isEmpty()) {
            $this->command->warn('User kosong. Pastikan tabel users sudah ada minimal 1 user.');

            return;
        }

        for ($i = 1; $i <= 50; $i++) {
            $saleDate = Carbon::now()->subDays(rand(0, 30));
            $reportDate = (clone $saleDate)->subDays(rand(0, 2));

            $warehouse = $warehouses->random();
            $personResponsible = $users->random();

            $saleId = DB::table('sales')->insertGetId([
                'report_date' => $reportDate->format('Y-m-d'),
                'sale_date' => $saleDate->format('Y-m-d'),
                'person_responsible_id' => $personResponsible->id,
                'warehouse_id' => $warehouse->id,
                'sale_type' => collect(['langsung', 'tempo'])->random(),
                'customer_province' => 'SUMATERA UTARA',
                'customer_city' => 'KOTA MEDAN',
                'customer_address' => 'Jl. Pelanggan No. '.rand(1, 200),
                'customer_name' => 'Customer '.$i,
                'customer_contact' => '08'.rand(1111111111, 9999999999),
                'total_amount' => 0,
                'paid_amount' => 0,
                'debt_amount' => 0,
                'notes' => 'Penjualan dummy seeder',
                'status' => 'selesai',
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            $totalAmount = 0;

            $availableStocks = $productStocks->where('warehouse_id', $warehouse->id);

            if ($availableStocks->count() < 1) {
                $availableStocks = $productStocks;
            }

            $itemCount = rand(1, min(5, $availableStocks->count()));
            $saleItems = $availableStocks->shuffle()->take($itemCount);

            foreach ($saleItems as $stock) {
                $variant = DB::table('product_variants')
                    ->where('id', $stock->product_variant_id)
                    ->first();

                if (! $variant) {
                    continue;
                }

                $quantity = rand(1, 10);
                $price = (int) $variant->price;
                $discount = rand(0, min(5000, $price));
                $subtotal = ($quantity * $price) - $discount;

                DB::table('sale_items')->insert([
                    'sale_id' => $saleId,
                    'product_stock_id' => $stock->id,
                    'quantity' => $quantity,
                    'price' => $price,
                    'discount' => $discount,
                    'subtotal' => $subtotal,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);

                $totalAmount += $subtotal;
            }

            if ($totalAmount <= 0) {
                DB::table('sales')->where('id', $saleId)->delete();

                continue;
            }

            $paymentScenario = rand(1, 3);
            $paidAmount = 0;

            // 1 = lunas, 2 = sebagian, 3 = belum bayar
            if ($paymentScenario === 1) {
                $paidAmount = $totalAmount;
            } elseif ($paymentScenario === 2) {
                $paidAmount = rand((int) ($totalAmount * 0.3), (int) ($totalAmount * 0.8));
            } else {
                $paidAmount = 0;
            }

            $debtAmount = $totalAmount - $paidAmount;

            DB::table('sales')->where('id', $saleId)->update([
                'total_amount' => $totalAmount,
                'paid_amount' => $paidAmount,
                'debt_amount' => $debtAmount,
                'updated_at' => now(),
            ]);

            // Buat history pembayaran jika ada pembayaran
            if ($paidAmount > 0) {
                $historyCount = rand(1, min(3, $paidAmount > 0 ? 3 : 1));

                $remainingPaid = $paidAmount;
                $paymentDates = collect();

                for ($j = 1; $j <= $historyCount; $j++) {
                    $paymentDates->push(
                        (clone $saleDate)->addDays(rand(0, 7))
                    );
                }

                $paymentDates = $paymentDates->sort()->values();

                for ($j = 0; $j < $historyCount; $j++) {
                    if ($j === $historyCount - 1) {
                        $amount = $remainingPaid;
                    } else {
                        $maxInstallment = max(1, (int) floor($remainingPaid / ($historyCount - $j)));
                        $amount = rand(1, $maxInstallment);
                    }

                    DB::table('history_sale_payments')->insert([
                        'sale_id' => $saleId,
                        'payment_date' => $paymentDates[$j]->format('Y-m-d'),
                        'amount' => $amount,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);

                    $remainingPaid -= $amount;
                }
            }
        }
    }
}
