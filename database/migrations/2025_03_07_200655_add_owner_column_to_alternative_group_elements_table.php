<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('alternative_group_elements', function (Blueprint $table) {
            $table->unsignedBigInteger('owner_u_fk')->after('part_id')->nullable();
            $table->foreign('owner_u_fk')->references('id')->on('users')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::table('alternative_group_elements', function (Blueprint $table) {
            $table->dropForeign(['owner_u_fk']);
            $table->dropColumn('owner_u_fk');
        });
    }
};
