<?php

namespace App\Http\Controllers;

use App\Imports\BomImport;
use App\Models\Bom;
use App\Models\BomElements;
use App\Models\BomRun;
use App\Services\CsvImportService;
use App\Services\StockService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;

class BomController extends Controller
{
    private static $table_name = 'boms';

    private static $id_field = 'bom_id';

    private static $bom_list_table_headers = ['state', 'bom_name', 'bom_description', 'bom_id'];

    private static $nice_bom_list_table_headers = ['BOM Name', 'Description', 'ID'];

    private static $bom_detail_table_headers = ['part_name', 'element_quantity', 'stock_available', 'can_build'];

    private static $nice_bom_detail_table_headers = ['Part Name', 'Quantity needed', 'Total stock available', 'Can build'];

    private static $bomRunsTableHeaders = ['bom_run_datetime', 'bom_run_quantity', 'name'];

    private static $nice_bomRunsTableHeaders = ['Build Time', 'Build Quantity', 'User'];

    protected $stockService;

    public function __construct(StockService $stockService)
    {
        $this->stockService = $stockService;
    }

    /**
     * Display a listing of the resource.
     */
    public static function index(Request $request)
    {
        $search_term = request()->has('search') ? request()->input('search') : '';
        $bom_list = Bom::searchBoms($search_term);

        // Return full BOM view or only BOM table depending on route
        $route = $request->route()->getName();
        if ($route == 'boms') {
            return view('boms.boms', [
                'title' => 'BOMs',
                'view' => 'boms',
                'bom_list' => $bom_list,
                'db_columns' => self::$bom_list_table_headers,
                'nice_columns' => self::$nice_bom_list_table_headers,
                'table_name' => self::$table_name,
                'id_field' => self::$id_field,
            ]);
        } elseif ($route == 'boms.bomsTable') {
            return view('boms.bomsTable', [
                'bom_list' => $bom_list,
                'db_columns' => self::$bom_list_table_headers,
                'nice_columns' => self::$nice_bom_list_table_headers,
                'table_name' => self::$table_name,
                'id_field' => self::$id_field,
            ]);
        }
    }

    public static function show($bom_id)
    {
        $bom = Bom::find($bom_id);
        $bom_name = $bom->bom_name;
        $bom_description = $bom->bom_description;
        $bom_owner = $bom->bom_owner_u_fk;

        if (Auth::user()->id === $bom_owner) {

            $bom_elements = BomElements::getBomElements($bom_id);
            $bom_runs = BomRun::getBomRunsByBomId($bom_id);

            return view(
                'boms.showBom',
                [
                    'bom_name' => $bom_name,
                    'bom_description' => $bom_description,
                    'bom_elements' => $bom_elements,

                    // Bom Details Table
                    'db_columns' => self::$bom_detail_table_headers,
                    'nice_columns' => self::$nice_bom_detail_table_headers,

                    // Bom Runs Table
                    'nice_bomRunsTableHeaders' => self::$nice_bomRunsTableHeaders,
                    'bomRunsTableHeaders' => self::$bomRunsTableHeaders,
                    'bom_runs' => $bom_runs,

                    // Tabs Settings
                    'tabId1' => 'info',
                    'tabText1' => 'Info',
                    'tabToggleId1' => 'bomInfo',
                    'tabId2' => 'history',
                    'tabText2' => 'Build History',
                    'tabToggleId2' => 'bomHistory',
                ]
            );
        } else {
            abort(403, 'Unauthorized access.'); // Return a 403 Forbidden status with an error message
        }
    }

    /**
     * Takes BOM(s), retrieves the BOM Elements (parts) and creates an array of changes to be requested
     *
     * @return mixed
     */
    public function prepareBomForAssembly(Request $request)
    {
        // Get the input values from the request
        $ids = $request->input('ids');
        $assemble_quantity = $request->input('assemble_quantity');
        $from_location = $request->input('from_location');

        // The array for all acquired stock changes to go in
        $all_stock_changes = [];

        // Iterate over each individual BOM
        foreach ($ids as $bom_id) {

            // Retrieve BOM elements (parts)
            $bom_elements = BomElements::getBomElements($bom_id);

            // Iterating over BOM elements (parts)
            foreach ($bom_elements as $element) {
                $element_quantity = $element->element_quantity;
                $part_id = $element->part->part_id;
                $reducing_quantity = $assemble_quantity * $element_quantity;

                // Prepare stock change array, one array for each part in the BOM
                // Stored in an array of arrays
                $stock_change = [
                    'bom_id' => $bom_id,
                    'part_id' => $part_id,
                    'change' => '-1',
                    'quantity' => $reducing_quantity,
                    'to_location' => null,
                    'from_location' => $from_location,
                    'comment' => 'BOM build of BOM with ID '.$bom_id,
                    'status' => 'BOM build request',
                    'assemble_quantity' => $assemble_quantity,
                ];

                $all_stock_changes[] = $stock_change;
            }
        }

        return $this->stockService->handleStockRequest($all_stock_changes);
    }

    public function importBom(Request $request)
    {
        // Form data
        $file = $request->file('bom_file');
        $bom_name = $request->input('bom_name');
        $bom_description = $request->input('bom_description');

        if (! $file) {
            return response()->json(['error' => 'No file uploaded'], 400);
        }

        try {
            // Begin SQL transaction
            DB::beginTransaction();

            // Create new BOM
            $bom_id = Bom::createBom($bom_name, $bom_description);

            // Instantiate the CsvImportService
            $csvImportService = new CsvImportService();

            // Process the uploaded file using BomImport
            Excel::import(new BomImport($bom_id, $csvImportService), $file);

            // Persist database changes and set success flash message
            DB::commit();

            return response()->json(['success' => 'BOM "'.$bom_name.'" imported successfully.', 'new_bom_id' => $bom_id]);

        } catch (\Exception $e) {
            // Roll back database changes made so far
            DB::rollback();

            // Set error flash message
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
}
