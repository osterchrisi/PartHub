<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BomRevisionElement extends Model
{
    use HasFactory;

    // BomRevisionElement.php
    public function part()
    {
        return $this->belongsTo(Part::class, 'part_id_fk', 'part_id');
    }

}
