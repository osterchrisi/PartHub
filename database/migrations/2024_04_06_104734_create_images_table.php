<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('images', function (Blueprint $table) {
            $table->id();
            $table->string('filename');
            $table->unsignedBigInteger('image_owner_u_id');
            $table->string('type'); // Type of image, e.g. part, location, ...
            $table->timestamps();

            // Define foreign key constraint
            $table->foreign('image_owner_u_id')->references('id')->on('users')->index('image_owner_u_fk');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('images');
    }
};
