<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('shipment_receipt_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('shipment_receipt_id')->constrained('shipment_receipts')->cascadeOnDelete();
            $table->foreignId('shipment_item_id')->constrained('shipment_items')->cascadeOnDelete();
            $table->integer('qty_received');
            $table->integer('qty_damaged')->default(0);
            $table->integer('qty_missing')->default(0);
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('shipment_receipt_items');
    }
};
