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

    protected $fillable = [
        'bom_id_fk',
        'part_id_fk',
        'element_quantity',
    ];

    public function part()
    {
        return $this->belongsTo(Part::class, 'part_id_fk');
    }

    public function bom()
    {
        return $this->belongsTo(Bom::class, 'bom_id_fk');
    }

    public static function getBomElements($bom_id)
    {
        return self::with('part')
            ->where('bom_id_fk', $bom_id)
            ->get();
    }
}
