<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;


class Location extends Model
{
    use HasFactory;
    protected $table = 'locations';
    protected $primaryKey = 'location_id';

    public static function availableLocations()
    {

        $user = Auth::user();

        // Find all locations for a user_id
        $locations = Location::where('location_owner_u_fk', $user->id)
            ->get();

        // Return the location as JSON response
        return $locations->toJson();
    }
}