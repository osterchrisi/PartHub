<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('alternative_parts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('part_id')->constrained('parts', 'part_id')->onDelete('cascade');
            $table->foreignId('alternative_part_id')->constrained('parts', 'part_id')->onDelete('cascade');
            $table->unique(['part_id', 'alternative_part_id']); // Prevent duplicates
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
