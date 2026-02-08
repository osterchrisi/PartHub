<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('bom_revisions', function (Blueprint $table) {
            $table->id('bom_revision_id');
            $table->integer('bom_id_fk');
            $table->string('label')->nullable();
            $table->text('note')->nullable();
            $table->unsignedBigInteger('created_by_u_fk')->nullable();
            $table->timestamps();

            $table->foreign('bom_id_fk')->references('bom_id')->on('boms')->onDelete('cascade');
            $table->foreign('created_by_u_fk')->references('id')->on('users')->nullOnDelete();

            $table->index(['bom_id_fk', 'created_at']);
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bom_revisions');
    }
};
