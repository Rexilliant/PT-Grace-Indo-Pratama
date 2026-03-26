<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('sales', function (Blueprint $table) {
            $table->id();

            $table->date('report_date');
            $table->date('sale_date');

            $table->foreignId('person_responsible_id')
                ->constrained('users')
                ->cascadeOnDelete();

            $table->string('sale_type');

            $table->string('customer_province');
            $table->string('customer_city')->nullable();
            $table->text('customer_address')->nullable();
            $table->foreignId('warehouse_id')->constrained('warehouses');

            $table->string('customer_name');
            $table->string('customer_contact');

            $table->unsignedBigInteger('total_amount')->default(0);
            $table->unsignedBigInteger('paid_amount')->default(0);
            $table->unsignedBigInteger('debt_amount')->default(0);

            $table->text('notes')->nullable();
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
        Schema::dropIfExists('sales');
    }
};