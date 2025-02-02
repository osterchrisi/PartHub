<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class Part extends Model
{
    protected $table = 'parts';

    protected $primaryKey = 'part_id';

    public function category()
    {
        return $this->belongsTo(Category::class, 'part_category_fk');
    }

    public function footprint()
    {
        return $this->belongsTo(Footprint::class, 'part_footprint_fk');
    }

    public function supplier()
    {
        return $this->belongsTo(Supplier::class, 'part_supplier_fk');
    }

    public function unit()
    {
        return $this->belongsTo(Unit::class, 'part_unit_fk');
    }

    public function stockLevels()
    {
        return $this->hasMany(StockLevel::class, 'part_id_fk');
    }

    public function bomElements()
    {
        return $this->hasMany(BomElements::class, 'part_id_fk');
    }

    public function alternatives()
    {
        return $this->belongsToMany(
            Part::class,
            'alternative_parts',
            'part_id', // Foreign key on the pivot table
            'alternative_part_id' // Related key
        )->withPivot('id');
    }


    private static $column_names = [
        'part_id',
        'part_name',
        'part_description',
        'part_comment',
        'created_at',
        'part_category_fk',
        'part_footprint_fk',
        'part_unit_fk',
        'part_owner_u_fk',
        'part_owner_g_fk',
        'stocklevel_notification_threshold', // Total Stock Minimum Quantity
    ];

    public static function getColumnNames()
    {
        return self::$column_names;
    }

    public static function queryParts($search_column, $search_term, $column_names, $search_category, $user_id)
    {
        $query = Part::query()->where('part_owner_u_fk', $user_id);

        // Search Column
        if ($search_column == 'everywhere') {
            // All columns (standard)
            $query->where(function ($query) use ($column_names, $search_term) {
                foreach ($column_names as $column) {
                    $query->orWhere($column, 'like', "%$search_term%");
                }
            });
        }
        else {
            // Only in specified colum (single)
            $query->where($search_column, 'like', "%$search_term%");
        }

        // Filter for categories
        if (!in_array('all', $search_category)) {
            $query->whereHas('category', function ($query) use ($search_category) {
                $query->whereIn('category_id', $search_category)
                    ->orWhereIn('parent_category', $search_category);
            });
        }

        $parts = $query->with('category', 'unit', 'stockLevels', 'footprint', 'supplier')->get()->toArray();

        // dd($parts);
        return $parts;
    }

    public static function getBomsContainingPart($part_id)
    {
        $part = Part::find($part_id);
        if (!$part) {
            return [];
        }

        $bom_list = $part->bomElements()
            ->join('boms', 'bom_elements.bom_id_fk', '=', 'boms.bom_id')
            ->get()
            ->toArray();

        return $bom_list;
    }

    public static function createPart($part_name, $comment, $description, $footprint, $category, $supplier, $min_quantity)
    {
        $user_id = Auth::user()->id;

        $part = new Part();
        $part->part_name = $part_name;
        $part->part_description = $description;
        $part->part_comment = $comment;
        $part->part_category_fk = $category;
        $part->part_footprint_fk = $footprint;
        $part->part_supplier_fk = $supplier;
        $part->stocklevel_notification_threshold = $min_quantity;   // Total Stock Minimum Quantity
        $part->part_unit_fk = null;
        $part->part_owner_u_fk = $user_id;
        $part->part_owner_g_fk = null;
        $part->save();

        $new_part_id = $part->part_id;

        return $new_part_id;

    }
}
