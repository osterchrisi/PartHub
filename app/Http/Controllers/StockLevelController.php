<?php

namespace App\Http\Controllers;

use App\Models\StockLevel;

class StockLevelController extends Controller
{
    public static function index($part_id)
    {
        $stock = (new StockLevel())->getStockLevelsByPartID($part_id);

        return $stock;
    }
}
