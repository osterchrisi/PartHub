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
        Schema::create('stock_level_change_history', function (Blueprint $table) {
            $table->integer('stock_lvl_chng_id', true);
            $table->integer('part_id_fk')->index('stock_level_change_history_ibfk_1');
            $table->integer('from_location_fk')->nullable()->index('from_location');
            $table->integer('to_location_fk')->nullable()->index('to_location');
            $table->integer('stock_lvl_chng_quantity');
            $table->timestamp('stock_lvl_chng_timestamp')->useCurrent();
            $table->string('stock_lvl_chng_comment')->nullable();
            $table->integer('stock_lvl_chng_user_fk')->index('stock_lvl_chng_user_fk');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('stock_level_change_history');
    }
};
