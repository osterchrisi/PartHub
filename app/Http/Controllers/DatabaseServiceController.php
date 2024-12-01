<?php

namespace App\Http\Controllers;

use App\Services\DatabaseService;
use App\Services\Validators\PartValidatorService;
use App\Services\Validators\StockValidatorService;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class DatabaseServiceController extends Controller
{
    protected $partValidator;

    protected $stockValidator;

    public function __construct(StockValidatorService $stockValidatorService, PartValidatorService $partValidatorService)
    {
        $this->partValidator = $partValidatorService;
        $this->stockValidator = $stockValidatorService;
    }

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

    //TODO: I guess what I'll do is to make a mapping for the column_names to the "clear" names in any given rules of any given Validator.
    //TODO: This way the DatabaseServiceController takes care of the incoming request, swaps the column_name and goes on.
    //TODO: For this, I need different update methods inside DSC for any given resource so I have it nicely separated.

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

        $data = [$column => $new_value, 'part_id' => $id];

        $validated = $this->partValidator->validate($data, $request->method());

        //TODO: This line just makes sure that everything that does not yet have a validator also works.
        //TODO:  Failed validation throws an exception so I guess it's 'safe' for now. All remaining data is strings anyway...
        $new_value = $validated[$column] ?? $new_value;

        try {
            // Pass the response from DatabaseService directly to the client
            $response = DatabaseService::updateCell($table_name, $id_field, $id, $column, $new_value);

            return response()->json($response, 200);
        } catch (Exception $e) {
            Log::error($e->getMessage());

            return response()->json(['error' => $e->getMessage()], 400);
        }
    }
}
