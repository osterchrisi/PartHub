<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CategoryController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        $categories = Category::where('part_category_owner_u_fk', $user->id)
            ->with('children')
            ->get();

        return view('categories.categories',
                    ['categories' => $categories,
                    'title' => 'Categories',
                    'view' => 'categories']);
    }

    public function list(){
        return Category::availableCategories();
    }

    public function show($category_id)
    {
        $category = Category::getCategoryById($category_id);
        $parts = Category::getPartsByCategory($category_id);
        $parts_with_category = [];
        foreach ($parts as $part) {
            $parts_with_category[] = ['part_name' => $part->part_name,
                                      'part_id' => $part->part_id];
        }
        // $parts_with_category[] = ['id' => $category_id];
        return view(
            'categories.showCategory',
            [
                'category_name' => $category->category_name,
                // 'category_alias' => $category->category_alias,
                // Tabs Settings
                'tabId1' => 'info',
                'tabText1' => 'Info',
                'tabToggleId1' => 'categoryInfo',
                'tabId2' => 'history',
                'tabText2' => 'Category History',
                'tabToggleId2' => 'categoryHistory',
                // 'Parts with Category' table
                'parts_with_category' => $parts_with_category,
                'db_columns' => ['part_name', 'part_id'],
                'nice_columns' => ['Part', 'ID']
            ]
        );
    }
}
