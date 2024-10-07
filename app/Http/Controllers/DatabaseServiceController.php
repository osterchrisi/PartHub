<?php

namespace App\Http\Controllers;

use App\Services\DatabaseService;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class DatabaseServiceController extends Controller
{
    /**
     * Delete a row from the table
     */
    public function deleteRow(Request $request)
    {
        $table = $request->input('table');
        $column = $request->input('column');
        $ids = $request->input('ids');

        try {
            foreach ($ids as $id) {
                DatabaseService::deleteRow($table, $column, $id);
            }

            return response()->json(['message' => 'Rows deleted successfully'], 200);
        } catch (Exception $e) {
            Log::error($e->getMessage());

            return response()->json(['error' => $e->getMessage()], 400);
        }
    }

    /**
     * Update a cell in the table
     */
    public function updateCell(Request $request)
    {
        $table_name = $request->input('table_name');
        $id_field = $request->input('id_field');
        $id = $request->input('id');
        $column = $request->input('column');
        $new_value = $request->input('new_value');

        try {
            DatabaseService::updateCell($table_name, $id_field, $id, $column, $new_value);

            return response()->json(['message' => 'Cell updated successfully'], 200);
        } catch (Exception $e) {
            Log::error($e->getMessage());

            return response()->json(['error' => $e->getMessage()], 400);
        }
    }
}
