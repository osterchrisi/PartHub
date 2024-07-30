<?php

namespace App\Services;

use App\Models\StockLevel;
use App\Models\StockLevelHistory;

class StockService
{
    /**
     * Takes an of stock level entries and returns the total
     */
    public function calculateTotalStock($stockLevels)
    {
        $total_stock = 0;
    
        foreach ($stockLevels as $stockLevel) {
            $total_stock += $stockLevel['stock_level_quantity'];
        }
    
        return $total_stock;
    }
}