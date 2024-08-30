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

        Schema::table('bom_elements', function (Blueprint $table) {
            $table->foreign(['part_id_fk'], 'FK_BOM_Elements_Part_ID_To_Parts_Part_ID')->references(['part_id'])->on('parts')->onDelete('cascade')->onUpdate('cascade');
            $table->foreign(['bom_id_fk'], 'FK_BOM_Elements_BOM_ID_To_BOMs_BOM_ID')->references(['bom_id'])->on('boms')->onDelete('cascade')->onUpdate('cascade');
        });

        Schema::table('bom_runs', function (Blueprint $table) {
            $table->foreign(['bom_id_fk'], 'FK_BOM_Runs_BOM_ID_FK_To_BOMs_BOM_ID')->references(['bom_id'])->on('boms')->onDelete('cascade')->onUpdate('cascade');
        });

        Schema::table('locations', function (Blueprint $table) {
            $table->foreign(['location_owner_g_fk'], 'FK_Locations_Location_Owner_G_FK_To_User_Groups_Group_ID')->references(['group_id'])->on('user_groups')->onDelete('set null')->onUpdate('cascade');
        });

        Schema::table('minstock_levels', function (Blueprint $table) {
            $table->foreign(['part_id_fk'], 'FK_Minstock_Levels_Part_ID_FK_To_Parts_Part_ID')->references(['part_id'])->on('parts')->onDelete('cascade')->onUpdate('cascade');
            $table->foreign(['location_id_fk'], 'FK_Minstock_Levels_Location_ID_FK_To_Locations_Location_ID')->references(['location_id'])->on('locations')->onDelete('cascade')->onUpdate('cascade');
        });

        Schema::table('parts', function (Blueprint $table) {
            $table->foreign(['part_footprint_fk'], 'FK_Parts_Part_Footprint_FK_To_Footprints_Footprint_ID')->references(['footprint_id'])->on('footprints')->onDelete('set null')->onUpdate('cascade');
            $table->foreign(['part_unit_fk'], 'FK_Parts_Part_Unit_FK_To_Part_Units_Unit_ID')->references(['unit_id'])->on('part_units')->onDelete('set null')->onUpdate('cascade');
            $table->foreign(['part_owner_g_fk'], 'FK_Parts_Part_Owner_G_FK_To_User_Groups_Group_ID')->references(['group_id'])->on('user_groups')->onDelete('set null')->onUpdate('cascade');
            $table->foreign(['part_category_fk'], 'FK_Parts_Part_Category_FK_To_Part_Categories_Category_ID')->references(['category_id'])->on('part_categories')->onDelete('set null')->onUpdate('cascade');
            $table->foreign(['part_supplier_fk'], 'FK_Parts_Part_Supplier_FK_To_Suppliers_Supplier_ID')->references('supplier_id')->on('suppliers')->onDelete('set null')->onUpdate('cascade');
        });

        Schema::table('stock_level_change_history', function (Blueprint $table) {
            $table->foreign(['to_location_fk'], 'FK_StckLvlChng_Hst_To_Location_FK_To_Locations_Location_ID')->references(['location_id'])->on('locations')->onDelete('cascade')->onUpdate('cascade');
            $table->foreign(['from_location_fk'], 'FK_StckLvlChng_Hst_From_Location_FK_To_Locations_Location_ID')->references(['location_id'])->on('locations')->onDelete('cascade')->onUpdate('cascade');
            $table->foreign(['part_id_fk'], 'FK_StckLvlChng_Hst_Part_ID_FK_To_Parts_Part_ID')->references(['part_id'])->on('parts')->onDelete('cascade')->onUpdate('cascade');
        });

        Schema::table('stock_levels', function (Blueprint $table) {
            $table->foreign(['location_id_fk'], 'FK_Stock_Levels_Location_ID_FK_To_Locations_Location_ID')->references(['location_id'])->on('locations')->onDelete('cascade')->onUpdate('cascade');
            $table->foreign(['part_id_fk'], 'FK_Stock_Levels_Part_ID_FK_To_Parts_Part_ID')->references(['part_id'])->on('parts')->onDelete('cascade')->onUpdate('cascade');
        });

        Schema::table('suppliers', function (Blueprint $table) {
            $table->foreign(['supplier_owner_u_fk'], 'FK_Suppliers_Supplier_Owner_U_FK_To_Users_ID')->references('id')->on('users')->onDelete('cascade')->onUpdate('cascade');
        });

        Schema::table('part_meta', function (Blueprint $table) {
            $table->foreign(['part_id_fk'], 'FK_Part_Meta_Part_ID_FK_To_Parts_Part_ID')->references('part_id')->on('parts')->onDelete('cascade')->onUpdate('cascade');
            $table->foreign(['meta_owner_u_fk'], 'FK_Part_Meta_Meta_Owner_U_FK_To_Users_ID')->references('id')->on('users')->onDelete('cascade')->onUpdate('cascade');
        });

        Schema::table('images', function (Blueprint $table) {
            $table->foreign(['image_owner_u_id'], 'FK_Images_Image_Owner_U_ID_To_Users_ID')->references('id')->on('users')->onDelete('cascade')->onUpdate('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {

        Schema::table('bom_elements', function (Blueprint $table) {
            $table->dropForeign('FK_BOM_Elements_Part_ID_To_Parts_Part_ID');
            $table->dropForeign('FK_BOM_Elements_BOM_ID_To_BOMs_BOM_ID');
        });

        Schema::table('bom_runs', function (Blueprint $table) {
            $table->dropForeign('FK_BOM_Runs_BOM_ID_FK_To_BOMs_BOM_ID');
        });

        Schema::table('locations', function (Blueprint $table) {
            $table->dropForeign('FK_Locations_Location_Owner_G_FK_To_User_Groups_Group_ID');
        });

        Schema::table('minstock_levels', function (Blueprint $table) {
            $table->dropForeign('FK_Minstock_Levels_Part_ID_FK_To_Parts_Part_ID');
            $table->dropForeign('FK_Minstock_Levels_Location_ID_FK_To_Locations_Location_ID');
        });

        Schema::table('parts', function (Blueprint $table) {
            $table->dropForeign('FK_Parts_Part_Footprint_FK_To_Footprints_Footprint_ID');
            $table->dropForeign('FK_Parts_Part_Unit_FK_To_Part_Units_Unit_ID');
            $table->dropForeign('FK_Parts_Part_Owner_G_FK_To_User_Groups_Group_ID');
            $table->dropForeign('FK_Parts_Part_Category_FK_To_Part_Categories_Category_ID');
            $table->dropForeign('FK_Parts_Part_Supplier_FK_To_Suppliers_Supplier_ID');
        });

        Schema::table('stock_level_change_history', function (Blueprint $table) {
            $table->dropForeign('FK_StckLvlChng_Hst_To_Location_FK_To_Locations_Location_ID');
            $table->dropForeign('FK_StckLvlChng_Hst_From_Location_FK_To_Locations_Location_ID');
            $table->dropForeign('FK_StckLvlChng_Hst_Part_ID_FK_To_Parts_Part_ID');
        });

        Schema::table('stock_levels', function (Blueprint $table) {
            $table->dropForeign('FK_Stock_Levels_Location_ID_FK_To_Locations_Location_ID');
            $table->dropForeign('FK_Stock_Levels_Part_ID_FK_To_Parts_Part_ID');
        });

        Schema::table('suppliers', function (Blueprint $table) {
            $table->dropForeign('FK_Suppliers_Supplier_Owner_U_FK_To_Users_ID');
        });

        Schema::table('part_meta', function (Blueprint $table) {
            $table->dropForeign('FK_Part_Meta_Part_ID_FK_To_Parts_Part_ID');
            $table->dropForeign('FK_Part_Meta_Meta_Owner_U_FK_To_Users_ID');
        });

        Schema::table('images', function (Blueprint $table) {
            $table->dropForeign('FK_Images_Image_Owner_U_ID_To_Users_ID');
        });
    }
};
