<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StockLevelHistory extends Model
{
    use HasFactory;

    protected $table = 'stock_level_change_history';
    protected $primaryKey = 'stock_lvl_chng_id';

    // Need relationships here with Locations, Parts and Users
}
