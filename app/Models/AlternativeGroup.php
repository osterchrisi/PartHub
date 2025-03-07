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
        return $this->belongsToMany(Part::class, 'alternative_group_elements', 'alternative_group_id', 'alternative_part_id')->withPivot('id');
    }

    public function alternativeParts()
    {
        return $this->belongsToMany(Part::class, 'alternative_group_elements', 'alternative_group_id', 'alternative_part_id');
    }
}
