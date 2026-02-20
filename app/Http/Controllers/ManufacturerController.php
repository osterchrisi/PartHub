<?php

namespace App\Http\Controllers;

use App\Models\Manufacturer;
use Illuminate\Http\Request;

class ManufacturerController extends Controller
{
    private static $table_name = 'manufacturers';
    private static $id_field = 'manufacturer_id';

    private static $manufacturer_list_table_headers = [
        'state',
        'manufacturer_name',
        'manufacturer_contact_name',
        'manufacturer_email',
        'manufacturer_phone',
        'manufacturer_website',
        'manufacturer_comment',
        'manufacturer_id',
    ];

    private static $nice_manufacturer_list_table_headers = [
        'Name',
        'Contact',
        'Email',
        'Phone',
        'Website',
        'Comment',
        'ID',
    ];

    public static function getManufacturers()
    {
        return Manufacturer::availableManufacturers();
    }

    public function index(Request $request)
    {
        $manufacturers_list = Manufacturer::availableManufacturers('array');

        $route = $request->route()->getName();

        if ($route == 'manufacturers') {
            return view('manufacturers.manufacturers', [
                'title' => 'Manufacturers',
                'view' => 'manufacturers',
                'manufacturers_list' => $manufacturers_list,
                'db_columns' => self::$manufacturer_list_table_headers,
                'nice_columns' => self::$nice_manufacturer_list_table_headers,
                'table_name' => self::$table_name,
                'id_field' => self::$id_field,
            ]);
        }
        elseif ($route == 'manufacturers.manufacturersTable') {
            return view('manufacturers.manufacturersTable', [
                'manufacturers_list' => $manufacturers_list,
                'db_columns' => self::$manufacturer_list_table_headers,
                'nice_columns' => self::$nice_manufacturer_list_table_headers,
                'table_name' => self::$table_name,
                'id_field' => self::$id_field,
            ]);
        }
    }

    public function show($manufacturer_id)
    {
        $m = Manufacturer::getManufacturerById($manufacturer_id);

        return view('manufacturers.showManufacturer', [
            'manufacturer_name' => $m->manufacturer_name,
            'manufacturer_contact_name' => $m->manufacturer_contact_name,
            'manufacturer_email' => $m->manufacturer_email,
            'manufacturer_phone' => $m->manufacturer_phone,
            'manufacturer_website' => $m->manufacturer_website,
            'manufacturer_comment' => $m->manufacturer_comment,
            // Tabs Settings
            'tabId1' => 'info',
            'tabText1' => 'Info',
            'tabToggleId1' => 'manufacturerInfo',
        ]);
    }

    public function create(Request $request)
    {
        $new_id = Manufacturer::createManufacturer([
            'manufacturer_name' => $request->input('manufacturer_name'),
            'manufacturer_contact_name' => $request->input('manufacturer_contact_name'),
            'manufacturer_email' => $request->input('manufacturer_email'),
            'manufacturer_phone' => $request->input('manufacturer_phone'),
            'manufacturer_website' => $request->input('manufacturer_website'),
            'manufacturer_comment' => $request->input('manufacturer_comment'),
        ]);

        return response()->json(['Manufacturer ID' => $new_id]);
    }
}
