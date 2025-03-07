<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::table('alternative_group_elements', function (Blueprint $table) {
            $table->renameColumn('part_id', 'alternative_part_id');
        });
    }

    public function down()
    {
        Schema::table('alternative_group_elements', function (Blueprint $table) {
            $table->renameColumn('alternative_part_id', 'part_id'); // Rollback
        });
    }
};
