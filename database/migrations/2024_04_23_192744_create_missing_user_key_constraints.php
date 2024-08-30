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
        Schema::table('boms', function (Blueprint $table) {
            $table->unsignedBigInteger('bom_owner_u_fk')->change();
            $table->foreign(['bom_owner_u_fk'], 'FK_BOMs_BOM_Owner_U_FK_To_Users_ID')->references('id')->on('users')->onDelete('cascade')->onUpdate('cascade');
        });

        Schema::table('bom_runs', function (Blueprint $table) {
            $table->unsignedBigInteger('bom_run_user_fk')->change();
            $table->foreign(['bom_run_user_fk'], 'FK_BOM_Runs_BOM_Run_User_FK_To_Users_ID')->references('id')->on('users')->onDelete('cascade')->onUpdate('cascade');
        });

        Schema::table('footprints', function (Blueprint $table) {
            $table->unsignedBigInteger('footprint_owner_u_fk')->change();
            $table->foreign(['footprint_owner_u_fk'], 'FK_Footprints_Footprint_Owner_U_FK_To_Users_ID')->references('id')->on('users')->onDelete('cascade')->onUpdate('cascade');
        });

        Schema::table('locations', function (Blueprint $table) {
            $table->unsignedBigInteger('location_owner_u_fk')->change();
            $table->foreign(['location_owner_u_fk'], 'FK_Locations_Location_Owner_U_FK_To_Users_ID')->references('id')->on('users')->onDelete('cascade')->onUpdate('cascade');
        });

        Schema::table('parts', function (Blueprint $table) {
            $table->unsignedBigInteger('part_owner_u_fk')->change();
            $table->foreign(['part_owner_u_fk'], 'FK_Parts_Part_Owner_U_FK_To_Users_ID')->references('id')->on('users')->onDelete('cascade')->onUpdate('cascade');
        });

        Schema::table('part_categories', function (Blueprint $table) {
            $table->unsignedBigInteger('part_category_owner_u_fk')->change();
            $table->foreign(['part_category_owner_u_fk'], 'FK_Part_Categories_Part_Category_Owner_U_FK_To_Users_ID')->references('id')->on('users')->onDelete('cascade')->onUpdate('cascade');
        });

        Schema::table('stock_level_change_history', function (Blueprint $table) {
            $table->unsignedBigInteger('stock_lvl_chng_user_fk')->change();
            $table->foreign(['stock_lvl_chng_user_fk'], 'FK_StckLvlChng_Hst_Stock_Lvl_Chng_User_FK_To_Users_ID')->references('id')->on('users')->onDelete('cascade')->onUpdate('cascade');
        });

        Schema::table('user_settings', function (Blueprint $table) {
            $table->unsignedBigInteger('user_id_fk')->change();
            $table->foreign(['user_id_fk'], 'FK_User_Settings_User_ID_FK_To_Users_ID')->references('id')->on('users')->onDelete('cascade')->onUpdate('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('boms', function (Blueprint $table) {
            $table->dropForeign('FK_BOMs_BOM_Owner_U_FK_To_Users_ID');
        });

        Schema::table('bom_runs', function (Blueprint $table) {
            $table->dropForeign('FK_BOM_Runs_BOM_Run_User_FK_To_Users_ID');
        });

        Schema::table('footprints', function (Blueprint $table) {
            $table->dropForeign('FK_Footprints_Footprint_Owner_U_FK_To_Users_ID');
        });

        Schema::table('locations', function (Blueprint $table) {
            $table->dropForeign('FK_Locations_Location_Owner_U_FK_To_Users_ID');
        });

        Schema::table('parts', function (Blueprint $table) {
            $table->dropForeign('FK_Parts_Part_Owner_U_FK_To_Users_ID');
        });

        Schema::table('part_categories', function (Blueprint $table) {
            $table->dropForeign('FK_Part_Categories_Part_Category_Owner_U_FK_To_Users_ID');
        });

        Schema::table('stock_level_change_history', function (Blueprint $table) {
            $table->dropForeign('FK_StckLvlChng_Hst_Stock_Lvl_Chng_User_FK_To_Users_ID');
        });

        Schema::table('user_settings', function (Blueprint $table) {
            $table->dropForeign('FK_User_Settings_User_ID_FK_To_Users_ID');
        });
    }
};
