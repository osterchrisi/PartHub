<?php

namespace App\Services;

use App\Models\Part;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;


class DatabaseService
{
    // Map of owner columns
    private static $owner_columns = [
        'boms' => 'bom_owner_u_fk',
        'bom_elements_id' =>  'bom_element_owner_u_fk',
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
        // Start the database transaction
        DB::beginTransaction();

        try {
            // Get the owner column for the specified table
            $owner_column = self::$owner_columns[$table] ?? null;
            if (!$owner_column) {
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

            if (!$deleted) {
                throw new Exception('Unauthorized or row not found for deletion');
            }

            // Additional logic if the deleted row is a category
            if ($table === 'part_categories' && !empty($partsToUpdate)) {
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
        $rules = self::getValidationRulesForTable($table_name, $column);

        // If validation rules exist for the column, validate before updating
        if ($rules) {
            $validator = Validator::make([$column => $new_value], $rules);
            if ($validator->fails()) {
                throw new Exception("Validation failed: " . $validator->errors()->first());
            }
        }

        $owner_column = self::$owner_columns[$table_name] ?? null;
        if (!$owner_column) {
            throw new Exception("No owner column found for table {$table_name}");
        }

        $user_id = Auth::id();

        // Check if the new value is different from the current one
        $currentValue = DB::table($table_name)
            ->where($id_field, $id)
            ->where($owner_column, $user_id)
            ->value($column);

        if ($currentValue === $new_value) {
            return ['message' => 'No changes were made.'];
        }

        // Proceed with the update only if the value is different
        $updated = DB::table($table_name)
            ->where($id_field, $id)
            ->where($owner_column, $user_id)
            ->update([$column => $new_value]);

        if (!$updated) {
            throw new Exception('Unauthorized or row not found for updating');
        }
        return ['message' => 'Cell updated successfully.'];
    }


    /**
     * Dynamically retrieve validation rules for the specified table and column.
     */
    protected static function getValidationRulesForTable($table_name, $column)
    {
        $requestClass = "App\\Http\\Requests\\" . ucfirst(Str::camel($table_name)) . "Request";

        if (class_exists($requestClass)) {
            \Log::info("I found rules omg!");
            $requestInstance = new $requestClass;
            return [$column => $requestInstance->rules()[$column] ?? []];
        }

        return [];
    }
}
