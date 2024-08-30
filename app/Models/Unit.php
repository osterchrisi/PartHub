<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Unit extends Model
{
    use HasFactory;

    protected $table = 'part_units';

    protected $primaryKey = 'unit_id';

    public function parts()
    {
        return $this->hasMany(Part::class, 'part_unit_fk');
    }
}
