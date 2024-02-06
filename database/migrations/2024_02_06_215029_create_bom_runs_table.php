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
        Schema::create('bom_runs', function (Blueprint $table) {
            $table->integer('bom_run_id', true);
            $table->integer('bom_id_fk')->index('bom_runs_ibfk_1');
            $table->integer('bom_run_quantity');
            $table->dateTime('bom_run_datetime')->useCurrent();
            $table->integer('bom_run_user_fk')->index('bom_run_user_fk');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('bom_runs');
    }
};
