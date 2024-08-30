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
        Schema::table('stock_levels', function (Blueprint $table) {
            $table->dropForeign('stock_levels_ibfk_1');
            $table->dropForeign('part_id');
        });

        Schema::table('stock_level_change_history', function (Blueprint $table) {
            $table->dropForeign('stock_level_change_history_ibfk_3');
            $table->dropForeign('stock_level_change_history_ibfk_2');
            $table->dropForeign('stock_level_change_history_ibfk_1');
        });

        Schema::table('parts', function (Blueprint $table) {
            $table->dropForeign('parts_ibfk_3');
            $table->dropForeign('parts_ibfk_2');
            $table->dropForeign('parts_ibfk_5');
            $table->dropForeign('parts_ibfk_1');
        });

        Schema::table('minstock_levels', function (Blueprint $table) {
            $table->dropForeign('minstock_levels_ibfk_1');
            $table->dropForeign('location_id');
        });

        Schema::table('locations', function (Blueprint $table) {
            $table->dropForeign('locations_ibfk_1');
        });

        Schema::table('bom_runs', function (Blueprint $table) {
            $table->dropForeign('bom_runs_ibfk_1');
        });

        Schema::table('bom_elements', function (Blueprint $table) {
            $table->dropForeign('bom_elements_ibfk_2');
            $table->dropForeign('bom_elements_ibfk_1');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('bom_elements', function (Blueprint $table) {
            $table->foreign(['part_id_fk'], 'bom_elements_ibfk_2')->references(['part_id'])->on('parts')->onDelete('CASCADE');
            $table->foreign(['bom_id_fk'], 'bom_elements_ibfk_1')->references(['bom_id'])->on('boms')->onDelete('CASCADE');
        });

        Schema::table('bom_runs', function (Blueprint $table) {
            $table->foreign(['bom_id_fk'], 'bom_runs_ibfk_1')->references(['bom_id'])->on('boms')->onDelete('CASCADE');
        });

        Schema::table('locations', function (Blueprint $table) {
            $table->foreign(['location_owner_g_fk'], 'locations_ibfk_1')->references(['group_id'])->on('user_groups')->onDelete('CASCADE');
        });

        Schema::table('minstock_levels', function (Blueprint $table) {
            $table->foreign(['part_id_fk'], 'minstock_levels_ibfk_1')->references(['part_id'])->on('parts');
            $table->foreign(['location_id_fk'], 'location_id')->references(['location_id'])->on('locations');
        });

        Schema::table('parts', function (Blueprint $table) {
            $table->foreign(['part_footprint_fk'], 'parts_ibfk_3')->references(['footprint_id'])->on('footprints');
            $table->foreign(['part_unit_fk'], 'parts_ibfk_2')->references(['unit_id'])->on('part_units');
            $table->foreign(['part_owner_g_fk'], 'parts_ibfk_5')->references(['group_id'])->on('user_groups');
            $table->foreign(['part_category_fk'], 'parts_ibfk_1')->references(['category_id'])->on('part_categories');
        });

        Schema::table('stock_level_change_history', function (Blueprint $table) {
            $table->foreign(['to_location_fk'], 'stock_level_change_history_ibfk_3')->references(['location_id'])->on('locations')->onDelete('CASCADE');
            $table->foreign(['from_location_fk'], 'stock_level_change_history_ibfk_2')->references(['location_id'])->on('locations')->onDelete('CASCADE');
            $table->foreign(['part_id_fk'], 'stock_level_change_history_ibfk_1')->references(['part_id'])->on('parts')->onDelete('CASCADE');
        });

        Schema::table('stock_levels', function (Blueprint $table) {
            $table->foreign(['location_id_fk'], 'stock_levels_ibfk_1')->references(['location_id'])->on('locations')->onDelete('CASCADE');
            $table->foreign(['part_id_fk'], 'part_id')->references(['part_id'])->on('parts')->onDelete('CASCADE');
        });
    }
};
