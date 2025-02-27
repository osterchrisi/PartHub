<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        // Create alternative_groups table
        Schema::create('alternative_groups', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('owner_u_fk'); // Owner of the group
            $table->timestamps();

            $table->foreign('owner_u_fk')->references('id')->on('users')->onDelete('cascade');
        });

        // Create alternative_group_part table
        Schema::create('alternative_group_elements', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('alternative_group_id');
            $table->integer('part_id')->index();

            $table->foreign('alternative_group_id')->references('id')->on('alternative_groups')->onDelete('cascade');
            $table->foreign('part_id')->references('part_id')->on('parts')->onDelete('cascade');

            $table->unique(['alternative_group_id', 'part_id']); // Prevent duplicates
        });

        // Drop old alternative_parts table
        Schema::dropIfExists('alternative_parts');
    }

    public function down()
    {
        Schema::dropIfExists('alternative_group_elements');
        Schema::dropIfExists('alternative_groups');
    }
};
