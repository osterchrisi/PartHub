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
        // Select a limited set of data from the table, based on the current page and number of results per page, filtered by the search term and search column
        $query = DB::table('parts')
            ->join('part_categories', 'parts.part_category_fk', '=', 'part_categories.category_id')
            ->join('part_units', 'parts.part_unit_fk', '=', 'part_units.unit_id')
            // ->where('part_owner_u_fk', $user_id);
            ->where('part_owner_u_fk', $user_id);

        if ($search_column == 'everywhere') {
            // Search all columns
            $query->where(DB::raw("CONCAT_WS(' ', " . implode(", ", $column_names) . ")"), 'like', "%$search_term%");
        }
        else {
            // Search only the specified column
            $query->where($search_column, 'like', "%$search_term%");
        }

        if (!in_array('all', $search_category)) {
            // Make a list out of selected categories
            $cats_selected = implode(", ", $search_category);

            // Also select all sub-categories of those categories
            $query->whereIn('part_category_fk', function ($query) use ($cats_selected) {
                $query->select('category_id')
                    ->from('part_categories')
                    ->whereIn('category_id', explode(", ", $cats_selected))
                    ->orWhereIn('parent_category', explode(", ", $cats_selected));
            });
        }

        $parts = $query->select('*', 'part_id as id')->get();

        $parts = $parts->map(function ($part) {
            return (array) $part;
        })->toArray();
    

        // $answer = array('result' => $parts, 'query' => $query->toSql());
        // return $answer;
        return $parts;
    }


}