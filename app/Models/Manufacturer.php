<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Manufacturer extends Model
{
    use HasFactory;

    protected $table = 'manufacturers';
    protected $primaryKey = 'manufacturer_id';

    protected $fillable = [
        'manufacturer_name',
        'manufacturer_owner_u_fk',
        'website',
        'phone',
        'email',
        'contact_name',
    ];

    public function parts()
    {
        return $this->hasMany(Part::class, 'part_manufacturer_fk', 'manufacturer_id');
    }
}
