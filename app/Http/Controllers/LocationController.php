<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Location;

class LocationController extends Controller
{

    private static $table_name = 'locations';
    private static $id_field = 'location_id';
    private static $location_list_table_headers = array('state', 'location_name', 'location_description', 'bom_id');
    private static $nice_location_list_table_headers = array("Location", 'Description', 'ID');
    public static function getLocations()
    {
        return Location::availableLocations();
    }

    public static function index(Request $request)
    {
        $search_term = request()->has('search') ? request()->input('search') : '';
        $locations_list = Location::availableLocations();

        // Return full parts view or only parts table depending on route
        $route = $request->route()->getName();
        if ($route == 'locations') {
            return view('locations.locations', [
                'title' => 'Storage Locations',
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
}