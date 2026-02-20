<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AlternativeGroup extends Model
{
    use HasFactory;

    protected $table = 'alternative_groups';

    protected $fillable = ['owner_u_fk'];

    public function parts()
    {
        // Not actually using owner_u_fk when reading but keeping it for potential future use (e.g. filtering alternatives by user ownership)
        return $this->belongsToMany(Part::class, 'alternative_group_elements', 'alternative_group_id', 'alternative_part_id')->withPivot(['id', 'owner_u_fk']);
    }
}
