<?php

namespace App\Http\Controllers;

use App\Services\DatabaseService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DatabaseServiceController extends Controller
{
    public function deleteRow(Request $request)
    {
        $table = $request->input('table');
        $column = $request->input('column');
        $ids = $request->input('ids');

        $owner_columns = [
            'boms' => 'bom_owner_u_fk',
            'footprints' => 'footprint_owner_u_fk',
            'locations' => 'location_owner_u_fk',
            'parts' => 'part_owner_u_fk',
            'part_categories' => 'part_category_owner_u_fk',
            'stock_level_change_history' => 'stock_lvl_chng_user_fk',
            'suppliers' => 'supplier_owner_u_fk',
            'supplier_data' => 'supplier_data_owner_u_fk',
        ];

        $owner_column = $owner_columns[$table];
        $user_id = Auth::id();

        foreach ($ids as $id) {
            //! Add try / catch here in case user is not authorized
            DatabaseService::deleteRow($table, $column, $id, $owner_column, $user_id);
            echo json_encode([$ids, $table, $column]);
        }

    }
}
