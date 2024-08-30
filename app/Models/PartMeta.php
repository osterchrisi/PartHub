<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class PartMeta extends Model
{
    use HasFactory;

    protected $table = 'part_meta';

    protected $primaryKey = 'meta_id';

    public function part()
    {
        return $this->belongsTo(Part::class, 'part_id_fk', 'part_id');
    }

    /**
     * Returns all available Metas, so they can be selected
     *
     * @param  mixed  $format
     * @return array|string
     */
    public static function availablePartMetas($format = 'json')
    {

        $user = Auth::user();

        // Find all meta infos for a user_id
        $metas = PartMeta::where('meta_owner_u_fk', $user->id)
            ->get();

        // Return the metas as JSON response (for JS)
        if ($format === 'json') {
            return $metas->toJson();
        }

        // Return as an array of associative arrays
        elseif ($format === 'array') {
            return $metas->toArray();
        }
    }

    public static function getPartMetaById($meta_id)
    {
        return self::find($meta_id);
    }

    //TODO Code function to return array / json with key, value pairs for a given meta

    public static function createPartMeta($footprint_name, $footprint_alias)
    //TODO: Code this
    {
        // $user_id = Auth::user()->id;

        // $footprint = new PartMeta();
        // $footprint->footprint_name = $footprint_name;
        // $footprint->footprint_alias = $footprint_alias;
        // $footprint->footprint_owner_u_fk = $user_id;
        // // $footprint->footprint_owner_g_fk = null;
        // $footprint->save();

        // $new_footprint_id = $footprint->footprint_id;

        // return $new_footprint_id;

    }
}
