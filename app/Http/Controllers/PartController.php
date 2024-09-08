<?php

namespace App\Http\Controllers;

use App\Events\StockMovementOccured;
use App\Models\Category;
use App\Models\Footprint;
use App\Models\Part;
use App\Models\StockLevel;
use App\Models\StockLevelHistory;
use App\Models\Supplier;
use App\Services\CategoryService;
use App\Services\DatabaseService;
use App\Services\StockService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class PartController extends Controller
{
    private static $table_name = 'parts';

    private static $id_field = 'part_id';

    private static $db_columns = ['state', 'part_name', 'part_description', 'part_comment', 'category_name', 'total_stock', 'footprint_name', 'supplier_name', 'unit_name', 'part_id'];

    // 'state' doesn't contain data but is necessary for boostrapTable's selected row to work
    private static $nice_columns = ['Name', 'Description', 'Comment', 'Category', 'Total Stock', 'Footprint', 'Supplier', 'Unit', 'ID'];

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
        $categoriesForCategoriesTable = Category::where('part_category_owner_u_fk', $user_id)->with('children')->orderBy('category_name', 'asc')->get(); // These are used for the categories tree table in the left side of parts view
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
                'suppliers' => $suppliers,
            ]);
        }
        elseif ($route == 'parts.partsTable') {
            return view('parts.partsTable', [
                'parts' => $parts,
                'db_columns' => self::$db_columns,
                'nice_columns' => self::$nice_columns,
                'table_name' => self::$table_name,
                'id_field' => self::$id_field,
                'categories' => $categories,
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
        $comment = $request->input('comment', null);
        $description = $request->input('description', null);
        $footprint = $request->input('footprint', null);
        $category = $request->input('category', null);
        $supplier = $request->input('supplier', null);
        $min_quantity = $request->input('min_quantity') ?? 0;       // Total Stock Minimum Quantity
        $user_id = Auth::user()->id;
        $response = [];

        try {
            DB::beginTransaction();

            $new_part_id = Part::createPart($part_name, $comment, $description, $footprint, $category, $supplier, $min_quantity);
            if ($quantity && $to_location) {
                $new_stock_entry_id = StockLevel::createStockLevelRecord($new_part_id, $to_location, $quantity);
                $new_stock_level_hist_id = StockLevelHistory::createStockLevelHistoryRecord($new_part_id, null, $to_location, $quantity, $comment, $user_id);
                $response = [
                    'Stock Entry ID' => $new_stock_entry_id,
                    'Stock Level History ID' => $new_stock_level_hist_id,
                ];
            }

            DB::commit();

            $user = Auth::user();
            $stock_level = [$new_part_id, $quantity, $to_location];
            event(new StockMovementOccured($stock_level, $user));

            $response['Part ID'] = $new_part_id;

            return response()->json($response);

        } catch (\Exception $e) {
            DB::rollback();

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
        $stockHistory = StockLevelHistory::getPartStockHistory($part_id);

        // Need to jump through a few hoops for proper time-zoning
        // TODO: Investigate this
        foreach ($stockHistory as $historyItem) {
            // Parse the timestamp as UTC without altering the time itself
            $utcTimestamp = Carbon::createFromFormat('Y-m-d H:i:s', $historyItem->stock_lvl_chng_timestamp, 'UTC');

            // Now convert to the user's timezone
            $localizedTimestamp = $utcTimestamp->setTimezone(config('app.timezone'));

            // Assign the localized timestamp back to the history item
            $historyItem->stock_lvl_chng_timestamp = $localizedTimestamp;
        }

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
                    'column_names' => ['location_name', 'stock_level_quantity'],
                    'nice_columns' => ['Location', 'Quantity'],
                    'stock_levels' => $part['stock_levels'],
                    //Bom Table
                    'bomTableHeaders' => ['bom_name', 'element_quantity', 'bom_description'],
                    'nice_bomTableHeaders' => ['BOM', 'Quantity', 'BOM Description'],
                    'bom_list' => Part::getBomsContainingPart($part_id),
                    // Stock History Table
                    'stockHistoryTableHeaders' => ['stock_lvl_chng_timestamp', 'stock_lvl_chng_quantity', 'from_location_name', 'to_location_name', 'stock_lvl_chng_comment', 'user_name'],
                    'nice_stockHistoryTableHeaders' => ['Date', 'Quantity', 'From', 'To', 'Comment', 'User'],
                    'stock_history' => $stockHistory,
                    // Tabs Settings
                    'tabId1' => 'info',
                    'tabText1' => 'Info',
                    'tabToggleId1' => 'partInfo',
                    'tabId2' => 'history',
                    'tabText2' => 'Stock History',
                    'tabToggleId2' => 'partHistory',
                    'tabId3' => 'suppliers',
                    'tabText3' => 'Suppliers',
                    'tabToggleId3' => 'partSuppliers',
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
            echo json_encode([$ids, $table, $column]);
        }
    }

    /**
     * Get the part name for a given part ID.
     * Used in the stock changing modal.
     *
     * @return string
     */
    public function getName(Request $request)
    {
        $part_id = request()->input('part_id');
        $part = Part::find($part_id)->toArray();

        return $part['part_name'];
    }

    /**
     * Handle and process stock change requests.
     *
     * Processes an array of stock change requests, determining the type of change (add, reduce, or move stock)
     * and updating stock levels accordingly. If changes result in negative stock levels, generates a response
     * requesting user permission; otherwise, processes the changes directly.
     *
     * @param  Request  $request  The HTTP request containing stock change data.
     * @return \Illuminate\Http\JsonResponse A JSON response indicating success or requesting user action.
     */
    public function handleStockRequests(Request $request)
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

            // Extract change details from request
            $requested_change_details = $this->stockService->parseRequestedChangeDetails($requested_change);

            // Get relevant stock levels for currently iterated part
            $requested_change_stock_levels = $this->stockService->getRelevantStockLevelsForChange($requested_change_details);

            // Collect changes to be made
            $result = $this->stockService->collectStockChangeDetails($requested_change_details, $requested_change_stock_levels, $negative_stock);

            // Append array of collected changes to the main arrays
            $changes[] = $result['changes'];
            if (array_key_exists('negative_stock', $result)) {
                $negative_stock[] = $result['negative_stock'];
            }
        }

        //* Stock shortage (i.e. entries in the negative_stock array), inform user and ask permission
        if (!empty($negative_stock)) {
            $response = $this->stockService->generateStockShortageResponse($negative_stock, $changes, $change);

            return response()->json($response);
        }

        //* No user permission necessary
        else {
            $result = $this->stockService->processStockChanges($changes);

            return response()->json($result);
        }
    }
}
