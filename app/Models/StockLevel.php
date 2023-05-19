<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StockLevel extends Model
{
    use HasFactory;
    protected $table = 'stock_levels';
    protected $primaryKey = 'stock_level_id';

    public function part()
    {
        return $this->belongsTo(Part::class, 'part_id_fk', 'part_id');
    }

    public function locations()
    {
        return $this->belongsTo(Location::class, 'location_id_fk', 'location_id');
    }

    public function getStockLevelsByPartID($part_id)
    {
        return $this->join('locations', 'stock_levels.location_id_fk', '=', 'locations.location_id')
            ->select('location_id', 'location_name', 'stock_level_quantity')
            ->where('part_id_fk', $part_id)
            //->where('stock_level_quantity', '>', 0) // Only for locations with stock
            ->get();
    }

}