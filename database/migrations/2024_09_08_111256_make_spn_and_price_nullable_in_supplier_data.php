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
        Schema::table('supplier_data', function (Blueprint $table) {
            $table->string('SPN')->nullable()->change();   // Modify the SPN column to be nullable
            $table->decimal('price', 20, 6)->nullable()->change();   // Modify the price column to be nullable
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('supplier_data', function (Blueprint $table) {
            $table->string('SPN')->nullable(false)->change();   // Revert the SPN column back to non-nullable
            $table->decimal('price', 20, 6)->nullable(false)->change();   // Revert the price column back to non-nullable
        });
    }
};
