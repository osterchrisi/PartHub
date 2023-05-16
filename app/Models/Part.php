<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;


class Part extends Model
{
    protected $table = 'parts';
    protected $primaryKey = 'part_id';

    private static $column_names = array(
        'part_id',
        'part_name',
        'part_description',
        'part_comment',
        'created_at',
        'part_category_fk',
        'part_footprint_fk',
        'part_unit_fk',
        'part_owner_u_fk',
        'part_owner_g_fk'
    );


    public function category()
    {
        return $this->belongsTo(Category::class, 'part_category_fk');
    }

    public function unit()
    {
        return $this->belongsTo(Unit::class, 'part_unit_fk');
    }

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

    $parts = $query->with('category', 'unit')->get()->toArray();

    return $parts;
}

    


}