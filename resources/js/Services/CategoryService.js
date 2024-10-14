export { CategoryService }

class CategoryService {
    constructor() {

    }

    /**
  * Fabricate array of category names matching the given category ID and its children.
  * This array is suited to work with bootstrap-tables' filter algorithm
  * @param {*} categories 
  * @param {*} categoryId 
  * @returns 
  */
    static getChildCategoriesNames(categories, categoryId) {
        // Initialize an array to store matching category names
        let childCategoriesNames = [];

        // Find the category name corresponding to the provided category ID
        const category = categories.find(cat => cat.category_id === categoryId);
        if (category) {
            childCategoriesNames.push(category.category_name);
        }

        // Helper function to recursively find child categories
        function findChildCategoriesNames(parentCategoryId) {
            // Find categories whose parent category matches the given category ID
            const children = categories.filter(category => category.parent_category === parentCategoryId);

            // Add the names of found children to the result array
            children.forEach(child => {
                childCategoriesNames.push(child.category_name);
                // Recursively find children of children
                findChildCategoriesNames(child.category_id);
            });
        }

        // Find child categories starting from the given category ID
        findChildCategoriesNames(categoryId);

        return childCategoriesNames;
    }

    /**
     * Recursively find child categories in case a user wants to delete a category and it has child categories.
     * @param {string} parentId Category ID of the clicked category.
     * @returns {Array} Array of category IDs including the clicked category and its recursive child categories.
     */
    static findChildCategories(parentId) {
        var categoryIds = [parentId];

        function findChildren(parentId) {
            $('#categories_list_table tbody tr').each(function () {
                var $currentRow = $(this);
                var currentParentId = $currentRow.data('parent-id');
                if (currentParentId === parentId) {
                    var childCategoryId = $currentRow.data('id');
                    categoryIds.push(childCategoryId);
                    // Recursively find child categories of this child category
                    findChildren(childCategoryId);
                }
            });
        }

        findChildren(parentId);
        return categoryIds;
    }

}