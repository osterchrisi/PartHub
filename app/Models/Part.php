<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Part extends Model
{
    protected $table = 'parts';

    public function category()
    {
        return $this->belongsTo(Category::class, 'part_category_fk');
    }

    public function unit()
    {
        return $this->belongsTo(Unit::class, 'part_unit_fk');
    }
}
