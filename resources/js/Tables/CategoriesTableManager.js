export { CategoryTableManager }
import { CategoryService } from "../Services/CategoryService";
import { TableManager } from "./TableManager";
import { DataFetchService } from "../Services/DataFetchService";
import { Layout } from "../User Interface/Layout";

/**
 * The CategoryTableManager is special because:
 * - It uses treegrid on the bootstrap method
 * - It can dynamically add categories depending on which row was clicked (defines parent category)
 * - It therefor needs its own CategoryCreator
 */
class CategoryTableManager extends TableManager {
    constructor(options) {
        super(options);
        this.attachShowCategoriesButtonClickListener();

        DataFetchService.getCategories()
            .done(categories => {
                this.categories = categories;
                this.defineActions();
            })
            .fail(error => {
                console.error(error);
            });

        // Needs a Category Creator. If not passed via the concstructor, ends up in an import loop
        this.categoryCreator = options.resourceCreator;
    }

    bootstrapTable() {
        this.bootstrapCategoriesListTable(); // Custom bootstrap logic for the category table
        this.hideEditColumn();
    }

    rebuildTable(queryString = '', postRebuildCallback = null) {
        super.rebuildTable(queryString, postRebuildCallback)
            .done(() => {
                // Only run this additional code after the parent rebuildTable is done
                DataFetchService.getCategories()
                    .done(categories => {
                        this.categories = categories;
                        this.defineActions();
                    })
                    .fail(error => {
                        console.error(error);
                    });
            });
    }

    hideEditColumn() {
        this.$table.find('[data-field="category_edit"]').hide();
    }

    defineActions() {
        super.defineActions();
    }

    bootstrapCategoriesListTable(treeColumn = 1) {
        this.$table.bootstrapTable({
            rootParentId: '0',
            onPostBody: () => {
                // Treegrid
                this.$table.treegrid({
                    treeColumn: treeColumn,
                });

                this.restructureTreegridCells();
                this.attachEditCategoriesButtonClickListener();
                this.attachCategorySpecificListeners();
            }
        });
    };

    /**
     * Restructures the editable-category cells after the layout gets lost
     * due to treegrid'ing the table.
     */
    restructureTreegridCells() {
        // Target only the cells within the current table instance
        this.$table.find('td.editable.editable-text').each(function () {
            // Create a wrapper div with the same structure as the Blade component
            const wrapperDiv = $('<div class="d-flex justify-content-between w-100"></div>');

            // Collect treegrid elements
            const treegridElements = $(this).children('.treegrid-indent, .treegrid-expander');

            // Move the content span using the specific ID (adjust if you prefer using a class)
            const contentSpan = $(this).find('#contentSpan').detach();

            // Move the edit-pen if it exists
            const editPen = $(this).find('.edit-pen').detach().addClass('ms-auto');

            // Append elements in order to the wrapperDiv
            treegridElements.appendTo(wrapperDiv);
            wrapperDiv.append(contentSpan);
            wrapperDiv.append(editPen);

            // Clear the existing td content and append the modified wrapper div
            $(this).empty().append(wrapperDiv);
        });
    }



    attachCategorySpecificListeners() {
        // Attach each listener
        this.attachEditCategoryListener();
        this.attachDeleteCategoryListener();
        this.attachAddCategoryListener();
        this.attachToggleExpandCollapseListener();
    }

    // Edit category listener
    attachEditCategoryListener() {
        this.$table.on('click', 'tbody .edit-button', (event) => {
            const $row = $(event.currentTarget).closest('tr');
            const parentId = $row.data('parent-id');
            const categoryId = $row.data('id');
            const action = $(event.currentTarget).data('action');
        });
    }

    // Delete category listener
    attachDeleteCategoryListener() {
        this.$table.on('click', 'tbody .trash-button', (event) => {
            const $row = $(event.currentTarget).closest('tr');
            const categoryId = $row.data('id');
            const categoryIds = CategoryService.findChildCategories(categoryId);

            // Delete categories and related children
            this.deleteRows(categoryIds, 'part_categories', 'category_id');
        });
    }

    // Add category listener
    attachAddCategoryListener() {
        this.$table.on('click', 'tbody .addcat-button', (event) => {
            const $row = $(event.currentTarget).closest('tr');
            const parentCategoryId = $row.data('id');
            $('#parentCategoryId').val(parentCategoryId);

            // CategoryCreator takes over from here on
            this.categoryCreator.showModal();
        });
    }

    // Expand/Collapse categories listener
    attachToggleExpandCollapseListener() {
        let isCollapsed = false;
        $('#category-toggle-collapse-expand').click(() => {
            if (isCollapsed) {
                this.$table.treegrid('expandAll');
                $('#category-toggle-collapse-expand').text('Toggle');
            } else {
                this.$table.treegrid('collapseAll');
                $('#category-toggle-collapse-expand').text('Toggle');
            }
            isCollapsed = !isCollapsed;
        });
    }

    /**
     * Attach the click listener to the "Edit Categories" button.
     * The button toggles the visibility of the Categories Edit column
     */
    attachEditCategoriesButtonClickListener() {
        $('#cat-edit-btn').off('click').on('click', function () {
            var columnIndex = 0;
            $('#categories_list_table th[data-field="category_edit"], #categories_list_table td[data-field="category_edit"]').toggle();
        });
    }

    /**
     * Attach the click listener to the "Toggle Categories" button.
     * The button toggles the visibility of the Categories div in the parts view
     */
    attachShowCategoriesButtonClickListener() {
        $('#cat-show-btn').off('click').on('click', function () {
            $('#category-window-container').toggle();
            Layout.saveLayoutSettings(); // Save visibility after toggling
        });
    }
}
