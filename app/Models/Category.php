<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    use HasFactory;

    protected $table = 'part_categories';
    protected $primaryKey = 'category_id';

    public function parts()
    {
        return $this->hasMany(Part::class, 'part_category_fk');
    }
}
