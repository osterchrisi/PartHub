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
        Schema::create('suppliers', function (Blueprint $table) {
            $table->integer('supplier_id', true);
            $table->string('supplier_name');
            $table->integer('supplier_owner_g_fk')->nullable();
            // $table->integer('supplier_owner_u_fk')->index('supplier_owner_u_fk');
            $table->timestamps();

            $table->foreignId('supplier_owner_u_fk')->constrained(
                table: 'users',
                indexName: '_id'
            )->index('supplier_owner_u_fk');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('suppliers');
    }
};
