<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;


class Category extends Model
{
    use HasFactory;

    protected $table = 'part_categories';
    protected $primaryKey = 'category_id';

    protected $fillable = ['category_name', 'parent_category'];

    public function part()
    {
        return $this->belongsTo(Part::class, 'part_category_fk', 'part_id');
    }


    public function parent()
    {
        return $this->belongsTo(Category::class, 'parent_category');
    }

    public function children()
    {
        return $this->hasMany(Category::class, 'parent_category');
    }

    public function owner()
    {
        return $this->belongsTo(User::class, 'part_category_owner_u_fk');
    }

    public static function availableCategories($format = 'json')
    {

        $user = Auth::user();

        // Find the category with the given id and user_id
        $categories = Category::where('part_category_owner_u_fk', $user->id)
            ->get();

        // Return the category as JSON response (for JS)
        if ($format === 'json') {
            return $categories->toJson();
        }

        // Return as an array of associative arrays
        elseif ($format === 'array') {
            return $categories->toArray();
        }
    }

    public static function getCategoryById($category_id)
    {
        return self::find($category_id);
    }

    public static function getPartsByCategory($category_id)
    {
        return Part::where('part_category_fk', $category_id)->get();
    }

}