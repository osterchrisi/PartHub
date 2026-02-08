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
        Schema::table('boms', function (Blueprint $table) {
            $table->unsignedBigInteger('current_revision_id')->nullable()->after('bom_description');

            $table->foreign('current_revision_id')
                ->references('bom_revision_id')
                ->on('bom_revisions')
                ->nullOnDelete();
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('boms', function (Blueprint $table) {
            $table->dropForeign(['current_revision_id']);
            $table->dropColumn('current_revision_id');
        });
    }
};
