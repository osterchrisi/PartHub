<?php

namespace App\Http\Controllers;

use App\Events\StockMovementOccured;
use App\Models\Category;
use App\Models\Location;
use App\Models\Part;
use App\Models\StockLevel;
use App\Models\StockLevelHistory;
use App\Models\BomRun;
use App\Models\Footprint;
use App\Models\Supplier;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Services\DatabaseService;
use App\Services\CategoryService;
use App\Services\StockService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;

class PartController extends Controller
{
    private static $table_name = 'parts';
    private static $id_field = 'part_id';
    private static $db_columns = ['state', 'part_name', 'part_description', 'part_comment', 'category_name', 'total_stock', 'footprint_name', 'supplier_name', 'unit_name', 'part_id'];
    // 'state' doesn't contain data but is necessary for boostrapTable's selected row to work
    private static $nice_columns = ['Name', 'Description', 'Comment', 'Category', 'Total Stock', 'Footprint', 'Supplier', 'Unit', "ID"];

    protected $categoryService;
    protected $databaseService;
    protected $stockService;


    public function __construct(CategoryService $categoryService, DatabaseService $databaseService, StockService $stockService)
    {
        $this->categoryService = $categoryService;
        $this->databaseService = $databaseService;
        $this->stockService = $stockService;
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $search_column = 'everywhere';
        $search_term = request()->has('search') ? request()->input('search') : '';
        $column_names = Part::getColumnNames();
        $user_id = Auth::user()->id;

        $search_category = request()->has('cat') ? request()->input('cat') : ['all'];
        $search_category = $this->categoryService->extractCategoryIds($search_category);

        $parts = Part::queryParts($search_column, $search_term, $column_names, $search_category, $user_id);

        $categories = Category::availableCategories('array'); // Request as array of arrays - these are used for displaying / chosing in the parts table
        $categoriesForCategoriesTable = Category::where('part_category_owner_u_fk', $user_id)->with('children')->get(); // These are used for the categories tree table in the left side of parts view
        $footprints = Footprint::availableFootprints();
        $suppliers = Supplier::availableSuppliers();

        /* Calculate and append each part's total stock
        / Passing a reference, so modifications made to $part directly affect 
        / the corresponding element in the original $parts array.
        */
        foreach ($parts as &$part) {
            $totalStock = $this->stockService->calculateTotalStock($part['stock_levels']);
            $part['total_stock'] = $totalStock;
        }

        // Return full parts view or only parts table depending on route
        $route = $request->route()->getName();
        if ($route == 'parts') {
            return view('parts.parts', [
                'title' => 'Parts',
                'view' => 'parts',
                'parts' => $parts,
                'db_columns' => self::$db_columns,
                'nice_columns' => self::$nice_columns,
                'table_name' => self::$table_name,
                'id_field' => self::$id_field,
                'search_term' => $search_term,
                'search_column' => $search_column,
                'search_category' => $search_category,
                'categoriesForCategoriesTable' => $categoriesForCategoriesTable,
                // These are sent to extract clear names from foreign keys for the dropdown menus in the table
                'categories' => $categories,
                'footprints' => $footprints,
                'suppliers' => $suppliers
            ]);
        }
        elseif ($route == 'parts.partsTable') {
            return view('parts.partsTable', [
                'parts' => $parts,
                'db_columns' => self::$db_columns,
                'nice_columns' => self::$nice_columns,
                'table_name' => self::$table_name,
                'id_field' => self::$id_field,
                'categories' => $categories
            ]);
        }
    }

    /**
     * Create a new part in the database including stock level record and stock level history record
     */
    public function create(Request $request)
    {
        $part_name = $request->input('part_name');
        $quantity = $request->input('quantity', 0);
        $to_location = $request->input('to_location');
        $comment = $request->input('comment', NULL);
        $description = $request->input('description', NULL);
        $footprint = $request->input('footprint', NULL);
        $category = $request->input('category', NULL);
        $supplier = $request->input('supplier', NULL);
        $min_quantity = $request->input('min_quantity') ?? 0;       // Total Stock Minimum Quantity
        $user_id = Auth::user()->id;

        try {

            // Begin SQL transaction
            DB::beginTransaction();

            // Insert new part
            $new_part_id = Part::createPart($part_name, $comment, $description, $footprint, $category, $supplier, $min_quantity);
            // Create a stock level entry
            $new_stock_entry_id = StockLevel::createStockLevelRecord($new_part_id, $to_location, $quantity);
            // Create a stock level history entry (from_location is NULL)
            $new_stock_level_hist_id = StockLevelHistory::createStockLevelHistoryRecord($new_part_id, NULL, $to_location, $quantity, $comment, $user_id);

            // Persist database changes and set success flash message
            DB::commit();
            //TODO: Should I flash something here?
            // Session::flash('success', 'BOM "' . $bom_name . '" imported successfully.');

            $user = Auth::user();
            $stock_level = [$new_part_id, $quantity, $to_location];
            event(new StockMovementOccured($stock_level, $user));

            $response = [
                'Part ID' => $new_part_id,
                'Stock Entry ID' => $new_stock_entry_id,
                'Stock Level History ID' => $new_stock_level_hist_id
            ];
            return response()->json($response);

        } catch (\Exception $e) {
            // Roll back database changes made so far
            DB::rollback();

            // Set error flash message
            //TODO: Should I flash something here?
            // Session::flash('error', 'Error importing BOM: ' . $e->getMessage());
        }
    }

    /**
     * Display part details and return the showPart view
     */
    public function show(string $part_id)
    {
        // Fetch the part with its related stock levels
        $part = Part::with('stockLevels.location')->find($part_id)->toArray();

        // Check if request is authorized
        if (Auth::user()->id === $part['part_owner_u_fk']) {
            // Calculate total stock level
            $total_stock = $this->stockService->calculateTotalStock($part['stock_levels']);

            // Return view
            return view(
                'parts.showPart',
                [
                    'part' => $part,
                    // Stock Table
                    'total_stock' => $total_stock,
                    'column_names' => array('location_name', 'stock_level_quantity'),
                    'nice_columns' => array('Location', 'Quantity'),
                    'stock_levels' => $part['stock_levels'],
                    //Bom Table
                    'bomTableHeaders' => array('bom_name', 'element_quantity', 'bom_description'),
                    'nice_bomTableHeaders' => array('BOM', 'Quantity', 'BOM Description'),
                    'bom_list' => Part::getBomsContainingPart($part_id),
                    // Stock History Table
                    'stockHistoryTableHeaders' => array('stock_lvl_chng_timestamp', 'stock_lvl_chng_quantity', 'from_location_name', 'to_location_name', 'stock_lvl_chng_comment', 'user_name'),
                    'nice_stockHistoryTableHeaders' => array('Date', 'Quantity', 'From', 'To', 'Comment', 'User'),
                    'stock_history' => StockLevelHistory::getPartStockHistory($part_id),
                    // Tabs Settings
                    'tabId1' => 'info',
                    'tabText1' => 'Info',
                    'tabToggleId1' => 'partInfo',
                    'tabId2' => 'history',
                    'tabText2' => 'Stock History',
                    'tabToggleId2' => 'partHistory'
                ]
            );
        }
        else {
            abort(403, 'Unauthorized access.'); // Return a 403 Forbidden status with an error message
        }
    }


    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request)
    {
        $table = $request->input('table');
        $column = $request->input('column');
        $ids = $request->input('ids');

        foreach ($ids as $id) {
            $this->databaseService->deleteRow($table, $column, $id);
            echo json_encode(array($ids, $table, $column));
        }
    }

    /**
     * Get the part name for a given part ID.
     * Used in the stock changing modal.
     *
     * @param Request $request
     * @return string
     */
    public function getName(Request $request)
    {
        // Get part ID
        $part_id = request()->input('part_id');

        // Fetch the part 
        $part = Part::find($part_id)->toArray();

        // Return part name only
        return $part['part_name'];
    }

    /**
     * Not yet documented after huge refactoring...
     */
    public function prepareStockChanges(Request $request)
    {
        // Access stock changes to prepare
        $requested_changes = $request->all()['stock_changes'];

        // Extract type of change from the first entry in the array (all entries have same type)
        $change = $requested_changes[0]['change'];

        // Initialize arrays
        $changes = [];
        $negative_stock = [];

        //* Fill above empty arrays with all requested changes, each $requested_change entry holds one part and its changes
        foreach ($requested_changes as $requested_change) {

            // Extract variables from request
            $requested_change_details = $this->stockService->parseRequestedChangeDetails($requested_change);

            // Get relevant stock levels for currently iterated part
            $requested_change_stock_levels = $this->stockService->getRelevantStockLevelsForChange($requested_change_details);

            // Collect changes to be made
            $result = $this->stockService->prepareStockChangesArrays($requested_change_details, $requested_change_stock_levels, $negative_stock);

            // Append array of collected changes to the main arrays
            $changes[] = $result['changes'];
            if (array_key_exists('negative_stock', $result)) {
                $negative_stock[] = $result['negative_stock'];
            }
        }

        //* Stock shortage (i.e. entries in the negative_stock array), inform user and exit
        if (!empty($negative_stock)) {
            $this->stockService->generateStockShortageResponse($negative_stock, $changes, $change);
            exit;
            // return response()->json($response);
        }
        //* To location and From location are the same - not using it right now as I take care of it directly in the front end
        // else if ($requested_change['to_location'] == $requested_change['from_location']) {
        //     dd($requested_changes, $change);
        // }
        //* No user permission necessary
        else {
            $result = $this->stockService->processApprovedChanges($changes);
            return response()->json($result);
            // echo json_encode(['status' => 'success', 'result' => $result]);
        }
    }

    // private function parseRequestedChangeDetails($requested_change)
    // {
    //     $change = $requested_change['change'];

    //     $part_id = $requested_change['part_id'];
    //     $quantity = $requested_change['quantity'];
    //     $comment = $requested_change['comment'];

    //     $to_location = $requested_change['to_location'];
    //     $from_location = $requested_change['from_location'];

    //     $status = $requested_change['status'] ?? NULL;
    //     $bom_id = $requested_change['bom_id'] ?? NULL;
    //     $assemble_quantity = $requested_change['assemble_quantity'] ?? NULL;

    //     return array(
    //         'change' => $change,
    //         'bom_id' => $bom_id,
    //         'assemble_quantity' => $assemble_quantity,
    //         'part_id' => $part_id,
    //         'quantity' => $quantity,
    //         'to_location' => $to_location,
    //         'from_location' => $from_location,
    //         'comment' => $comment,
    //         'status' => $status
    //     );
    // }

    // private function getRelevantStockLevelsForChange($requested_change_details)
    // {
    //     // Retrieve all stock levels for the given part from the database
    //     $stock_levels = StockLevel::getStockLevelsByPartID($requested_change_details['part_id']);

    //     // Get the relevant stock leves for the requested change
    //     $current_stock_level_to = StockLevel::getStockInLocation($stock_levels, $requested_change_details['to_location']);
    //     $current_stock_level_from = StockLevel::getStockInLocation($stock_levels, $requested_change_details['from_location']);

    //     return array(
    //         'current_stock_level_to' => $current_stock_level_to,
    //         'current_stock_level_from' => $current_stock_level_from
    //     );
    // }

    // /**
    //  * Calculates resulting stock levels from requested stock changes. If stock level would go negative, set status 'permission_required',
    //  * otherwise set status 'gtg' (good to go).
    //  *
    //  * @param array $requested_change_details The array that came back from parseRequestedChangeDetails, containing the requested change for a part / stock level
    //  * @param array $requested_change_stock_levels The array that came back from getRelevantStockLevelsForChange, holding current stock levels in to and from locations
    //  * @param array $negative_stock An empty array to be populated with details of a change resulting in negative stock
    //  * @return array
    //  */
    // private function prepareStockChangesArrays($requested_change_details, $requested_change_stock_levels, $negative_stock)
    // {
    //     $changes = $requested_change_details;
    //     $change = $requested_change_details['change'];

    //     //* Add Stock
    //     if ($change == 1) {
    //         $new_quantity = $requested_change_stock_levels['current_stock_level_to'] + $requested_change_details['quantity'];
    //         $changes['new_quantity'] = $new_quantity;
    //         $status = 'gtg';
    //     }

    //     //* Reduce Stock
    //     elseif ($change == -1) {
    //         $new_quantity = $requested_change_stock_levels['current_stock_level_from'] - $requested_change_details['quantity'];
    //         $changes['new_quantity'] = $new_quantity;

    //         // Stock would go negative and this change is not approved yet
    //         if ($new_quantity < 0 && $requested_change_details['status'] != 'gtg') {
    //             $status = 'permission_required';
    //         }
    //         else {
    //             // Need to explicitly assign it because in the primary request it comes as NULL
    //             $status = 'gtg';
    //         }
    //     }

    //     //* Move Stock
    //     if ($change == 0) {
    //         // New quantity in 'to location'
    //         $to_quantity = $requested_change_stock_levels['current_stock_level_to'] + $requested_change_details['quantity'];
    //         // New quantity in 'from location'
    //         $from_quantity = $requested_change_stock_levels['current_stock_level_from'] - $requested_change_details['quantity'];

    //         // Stock in 'from location' would go negative and this change is not approved yet
    //         if ($from_quantity < 0 && $requested_change_details['status'] != 'gtg') {
    //             $status = 'permission_required';
    //         }
    //         else {
    //             $status = 'gtg';
    //         }

    //         // Append the new quantities
    //         $changes['to_quantity'] = $to_quantity;
    //         $changes['from_quantity'] = $from_quantity;
    //     }

    //     // Append the status
    //     $changes['status'] = $status;

    //     // Append originally requested quantity (essentially Bom build quantity)
    //     $assemble_quantity = $requested_change_details['assemble_quantity'];
    //     $changes['assemble_quantity'] = $assemble_quantity;

    //     // Produce a result array
    //     $result = array('changes' => $changes);

    //     // If user permission is required because stock is short, also append the negative_stock array
    //     // for generating a table to show the user for approval
    //     //TODO: There might be a better way to do this than to have two separate tables that have duplicate entries...
    //     if ($status == 'permission_required') {
    //         $negative_stock = $changes;
    //         $result['negative_stock'] = $negative_stock;
    //     }

    //     return $result;
    // }

    // private function generateStockShortageResponse($negative_stock, $changes, $change)
    // {
    //     //* Stock shortage while processing parts within BOMs
    //     if (!is_null($changes[0]['bom_id'])) {
    //         $column_names = ['bom_id', 'part_id', 'quantity', 'from_location', 'new_quantity'];
    //         $nice_columns = ['BOM ID', 'Part ID', 'Quantity needed', 'Location', 'Resulting Quantity'];
    //     }
    //     //* Stock shortage while processing parts without BOMs
    //     else {
    //         // Need to select the correct key from the array, depending on the requested change
    //         if ($change == 0) {
    //             $column_names = ['part_id', 'quantity', 'from_location', 'from_quantity'];
    //         }
    //         else {
    //             $column_names = ['part_id', 'quantity', 'from_location', 'new_quantity'];
    //         }

    //         $nice_columns = ['Part ID', 'Quantity needed', 'Location', 'Resulting Quantity'];
    //     }

    //     //* Produce HTML table and send the whole lot back to the user
    //     $negative_stock_table = \buildHTMLTable($column_names, $nice_columns, $negative_stock);
    //     echo json_encode(
    //         [
    //             'changes' => $changes,
    //             'negative_stock' => $negative_stock,
    //             'negative_stock_table' => $negative_stock_table,
    //             'status' => 'permission_requested'
    //         ]
    //     );
    // }
}