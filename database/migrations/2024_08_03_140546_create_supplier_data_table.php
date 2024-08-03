<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('supplier_data', function (Blueprint $table) {
            $table->id();                                   // Primary key
            $table->integer('part_id_fk');                  // Foreign key for parts table
            $table->integer('supplier_id_fk');              // Foreign key for suppliers table
            $table->string('URL')->nullable();              // Part URL
            $table->string('SPN');                          // Supplier Part Number
            $table->decimal('price', 20, 6);                // Price with precision 20 and scale 6
            $table->timestamps();                           // Created_at and updated_at columns

            // Foreign key constraints
            $table->foreign('part_id_fk')->references('part_id')->on('parts');
            $table->foreign('supplier_id_fk')->references('supplier_id')->on('suppliers');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('supplier_data', function (Blueprint $table) {
            // Drop foreign key constraints
            $table->dropForeign(['part_id_fk']);
            $table->dropForeign(['supplier_id_fk']);
        });

        Schema::dropIfExists('supplier_data');
    }
};
