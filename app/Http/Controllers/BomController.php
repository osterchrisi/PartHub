<?php

namespace App\Http\Controllers;

use App\Models\BomElements;
use App\Models\BomRun;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Bom;
use App\Imports\BomImport;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Maatwebsite\Excel\Facades\Excel;

class BomController extends Controller
{
    private static $table_name = 'boms';
    private static $id_field = 'bom_id';
    private static $bom_list_table_headers = array('state', 'bom_name', 'bom_description', 'bom_id');
    private static $nice_bom_list_table_headers = array("BOM Name", 'Description', 'ID');
    private static $bom_detail_table_headers = array('part_name', 'element_quantity', 'stock_available', 'can_build');
    private static $nice_bom_detail_table_headers = array('Part Name', 'Quantity needed', 'Total stock available', 'Can build');
    private static $bomRunsTableHeaders = array('bom_run_datetime', 'bom_run_quantity', 'name');
    private static $nice_bomRunsTableHeaders = array('Build Time', 'Quantity', 'User');

    /**
     * Display a listing of the resource.
     */
    public static function index(Request $request)
    {
        $search_term = request()->has('search') ? request()->input('search') : '';
        $bom_list = Bom::searchBoms($search_term);

        // Return full parts view or only parts table depending on route
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
        }
        elseif ($route == 'boms.bomsTable') {
            return view('boms.bomsTable', [
                'bom_list' => $bom_list,
                'db_columns' => self::$bom_list_table_headers,
                'nice_columns' => self::$nice_bom_list_table_headers,
                'table_name' => self::$table_name,
                'id_field' => self::$id_field
            ]);
        }
    }

    public static function show($bom_id)
    {
        $bom_info = Bom::getBomById($bom_id);
        $bom_name = $bom_info[0]->bom_name;
        $bom_description = $bom_info[0]->bom_description;
        $bom_owner = $bom_info[0]->bom_owner_u_fk;

        if (Auth::user()->id === $bom_owner) {

            // Get BOM elements
            $bom_elements = BomElements::getBomElements($bom_id);

            // Get BOM Run History
            $bom_runs = BomRun::getBomRunsByBomId($bom_id);

            // dd($bom_runs);

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
                    'nice_bomRunsTableHeaders' => self::$bomRunsTableHeaders,
                    'bomRunsTableHeaders' => self::$nice_bomRunsTableHeaders,
                    'bom_runs' => $bom_runs,
                    // Tabs Settings
                    'tabId1' => 'info',
                    'tabText1' => 'Info',
                    'tabToggleId1' => 'bomInfo',
                    'tabId2' => 'history',
                    'tabText2' => 'Build History',
                    'tabToggleId2' => 'bomHistory'
                ]
            );
        }
        else {
            abort(403, 'Unauthorized access.'); // Return a 403 Forbidden status with an error message
        }
    }

    public static function prepareBomForAssembly(Request $request)
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
            $elements = BomElements::getBomElements($bom_id);

            // Iterating over BOM elements (parts)
            foreach ($elements as $element) {
                $element_quantity = $element->element_quantity;
                $part_id = $element->part_id;
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
                    'comment' => 'BOM build of BOM with ID ' . $bom_id,
                    'status' => 'BOM build request',
                    'assemble_quantity' => $assemble_quantity
                ];

                $all_stock_changes[] = $stock_change;
            }
        }

        // Assign the final array to the stock_changes key in the request input
        $request->merge(['stock_changes' => $all_stock_changes]);

        // Make new PartsController and let it do its thing
        $partsController = new PartsController();
        $partsController->prepareStockChanges($request);
    }

    public function importBom(Request $request)
    {
        // Form data
        $file = $request->file('bom_file');
        $bom_name = $request->input('bom_name');
        $bom_description = $request->input('bom_description');

        //! Either validate file here or in middleware

        try {

            // Begin SQL transaction
            DB::beginTransaction();

            // Create new BOM
            $bom_id = Bom::createBom($bom_name, $bom_description);

            // Process the uploaded file
            Excel::import(new BomImport($bom_id), $file);

            // Persist database changes and set success flash message
            DB::commit();
            Session::flash('success', 'BOM "' . $bom_name . '" imported successfully.');

            // Redirect to the previous page
            return redirect()->back();

        } catch (\Exception $e) {
            // Roll back database changes made so far
            DB::rollback();

            // Set error flash message
            Session::flash('error', 'Error importing BOM: ' . $e->getMessage());

            // Redirect to the previous page with error status
            return redirect()->back()->withErrors(['import_error' => 'Failed to import BOMs.']);
        }
    }

}