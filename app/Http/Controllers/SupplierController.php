<?php

namespace App\Http\Controllers;

use App\Models\Supplier;
use App\Models\SupplierData;
use Illuminate\Http\Request;

class SupplierController extends Controller
{
    private static $table_name = 'suppliers';

    private static $id_field = 'supplier_id';

    private static $supplier_list_table_headers = ['state', 'supplier_name', 'supplier_id'];

    private static $nice_supplier_list_table_headers = ['Supplier', 'ID'];

    public static function getSuppliers()
    {
        return Supplier::availableSuppliers();
    }

    public function index(Request $request)
    {
        $search_term = request()->has('search') ? request()->input('search') : '';
        $suppliers_list = Supplier::availableSuppliers('array');

        // Return full parts view or only parts table depending on route
        $route = $request->route()->getName();
        if ($route == 'suppliers') {
            return view('suppliers.suppliers', [
                'title' => 'Suppliers',
                'view' => 'suppliers',
                'suppliers_list' => $suppliers_list,
                'db_columns' => self::$supplier_list_table_headers,
                'nice_columns' => self::$nice_supplier_list_table_headers,
                'table_name' => self::$table_name,
                'id_field' => self::$id_field,
            ]);
        }
        elseif ($route == 'suppliers.suppliersTable') {
            return view('suppliers.suppliersTable', [
                'suppliers_list' => $suppliers_list,
                'db_columns' => self::$supplier_list_table_headers,
                'nice_columns' => self::$nice_supplier_list_table_headers,
                'table_name' => self::$table_name,
                'id_field' => self::$id_field,
            ]);
        }
    }

    public function show($supplier_id)
    {
        // Fetch supplier information
        $supplier = Supplier::find($supplier_id);

        // Fetch parts related to this supplier via the supplier_data table
        $suppliedParts = SupplierData::with('part')
            ->where('supplier_id_fk', $supplier_id)
            ->get();

        // Prepare data for view
        $parts_from_supplier = [];
        foreach ($suppliedParts as $suppliedPart) {
            $parts_from_supplier[] = [
                'part_name' => $suppliedPart->part->part_name,
                'part_id' => $suppliedPart->part->part_id,
                'URL' => $suppliedPart->URL, // Include any additional data you need
                'SPN' => $suppliedPart->SPN,
                'price' => $suppliedPart->price,
            ];
        }

        // Pass data to view
        return view('suppliers.showSupplier', [
            'supplier' => $supplier,
            'suppliedParts' => $suppliedParts,
            // Tabs Settings
            'tabId1' => 'info',
            'tabText1' => 'Info',
            'tabToggleId1' => 'supplierInfo',
            'tabId2' => 'history',
            'tabText2' => 'Supplier History',
            'tabToggleId2' => 'supplierHistory',
            // Parts from Supplier table
            'parts_from_supplier' => $parts_from_supplier,
            'db_columns' => ['part_name', 'URL', 'SPN', 'price', 'part_id'], // Add additional columns as needed
            'nice_columns' => ['Part', 'URL', 'SPN', 'Price', 'Part ID'], // These are the table headers
        ]);
    }

    /**
     * Create a new supplier in the database
     */
    public function create(Request $request)
    {
        $supplier_name = $request->input('supplier_name');

        // Insert new supplier
        $new_supplier_id = Supplier::createSupplier($supplier_name);

        $response = ['Supplier ID' => $new_supplier_id];

        return response()->json($response);
    }
}
