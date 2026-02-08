<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BomRevision extends Model
{
    use HasFactory;

    public function elements()
    {
        return $this->hasMany(BomRevisionElement::class, 'bom_revision_id_fk', 'bom_revision_id');
    }

}
