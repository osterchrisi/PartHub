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
        Schema::table('bom_elements', function (Blueprint $table) {
            $table->unsignedBigInteger('bom_element_owner_u_fk')->nullable();

            $table->foreign('bom_element_owner_u_fk')
                ->references('id')
                ->on('users')
                ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('bom_elements', function (Blueprint $table) {
            $table->dropForeign(['bom_element_owner_u_fk']);
            $table->dropColumn('bom_element_owner_u_fk');
        });
    }
};
