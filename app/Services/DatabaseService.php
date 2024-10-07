<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Exception;

class DatabaseService
{
    // Map of owner columns
    private static $owner_columns = [
        'boms' => 'bom_owner_u_fk',
        'footprints' => 'footprint_owner_u_fk',
        'locations' => 'location_owner_u_fk',
        'parts' => 'part_owner_u_fk',
        'part_categories' => 'part_category_owner_u_fk',
        'stock_level_change_history' => 'stock_lvl_chng_user_fk',
        'suppliers' => 'supplier_owner_u_fk',
        'supplier_data' => 'supplier_data_owner_u_fk',
    ];

    /**
     * Delete a row after verifying ownership
     */
    public static function deleteRow($table, $column, $id)
    {
        // Get the owner column for the specified table
        $owner_column = self::$owner_columns[$table] ?? null;
        if (!$owner_column) {
            throw new Exception("No owner column found for table {$table}");
        }

        // Get the currently authenticated user's ID
        $user_id = Auth::id();

        // Ensure the row belongs to the current user before deleting
        $deleted = DB::table($table)
            ->where($column, $id)
            ->where($owner_column, $user_id)
            ->delete();

        if (!$deleted) {
            throw new Exception("Unauthorized or row not found for deletion");
        }
    }

    /**
     * Update a specific cell in the database
     */
    public static function updateCell($table_name, $id_field, $id, $column, $new_value)
    {
        // Get the owner column for the specified table
        $owner_column = self::$owner_columns[$table_name] ?? null;
        if (!$owner_column) {
            throw new Exception("No owner column found for table {$table_name}");
        }

        // Get the authenticated user's ID
        $user_id = Auth::id();

        // Ensure the row belongs to the current user before updating
        $updated = DB::table($table_name)
            ->where($id_field, $id)
            ->where($owner_column, $user_id)
            ->update([$column => $new_value]);

        if (!$updated) {
            throw new Exception("Unauthorized or row not found for updating");
        }
    }
}
