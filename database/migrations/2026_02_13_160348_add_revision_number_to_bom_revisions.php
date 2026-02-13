<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('bom_revisions', function (Blueprint $table) {
            $table->unsignedInteger('revision_number')->nullable()->after('bom_revision_id');
        });

        // Backfill: per BOM, order by created_at then bom_revision_id
        $bomIds = DB::table('bom_revisions')->select('bom_id_fk')->distinct()->pluck('bom_id_fk');

        foreach ($bomIds as $bomId) {
            $revs = DB::table('bom_revisions')
                ->where('bom_id_fk', $bomId)
                ->orderBy('created_at')
                ->orderBy('bom_revision_id')
                ->pluck('bom_revision_id');

            $n = 1;
            foreach ($revs as $revId) {
                DB::table('bom_revisions')
                    ->where('bom_revision_id', $revId)
                    ->update(['revision_number' => $n++]);
            }
        }

        Schema::table('bom_revisions', function (Blueprint $table) {
            $table->unsignedInteger('revision_number')->nullable(false)->change();
            $table->unique(['bom_id_fk', 'revision_number'], 'bom_revisions_bom_revnum_unique');
        });
    }

    public function down(): void
    {
        Schema::table('bom_revisions', function (Blueprint $table) {
            $table->dropUnique('bom_revisions_bom_revnum_unique');
            $table->dropColumn('revision_number');
        });
    }

};
