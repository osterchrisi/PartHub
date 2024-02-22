<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class Bom extends Model
{
    use HasFactory;

    protected $table = 'boms';
    protected $primaryKey = 'bom_id';
    protected $fillable = [
        'bom_name',
        'bom_description'
    ];

    public function users()
    {
        return $this->belongsTo(User::class, 'bom_owner_u_fk');
    }

    /**
     * Search for BOMs based on a search term, limited to the BOMs owned by the authenticated user.
     *
     * @param string $search_term The term to search for within BOM names.
     * @return Illuminate\Database\Eloquent\Collection The collection of BOMs matching the search term and owned by the user.
     */
    public static function searchBoms($search_term)
    {
        $user_id = Auth::user()->id;

        $results = Bom::select('bom_id', 'bom_name', 'bom_description')
            ->where('bom_name', 'LIKE', '%' . $search_term . '%')
            ->where('bom_owner_u_fk', $user_id)
            ->get();

        return $results;
    }

    /**
     * Retrieve a BOM record by its ID.
     *
     * @param int $bom_id The ID of the BOM to retrieve.
     * @return \Illuminate\Support\Collection|static[] The BOM record matching the given ID.
     */
    public static function getBomById($bom_id)
    {
        return DB::table('boms')
            ->where('bom_id', $bom_id)
            ->get();
    }

    /**
     * Create a new BOM with the given name and description.
     *
     * @param string $bom_name The name of the BOM.
     * @param string $bom_description The description of the BOM.
     * @return int The ID of the newly created BOM.
     */
    public static function createBom($bom_name, $bom_description)
    {
        $bom = new Bom([
            'bom_name' => $bom_name,
            'bom_description' => $bom_description
        ]);
        $bom->bom_owner_u_fk = auth()->user()->id;
        $bom->save();

        $bom_id = $bom->bom_id;

        return $bom_id;
    }

}