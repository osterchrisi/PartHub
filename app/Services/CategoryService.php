<?php

namespace App\Services;

use App\Models\Category;
use Illuminate\Support\Facades\Auth;

class CategoryService
{
    /**
     * Extracts category IDs from a JSON-encoded array and returns a simple array of digits.
     *
     * The input array is expected to be in the format [[3], [5]] due to the limitations of selectizing
     * the multi-select input field. This function decodes the JSON-encoded array and extracts the
     * numeric values, returning a simplified array of category IDs.
     *
     * @param  array  $searchCategory  The array containing the JSON-encoded category IDs.
     * @return array The simplified array of category IDs as digits.
     */
    public function extractCategoryIds($searchCategory)
    {
        if (! in_array('all', $searchCategory)) {
            $catIds = [];

            foreach ($searchCategory as $catArray) {
                $decodedArray = json_decode($catArray);

                foreach ($decodedArray as $element) {
                    $catIds[] = $element[0];
                }
            }

            return $catIds;
        }

        return $searchCategory;
    }

    public function categoriesForCategoryTable($user_id)
    {
        $categories = Category::where('part_category_owner_u_fk', $user_id)
            ->with('children')
            ->orderBy('category_name', 'asc')
            ->get();

        return $categories;
    }

    public static function createNewRootCategory()
    {
        $user = Auth::user();

        $category = new Category();

        // Assign values to the category attributes
        $category->category_name = 'All Parts';
        $category->parent_category = '0';
        $category->part_category_owner_u_fk = $user->id;

        $category->save();

        $new_category_id = $category->category_id;

        return $new_category_id;

    }

    public function findRootCategory($user_id)
    {
        return Category::where('part_category_owner_u_fk', $user_id)
            ->where('parent_category', 0)
            ->first();

    }
}
