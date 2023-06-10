<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Location;

class LocationController extends Controller
{

    private static $table_name = 'locations';
    private static $id_field = 'location_id';
    private static $location_list_table_headers = array('state', 'location_name', 'location_description', 'location_id');
    private static $nice_location_list_table_headers = array("Location", 'Description', 'ID');
    public static function getLocations()
    {
        return Location::availableLocations();
    }

    public function index(Request $request)
    {
        $search_term = request()->has('search') ? request()->input('search') : '';
        $locations_list = Location::availableLocations('array');

        // Return full parts view or only parts table depending on route
        $route = $request->route()->getName();
        if ($route == 'locations') {
            return view('locations.locations', [
                'title' => 'Storage Locations',
                'view' => 'locations',
                'locations_list' => $locations_list,
                'db_columns' => self::$location_list_table_headers,
                'nice_columns' => self::$nice_location_list_table_headers,
                'table_name' => self::$table_name,
                'id_field' => self::$id_field,
            ]);
        }
        elseif ($route == 'locations.locationsTable') {
            return view('locations.locationsTable', [
                'locations_list' => $locations_list,
                'db_columns' => self::$location_list_table_headers,
                'nice_columns' => self::$nice_location_list_table_headers,
                'table_name' => self::$table_name,
                'id_field' => self::$id_field,
            ]);
        }
    }

    public function show($location_id)
    {
        $location = Location::getLocationById($location_id);
        $stock_levels = $location->getStockLevelEntries();
        $result = [];
        foreach ($stock_levels as $stock_level) {
            $result[] = [$stock_level['part_id_fk'] => $stock_level['stock_level_quantity']];
        }
        // dd($stock_levels);
        dd($result);
    }
}