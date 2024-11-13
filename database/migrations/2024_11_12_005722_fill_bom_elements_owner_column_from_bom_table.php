<?php

use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        // Update each bom_element with the corresponding owner from the boms table
        DB::table('bom_elements')
            ->join('boms', 'bom_elements.bom_id_fk', '=', 'boms.bom_id')
            ->update([
                'bom_elements.bom_element_owner_u_fk' => DB::raw('boms.bom_owner_u_fk'),
            ]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // Optionally, you can set bom_element_owner_u_fk back to null or handle reversing differently
        DB::table('bom_elements')
            ->update([
                'bom_elements.bom_element_owner_u_fk' => null,
            ]);
    }
};
