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
        Schema::create('parts', function (Blueprint $table) {
            $table->integer('part_id', true);
            $table->string('part_name')->index('part_name');
            $table->string('part_description')->nullable();
            $table->string('part_comment')->nullable();
            $table->timestamp('created_at')->useCurrent();
            $table->integer('part_category_fk')->nullable()->default(1)->index('part_category_fk');
            $table->integer('part_footprint_fk')->nullable()->default(1)->index('part_footprint_fk');
            $table->integer('part_unit_fk')->nullable()->default(1)->index('part_unit_fk');
            $table->integer('part_owner_u_fk')->index('part_owner_u_fk');
            $table->integer('part_owner_g_fk')->nullable()->index('part_owner_g_fk');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('parts');
    }
};
