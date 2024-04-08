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
        Schema::create('part_meta', function (Blueprint $table) {
            $table->integer('meta_id', true);
            $table->string('meta_key');
            $table->string('meta_description');
            $table->string('meta_value');
            $table->integer('part_id_fk');
            $table->unsignedBigInteger('meta_owner_u_fk');
            $table->timestamps();

            $table->foreign('part_id_fk')->references('part_id')->on('parts')->index('part_id_fk');
            $table->foreign('meta_owner_u_fk')->references('id')->on('users')->index('meta_owner_u_fk');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('part_meta');
    }
};
