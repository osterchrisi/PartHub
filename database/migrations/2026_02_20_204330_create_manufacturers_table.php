<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {

    public function up(): void
    {
        Schema::create('manufacturers', function (Blueprint $table) {
            $table->id('manufacturer_id');

            $table->string('manufacturer_name');
            $table->string('manufacturer_contact_name')->nullable();
            $table->string('manufacturer_email')->nullable();
            $table->string('manufacturer_phone')->nullable();
            $table->string('manufacturer_website')->nullable();
            $table->text('manufacturer_comment')->nullable();

            $table->unsignedBigInteger('manufacturer_owner_u_fk');
            $table->unsignedBigInteger('manufacturer_owner_g_fk')->nullable(); // keep consistency with other tables

            $table->timestamps();

            $table->index('manufacturer_owner_u_fk');
            $table->unique(['manufacturer_owner_u_fk', 'manufacturer_name']); // basic dedupe per user
        });
    }


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('manufacturers');
    }
};
