import { updateInfoWindow } from "../js/custom";

/**
 * Manages the table rows, including row selection, highlighting, and page switching.
 * Handles Bootstrap table data and row interactions.
 */
class TableRowManager {
    constructor(table, type) {
        this.table = $(table);
        this.type = type;
    }

    /**
     * Highlights and selects a newly added row in a Bootstrap table.
     *
     * This method finds the newly added row in the table data using the provided ID,
     * determines the appropriate page where the new row should be displayed, switches to that page,
     * and highlights the new row with visual effects.
     *
     * @param {number} id - The ID of the newly added row to be selected and highlighted.
     */
    selectNewRow(id) {
        // Get the table data after bootstrapping
        let tableData = this.table.bootstrapTable('getData');

        if (!Array.isArray(tableData)) {
            console.error("Expected an array but got:", tableData);
            return; // Exit early if the data is not as expected
        }

        // Find the position of the new part in the data array
        let newRowPosition = tableData.findIndex(row => row['_ID_data'].id == id);

        if (newRowPosition !== -1) {
            // Get current page size
            let pageSize = this.table.bootstrapTable('getOptions').pageSize;

            // Calculate the page number where the new part will be displayed
            let pageNumber = Math.floor(newRowPosition / pageSize) + 1;

            // Switch to the appropriate page
            this.table.bootstrapTable('selectPage', pageNumber);

            // Highlight the new row after changing the page
            this.highlightAndSelectRow(id, 1000, 10);
        } else {
            console.warn('New row position not found for id:', id);
        }
    }

    /**
     * Highlights and selects a table row by ID.
     *
     * @param {string} id - The ID of the row to highlight and select.
     * @param {number} [highlightDuration=1000] - Duration (ms) to keep the row highlighted.
     * @param {number} [initialDelay=0] - Delay (ms) before starting the highlight.
     */
    highlightAndSelectRow(id, highlightDuration = 1000, initialDelay = 0) {
        setTimeout(() => {
            let $newRow = $(`tr[data-id="${id}"]`);
            if ($newRow.length > 0) {
                $newRow.addClass('highlight-new selected selected-last');
                setTimeout(() => {
                    $newRow.removeClass('highlight-new');
                }, highlightDuration); // Keep the highlight for the specified duration
            }
        }, initialDelay); // Initial delay to wait until page change happens
    }


    /**
    * Saves the selected row ID for a specific table in local storage.
    * 
    * @param {string} table - The identifier of the table.
    * @param {string} rowId - The ID of the selected row.
    */
    saveSelectedRow(rowId) {
        if (rowId) {
            localStorage.setItem('lastSelectedRow_' + this.table.attr('id'), rowId);
        }
    }

    /**
     * Loads the selected row for a specific table from local storage and marks it as selected.
     * 
     * @param {string} type - The type of data to update ('part', 'bom', 'location', 'footprint', 'supplier', 'category').
     * @param {string} tableId - The identifier of the table.
     * @param {function} onSelect - Optional callback to call with the selected row ID.
     */
    loadSelectedRow(onSelect = null) {

        let savedRowId = localStorage.getItem('lastSelectedRow_' + this.table.attr('id'));

        if (savedRowId) {
            this.selectNewRow(savedRowId);
            updateInfoWindow(this.type, savedRowId);

            if (onSelect) {
                onSelect(savedRowId);
            }
        }
    }
}

export { TableRowManager };
