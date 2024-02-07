<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('bom_elements', function (Blueprint $table) {
            $table->integer('bom_elements_id', true);
            $table->integer('bom_id_fk')->index('bom_elements_ibfk_1');
            $table->integer('part_id_fk')->index('bom_elements_ibfk_2');
            $table->integer('element_quantity');
        });

        Schema::create('bom_runs', function (Blueprint $table) {
            $table->integer('bom_run_id', true);
            $table->integer('bom_id_fk')->index('bom_runs_ibfk_1');
            $table->integer('bom_run_quantity');
            $table->dateTime('bom_run_datetime')->useCurrent();
            $table->integer('bom_run_user_fk')->index('bom_run_user_fk');
        });

        Schema::create('boms', function (Blueprint $table) {
            $table->integer('bom_id', true);
            $table->string('bom_name');
            $table->string('bom_description')->nullable();
            $table->integer('bom_owner_g_fk')->nullable();
            $table->integer('bom_owner_u_fk')->index('bom_owner_u_fk');
        });

        Schema::create('failed_jobs', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('uuid')->unique();
            $table->text('connection');
            $table->text('queue');
            $table->longText('payload');
            $table->longText('exception');
            $table->timestamp('failed_at')->useCurrent();
        });

        Schema::create('footprints', function (Blueprint $table) {
            $table->integer('footprint_id', true);
            $table->string('footprint_name');
            $table->string('footprint_alias');
            $table->integer('footprint_owner_u_fk');
        });

        Schema::create('locations', function (Blueprint $table) {
            $table->integer('location_id', true);
            $table->string('location_name');
            $table->string('location_description')->nullable();
            $table->integer('location_owner_u_fk')->index('location_owner_u_fk');
            $table->integer('location_owner_g_fk')->nullable()->index('location_owner_g_fk');
        });

        Schema::create('minstock_levels', function (Blueprint $table) {
            $table->integer('minstock_lvl_id', true);
            $table->integer('part_id_fk')->index('part_id');
            $table->integer('location_id_fk')->index('location_id');
            $table->integer('minstock_level');
        });

        Schema::create('part_categories', function (Blueprint $table) {
            $table->integer('category_id', true);
            $table->string('category_name');
            $table->integer('parent_category')->default(1);
            $table->integer('part_category_owner_u_fk')->index('part_category_owner_u_fk');
        });

        Schema::create('part_units', function (Blueprint $table) {
            $table->integer('unit_id', true);
            $table->string('unit_name');
        });

        Schema::create('parts', function (Blueprint $table) {
            $table->integer('part_id', true);
            $table->string('part_name')->index('part_name');
            $table->string('part_description')->nullable();
            $table->string('part_comment')->nullable();
            $table->timestamp('created_at')->useCurrent();
            $table->integer('part_category_fk')->nullable()->default(1)->index('part_category_fk');
            $table->integer('part_footprint_fk')->nullable()->default(1)->index('part_footprint_fk');
            $table->integer('part_unit_fk')->nullable()->default(1)->index('part_unit_fk');
            $table->integer('part_owner_u_fk')->index('part_owner_u_fk');
            $table->integer('part_owner_g_fk')->nullable()->index('part_owner_g_fk');
        });

        Schema::create('password_reset_tokens', function (Blueprint $table) {
            $table->string('email')->primary();
            $table->string('token');
            $table->timestamp('created_at')->nullable();
        });

        Schema::create('stock_level_change_history', function (Blueprint $table) {
            $table->integer('stock_lvl_chng_id', true);
            $table->integer('part_id_fk')->index('stock_level_change_history_ibfk_1');
            $table->integer('from_location_fk')->nullable()->index('from_location');
            $table->integer('to_location_fk')->nullable()->index('to_location');
            $table->integer('stock_lvl_chng_quantity');
            $table->timestamp('stock_lvl_chng_timestamp')->useCurrent();
            $table->string('stock_lvl_chng_comment')->nullable();
            $table->integer('stock_lvl_chng_user_fk')->index('stock_lvl_chng_user_fk');
        });

        Schema::create('stock_levels', function (Blueprint $table) {
            $table->integer('stock_level_id')->unique('stock_level_id');
            $table->integer('part_id_fk');
            $table->integer('location_id_fk')->index('location_id');
            $table->integer('stock_level_quantity');

            $table->primary(['part_id_fk', 'location_id_fk']);
        });

        Schema::create('user_groups', function (Blueprint $table) {
            $table->integer('group_id', true);
            $table->string('group_name');
        });

        Schema::create('user_settings', function (Blueprint $table) {
            $table->integer('user_settings_id', true);
            $table->integer('user_id_fk')->index('user_id_fk');
            $table->string('setting_name');
            $table->string('setting_value');
        });

        Schema::create('users', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('name');
            $table->string('email')->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            $table->rememberToken();
            $table->timestamps();
        });

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

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
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

        Schema::dropIfExists('users');

        Schema::dropIfExists('user_settings');

        Schema::dropIfExists('user_groups');

        Schema::dropIfExists('stock_levels');

        Schema::dropIfExists('stock_level_change_history');

        Schema::dropIfExists('password_reset_tokens');

        Schema::dropIfExists('parts');

        Schema::dropIfExists('part_units');

        Schema::dropIfExists('part_categories');

        Schema::dropIfExists('minstock_levels');

        Schema::dropIfExists('locations');

        Schema::dropIfExists('footprints');

        Schema::dropIfExists('failed_jobs');

        Schema::dropIfExists('boms');

        Schema::dropIfExists('bom_runs');

        Schema::dropIfExists('bom_elements');
    }
};
