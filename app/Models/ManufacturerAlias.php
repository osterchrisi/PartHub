<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ManufacturerAlias extends Model
{
    protected $fillable = [
        'manufacturer_id_fk',
        'manufacturer_alias_owner_u_fk',
        'alias',
        'alias_normalized',
    ];

    public function manufacturer()
    {
        return $this->belongsTo(Manufacturer::class, 'manufacturer_id_fk', 'manufacturer_id');
    }
}
