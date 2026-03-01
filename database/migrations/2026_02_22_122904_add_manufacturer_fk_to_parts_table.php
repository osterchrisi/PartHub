<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('parts', function (Blueprint $table) {
            if (! Schema::hasColumn('parts', 'part_manufacturer_fk')) {
                $table->unsignedBigInteger('part_manufacturer_fk')->nullable()->after('part_supplier_fk');
            }

            $table->foreign('part_manufacturer_fk')
                ->references('manufacturer_id')
                ->on('manufacturers')
                ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('parts', function (Blueprint $table) {
            // Drop FK first, then column
            try {
                $table->dropForeign(['part_manufacturer_fk']);
            } catch (\Throwable $e) {
            }
            $table->dropColumn('part_manufacturer_fk');
        });
    }
};
