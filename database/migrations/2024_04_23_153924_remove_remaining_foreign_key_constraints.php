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
        Schema::table('suppliers', function (Blueprint $table) {
            $table->dropForeign('suppliers_supplier_owner_u_fk');
        });

        Schema::table('parts', function (Blueprint $table) {
            $table->dropForeign('parts_part_supplier_fk_foreign');
        });

        Schema::table('part_meta', function (Blueprint $table) {
            $table->dropForeign('part_id_fk');
            $table->dropForeign('meta_owner_u_fk');
        });

        Schema::table('images', function (Blueprint $table) {
            $table->dropForeign('image_owner_u_fk');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('suppliers', function (Blueprint $table) {
            $table->foreign(['supplier_owner_u_fk'], 'suppliers_supplier_owner_u_fk')->references('id')->on('users');
            // $table->foreign('supplier_owner_u_fk')->constrained(
            //     table: 'users',
            //     indexName: '_id'
            // )->index('supplier_owner_u_fk');
        });

        Schema::table('parts', function (Blueprint $table) {
            $table->foreign('part_supplier_fk')
                ->references('supplier_id')
                ->on('suppliers')
                ->onDelete('restrict');
        });

        Schema::table('part_meta', function (Blueprint $table) {
            $table->foreign('part_id_fk')->references('part_id')->on('parts')->index('part_id_fk');
            $table->foreign('meta_owner_u_fk')->references('id')->on('users')->index('meta_owner_u_fk');
        });

        Schema::table('images', function (Blueprint $table) {
            $table->foreign('image_owner_u_id')->references('id')->on('users')->index('image_owner_u_fk');
        });
    }
};
