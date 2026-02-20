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
        Schema::create('bom_revision_elements', function (Blueprint $table) {
            $table->id('bom_revision_element_id');
            $table->unsignedBigInteger('bom_revision_id_fk');
            $table->integer('part_id_fk');
            $table->unsignedInteger('quantity');
            $table->text('refdes')->nullable();
            $table->uuid('line_uuid')->nullable();
            $table->timestamps();

            $table->foreign('bom_revision_id_fk')->references('bom_revision_id')->on('bom_revisions')->onDelete('cascade');
            $table->foreign('part_id_fk')->references('part_id')->on('parts')->restrictOnDelete();

            $table->index(['bom_revision_id_fk']);
            $table->index(['part_id_fk']);
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bom_revision_elements');
    }
};
