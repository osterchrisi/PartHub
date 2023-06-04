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
    public $timestamps = false;
    protected $fillable = [
        'bom_name',
        'bom_description'
    ];

    public function users()
    {
        return $this->belongsTo(User::class, 'bom_owner_u_fk');
    }

    public static function searchBoms($search_term)
    {
        $user_id = Auth::user()->id;

        $results = Bom::select('bom_id', 'bom_name', 'bom_description')
            ->where('bom_name', 'LIKE', '%' . $search_term . '%')
            ->where('bom_owner_u_fk', $user_id)
            ->get();
        // ->toArray();

        return $results;
    }

    public static function getBomNameAndDescription($bom_id)
    {
        return DB::table('boms')
            ->select('bom_name', 'bom_description')
            ->where('bom_id', $bom_id)
            ->get();
        // ->toArray();
    }

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