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

    public static function availableCategories()
    {

        $user = Auth::user();

        // Find the category with the given id and user_id
        $categories = Category::where('part_category_owner_u_fk', $user->id)
            ->get();

        // Return the categories as JSON response
        return $categories->toArray();
    }
}