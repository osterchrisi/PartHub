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
            $table->foreignId('part_id_fk')->constrained(
                table: 'parts',
                indexName: '_id'
            )->index('part_id_fk');
            $table->foreignId('meta_owner_u_fk')->constrained(
                table: 'users',
                indexName: '_id'
            )->index('meta_owner_u_fk');
            $table->timestamps();
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
