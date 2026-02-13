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
        Schema::table('bom_runs', function (Blueprint $table) {
            $table->unsignedBigInteger('bom_revision_id_fk')->nullable()->after('bom_id_fk');

            $table->foreign('bom_revision_id_fk')
                ->references('bom_revision_id')
                ->on('bom_revisions')
                ->nullOnDelete();

            $table->index(['bom_id_fk', 'bom_revision_id_fk']);
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('bom_runs', function (Blueprint $table) {
            $table->dropForeign(['bom_revision_id_fk']);
            $table->dropIndex(['bom_id_fk', 'bom_revision_id_fk']);
            $table->dropColumn('bom_revision_id_fk');
        });
    }
};
