<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class StockLevelHistory extends Model
{
    use HasFactory;

    protected $table = 'stock_level_change_history';

    protected $primaryKey = 'stock_lvl_chng_id';

    protected $fillable = [
        'part_id_fk',
        'from_location_fk',
        'to_location_fk',
        'stock_lvl_chng_quantity',
        'stock_lvl_chng_timestamp',
        'stock_lvl_chng_comment',
        'stock_lvl_chng_user_fk',
    ];

    protected $casts = [
        'stock_lvl_chng_timestamp' => 'datetime',
    ];

    public function part()
    {
        return $this->belongsTo(Part::class, 'part_id_fk', 'part_id');
    }

    public function fromLocation()
    {
        return $this->belongsTo(Location::class, 'from_location_fk', 'location_id');
    }
    
    public function toLocation()
    {
        return $this->belongsTo(Location::class, 'to_location_fk', 'location_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'stock_lvl_chng_user_fk', 'id');
    }

    public static function createStockLevelHistoryRecord($part_id, $from_location, $to_location, $quantity, $comment, $user_id)
    {
        $newId = DB::table('stock_level_change_history')->insertGetId([
            'part_id_fk' => $part_id,
            'from_location_fk' => $from_location,
            'to_location_fk' => $to_location,
            'stock_lvl_chng_quantity' => $quantity,
            'stock_lvl_chng_timestamp' => now()->timezone('UTC'),
            'stock_lvl_chng_comment' => $comment,
            'stock_lvl_chng_user_fk' => $user_id,
        ]);

        return $newId;
    }

    public static function getPartStockHistory($part_id)
    {
        return self::with(['fromLocation', 'toLocation', 'user'])
            ->where('part_id_fk', $part_id)
            ->get();
    }
    
}
