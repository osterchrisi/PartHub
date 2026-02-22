<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('manufacturer_aliases', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('manufacturer_id_fk');
            $table->unsignedBigInteger('manufacturer_alias_owner_u_fk');

            $table->string('alias');
            $table->string('alias_normalized');

            $table->timestamps();

            $table->foreign('manufacturer_id_fk')
                ->references('manufacturer_id')
                ->on('manufacturers')
                ->cascadeOnDelete();

            // One normalized alias per user max
            $table->unique(
                ['manufacturer_alias_owner_u_fk', 'alias_normalized'],
                'manufacturer_alias_unique_per_user'
            );
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('manufacturer_aliases');
    }
};
