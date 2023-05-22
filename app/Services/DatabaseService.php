<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;

class DatabaseService
{
    public static function deleteRow($table, $column, $id)
    {
        DB::table($table)->where($column, $id)->delete();
    }

    public static function updateCell(Request $request)
    {

        $table_name = $request->input('table_name');
        $id_field = $request->input('id_field');
        $id = $request->input('id');
        $column = $request->input('column');
        $new_value = $request->input('new_value');

        DB::table($table_name)
            ->where($id_field, $id)
            ->update([$column => $new_value]);
    }
}