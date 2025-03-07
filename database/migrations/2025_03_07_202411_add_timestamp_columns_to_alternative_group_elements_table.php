<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('alternative_group_elements', function (Blueprint $table) {
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::table('alternative_group_elements', function (Blueprint $table) {
            $table->dropTimestamps();
        });
    }
};
