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
        Schema::create('locations', function (Blueprint $table) {
            $table->integer('location_id', true);
            $table->string('location_name');
            $table->string('location_description')->nullable();
            $table->integer('location_owner_u_fk')->index('location_owner_u_fk');
            $table->integer('location_owner_g_fk')->nullable()->index('location_owner_g_fk');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('locations');
    }
};
