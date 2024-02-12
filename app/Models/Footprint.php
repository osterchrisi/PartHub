<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;


class Footprint extends Model
{
    use HasFactory;
    protected $table = 'footprints';
    protected $primaryKey = 'footprint_id';

    public function part()
    {
        return $this->belongsTo(Part::class, 'part_footprint_fk', 'part_id');
    }

    public static function availableFootprints($format = 'json')
    {

        $user = Auth::user();

        // Find all footprints for a user_id
        $footprints = Footprint::where('footprint_owner_u_fk', $user->id)
            ->get();

        // Return the footprint as JSON response (for JS)
        if ($format === 'json') {
            return $footprints->toJson();
        }

        // Return as an array of associative arrays
        elseif ($format === 'array') {
            return $footprints->toArray();
        }
    }

    public static function getFootprintById($footprint_id)
    {
        return self::find($footprint_id);
    }

    public static function getPartsByFootprint($footprint_id)
    {
        return Part::where('part_footprint_fk', $footprint_id)->get();
    }
}
