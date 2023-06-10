<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;


class Location extends Model
{
    use HasFactory;
    protected $table = 'locations';
    protected $primaryKey = 'location_id';

    public function stockLevelEntries()
    {
        return $this->hasMany(StockLevel::class, 'location_id_fk');
    }

    public static function availableLocations($format = 'json')
    {

        $user = Auth::user();

        // Find all locations for a user_id
        $locations = Location::where('location_owner_u_fk', $user->id)
            ->get();

        // Return the location as JSON response (for JS)
        if ($format === 'json') {
            return $locations->toJson();
        }

        // Return as an array of associative arrays
        elseif ($format === 'array') {
            return $locations->toArray();
        }
    }

    public static function getLocationById($location_id)
    {
        return self::find($location_id);
    }

    public function getStockLevelEntries()
    {
        return $this->stockLevelEntries()->get();
    }
}