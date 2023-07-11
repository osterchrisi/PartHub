<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Location;
use App\Models\Part;
use App\Models\StockLevel;
use App\Models\StockLevelHistory;
use App\Models\BomRun;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Services\DatabaseService;

class PartsController extends Controller
{
    private static $table_name = 'parts';
    private static $id_field = 'part_id';
    private static $db_columns = array('state', 'part_name', 'part_description', 'part_comment', 'category_name', 'total_stock', 'part_footprint_fk', 'unit_name', "part_id");
    // 'state' doesn't contain data but is necessary for boostrapTable's selected row to work
    private static $nice_columns = array('Name', 'Description', 'Comment', 'Category', 'Total Stock', 'Footprint', 'Unit', "ID");

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
        $search_category = $this->extractCategoryIds($search_category);

        $parts = Part::queryParts($search_column, $search_term, $column_names, $search_category, $user_id);

        $categories = Category::availableCategories();
        $column_names = Part::getColumnNames();
        $locations = Location::availableLocations();

        /* Calculate and append each part's total stock
        / Passing a reference, so modifications made to $part directly affect 
        / the corresponding element in the original $parts array.
        */
        foreach ($parts as &$part) {
            $totalStock = \calculateTotalStock($part['stock_levels']);
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
                'categories' => $categories
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
        $quantity = $request->input('quantity');
        $to_location = $request->input('to_location');
        $comment = $request->input('comment', NULL);
        $description = $request->input('description', NULL);
        $user_id = Auth::user()->id;

        // Insert new part 
        $new_part_id = Part::createPart($part_name, $comment, $description);
        // Create a stock level entry
        $new_stock_entry_id = StockLevel::createStockLevelRecord($new_part_id, $to_location, $quantity);
        // Create a stock level history entry (from_location is NULL)
        $new_stock_level_id = StockLevelHistory::createStockLevelHistoryRecord($new_part_id, NULL, $to_location, $quantity, $comment, $user_id);

        echo json_encode(
            array(
                'Part ID' => $new_part_id,
                'Stock Entry ID' => $new_stock_entry_id,
                'Stock Level History ID' => $new_stock_level_id
            )
        );
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
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
            $total_stock = \calculateTotalStock($part['stock_levels']);

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
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
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
            DatabaseService::deleteRow($table, $column, $id);
            echo json_encode(array($ids, $table, $column));
        }

    }

    /**
     * Extracts category IDs from a JSON-encoded array and returns a simple array of digits.
     *
     * The input array is expected to be in the format [[3], [5]] due to the limitations of selectizing
     * the multi-select input field. This function decodes the JSON-encoded array and extracts the
     * numeric values, returning a simplified array of category IDs.
     *
     * @param array $searchCategory The array containing the JSON-encoded category IDs.
     * @return array The simplified array of category IDs as digits.
     */
    public static function extractCategoryIds($search_category)
    {
        if (!in_array('all', $search_category)) {
            $cat_ids = [];

            foreach ($search_category as $cat_array) {
                $decoded_array = json_decode($cat_array);

                foreach ($decoded_array as $element) {
                    $cat_ids[] = $element[0];
                }
            }

            return $cat_ids;
        }

        return $search_category;
    }

    /**
     * Get the part name for a given part ID
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

        // Extracting the type of change from the first entry in the array (all entries have same type)
        $change = $requested_changes[0]['change'];

        // Initialize the changes array and negative stock array
        $changes = array();
        $negative_stock = array();

        //* Fill above empty arrays with all requested changes, each $requested_change entry holds one part and its changes
        foreach ($requested_changes as $requested_change) {

            // Extract variables from request
            $requested_change_details = $this->parseRequestedChangeDetails($requested_change);

            // Get relevant stock levels for currently iterated part
            $requested_change_stock_levels = $this->getRelevantStockLevelsForChange($requested_change_details);

            // Collect changes to be made
            $result = $this->prepareStockChangesArrays($requested_change_details, $requested_change_stock_levels, $changes, $negative_stock);

            // Append array of collected changes to the main arrays
            $changes[] = $result['changes'];
            if (array_key_exists('negative_stock', $result)) {
                $negative_stock[] = $result['negative_stock'];
            }

        }

        //* Stock shortage, inform user and exit
        if (!empty($negative_stock)) {
            $this->generateStockShortageResponse($negative_stock, $changes, $change);
            exit;
        }

        //* No user permission necessary
        else {
            $this->processApprovedChanges($changes);
        }
    }

    private function parseRequestedChangeDetails($requested_change)
    {
        $change = $requested_change['change'];

        $part_id = $requested_change['part_id'];
        $quantity = $requested_change['quantity'];
        $comment = $requested_change['comment'];

        $to_location = $requested_change['to_location'];
        $from_location = $requested_change['from_location'];

        $status = $requested_change['status'] ?? NULL;
        $bom_id = $requested_change['bom_id'] ?? NULL;

        return array(
            'change' => $change,
            'bom_id' => $bom_id,
            'part_id' => $part_id,
            'quantity' => $quantity,
            'to_location' => $to_location,
            'from_location' => $from_location,
            'comment' => $comment,
            'status' => $status
        );
    }

    private function getRelevantStockLevelsForChange($requested_change_details)
    {
        // Retrieve all stock levels for the given part from the database
        $stock_levels = StockLevel::getStockLevelsByPartID($requested_change_details['part_id']);

        // Get the relevant stock leves for the requested change
        $current_stock_level_to = StockLevel::getStockInLocation($stock_levels, $requested_change_details['to_location']);
        $current_stock_level_from = StockLevel::getStockInLocation($stock_levels, $requested_change_details['from_location']);

        return array(
            'current_stock_level_to' => $current_stock_level_to,
            'current_stock_level_from' => $current_stock_level_from
        );
    }

    private function prepareStockChangesArrays($requested_change_details, $requested_change_stock_levels, $changes, $negative_stock)
    {
        $changes = $requested_change_details;
        $change = $requested_change_details['change'];

        //* Add Stock
        if ($change == 1) {
            $new_quantity = $requested_change_stock_levels['current_stock_level_to'] + $requested_change_details['quantity'];
            $changes['new_quantity'] = $new_quantity;
            $status = 'gtg';
        }

        //* Reduce Stock
        elseif ($change == -1) {
            $new_quantity = $requested_change_stock_levels['current_stock_level_from'] - $requested_change_details['quantity'];
            $changes['new_quantity'] = $new_quantity;

            // Stock would go negative and this change is not approved yet
            if ($new_quantity < 0 && $requested_change_details['status'] != 'gtg') {
                $status = 'permission_required';
            }
            else {
                // Need to explicitly assign it because in the primary request it comes as NULL
                $status = 'gtg';
            }
        }

        //* Move Stock
        if ($change == 0) {
            // New quantity in 'to location'
            $to_quantity = $requested_change_stock_levels['current_stock_level_to'] + $requested_change_details['quantity'];
            // New quantity in 'from location'
            $from_quantity = $requested_change_stock_levels['current_stock_level_from'] - $requested_change_details['quantity'];

            // Stock in 'from location' would go negative and this change is not approved yet
            if ($from_quantity < 0 && $requested_change_details['status'] != 'gtg') {
                $status = 'permission_required';
            }
            else {
                $status = 'gtg';
            }

            // Append the new quantities
            $changes['to_quantity'] = $to_quantity;
            $changes['from_quantity'] = $from_quantity;
        }

        // Append the status
        $changes['status'] = $status;

        // Produce a result array
        $result = array('changes' => $changes);

        // If user permission is required because stock is short, also append the negative_stock array
        // for generating a table to show the user for approval
        //TODO: There might be a better way to do this than to have two separate tables that have duplicate entries...
        if ($status == 'permission_required') {
            $negative_stock = $changes;
            $result['negative_stock'] = $negative_stock;
        }

        return $result;
    }

    private function generateStockShortageResponse($negative_stock, $changes, $change)
    {
        //* Stock shortage while processing parts within BOMs
        if (!is_null($changes[0]['bom_id'])) {
            $column_names = array('bom_id', 'part_id', 'quantity', 'from_location', 'new_quantity');
            $nice_columns = array('BOM ID', 'Part ID', 'Quantity needed', 'Location', 'Resulting Quantity');
        }
        //* Stock shortage while processing parts without BOMs
        else {
            // Need to select the correct key from the array, depending on the requested change
            if ($change == 0) {
                $column_names = array('part_id', 'quantity', 'from_location', 'from_quantity');
            }
            else {
                $column_names = array('part_id', 'quantity', 'from_location', 'new_quantity');
            }

            $nice_columns = array('Part ID', 'Quantity needed', 'Location', 'Resulting Quantity');
        }

        //* Produce HTML table and send the whole lot back to the user
        $negative_stock_table = \buildHTMLTable($column_names, $nice_columns, $negative_stock);
        echo json_encode(
            array(
                'changes' => $changes,
                'negative_stock' => $negative_stock,
                'negative_stock_table' => $negative_stock_table,
                'status' => 'permission_requested'
            )
        );
    }

    /**
     * Process the stock changes once they've been approved or no approval was necessary
     *
     * @param [type] $changes
     * @return void
     */
    private function processApprovedChanges($changes)
    {
        // Get current authenticated user
        $user_id = Auth::user()->id;

        foreach ($changes as $approved_change) {
            // First extract variables
            $part_id = $approved_change['part_id'];
            $bom_id = $approved_change['bom_id'];
            $change = $approved_change['change'];

            $quantity = $approved_change['quantity'];

            // Need to check these three because depending on stock change type (1, -1, 0)
            // they might be present or not. If not, set them to NULL so the database doesn't complain
            $to_quantity = $approved_change['to_quantity'] ?? NULL;
            $from_quantity = $approved_change['from_quantity'] ?? NULL;
            $new_quantity = $approved_change['new_quantity'] ?? NULL;

            $to_location = $approved_change['to_location'];
            $from_location = $approved_change['from_location'];
            $comment = $approved_change['comment'];

            //* Make records in Stock Level model
            // Add Stock
            if ($change == 1) {
                $stock_level_id = StockLevel::updateOrCreateStockLevelRecord($part_id, $new_quantity, $to_location);
            }
            // Reduce Stock
            elseif ($change == -1) {
                $stock_level_id = StockLevel::updateOrCreateStockLevelRecord($part_id, $new_quantity, $from_location);
            }
            // Move Stock (need to create or update two entries)
            elseif ($change == 0) {
                // First add stock in 'to location'
                $stock_level_id = StockLevel::updateOrCreateStockLevelRecord($part_id, $to_quantity, $to_location);

                // Then reduce stock in 'from location'
                $stock_level_id = StockLevel::updateOrCreateStockLevelRecord($part_id, $from_quantity, $from_location);
            }

            //* Make record in Stock Level History model
            $hist_id = StockLevelHistory::createStockLevelHistoryRecord($part_id, $from_location, $to_location, $quantity, $comment, $user_id);

            // Calculate new stock for updating the origin table in browser
            $stock = StockLevel::getStockLevelsByPartID($part_id);
            $total_stock = \calculateTotalStock($stock);

            // Add entries to the result array
            $result[] = ['hist_id' => $hist_id, 'stock_level_id' => $stock_level_id, 'new_total_stock' => $total_stock];
            
            // Add entries to the BOM IDs array
            $processed_bom[] = $bom_id;
        }
        // Array with unique processed BOM IDs
        $processed_boms = array_unique($processed_bom);

        //TODO: Make BOM History entries with the IDs here
        // foreach($processed_boms as $processed_bom){
        //     BomRun::createBomRun($processed_bom, $quantity, $user_id);
        // }

        // Report all the goodies back for updating tables
        echo json_encode(
            array(
                'status' => 'success',
                'result' => $result
            )
        );
    }
}