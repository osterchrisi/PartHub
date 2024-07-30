<?php

namespace App\Services;

use App\Models\Category;

class CategoryService
{
    /**
     * Extracts category IDs from a JSON-encoded array and returns a simple array of digits.
     *
     * The input array is expected to be in the format [[3], [5]] due to the limitations of selectizing
     * the multi-select input field. This function decodes the JSON-encoded array and extracts the
     * numeric values, returning a simplified array of category IDs.
     *
     * @param array $searchCategory The array containing the JSON-encoded category IDs.
     * @return array The simplified array of category IDs as digits.
     */
    public function extractCategoryIds($searchCategory)
    {
        if (!in_array('all', $searchCategory)) {
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

}