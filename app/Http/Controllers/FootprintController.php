<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Footprint;

class FootprintController extends Controller
{

    private static $table_name = 'footprints';
    private static $id_field = 'footprint_id';
    private static $footprint_list_table_headers = array('state', 'footprint_name', 'footprint_alias', 'footprint_id');
    private static $nice_footprint_list_table_headers = array("Footprint", 'Alias', 'ID');
    
    public static function getFootprints()
    {
        return Footprint::availableFootprints();
    }

    public function index(Request $request)
    {
        $search_term = request()->has('search') ? request()->input('search') : '';
        $footprints_list = Footprint::availableFootprints('array');

        // Return full parts view or only parts table depending on route
        $route = $request->route()->getName();
        if ($route == 'footprints') {
            return view('footprints.footprints', [
                'title' => 'Footprints',
                'view' => 'footprints',
                'footprints_list' => $footprints_list,
                'db_columns' => self::$footprint_list_table_headers,
                'nice_columns' => self::$nice_footprint_list_table_headers,
                'table_name' => self::$table_name,
                'id_field' => self::$id_field,
            ]);
        }
        elseif ($route == 'footprints.footprintsTable') {
            return view('footprints.footprintsTable', [
                'footprints_list' => $footprints_list,
                'db_columns' => self::$footprint_list_table_headers,
                'nice_columns' => self::$nice_footprint_list_table_headers,
                'table_name' => self::$table_name,
                'id_field' => self::$id_field,
            ]);
        }
    }

    public function show($footprint_id)
    {
        $footprint = Footprint::getFootprintById($footprint_id);
        $parts = Footprint::getPartsByFootprint($footprint_id);
        $parts_with_footprint = [];
        foreach ($parts as $part) {
            $parts_with_footprint[] = ['part_name' => $part->part_name,
                                       'part_id' => $part->part_id];
        }
        return view(
            'footprints.showFootprint',
            [
                'footprint_name' => $footprint->footprint_name,
                'footprint_alias' => $footprint->footprint_alias,
                // Tabs Settings
                'tabId1' => 'info',
                'tabText1' => 'Info',
                'tabToggleId1' => 'footprintInfo',
                'tabId2' => 'history',
                'tabText2' => 'footprint History',
                'tabToggleId2' => 'footprintHistory',
                // 'Parts with Footprint' table
                'parts_with_footprint' => $parts_with_footprint,
                'db_columns' => ['part_name', 'part_id'],
                'nice_columns' => ['Part', 'ID']
            ]
        );
    }

     /**
     * Create a new footprint in the database
     */
    public function create(Request $request)
    {
        $footprint_name = $request->input('footprint_name');
        $footprint_alias = $request->input('footprint_alias');

        // Insert new part 
        $new_footprint_id = Footprint::createFootprint($footprint_name, $footprint_alias);

        echo json_encode(
            array(
                'Footprint ID' => $new_footprint_id,
            )
        );
    }
}