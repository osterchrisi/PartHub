export { CategoryTableManager }
import { TableManager } from "./TableManager";
import { saveLayoutSettings } from "./custom";

class CategoryTableManager extends TableManager {
    constructor(options) {
        super(options);
        this.attachShowCategoriesButtonClickListener();
    }

    bootstrapTable() {
        this.bootstrapCategoriesListTable(); // Custom bootstrap logic for the category table
        this.hideEditColumn();
    }

    rebuildTable(queryString = '', postRebuildCallback = null) {
        super.rebuildTable(queryString, postRebuildCallback)
            .done(() => {
                // Only run this additional code after the parent rebuildTable is done
                $.ajax({
                    url: '/categories.get',
                    dataType: 'json',
                    error: function (error) {
                        console.log(error);
                    }
                }).done(categories => {
                    this.defineActions(categories);
                });
            });
    }

    hideEditColumn() {
        $('#categories_list_table th[data-field="category_edit"], #categories_list_table td[data-field="category_edit"]').hide();
    }

    defineActions(categories) {
        super.defineActions(categories);
    }

    bootstrapCategoriesListTable(treeColumn = 1) {
        this.$table.bootstrapTable({
            rootParentId: '0',
            onPostBody: () => {
                // Treegrid
                this.$table.treegrid({
                    treeColumn: treeColumn,
                });

                // Edit toggle button click listener
                this.attachEditCategoriesButtonClickListener();

                //TODO: Use this structure to trigger Deletion / Adding of Categories
                // Attach click listeners to edit buttons
                this.$table.on('click', 'tbody .edit-button', () => {
                    // Get the parent <tr> element
                    var $row = $(this).closest('tr');
                    // Extract the data attributes from the <tr> element
                    var parentId = $row.data('parent-id');
                    var categoryId = $row.data('id');
                    // Extract the action from the clicked icon's data attribute
                    var action = $(this).data('action');
                });

                //* Delete Category
                //TODO: This info should be encoded into the HTML table like with my other tables
                this.$table.on('click', 'tbody .trash-button', () => {
                    var $row = $(this).closest('tr');
                    var categoryId = $row.data('id');

                    // Find child categories recursively and return an array of category IDs
                    var categoryIds = this.findChildCategories(categoryId);
                    console.log("$row = ", $row);
                    console.log("categoryId = ", categoryId);
                    console.log("categoryIds = ", categoryIds);
                    this.deleteRows(categoryIds, 'part_categories', 'category_id');
                    // deleteSelectedRows(categoryIds, 'part_categories', 'category_id', this.rebuildTable);
                });

                //* Add Category
                //! There used to be a resourceCreator here but I kept running into issues, so hardcoded it for now
                this.$table.on('click', 'tbody .addcat-button', () => {
                    const $row = $(this).closest('tr');
                    const parentCategoryId = $row.data('id'); // Get the parent category ID from the row

                    // Show modal for category entry
                    $('#mCategoryEntry').modal('show');

                    // Set the parent category ID in a hidden field in the modal
                    $('#parentCategoryId').val(parentCategoryId);
                });

                $('#addCategory').off().on('click', () => {
                    const categoryName = $('#addCategoryName').val();
                    const parentCategoryId = $('#parentCategoryId').val();
                    const token = $('input[name="_token"]').attr('value'); // CSRF token

                    // Ensure that the category name is not empty
                    if (!categoryName) {
                        alert("Category name cannot be empty!");
                        return;
                    }

                    // Make AJAX request to create the new category
                    $.ajax({
                        url: '/category.create',
                        type: 'POST',
                        data: {
                            _token: token,
                            category_name: categoryName,
                            parent_category: parentCategoryId,
                            type: 'category'
                        },
                        success: function (response) {
                            // Close modal on success
                            $('#mCategoryEntry').modal('hide');

                            // Clear the input fields for next time
                            $('#addCategoryName').val('');
                            $('#parentCategoryId').val('');

                            // Rebuild the categories table after creation
                            this.rebuildTable();
                            const partsTable = new TableManager({ type: 'part'});
                            partsTable.rebuildTable();
                        },
                        error: function (xhr) {
                            alert("Error creating category. Please try again.");
                        }
                    });
                });

                // Initial state is collapsed
                let isCollapsed = false;

                // Toggle Expand/Collapse on button click
                $('#category-toggle-collapse-expand').click( () => {
                    if (isCollapsed) {
                        this.$table.treegrid('expandAll');
                        $(this).text('Toggle');
                    } else {
                        this.$table.treegrid('collapseAll');
                        $(this).text('Toggle');
                    }
                    isCollapsed = !isCollapsed; // Toggle the state
                });
            }
        });
    };

    /**
 * Attach the click listener to the "Edit Categories" button. The button toggles the visibility of the Categories Edit column
 */
    attachEditCategoriesButtonClickListener() {
        $('#cat-edit-btn').off('click').on('click', function () {
            var columnIndex = 0;
            $('#categories_list_table th[data-field="category_edit"], #categories_list_table td[data-field="category_edit"]').toggle();
        });
    }

    /**
     * Attach the click listener to the "Toggle Categories" button. The button toggles the visibility of the Categories div in the parts view
     */
    attachShowCategoriesButtonClickListener() {
        $('#cat-show-btn').off('click').on('click', function () {
            $('#category-window-container').toggle();
            saveLayoutSettings(); // Save visibility after toggling
        });
    }

    /**
   * Fabricate array of category names matching the given category ID and its children.
   * This array is suited to work with bootstrap-tables' filter algorithm
   * @param {*} categories 
   * @param {*} categoryId 
   * @returns 
   */
    getChildCategoriesNames(categories, categoryId) {
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
    findChildCategories(parentId) {
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

    attachShowCategoriesButtonClickListener() {
        $('#cat-show-btn').off('click').on('click', function () {
          $('#category-window-container').toggle();
          saveLayoutSettings(); // Save visibility after toggling
        });
      }
}
