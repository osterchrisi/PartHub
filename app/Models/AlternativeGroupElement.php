<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AlternativeGroupElement extends Model
{
    use HasFactory;

    protected $table = 'alternative_group_elements';

    protected $fillable = ['alternative_part_id'];
}
