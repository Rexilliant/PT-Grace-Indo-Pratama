<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Update raw material stock movements for production batches
        DB::table('raw_material_stock_movements')
            ->where('ref_type', 'production_batches')
            ->where('note', 'Pemakaian bahan baku untuk produksi')
            ->update([
                'note' => DB::raw("CONCAT('Pemakaian bahan baku untuk produksi (PB-', LPAD(ref_id, 5, '0'), ')')")
            ]);

        // Update product stock movements for production batches
        DB::table('product_stock_movements')
            ->where('ref_type', 'production_batches')
            ->where('note', 'Hasil tambah produksi')
            ->update([
                'note' => DB::raw("CONCAT('Hasil tambah produksi (PB-', LPAD(ref_id, 5, '0'), ')')")
            ]);

        DB::table('product_stock_movements')
            ->where('ref_type', 'production_batches')
            ->where('note', 'Hasil produksi barang jadi')
            ->update([
                'note' => DB::raw("CONCAT('Hasil produksi barang jadi (PB-', LPAD(ref_id, 5, '0'), ')')")
            ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::table('raw_material_stock_movements')
            ->where('ref_type', 'production_batches')
            ->where('note', 'LIKE', 'Pemakaian bahan baku untuk produksi (PB-%')
            ->update([
                'note' => 'Pemakaian bahan baku untuk produksi'
            ]);

        DB::table('product_stock_movements')
            ->where('ref_type', 'production_batches')
            ->where('note', 'LIKE', 'Hasil tambah produksi (PB-%')
            ->update([
                'note' => 'Hasil tambah produksi'
            ]);

        DB::table('product_stock_movements')
            ->where('ref_type', 'production_batches')
            ->where('note', 'LIKE', 'Hasil produksi barang jadi (PB-%')
            ->update([
                'note' => 'Hasil produksi barang jadi'
            ]);
    }
};
