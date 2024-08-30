<?php

namespace App\Http\Controllers;

use App\Models\Supplier;
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
        } elseif ($route == 'suppliers.suppliersTable') {
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
        $supplier = Supplier::getSupplierById($supplier_id);
        $parts = Supplier::getPartsBySupplier($supplier_id);
        $parts_from_supplier = [];
        foreach ($parts as $part) {
            $parts_from_supplier[] = ['part_name' => $part->part_name,
                'part_id' => $part->part_id];
        }

        return view(
            'suppliers.showSupplier',
            [
                'supplier_name' => $supplier->supplier_name,
                'supplier_alias' => $supplier->supplier_alias,
                // Tabs Settings
                'tabId1' => 'info',
                'tabText1' => 'Info',
                'tabToggleId1' => 'supplierInfo',
                'tabId2' => 'history',
                'tabText2' => 'Supplier History',
                'tabToggleId2' => 'supplierHistory',
                // 'Parts with Supplier' table
                'parts_from_supplier' => $parts_from_supplier,
                'db_columns' => ['part_name', 'part_id'],
                'nice_columns' => ['Part', 'ID'],
            ]
        );
    }

    /**
     * Create a new supplier in the database
     */
    public function create(Request $request)
    {
        $supplier_name = $request->input('supplier_name');

        // Insert new part
        $new_supplier_id = Supplier::createSupplier($supplier_name);

        $response = ['Supplier ID' => $new_supplier_id];

        return response()->json($response);
    }
}
