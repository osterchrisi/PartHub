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
        Schema::create('minstock_levels', function (Blueprint $table) {
            $table->integer('minstock_lvl_id', true);
            $table->integer('part_id_fk')->index('part_id');
            $table->integer('location_id_fk')->index('location_id');
            $table->integer('minstock_level');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('minstock_levels');
    }
};
