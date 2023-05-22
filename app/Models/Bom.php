<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class Bom extends Model
{
    use HasFactory;

    protected $table = 'boms';
    protected $primaryKey = 'bom_id';
    public $timestamps = false;

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
            ->get()
            ->toArray();

        return $results;
    }

}