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
            $table->id();                       // Primary key
            $table->unsignedBigInteger('part_id_fk'); // Foreign key for parts table
            $table->unsignedBigInteger('supplier_id_fk'); // Foreign key for suppliers table
            $table->string('URL')->nullable();  // Part URL
            $table->string('SPN');              // Supplier Part Number
            $table->decimal('price', 20, 6);    // Price with precision 20 and scale 6
            $table->timestamps();               // Created_at and updated_at columns

            // Foreign key constraints
            $table->foreign('part_id_fk')->references('id')->on('parts')->onDelete('cascade');
            $table->foreign('supplier_id_fk')->references('id')->on('suppliers')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('supplier_data');
    }
};
