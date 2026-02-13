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
        Schema::table('bom_revisions', function (Blueprint $table) {
            $table->unsignedInteger('revision_number')->after('bom_id_fk');
            $table->unique(['bom_id_fk', 'revision_number'], 'bom_revisions_bom_revnum_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('bom_revisions', function (Blueprint $table) {
            $table->dropUnique('bom_revisions_bom_revnum_unique');
            $table->dropColumn('revision_number');
        });
    }
};
