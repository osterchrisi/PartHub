<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Services\DatabaseService;

class DatabaseServiceController extends Controller
{

    public function deleteRow(Request $request)
    {
        $table = $request->input('table');
        $column = $request->input('column');
        $ids = $request->input('ids');

        foreach ($ids as $id) {
            DatabaseService::deleteRow($table, $column, $id);
            echo json_encode(array($ids, $table, $column));
        }

    }

}