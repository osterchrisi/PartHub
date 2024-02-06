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
        Schema::create('boms', function (Blueprint $table) {
            $table->integer('bom_id', true);
            $table->string('bom_name');
            $table->string('bom_description')->nullable();
            $table->integer('bom_owner_g_fk')->nullable();
            $table->integer('bom_owner_u_fk')->index('bom_owner_u_fk');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('boms');
    }
};
