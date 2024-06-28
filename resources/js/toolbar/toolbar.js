import { deleteSelectedRows } from "../custom";
import { assembleBoms } from "../tables";

export function deleteSelectedRowsFromToolbar(table_id, model, id_column, successCallback) {

    // Get selected table rows
    var selectedRows = $('#' + table_id).bootstrapTable('getSelections');

    // Check if rows have been selected
    if (selectedRows.length === 0) {
        // console.log("Nothing selected")
        alert("Please select row(s) to be deleted.\nYou can use Ctrl and/or Shift to select multiple rows");
    }
    else {
        // Extract IDs
        var ids = selectedRows.map(obj => obj._data.id);

        // Show confirmation alert and proceed to delete selected rows
        if (confirm('Are you sure you want to delete ' + selectedRows.length + ' selected row(s)?\n\nThis will also delete the corresponding entries from BOMs, storage locations and stock history.')) {
            deleteSelectedRows(ids, model, id_column, successCallback);
        }
    }
}

/**
 *Attaches a click handler to the Delete button in the toolbar
 *@param table_id The HTML table from which the row to be deleted comes from
 *@param model The name of the model (database table)
 *@param id_colum The name of the id column in the table
 *@param successCallback Callback function after successful deletion
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
    // console.log("selectedRows: ", selectedRows);

    // Extract IDs
    var ids = selectedRows.map(obj => obj._data.id);
    // console.log("ids: ", ids);

    assembleBoms(selectedRows, ids);
}

export function attachAddBomHandler() {
    $('#toolbarAddButton').click(function () {
        $('#info-window').load('/bom.import-form');
    });
}