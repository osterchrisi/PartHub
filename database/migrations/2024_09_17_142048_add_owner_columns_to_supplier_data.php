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
            // Add the two new columns
            $table->unsignedBigInteger('supplier_data_owner_u_fk')->nullable(); // User ID, foreign key
            $table->unsignedBigInteger('supplier_data_owner_g_fk')->nullable(); // Generic ID or group foreign key

            // Add foreign key constraint on 'supplier_data_owner_u_fk' referencing 'id' in 'users' table
            $table->foreign('supplier_data_owner_u_fk')
                ->references('id')
                ->on('users')
                ->onDelete('cascade'); // Cascade delete if the user is deleted
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('supplier_data', function (Blueprint $table) {
            // Drop the foreign key constraint first
            $table->dropForeign(['supplier_data_owner_u_fk']);

            // Drop the columns
            $table->dropColumn(['supplier_data_owner_u_fk', 'supplier_data_owner_g_fk']);
        });
    }
};
