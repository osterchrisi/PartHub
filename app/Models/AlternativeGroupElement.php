<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AlternativeGroupElement extends Model
{
    use HasFactory;

    // This model represents the pivot table 'alternative_group_elements' which links parts to alternative groups.

    protected $table = 'alternative_group_elements';

    protected $fillable = ['alternative_group_id', 'alternative_part_id', 'owner_u_fk'];

}
