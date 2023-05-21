<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;

class DatabaseService
{
    public static function deleteRow($table, $column, $id)
    {
        DB::table($table)->where($column, $id)->delete();
    }
}
