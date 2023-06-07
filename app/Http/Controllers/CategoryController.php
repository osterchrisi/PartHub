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
            ->with('children') // Eager load the child categories
            ->get();

        return view('categories.categories',
                    ['categories' => $categories,
                    'title' => 'Categories',
                    'view' => 'categories']);
    }

    public function list(){
        return Category::availableCategories();
    }
}
