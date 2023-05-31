<?php

namespace App\Http\Controllers;

use App\Models\BomElements;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Bom;

class BomController extends Controller
{
    private static $table_name = 'boms';
    private static $id_field = 'bom_id';
    private static $bom_list_table_headers = array('state', 'bom_name', 'bom_description', 'bom_id');
    private static $nice_bom_list_table_headers = array("BOM Name", 'Description', 'ID');
    private static $bom_detail_table_headers = array('part_name', 'element_quantity', 'stock_available', 'can_build');
    private static $nice_bom_detail_table_headers = array('Part Name', 'Quantity needed', 'Total stock available', 'Can build');

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
                'id_field' => self::$id_field,
            ]);
        }
    }

    public static function show($bom_id)
    {
        $bom_info = Bom::getBomNameAndDescription($bom_id);
        // dd($bom_info);
        $bom_name = $bom_info[0]->bom_name;
        $bom_description = $bom_info[0]->bom_description;

        // Get BOM elements
        $bom_elements = BomElements::getBomElements($bom_id);

        return view(
            'boms.showBom',
            [
                'bom_name' => $bom_name,
                'bom_description' => $bom_description,
                'bom_elements' => $bom_elements,
                // Bom Details Table
                'db_columns' => self::$bom_detail_table_headers,
                'nice_columns' => self::$nice_bom_detail_table_headers,
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

}