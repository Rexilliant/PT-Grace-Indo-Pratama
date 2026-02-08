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
        Schema::create('production_batches', function (Blueprint $table) {
            $table->id();
            $table->foreignId('person_responsible_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('product_stock_id')->constrained('product_stocks')->cascadeOnDelete();
            $table->string('province');
            $table->date('entry_date');
            $table->integer('stock');
            $table->integer('quantity');
            $table->string('note')->nullable();
            $table->string('status');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('production_batches');
    }
};
