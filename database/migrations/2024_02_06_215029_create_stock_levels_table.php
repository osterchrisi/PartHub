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
        Schema::create('stock_levels', function (Blueprint $table) {
            $table->integer('stock_level_id', true)->unique('stock_level_id');
            $table->integer('part_id_fk');
            $table->integer('location_id_fk')->index('location_id');
            $table->integer('stock_level_quantity');

            $table->primary(['part_id_fk', 'location_id_fk']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('stock_levels');
    }
};
