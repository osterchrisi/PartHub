<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BomElements extends Model
{
    use HasFactory;

    protected $table = 'bom_elements';
    protected $primaryKey = 'bom_elements_id';

    public function part()
    {
        //! Pretty sure this should be hasMany
        return $this->belongsTo(Part::class, 'part_id_fk');
    }
}