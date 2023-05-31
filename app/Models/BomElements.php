<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class BomElements extends Model
{
    use HasFactory;

    protected $table = 'bom_elements';
    protected $primaryKey = 'bom_elements_id';

    public function part()
    {
        return $this->hasMany(Part::class, 'part_id_fk');
    }

    public static function getBomElements($bom_id)
    {
        $elements = DB::table('bom_elements')
            ->join('parts', 'part_id_fk', '=', 'parts.part_id')
            ->select('part_name', 'element_quantity', 'part_id', 'bom_elements_id')
            ->where('bom_id_fk', $bom_id)
            ->get()
            ->toArray();

        return $elements;
    }

}