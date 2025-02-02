<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::table('alternative_parts', function (Blueprint $table) {
            $table->unsignedBigInteger('alternative_parts_owner_u_fk')->after('alternative_part_id');

            // Assuming your users table has an 'id' column
            $table->foreign('alternative_parts_owner_u_fk')
                ->references('id')->on('users')
                ->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::table('alternative_parts', function (Blueprint $table) {
            $table->dropForeign(['alternative_parts_owner_u_fk']);
            $table->dropColumn('alternative_parts_owner_u_fk');
        });
    }
};
