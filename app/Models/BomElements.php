<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * BomElements (LEGACY)
 *
 * Legacy mutable BOM line table used before revisioned BOMs existed.
 * Still written to by the editor during transition, but no longer read.
 *
 * Scheduled for removal at some point in the future.
 */
class BomElements extends Model
{
    use HasFactory;

    protected $table = 'bom_elements';

    protected $primaryKey = 'bom_elements_id';

    protected $fillable = [
        'bom_id_fk',
        'part_id_fk',
        'element_quantity',
        'bom_element_owner_u_fk',
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
