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
        Schema::create('shippments', function (Blueprint $table) {
            $table->id();
            $table->string('shippment_code')->unique();
            $table->string('shippment_type');
            $table->foreignId('person_responsible_id')->constrained('users')->cascadeOnDelete();
            $table->string('status');
            $table->string('province');
            $table->text('address');
            $table->dateTime('shippment_request_at');
            $table->foreignId('created_by_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('received_by_id')->nullable()->constrained('users');
            $table->dateTime('approved_at')->nullable();
            $table->foreignId('approved_by_id')->nullable()->constrained('users');
            $table->foreignId('rejected_by_id')->nullable()->constrained('users');
            $table->dateTime('rejected_at')->nullable();
            $table->text('reason')->nullable();
            $table->foreignId('cancelled_by_id')->nullable()->constrained('users');
            $table->dateTime('cancelled_at')->nullable();
            $table->dateTime('shippment_at')->nullable();
            $table->string('shippment_services')->nullable();
            $table->dateTime('received_at')->nullable();
            $table->string('contact');
            $table->string('shipping_fleet');
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
        Schema::dropIfExists('shippments');
    }
};
