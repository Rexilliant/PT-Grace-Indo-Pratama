<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        // Tambah kolom di sales
        Schema::table('sales', function (Blueprint $table) {
            $table->string('stock_type')->default('ready')->after('sale_type'); // ready | po
        });

        // Tambah kolom di sale_items
        Schema::table('sale_items', function (Blueprint $table) {
            $table->unsignedInteger('fulfilled_quantity')->default(0)->after('quantity');
        });

        // Tabel history pemenuhan PO
        Schema::create('sale_item_fulfillments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('sale_id')->constrained('sales')->cascadeOnDelete();
            $table->foreignId('sale_item_id')->constrained('sale_items')->cascadeOnDelete();
            $table->foreignId('product_stock_id')->constrained('product_stocks');
            $table->unsignedInteger('quantity');                 // qty yang dipenuhi saat itu
            $table->date('fulfillment_date');                   // tanggal penyerahan
            $table->foreignId('created_by')->nullable()->constrained('users');
            $table->text('note')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sale_item_fulfillments');

        Schema::table('sales', function (Blueprint $table) {
            $table->dropColumn('stock_type');
        });

        Schema::table('sale_items', function (Blueprint $table) {
            $table->dropColumn('fulfilled_quantity');
        });
    }
};