<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * BomRevisionElement
 *
 * Line item belonging to a specific BOM revision.
 * Represents the exact structure that was built or assembled.
 *
 * Never edited in place once created.
 */
class BomRevisionElement extends Model
{
    use HasFactory;

    // BomRevisionElement.php
    public function part()
    {
        return $this->belongsTo(Part::class, 'part_id_fk', 'part_id');
    }

}
