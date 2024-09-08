<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\DB;


class SupplierDataController extends Controller
{
    protected static $table = 'supplier_data';

    public function createEntry($part_id, $supplier_id, $URL = null, $SPN = null, $price = null)
    {
        $newId = DB::table(self::$table)->insertGetId([
            'part_id_fk' => $part_id,
            'supplier_id_fk' => $supplier_id,
            'URL' => $URL,
            'SPN' => $SPN,  // Supplier Part Number
            'price' => $price,
        ]);

        return $newId;
    }
}
