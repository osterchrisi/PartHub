<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('alternative_parts', function (Blueprint $table) {
            $table->id();  // Laravel's default auto-increment BIGINT primary key

            $table->integer('part_id');
            $table->integer('alternative_part_id');

            $table->foreign('part_id')->references('part_id')->on('parts');
            $table->foreign('alternative_part_id')->references('part_id')->on('parts');

            $table->unique(['part_id', 'alternative_part_id']);

            $table->timestamps();
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::dropIfExists('alternative_parts');
    }
};
