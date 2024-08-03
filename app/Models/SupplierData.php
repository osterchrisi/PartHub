<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SupplierData extends Model
{
    use HasFactory;

    private static $table_name = 'supplier_data';

    public function part()
    {
        return $this->belongsTo(Part::class, 'part_id_fk', 'part_id');
    }

    public function supplier()
    {
        return $this->belongsTo(Supplier::class, 'supplier_id_fk', 'supplier_id');
    }
}
