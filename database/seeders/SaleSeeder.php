<?php

namespace Database\Seeders;

use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SaleSeeder extends Seeder
{
    public function run(): void
    {
        $warehouseId = 2;

        $warehouse = DB::table('warehouses')
            ->where('id', $warehouseId)
            ->first();

        $users = DB::table('users')
            ->select('id')
            ->get();

        $productStocks = DB::table('product_stocks')
            ->join('product_variants', 'product_stocks.product_variant_id', '=', 'product_variants.id')
            ->where('product_stocks.warehouse_id', $warehouseId)
            ->whereNull('product_stocks.deleted_at')
            ->whereNull('product_variants.deleted_at')
            ->select(
                'product_stocks.id',
                'product_stocks.product_variant_id',
                'product_variants.price'
            )
            ->get();

        if (! $warehouse) {
            $this->command->warn('Warehouse dengan ID 2 tidak ditemukan. Jalankan WarehouseSeeder terlebih dahulu atau pastikan gudang ID 2 tersedia.');
            return;
        }

        if ($users->isEmpty()) {
            $this->command->warn('User kosong. Pastikan tabel users sudah ada minimal 1 user.');
            return;
        }

        if ($productStocks->isEmpty()) {
            $this->command->warn('Product stock untuk warehouse ID 2 kosong. Jalankan ProductStockSeeder terlebih dahulu.');
            return;
        }

        for ($i = 1; $i <= 50; $i++) {
            DB::beginTransaction();

            try {
                $saleDate = Carbon::now()->subDays(rand(0, 30));
                $reportDate = (clone $saleDate)->subDays(rand(0, 2));
                $personResponsibleId = $users->random()->id;

                $saleId = DB::table('sales')->insertGetId([
                    'report_date' => $reportDate->format('Y-m-d'),
                    'sale_date' => $saleDate->format('Y-m-d'),
                    'person_responsible_id' => $personResponsibleId,
                    'warehouse_id' => $warehouseId,
                    'sale_type' => collect(['langsung', 'tempo'])->random(),
                    'customer_province' => 'SUMATERA UTARA',
                    'customer_city' => 'KOTA MEDAN',
                    'customer_address' => 'Jl. Pelanggan No. '.rand(1, 200),
                    'customer_name' => 'Customer '.$i,
                    'customer_contact' => '08'.rand(1111111111, 9999999999),
                    'total_amount' => 0,
                    'paid_amount' => 0,
                    'debt_amount' => 0,
                    'notes' => 'Penjualan dummy seeder gudang 2',
                    'status' => 'terhutang',
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);

                $totalAmount = 0;

                $itemCount = rand(1, min(5, $productStocks->count()));
                $saleItems = $productStocks->shuffle()->take($itemCount);

                foreach ($saleItems as $stock) {
                    $price = (int) $stock->price;

                    if ($price <= 0) {
                        continue;
                    }

                    $quantity = rand(1, 10);
                    $discount = rand(0, min(5000, $price));
                    $subtotal = max(0, ($quantity * $price) - $discount);

                    if ($subtotal <= 0) {
                        continue;
                    }

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
                    DB::commit();
                    continue;
                }

                /*
                |--------------------------------------------------------------------------
                | Status pembayaran hanya 2:
                | 1. lunas
                | 2. terhutang
                |--------------------------------------------------------------------------
                */
                $paymentScenario = rand(1, 2);

                if ($paymentScenario === 1) {
                    $paidAmount = $totalAmount; // lunas
                } else {
                    $paidAmount = rand(0, $totalAmount - 1); // terhutang
                }

                /*
                |--------------------------------------------------------------------------
                | Buat history pembayaran agar total history = paid_amount
                |--------------------------------------------------------------------------
                */
                if ($paidAmount > 0) {
                    $historyCount = rand(1, 3);
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

                $debtAmount = max(0, $totalAmount - $paidAmount);
                $status = $debtAmount > 0 ? 'terhutang' : 'lunas';

                DB::table('sales')->where('id', $saleId)->update([
                    'total_amount' => $totalAmount,
                    'paid_amount' => $paidAmount,
                    'debt_amount' => $debtAmount,
                    'status' => $status,
                    'updated_at' => now(),
                ]);

                DB::commit();
            } catch (\Throwable $th) {
                DB::rollBack();
                throw $th;
            }
        }
    }
}