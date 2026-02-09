<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * BomRevision
 *
 * Immutable snapshot of a BOM at a specific point in time.
 * Each save operation creates a new revision.
 * `created_at` is the authoritative revision timestamp.
 */
class BomRevision extends Model
{
    use HasFactory;
    protected $table = 'bom_revisions';
    protected $primaryKey = 'bom_revision_id';

    protected $fillable = [
        'bom_id_fk',
        'label',
        'note',
        'created_by_u_fk',
    ];

    public function elements()
    {
        return $this->hasMany(BomRevisionElement::class, 'bom_revision_id_fk', 'bom_revision_id');
    }

}
