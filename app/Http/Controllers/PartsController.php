<?php

namespace App\Http\Controllers;

use App\Models\Part;
use App\Models\StockLevel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PartsController extends Controller
{
    private static $table_name = 'parts';
    private static $id_field = 'part_id';
    private static $db_columns = array('state', 'part_name', 'part_description', 'part_comment', 'category_name', 'total_stock', 'part_footprint_fk', 'unit_name', "part_id");
    // 'state' doesn't actually exist but is there to make an empty column for boostrapTable's selected row to have a place
    private static $nice_columns = array('Name', 'Description', 'Comment', 'Category', 'Total Stock', 'Footprint', 'Unit', "ID");

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        // dd($request);
        $search_column = 'everywhere';
        $search_term = request()->has('search') ? request()->input('search') : '';
        $column_names = Part::getColumnNames();
        $user_id = Auth::user()->id;

        $search_category = request()->has('cat') ? request()->input('cat') : ['all'];
        $search_category = $this->extractCategoryIds($search_category);

        $parts = Part::queryParts($search_column, $search_term, $column_names, $search_category, $user_id);

        /* Calculate and append each part's total stock
        / Passing aa reference, so modifications made to $partdirectly affect 
        / the corresponding element in the original $parts array.
        */
        foreach ($parts as &$part) {
            $totalStock = self::calculateTotalStock($part['stock_levels']);
            $part['total_stock'] = $totalStock;
        }

        // Return full parts view or only parts table depending on route
        $route = $request->route()->getName();
        if ($route == 'parts') {
            return view('parts.parts', [
                'title' => 'Parts',
                'parts' => $parts,
                'db_columns' => self::$db_columns,
                'nice_columns' => self::$nice_columns,
                'table_name' => self::$table_name,
                'id_field' => self::$id_field,
                'search_term' => $search_term,
                'search_column' => $search_column
            ]);
        }
        elseif ($route == 'parts.partsTable') {
            return view('parts.partsTable', [
                'parts' => $parts,
                'db_columns' => self::$db_columns,
                'nice_columns' => self::$nice_columns,
                'table_name' => self::$table_name,
                'id_field' => self::$id_field
            ]);
        }
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
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
    public function show(string $id)
    {
        // Fetch the part with its related stock levels
        $part = Part::with('stockLevels.locations')->find($id)->toArray();

        // Calculate total stock level
        $total_stock = $this->calculateTotalStock($part['stock_levels']);

        // Return view
        return view(
            'parts.showPart',
            [
                'part' => $part,
                'total_stock' => $total_stock,
                'column_names' => array('location_name', 'stock_level_quantity'),
                'nice_columns' => array('Location', 'Quantity'),
                'stock_levels' => $part['stock_levels']

            ]
        );
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
    public function destroy(string $id)
    {
        //
    }

    private function calculateTotalStock($stockLevels)
    {
        $total_stock = 0;

        foreach ($stockLevels as $stockLevel) {
            $total_stock += $stockLevel['stock_level_quantity'];
        }

        return $total_stock;
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
        // Return part name
        return $part['part_name'];
    }

    /**
     * Add, reduce or move stock
     * 
     * Returns an array of arrays:
     * The $changes array that has ALL changes requested.
     * The $negative_stock array that contains only entries for stock changes that would result in negative stock.
     * The $negative_stock_table is an HTML string that contains a table built out of the negative_stock array
     */
    public function prepareStockChanges(Request $request)
    {
        // Access stock changes to prepare
        $requested_changes = $request->all()['stock_changes'];
        // return $requested_changes;
        // dd($requested_changes);

        // Initialize the changes array and negative stock array
        $changes = array();
        $negative_stock = array();



        //* Fill arrays with all requested changes
        foreach ($requested_changes as $requested_change) {

            // Gather variables
            $change = $requested_change['change'];

            $part_id = $requested_change['part_id'];
            $quantity = $requested_change['quantity'];
            $comment = $requested_change['comment'];

            $to_location = $requested_change['to_location'];
            if ($to_location == 'NULL') {
                $to_location = NULL;
            }

            $from_location = $requested_change['from_location'];
            if ($from_location == 'NULL') {
                $from_location = NULL;
            }

            if (isset($requested_change['status'])) {
                $status = $requested_change['status'];
            }
            else {
                $status = NULL;
            }

            if (isset($requested_change['bom_id'])) {
                $bom_id = $requested_change['bom_id'];
            }
            else {
                $bom_id = NULL;
            }

            // Get all dem stock levels for currently iterated part
            $stock_levels = StockLevel::getStockLevelsByPartID($part_id);
            $current_stock_level_to = StockLevel::getStockInLocation($stock_levels, $to_location);
            $current_stock_level_from = StockLevel::getStockInLocation($stock_levels, $from_location);

            //* Collect changes to be made
            if ($change == 1) { // Add Stock
                $new_quantity = $current_stock_level_to + $quantity;
                //Add entry to changes array
                $changes[] = array(
                    'bom_id' => $bom_id,
                    'part_id' => $part_id,
                    'quantity' => $quantity,
                    'to_location' => $to_location,
                    'from_location' => $from_location,
                    'change' => $change,
                    'new_quantity' => $new_quantity,
                    'comment' => $comment,
                    'status' => 'gtg'
                );
            }
            elseif ($change == -1) { // Reduce Stock
                $new_quantity = $current_stock_level_from - $quantity;

                // Stock would go negative
                if ($new_quantity < 0 && $status != 'gtg') {
                    $changes[] = array(
                        'bom_id' => $bom_id,
                        'part_id' => $part_id,
                        'quantity' => $quantity,
                        'to_location' => $to_location,
                        'from_location' => $from_location,
                        'change' => $change,
                        'new_quantity' => $new_quantity,
                        'comment' => $comment,
                        'status' => 'permission_required'
                    );
                    //Add entry to negative stock array
                    $negative_stock[] = array(
                        'bom_id' => $bom_id,
                        'part_id' => $part_id,
                        'quantity' => $quantity,
                        'to_location' => $to_location,
                        'from_location' => $from_location,
                        'change' => $change,
                        'new_quantity' => $new_quantity,
                        'comment' => $comment,
                        'status' => 'permission_required'
                    );
                }
                else {
                    //Add entry to changes array
                    $changes[] = array(
                        'bom_id' => $bom_id,
                        'part_id' => $part_id,
                        'quantity' => $quantity,
                        'to_location' => $to_location,
                        'from_location' => $from_location,
                        'change' => $change,
                        'new_quantity' => $new_quantity,
                        'comment' => $comment,
                        'status' => 'gtg'
                    );
                }
            }
            elseif ($change == 0) { // Move Stock
                // New quantity in 'to location'
                $to_quantity = $current_stock_level_to + $quantity;

                // New quantity in 'from_location'
                $from_quantity = $current_stock_level_from - $quantity;

                // Stock in 'from location' goes negative
                if ($from_quantity < 0 && $status != 'gtg') {
                    //Add entry to changes array
                    $changes[] = array(
                        'bom_id' => $bom_id,
                        'part_id' => $part_id,
                        'quantity' => $quantity,
                        'to_location' => $to_location,
                        'from_location' => $from_location,
                        'change' => $change,
                        'to_quantity' => $to_quantity,
                        'from_quantity' => $from_quantity,
                        'comment' => $comment,
                        'status' => 'permission_required'
                    );
                    //Add entry to negative stock array
                    $negative_stock[] = array(
                        'bom_id' => $bom_id,
                        'part_id' => $part_id,
                        'quantity' => $quantity,
                        'to_location' => $to_location,
                        'from_location' => $from_location,
                        'change' => $change,
                        'to_quantity' => $to_quantity,
                        'from_quantity' => $from_quantity,
                        'comment' => $comment,
                        'status' => 'permission_required'
                    );
                }
                else {
                    //Add entry to changes array
                    $changes[] = array(
                        'bom_id' => $bom_id,
                        'part_id' => $part_id,
                        'quantity' => $quantity,
                        'to_location' => $to_location,
                        'from_location' => $from_location,
                        'change' => $change,
                        'to_quantity' => $to_quantity,
                        'from_quantity' => $from_quantity,
                        'comment' => $comment,
                        'status' => 'gtg'
                    );
                }
            }

        }

        // return json_encode(array($changes, $negative_stock));

        //* Make the actual stock change entries and stock change history entries
        //TODO: Would be maybe nice to extract this to different file?

        //* If there are stock shortages processing BOMs, produce table and send back to user
        if (!empty($negative_stock) && !is_null($changes[0]['bom_id'])) {
            $column_names = array('bom_id', 'part_id', 'quantity', 'from_location', 'new_quantity');
            $nice_columns = array('BOM ID', 'Part ID', 'Quantity needed', 'Location', 'Resulting Quantity');
            //!
            $negative_stock_table = buildHTMLTable($column_names, $nice_columns, $negative_stock);
            echo json_encode(
                array(
                    'changes' => $changes,
                    'negative_stock' => $negative_stock,
                    'negative_stock_table' => $negative_stock_table,
                    'status' => 'permission_requested'
                )
            );
            exit;
        }
        //* If there are stock shortages processing parts, produce table and send back to user
        elseif (!empty($negative_stock) && is_null($changes[0]['bom_id'])) {
            //TODO: This can be made even better asking for type of change and selectively showing only / and "to_location" / "from_location"
            $column_names = array('part_id', 'quantity', 'from_location', 'new_quantity');
            $nice_columns = array('Part ID', 'Quantity needed', 'Location', 'Resulting Quantity');
            //!
            $negative_stock_table = buildHTMLTable($column_names, $nice_columns, $negative_stock);
            echo json_encode(
                array(
                    'changes' => $changes,
                    'negative_stock' => $negative_stock,
                    'negative_stock_table' => $negative_stock_table,
                    'status' => 'permission_requested'
                )
            );
            exit;
        }
        //* If no user permission is necessary
        else {
            foreach ($changes as $commit_change) {
                // First extract variables
                $part_id = $commit_change['part_id'];
                $bom_id = $commit_change['bom_id'];
                $change = $commit_change['change'];

                $quantity = $commit_change['quantity'];

                if (isset($commit_change['to_quantity'])) {
                    $to_quantity = $commit_change['to_quantity'];
                }
                else {
                    $to_quantity = NULL;
                }

                if (isset($commit_change['from_quantity'])) {
                    $from_quantity = $commit_change['from_quantity'];
                }
                else {
                    $from_quantity = NULL;
                }


                $new_quantity = $commit_change['new_quantity'];
                $to_location = $commit_change['to_location'];
                $from_location = $commit_change['from_location'];
                $comment = $commit_change['comment'];
                $status = $commit_change['status'];

                if ($change == 1) { // Add Stock
                    // Make records in stock_level and stock_level_change_history tables
                    $stock_level_id = StockLevel::updateOrCreateStockLevelEntry($part_id, $new_quantity, $to_location);
                    // return array($commit_change, $stock_level_id);
                    //! We're already making it to here, Laravel folks!
                    // $stock_level_id = changeQuantity($conn, $part_id, $new_quantity, $to_location);
                    $hist_id = stockChange($conn, $part_id, $from_location, $to_location, $quantity, $comment, $user_id);

                    // Calculate new stock
                    $stock = getStockLevels($conn, $part_id);
                    $total_stock = getTotalStock($stock);

                    //TODO: This ist part of my hicky hacky solution to update the stock level in the parts_table after updating
                    // Report back for updating tables
                    $result = [$hist_id, $stock_level_id, $total_stock];
                    echo json_encode(
                        array(
                            'changes' => array(),
                            'negative_stock' => array(),
                            'negative_stock_table' => array(),
                            'status' => 'success',
                            'result' => $result
                        )
                    );
                }
                elseif ($change == -1) { // Reduce Stock
                    $stock_level_id = changeQuantity($conn, $part_id, $new_quantity, $from_location);
                    //! Have to write NULL for $to_location. That's alright for now but would like to clean this
                    $hist_id = stockChange($conn, $part_id, $from_location, NULL, $quantity, $comment, $user_id);

                    // Calculate new stock
                    $stock = getStockLevels($conn, $part_id);
                    $total_stock = getTotalStock($stock);

                    //TODO: This ist part of my hicky hacky solution to update the stock level in the parts_table after updating
                    // Report back for updating tables
                    $result = [$hist_id, $stock_level_id, $total_stock];
                    echo json_encode(
                        array(
                            'changes' => array(),
                            'negative_stock' => array(),
                            'negative_stock_table' => array(),
                            'status' => 'success',
                            'result' => $result
                        )
                    );
                }
                elseif ($change == 0) {
                    // First add stock in 'to location'
                    $stock_level_id = changeQuantity($conn, $part_id, $to_quantity, $to_location);

                    // Then reduce stock in 'from location'
                    $stock_level_id = changeQuantity($conn, $part_id, $from_quantity, $from_location);

                    // History entry
                    $hist_id = stockChange($conn, $part_id, $from_location, $to_location, $quantity, $comment, $user_id);

                    // Reporting stock so that it can be updated in the origin table

                    // Calculate new stock
                    $stock = getStockLevels($conn, $part_id);
                    $total_stock = getTotalStock($stock);

                    //TODO: This ist part of my hicky hacky solution to update the stock level in the parts_table after updating
                    $result = [$hist_id, $stock_level_id, $total_stock];
                    echo json_encode(
                        array(
                            'changes' => array(),
                            'negative_stock' => array(),
                            'negative_stock_table' => array(),
                            'status' => 'success',
                            'result' => $result
                        )
                    );
                }
            }
        }
    }
}