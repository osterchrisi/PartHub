<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('parts', function (Blueprint $table) {
            $table->integer('part_supplier_fk')->nullable();

            $table->foreign('part_supplier_fk')
                ->references('supplier_id')
                ->on('suppliers')
                ->onDelete('restrict');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('parts', function (Blueprint $table) {
            $table->dropForeign(['part_supplier_fk']);
            $table->dropColumn('part_supplier_fk');
        });
    }
};
