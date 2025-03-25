<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PartUnit extends Model
{
    use HasFactory;

    protected $table = 'part_units';
    protected $primaryKey = 'unit_id';

    protected $fillable = [
        'unit_name',
    ];
}
