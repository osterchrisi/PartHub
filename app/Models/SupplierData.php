<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SupplierData extends Model
{
    use HasFactory;

    protected $fillable = [
        'part_id_fk',
        'supplier_id_fk',
        'URL',
        'SPN',
        'price',
        'supplier_data_owner_u_fk',
        'supplier_data_owner_g_fk',
    ];

    public function part()
    {
        return $this->belongsTo(Part::class, 'part_id_fk', 'part_id');
    }

    public function supplier()
    {
        return $this->belongsTo(Supplier::class, 'supplier_id_fk', 'supplier_id');
    }
}
