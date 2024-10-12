export { TableManager };
import { TableRowManager } from "./TableRowManager";
import {
    updateInfoWindow,
    showDeleteConfirmation,
    deleteSelectedRows,
    makeTableWindowResizable
} from "./custom";

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
        let id_name, tableId, menuId, rebuildUrl;
        switch (this.type) {
            case 'part':
                id_name = 'part_id';
                tableId = 'parts_table';
                menuId = 'parts_table_menu';
                rebuildUrl = '/parts.partsTable';
                break;
            default:
                this.id_name = 'id';
                break;
        }

        this.$table = $(`#${tableId}`);
        this.$menu = $(`#${menuId}`);
        this.id_name = id_name;
        this.rebuildUrl = rebuildUrl;

        // Set default callbacks, but allow customization
        this.bootstrapCallback = this.defaultBootstrapCallback();
        this.rowClickCallback = this.instantiateRowClickCallback(type);
        this.contextActions = contextActions || this.defaultContextActions();
        this.tableRowManager = this.instantiateTableRowManager(type);

        // this.bootstrapTable();
        // this.defineActions();

        this.hideMenuOnClickOutside();
        this.preventTextSelectionOnShift();
        this.instantiateRowClickCallback();
    }



    /**
* Instantiate the TableRowManager based on the type.
*/
    instantiateTableRowManager(type) {
        switch (type) {
            case 'part':
                return new TableRowManager({ table: '#parts_table', type: 'part' });
            case 'supplier':
                return new TableRowManager({ saveKey: 'suppliers_selected_row' });
            case 'footprint':
                return new TableRowManager({ saveKey: 'footprints_selected_row' });
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
                        console.log("saving newly created part");
                        this.tableRowManager.saveSelectedRow(id);
                    }
                };
            case 'supplier':
                return (id) => {
                    updateInfoWindow(this.type, id);
                    if (this.tableRowManager) {
                        this.tableRowManager.saveSelectedRow(id);
                    }
                };
            case 'footprint':
                return (id) => {
                    updateInfoWindow(this.type, id);
                    if (this.tableRowManager) {
                        this.tableRowManager.saveSelectedRow(id);
                    }
                };
            default:
                return (id) => {
                    console.log(`Row clicked, ID: ${id}`);
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
        this.bootstrapCallback();
    }

    /**
     * Defines the row click and context menu actions for the table.
     */
    defineActions() {
        this.defineTableRowClickActions();
        this.attachContextMenu();
    }

    /**
     * Defines the actions to perform when a table row is clicked.
     * Attaches a click event listener to the specified table rows and calls the
     * provided callback function with the extracted ID when a row is selected.
     */
    defineTableRowClickActions() {
        this.$table.on('click', 'tbody tr', (event) => {
            const selectedRow = this.$table.find('tr.selected-last');
            if (selectedRow.length > 0) {
                selectedRow.removeClass('selected-last');
            }
            const $currentRow = $(event.currentTarget);
            $currentRow.toggleClass('selected-last');

            const id = $currentRow.data('id');
            if (this.rowClickCallback) this.rowClickCallback(id);
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
                    //TODO: This is OG code:
                    // deleteSelectedRows(ids, this.type, this.id_name, () => this.rebuildTable());
                    deleteSelectedRows(ids, `${this.type}s`, this.id_name, () => this.rebuildTable());
                    
                });
            },
            edit: (selectedRows) => {
                editSelectedRows(selectedRows);
            },
            customAction1: (selectedRows) => {
                customAction1(selectedRows);
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
                $('#table-window').html(data);
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
