<?php

namespace App\Services;

use App\Models\Part;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class DatabaseService
{
    // Map of owner columns
    private static $owner_columns = [
        'boms' => 'bom_owner_u_fk',
        'bom_elements' => 'bom_element_owner_u_fk',
        'footprints' => 'footprint_owner_u_fk',
        'locations' => 'location_owner_u_fk',
        'parts' => 'part_owner_u_fk',
        'part_categories' => 'part_category_owner_u_fk',
        'stock_level_change_history' => 'stock_lvl_chng_user_fk',
        'suppliers' => 'supplier_owner_u_fk',
        'supplier_data' => 'supplier_data_owner_u_fk',
        'alternative_group_elements' => 'owner_u_fk',
    ];

    // Map of table names to Eloquent models
    private static $modelMap = [
        'boms' => \App\Models\Bom::class,
        'bom_elements' => \App\Models\BomElements::class,
        'footprints' => \App\Models\Footprint::class,
        'locations' => \App\Models\Location::class,
        'parts' => \App\Models\Part::class,
        'part_categories' => \App\Models\Category::class,
        'stock_level_change_history' => \App\Models\StockLevelHistory::class,
        'suppliers' => \App\Models\Supplier::class,
        'supplier_data' => \App\Models\SupplierData::class,
        'alternative_group_elements' => \App\Models\AlternativeGroupElement::class,
    ];

    /**
     * Delete a row after verifying ownership
     */
    public static function deleteRow($table, $column, $id)
    {
        // Start the database transaction
        DB::beginTransaction();

        try {
            // Get the owner column for the specified table
            $owner_column = self::$owner_columns[$table] ?? null;
            if (! $owner_column) {
                throw new Exception("No owner column found for table {$table}");
            }

            // Get the currently authenticated user's ID
            $user_id = Auth::id();

            // If a category gets deleted, gather parts with the category before deletion
            $partsToUpdate = [];
            if ($table === 'part_categories') {
                $partsToUpdate = Part::where('part_category_fk', $id)->pluck('part_id')->toArray();
            }

            // Ensure the row belongs to the current user before deleting
            $deleted = DB::table($table)
                ->where($column, $id)
                ->where($owner_column, $user_id)
                ->delete();

            if (! $deleted) {
                throw new Exception('Unauthorized or row not found for deletion');
            }

            // Additional logic if the deleted row is a category
            if ($table === 'part_categories' && ! empty($partsToUpdate)) {
                // Get the root category for the user
                $root_category = app()->make('App\Services\CategoryService')->findRootCategory($user_id)->category_id ?? null;

                if ($root_category) {
                    // Update all parts that were using the deleted category to assign them to the root category
                    Part::whereIn('part_id', $partsToUpdate)->update(['part_category_fk' => $root_category]);
                }
            }

            // Commit the transaction if all operations succeeded
            DB::commit();
        } catch (Exception $e) {
            // Rollback the transaction in case of error
            DB::rollBack();

            // Re-throw the exception to handle it elsewhere or log it
            throw $e;
        }
    }

    /**
     * Update a specific cell in the database
     */
    public static function updateCell($table_name, $id_field, $id, $column, $new_value)
    {
        if (! isset(self::$modelMap[$table_name])) {
            throw new Exception("No Eloquent model found for table {$table_name}");
        }

        $modelClass = self::$modelMap[$table_name];
        $owner_column = self::$owner_columns[$table_name] ?? null;

        if (! $owner_column) {
            throw new Exception("No owner column found for table {$table_name}");
        }

        $user_id = Auth::id();

        try {
            // Find the record
            $record = $modelClass::where($id_field, $id)
                ->where($owner_column, $user_id)
                ->first();

            if (! $record) {
                throw new Exception('Unauthorized or row not found for updating');
            }

            // Check if the new value is different
            if ($new_value === $record->$column) {
                return ['message' => 'No changes were made.'];
            }

            // Store old value for logging
            $old_value = $record->$column;

            // Update the column (Eloquent automatically updates updated_at)
            $record->$column = $new_value;
            $record->save();

            return ['message' => 'Cell updated successfully.'];
        } catch (Exception $e) {
            \Log::warning('Update failed', [
                'table_name' => $table_name,
                'id_field' => $id_field,
                'id' => $id,
                'column' => $column,
                'new_value' => $new_value,
                'old_value' => $old_value ?? 'N/A',
                'user_id' => $user_id,
                'owner_column' => $owner_column,
                'error_message' => $e->getMessage(),
            ]);

            throw $e;
        }
    }
}
