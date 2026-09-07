<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('shipments', function (Blueprint $table) {
            $table->string('received_name')->nullable()->after('created_by_id');
        });

        // Copy existing names if any
        if (Schema::hasColumn('shipments', 'received_by_id')) {
            DB::statement("
                UPDATE shipments s
                LEFT JOIN users u ON s.received_by_id = u.id
                SET s.received_name = u.name
                WHERE s.received_by_id IS NOT NULL
            ");

            Schema::table('shipments', function (Blueprint $table) {
                $table->dropForeign(['received_by_id']);
                $table->dropColumn('received_by_id');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('shipments', function (Blueprint $table) {
            $table->foreignId('received_by_id')->nullable()->after('created_by_id')->constrained('users');
            $table->dropColumn('received_name');
        });
    }
};
