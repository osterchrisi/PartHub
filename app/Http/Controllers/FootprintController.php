<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Footprint;

class FootprintController extends Controller
{

    private static $table_name = 'footprints';
    private static $id_field = 'footprint_id';
    private static $footprint_list_table_headers = array('state', 'footprint_name', 'footprint_alias');
    private static $nice_footprint_list_table_headers = array("Footprint", 'Alias', 'ID');
    // public static function getLocations()
    // {
    //     return Location::availableLocations();
    // }

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

    // public function show($location_id)
    // {
    //     $location = Location::getLocationById($location_id);
    //     $stock_levels = $location->getStockLevelEntries();
    //     $stock_in_location = [];
    //     foreach ($stock_levels as $stock_level) {
    //         $stock_in_location[] = ['part_name' => $stock_level->part->part_name, 'stock_level' => $stock_level->stock_level_quantity];
    //     }
    //     return view(
    //         'locations.showLocation',
    //         [
    //             'location_name' => $location->location_id,
    //             'location_description' => $location->location_description,
    //             // Tabs Settings
    //             'tabId1' => 'info',
    //             'tabText1' => 'Info',
    //             'tabToggleId1' => 'locationInfo',
    //             'tabId2' => 'history',
    //             'tabText2' => 'Location History',
    //             'tabToggleId2' => 'locationHistory',
    //             // Stock in Location table
    //             'stock_in_location' => $stock_in_location,
    //             'db_columns' => ['part_name', 'stock_level'],
    //             'nice_columns' => ['Part', 'Quantity']
    //         ]
    //     );
    // }
}