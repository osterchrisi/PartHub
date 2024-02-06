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
        Schema::create('bom_elements', function (Blueprint $table) {
            $table->integer('bom_elements_id', true);
            $table->integer('bom_id_fk')->index('bom_elements_ibfk_1');
            $table->integer('part_id_fk')->index('bom_elements_ibfk_2');
            $table->integer('element_quantity');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('bom_elements');
    }
};
