<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Bom;

class BomController extends Controller
{
    private static $table_name = 'boms';
    private static $id_field = 'bom_id';
    private static $db_columns = array('state', 'bom_name', 'bom_description', 'bom_id');
    private static $nice_columns = array("BOM Name", 'Description', 'ID');

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $search_term = request()->has('search') ? request()->input('search') : '';
        $bom_list = Bom::searchBoms($search_term);

        // Return full parts view or only parts table depending on route
        $route = $request->route()->getName();
        if ($route == 'boms') {
            return view('boms.boms', [
                'title' => 'BOMs',
                'bom_list' => $bom_list,
                'db_columns' => self::$db_columns,
                'nice_columns' => self::$nice_columns,
                'table_name' => self::$table_name,
                'id_field' => self::$id_field,
            ]);
        }
        elseif ($route == 'boms.bomsTable') {
            return view('boms.bomsTable', [
                'bom_list' => $bom_list,
                'db_columns' => self::$db_columns,
                'nice_columns' => self::$nice_columns,
                'table_name' => self::$table_name,
                'id_field' => self::$id_field,
            ]);
        }
    }

}