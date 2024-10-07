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
        //* Part ID forgeign key
        Schema::table('supplier_data', function (Blueprint $table) {
            // Drop the foreign key by its name to ensure accuracy
            $table->dropForeign('supplier_data_part_id_fk_foreign');

            // Now, recreate the foreign key constraint with 'onDelete' cascade
            $table->foreign('part_id_fk')
                ->references('part_id')->on('parts')
                ->onDelete('cascade');
        });

        //* Supplier ID foreign key
        Schema::table('supplier_data', function (Blueprint $table) {
            // Drop the foreign key by its name to ensure accuracy
            $table->dropForeign('supplier_data_supplier_id_fk_foreign');

            // Now, recreate the foreign key constraint with 'onDelete' cascade
            $table->foreign('part_id_fk')
                ->references('part_id')->on('parts')
                ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //* Part ID forgeign key
        Schema::table('supplier_data', function (Blueprint $table) {
            // Drop the foreign key by its name
            $table->dropForeign('supplier_data_part_id_fk_foreign');

            // Recreate the original foreign key constraint
            $table->foreign('part_id_fk')
                ->references('part_id')->on('parts')
                ->onDelete('restrict'); // or 'no action', depending on the original setup
        });

        //* Supplier ID foreign key
        Schema::table('supplier_data', function (Blueprint $table) {
            // Drop the foreign key by its name
            $table->dropForeign('supplier_data_supplier_id_fk_foreign');

            // Recreate the original foreign key constraint
            $table->foreign('part_id_fk')
                ->references('part_id')->on('parts')
                ->onDelete('restrict'); // or 'no action', depending on the original setup
        });
    }
};
