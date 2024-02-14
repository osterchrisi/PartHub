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
    public $timestamps = false;


    public function stockLevelEntries()
    {
        return $this->hasMany(StockLevel::class, 'location_id_fk');
    }

    public function users()
    {
        return $this->belongsTo(User::class, 'location_owner_u_fk');
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
        return $this->stockLevelEntries()->with('part')->get();
    }

    public static function createLocation($location_name, $location_description)
    {
        $user_id = Auth::user()->id;

        $location = new Location();
        $location->location_name = $location_name;
        $location->location_description = $location_description;
        $location->location_owner_u_fk = $user_id;
        $location->location_owner_g_fk = null;
        $location->save();

        $new_location_id = $location->location_id;

        return $new_location_id;
        
    }
}