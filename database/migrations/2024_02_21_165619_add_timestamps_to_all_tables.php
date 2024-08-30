<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    protected $tables = [
        'boms',
        'bom_elements',
        'bom_runs',
        'footprints',
        'locations',
        'minstock_levels',
        'parts',
        'part_categories',
        'part_units',
        'stock_levels',
        'stock_level_change_history',
        'user_groups',
        'user_settings',
    ];

    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('all_tables', function (Blueprint $table) {
            foreach ($this->tables as $table) {
                if (! Schema::hasColumn($table, 'created_at')) {
                    Schema::table($table, function (Blueprint $table) {
                        $table->timestamps();
                    });
                }
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('all_tables', function (Blueprint $table) {
            foreach ($this->tables as $table) {
                Schema::table($table, function (Blueprint $table) {
                    $table->dropTimestamps();
                });
            }
        });
    }
};
