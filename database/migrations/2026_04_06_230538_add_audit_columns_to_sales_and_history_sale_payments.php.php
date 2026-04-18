<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('sales', function (Blueprint $table) {
            $table->foreignId('updated_by')
                ->nullable()
                ->after('person_responsible_id')
                ->constrained('users')
                ->nullOnDelete();
        });

        Schema::table('history_sale_payments', function (Blueprint $table) {
            $table->foreignId('created_by')
                ->nullable()
                ->after('sale_id')
                ->constrained('users')
                ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('history_sale_payments', function (Blueprint $table) {
            $table->dropConstrainedForeignId('created_by');
        });

        Schema::table('sales', function (Blueprint $table) {
            $table->dropConstrainedForeignId('updated_by');
        });
    }
};