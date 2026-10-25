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
        Schema::table('employees', function (Blueprint $table) {
            $indexes = collect(Schema::getIndexes('employees'))->pluck('name')->toArray();

            if (in_array('employees_email_unique', $indexes)) {
                $table->dropUnique('employees_email_unique');
            }

            if (in_array('employees_phone_unique', $indexes)) {
                $table->dropUnique('employees_phone_unique');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('employees', function (Blueprint $table) {
            $table->unique('email');
            $table->unique('phone');
        });
    }
};
