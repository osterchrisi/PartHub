<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CategoryController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();

        $categories = Category::where('part_category_owner_u_fk', $user->id)
            ->with('children')
            ->get();

        $route = $request->route()->getName();
        if ($route == 'categories') {
            return view(
                'categories.categories',
                [
                    'categoriesForCategoriesTable' => $categories,
                    'title' => 'Categories',
                    'view' => 'categories',
                ]
            );
        } elseif ($route == 'categories.categoriesTable') {
            return view(
                'categories.categoriesTable',
                [
                    'categoriesForCategoriesTable' => $categories,
                ]
            );
        }
    }

    public function list()
    {
        return Category::availableCategories();
    }

    public function show($category_id)
    {
        $category = Category::getCategoryById($category_id);
        $parts = Category::getPartsByCategory($category_id);
        $parts_with_category = [];
        foreach ($parts as $part) {
            $parts_with_category[] = [
                'part_name' => $part->part_name,
                'part_id' => $part->part_id,
            ];
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
                'nice_columns' => ['Part', 'ID'],
            ]
        );
    }

    public function create()
    {
        $user = Auth::user();

        // Create a new category instance
        $category = new Category();

        // Assign values to the category attributes
        $category->category_name = request()->input('category_name');
        $category->parent_category = request()->input('parent_category');
        $category->part_category_owner_u_fk = $user->id;

        if ($category->save()) {
            $categoryId = $category->category_id;

            // Construct JSON response
            $response = [
                'Category ID' => $categoryId,
                'status' => 'success',
            ];

            return response()->json($response);

        } else {
            $errorResponse = [
                'status' => 'error',
            ];

            // Return error JSON response with HTTP status code 500 (Internal Server Error)
            return response()->json($errorResponse, 500);
        }
    }
}
