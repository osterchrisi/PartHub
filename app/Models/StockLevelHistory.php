<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StockLevelHistory extends Model
{
    use HasFactory;

    protected $table = 'stock_level_change_history';
    protected $primaryKey = 'stock_lvl_chng_id';

    public function part()
    {
        return $this->belongsTo(Part::class, 'part_id_fk', 'part_id');
    }

    public function stockLevelsFrom()
    {
        return $this->hasMany(StockLevel::class, 'from_location_fk', 'location_id');
    }

    public function stockLevelsTo()
    {
        return $this->hasMany(StockLevel::class, 'to_location_fk', 'location_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'stock_lvl_chng_user_fk', 'user_id');
    }

}