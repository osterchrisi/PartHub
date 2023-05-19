<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StockLevel extends Model
{
    use HasFactory;
    protected $table = 'stock_levels';
    protected $primaryKey = 'stock_level_id';
    protected $fillable = ['stock_level_quantity'];
    public $timestamps = false;



    public function part()
    {
        return $this->belongsTo(Part::class, 'part_id_fk', 'part_id');
    }

    public function locations()
    {
        return $this->belongsTo(Location::class, 'location_id_fk', 'location_id');
    }

    public static function getStockLevelsByPartID($part_id)
    {
        return self::join('locations', 'stock_levels.location_id_fk', '=', 'locations.location_id')
            ->select('location_id', 'location_name', 'stock_level_quantity')
            ->where('part_id_fk', $part_id)
            //->where('stock_level_quantity', '>', 0) // Only for locations with stock
            ->get();
        // ->collect();
    }


    public static function getStockInLocation($stock_levels, $location)
    {
        foreach ($stock_levels as $entry) {
            if (isset($entry['location_id']) && $entry['location_id'] == $location) {
                $current_stock_level = $entry['stock_level_quantity'];
                return $current_stock_level;
            }
            else {
                $current_stock_level = 0;
            }
        }

        return $current_stock_level;
    }

    public static function changeQuantity($part_id, $quantity, $to_location)
    {
        $stockLevel = self::updateOrCreate(
            [
                'part_id_fk' => $part_id,
                'location_id_fk' => $to_location,
            ],
            [
                'stock_level_quantity' => $quantity,
            ]
        );

        return $stockLevel->stock_level_id;
    }


}