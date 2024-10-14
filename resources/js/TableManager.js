export { TableManager };
import { TableRowManager } from "./TableRowManager";
import {
    updateInfoWindow,
    showDeleteConfirmation,
    deleteSelectedRows,
    makeTableWindowResizable,
} from "./custom";

import { assembleBoms, bootstrapCategoriesListTable } from "./tables";

class TableManager {
    /**
     * Constructs a new TableManager instance for managing table interactions.
     * @param {Object} options - Options for configuring the table manager.
     * @param {string} options.tableId - The ID of the table element.
     * @param {string} options.menuId - The ID of the context menu element.
     * @param {function} options.bootstrapCallback - Function to initialize the table.
     * @param {function} options.rowClickCallback - Function to call when a table row is clicked.
     * @param {Object} options.contextActions - Object containing action names as keys and action functions as values.
     * @param {string} options.rebuildUrl - URL to use when rebuilding the table.
     */
    constructor({ type, contextActions = null }) {

        this.type = type;
        switch (this.type) {
            case 'part':
                this.id_name = 'part_id';
                this.tableId = 'parts_table';
                this.menuId = 'parts_table_menu';
                this.rebuildUrl = '/parts.partsTable';
                this.container = 'table-window';
                break;
            case 'partHistory':
                this.tableId = 'partStockHistoryTable'
                this.container = 'partHistory';
                break;
            case 'partInBoms':
                this.tableId = 'partInBomsTable'
                //TODO: Come up with a div name
                break;
            case 'bom':
                this.id_name = 'bom_id';
                this.tableId = 'bom_list_table';
                this.menuId = 'bom_list_table_menu';
                this.rebuildUrl = '/boms.bomsTable';
                this.container = 'table-window';
                break;
            case 'footprint':
                this.id_name = 'footprint_id';
                this.tableId = 'footprints_list_table';
                this.rebuildUrl = '/footprints.footprintsTable';
                this.container = 'table-window';
                break;
            case 'location':
                this.id_name = 'location_id';
                this.tableId = 'locations_list_table';
                this.rebuildUrl = '/location.locationsTable';
                this.container = 'table-window';
                break;
            case 'supplier':
                this.id_name = 'supplier_id';
                this.tableId = 'suppliers_list_table';
                this.rebuildUrl = '/suppliers.suppliersTable';
                this.container = 'table-window';
                break;
            case 'category':
                this.id_name = 'category_id';
                this.tableId = 'categories_list_table';
                this.rebuildUrl = '/categories.categoriesTable';
                this.container = 'category-window';
                break;
            default:
                this.id_name = 'id';
                break;
        }

        this.$table = $(`#${this.tableId}`);
        if (this.menuId) {
            this.$menu = $(`#${this.menuId}`);
            this.hideMenuOnClickOutside();
        }

        // Set default callbacks, but allow customization
        this.bootstrapCallback = this.defaultBootstrapCallback();
        this.rowClickCallback = this.instantiateRowClickCallback(type);
        this.contextActions = contextActions || this.defaultContextActions();
        this.tableRowManager = this.instantiateTableRowManager(type);

        this.preventTextSelectionOnShift();
        this.instantiateRowClickCallback();
    }



    /**
    * Instantiate the TableRowManager based on the type.
    */
    instantiateTableRowManager(type) {
        switch (type) {
            case 'part':
                return new TableRowManager('#parts_table', 'part');
            case 'bom':
                return new TableRowManager('#bom_list_table', 'bom');
            case 'footprint':
                return new TableRowManager('#footprints_list_table', 'footprint');
            case 'location':
                return new TableRowManager('#locations_list_table', 'location');
            case 'supplier':
                return new TableRowManager('#suppliers_list_table', 'supplier');
            default:
                return new TableRowManager({ saveKey: 'default_selected_row' });
        }
    }

    /**
     * Instantiate the row click callback based on the type.
     */
    instantiateRowClickCallback(type) {
        switch (type) {
            case 'part':
                return (id) => {
                    updateInfoWindow(this.type, id);
                    this.updateStockModal(id);
                    if (this.tableRowManager) {
                        this.tableRowManager.saveSelectedRow(id);
                    }
                };
            case 'category':
                return (id, categories) => {
                    // Array of category and potential child category names as strings for filtering parts table
                    var cats = this.getChildCategoriesNames(categories, id);

                    // Filter by categories
                    $('#parts_table').bootstrapTable('filterBy', {
                        Category: cats
                    })
                }
            default:
                return (id) => {
                    updateInfoWindow(this.type, id);
                    if (this.tableRowManager) {
                        this.tableRowManager.saveSelectedRow(id);
                    }
                };
        }
    }

    /**
     * Default bootstrap callback method.
     */
    defaultBootstrapCallback() {
        return () => {
            this.$table.bootstrapTable({});
        };
    }

    bootstrapTable() {
        this.$table.bootstrapTable({});
    }

    /**
     * Defines the row click and context menu actions for the table.
     */
    defineActions(categories = null) {
        this.defineTableRowClickActions(categories);
        if (this.menuId) {
            this.attachContextMenu();
        }
    }

    /**
     * Defines the actions to perform when a table row is clicked.
     * Attaches a click event listener to the specified table rows and calls the
     * provided callback function with the extracted ID when a row is selected.
     */
    defineTableRowClickActions(categories) {
        this.$table.on('click', 'tbody tr', (event) => {
            const selectedRow = this.$table.find('tr.selected-last');
            if (selectedRow.length > 0) {
                selectedRow.removeClass('selected-last');
            }
            const $currentRow = $(event.currentTarget);
            $currentRow.toggleClass('selected-last');

            const id = $currentRow.data('id');
            if (this.rowClickCallback) this.rowClickCallback(id, categories);
        });
        this.preventTextSelectionOnShift();
    }

    /**
     * Defnies context menu actions that is attached to a table row and is triggered by right-clicking on a cell.
     */
    /**
   * Default context menu actions.
   */
    defaultContextActions() {
        return {
            delete: (selectedRows, ids) => {
                const question = `Are you sure you want to delete ${selectedRows.length} selected row(s)?`;
                showDeleteConfirmation(question, () => {
                    //TODO: Code the difference between part/parts, bom/boms,...
                    deleteSelectedRows(ids, `${this.type}s`, this.id_name, () => this.rebuildTable());

                });
            },
            edit: (selectedRows) => {
                editSelectedRows(selectedRows);
            },
            assemble: function (selectedRows, ids) {
                assembleBoms(selectedRows, ids);
            }
        };
    }

    attachContextMenu() {
        this.$table.on('contextmenu', 'td', (event) => {
            event.preventDefault();

            const selectedRows = this.$table.bootstrapTable('getSelections');
            const ids = selectedRows.map(row => row._data.id);
            const footprints = selectedRows.map(row => row.Footprint); // Extract footprints if needed

            this.showContextMenu(event);

            this.$menu.find('.dropdown-item').off('click').on('click', (menuEvent) => {
                const action = $(menuEvent.currentTarget).data('action');
                if (this.contextActions && typeof this.contextActions[action] === 'function') {
                    this.contextActions[action](selectedRows, ids, footprints);
                }
                this.hideContextMenu();
            });

            this.hideMenuOnClickOutside();
        });
    }

    showContextMenu(event) {
        this.$menu.css({
            left: event.pageX + 'px',
            top: event.pageY + 'px',
            display: 'block'
        });
    }

    hideContextMenu() {
        this.$menu.hide();
    }

    hideMenuOnClickOutside() {
        $(document).off('click').on('click', (event) => {
            if (!this.$menu.is(event.target) && this.$menu.has(event.target).length === 0) {
                this.hideContextMenu();
            }
        });
    }

    preventTextSelectionOnShift() {
        // Shift is pressed
        $(document).on('keydown', (event) => {
            if (event.shiftKey) {
                this.$table.addClass('table-no-select');
            }
        });

        // Shift is released
        $(document).on('keyup', (event) => {
            if (!event.shiftKey) {
                this.$table.removeClass('table-no-select');
            }
        });
    }

    /**
    * Load the contents of stockModals page, pass the id and replace HTML in modal
    * upon clicking a row in the parts table
    * @param {int} id The part ID for which to update the stock modal content
    * @return void
    */
    updateStockModal(id) {
        $.ajax({
            url: '/part.getName',
            type: 'GET',
            data: { part_id: id },
            success: function (name) {
                // Fill the name into the stock modal
                document.getElementById('partName').textContent = name;
            },
            error: function () {
                // Display an error message if the PHP page failed to load
                $('#mAddStock').html('Failed to load modal.');
            }
        });
    }

    /**
     * Rebuilds the table after changes, such as adding or deleting rows.
     * @param {string} [queryString=''] - Optional query string to send with the AJAX request.
     * @returns {Promise} - A promise that resolves when the table has been rebuilt.
     */
    rebuildTable(queryString = '', postRebuildCallback = null) {
        return $.ajax({
            url: `${this.rebuildUrl}${queryString}`,
            success: (data) => {
                this.$table.bootstrapTable('destroy');
                $(`#${this.container}`).html(data);
                this.$table = $(`#${this.tableId}`);
                this.bootstrapTable();
                this.defineActions();
                makeTableWindowResizable();

                if (typeof postRebuildCallback === 'function') {
                    postRebuildCallback();
                }
            }
        });
    }

    enableInlineProcessing() {
        this.$table.on('dblclick', '.editable', (event) => {
            const cell = $(event.currentTarget);

            if (cell.hasClass('editing')) {
                return;
            } else {
                cell.addClass('editing');
            }

            const originalValue = cell.text();
            const originTable = cell.closest('table').attr('id');

            const editor = new InlineTableCellEditor({
                type: cell.attr('class').split(' ')[1], // Assume the second class defines the type (e.g., 'category', 'footprint')
                $cell: cell,
                originalValue: originalValue,
                originTable: originTable
            }).editCell();
        });
    }
}
