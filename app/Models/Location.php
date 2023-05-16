<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

use App\Models\User;
use Illuminate\Http\Response;


class Location extends Model
{
    use HasFactory;
    protected $table = 'locations';
    protected $primaryKey = 'location_id';

    public static function availableLocations($user_id)
    {

        $user = Auth::user();

        // Find the location with the given id and user_id
        $locations = Location::where('location_owner_u_fk', $user->id)
            ->get();

        // Return the location as JSON response
        return $locations->toJson();
        // return response()->json($locations->toArray());
    }
}