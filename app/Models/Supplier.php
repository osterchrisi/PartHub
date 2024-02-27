<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;


class Supplier extends Model
{
    use HasFactory;
    protected $table = 'suppliers';
    protected $primaryKey = 'supplier_id';

    public function part()
    {
        return $this->belongsTo(Part::class, 'part_supplier_fk', 'part_id');
    }

    public static function availableSuppliers($format = 'json')
    {

        $user = Auth::user();

        // Find all suppliers for a user_id
        $suppliers = Supplier::where('supplier_owner_u_fk', $user->id)
            ->get();

        // Return the supplier as JSON response (for JS)
        if ($format === 'json') {
            return $suppliers->toJson();
        }

        // Return as an array of associative arrays
        elseif ($format === 'array') {
            return $suppliers->toArray();
        }
    }

    public static function getSupplierById($supplier_id)
    {
        return self::find($supplier_id);
    }

    public static function getPartsBySupplier($supplier_id)
    {
        return Part::where('part_supplier_fk', $supplier_id)->get();
    }

    public static function createSupplier($supplier_name)
    {
        $user_id = Auth::user()->id;

        $supplier = new Supplier();
        $supplier->supplier_name = $supplier_name;
        $supplier->supplier_owner_u_fk = $user_id;
        // $supplier->supplier_owner_g_fk = null;
        $supplier->save();

        $new_supplier_id = $supplier->supplier_id;

        return $new_supplier_id;
        
    }
}
