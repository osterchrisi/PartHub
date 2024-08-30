<?php

namespace App\Services;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DatabaseService
{
    public static function deleteRow($table, $column, $id, $owner_column, $user_id)
    {
        DB::table($table)
            ->where($column, $id)
            ->where($owner_column, $user_id)
            ->delete();
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
