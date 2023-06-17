import { deleteSelectedRows } from "../custom";
import { assembleBoms } from "../tables";

export function deleteSelectedRowsFromToolbar(table_id, model, id_column, successCallback) {

    //! I had `$table` jquery object instead of `table_id` but it bugs around weirdly
    //! Most likely due to variable scoping, so I just changed it to be a string
    // Get selected table rows
    var selectedRows = $('#' + table_id).bootstrapTable('getSelections');
    console.log("selectedRows: ", selectedRows);

    // Extract IDs
    var ids = selectedRows.map(obj => obj._data.id);

    if (confirm('Are you sure you want to delete ' + selectedRows.length + ' selected row(s)?\n\nThis will also delete the corresponding entries from BOMs, storage locations and stock history.')) {
        deleteSelectedRows(ids, model, id_column, successCallback);
    }
}

/**
 *Attaches a click handler to the Delete button in the toolbar
 */
export function attachDeleteRowsHandler(table_id, model, id_column, successCallback) {
    $('#toolbarDeleteButton').click(function () {
        deleteSelectedRowsFromToolbar(table_id, model, id_column, successCallback);
    });
}

/**
 *Attaches a click handler to the Assemble button in the BOMs toolbar
 */
export function attachAssembleBomHandler(table_id) {
    $('#toolbarAssembleBomButton').click(function () {
        assembleBomsFromToolbar(table_id);
    });
}

export function assembleBomsFromToolbar(table_id) {
    // Get selected table rows
    var selectedRows = $('#' + table_id).bootstrapTable('getSelections');
    console.log("selectedRows: ", selectedRows);

    // Extract IDs
    var ids = selectedRows.map(obj => obj._data.id);
    console.log("ids: ", ids);

    assembleBoms(selectedRows, ids);
}

export function attachAddBomHandler() {
    $('#toolbarAddButton').click(function () {
        $('#info-window').load('/bom.import-test');
    });
}