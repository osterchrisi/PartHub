import { deleteSelectedRows, showDeleteConfirmation, updateInfoWindow } from "../custom";
import { assembleBoms } from "../tables";
import { TableManager } from "../../Tables/TableManager";
import { TableRowManager } from "../../Tables/TableRowManager";

export function deleteRowsHandler(table_id, model, id_column, successCallback) {
    // Get selected table rows
    var selectedRows = $('#' + table_id).bootstrapTable('getSelections');

    // Check if rows have been selected
    if (selectedRows.length === 0) {
        alert("Please select row(s) to be deleted.\nYou can use Ctrl and/or Shift to select multiple rows");
    } else {
        // Extract IDs
        var ids = selectedRows.map(obj => obj._data.id);

        // Construct the question
        let question = 'Are you sure you want to delete ' + selectedRows.length + ' selected row(s)?';
        if (model === 'parts') {
            question += '\n\nThis will also delete the corresponding entries from BOMs, storage locations and stock history.';
        }

        // Show confirmation dialog and proceed to delete selected rows
        showDeleteConfirmation(question, () => {
            deleteSelectedRows(ids, model, id_column, successCallback);
        });
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
        deleteRowsHandler(table_id, model, id_column, successCallback);
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
        // Load the form into the #info-window element
        $('#info-window').load('/bom.import-form', function () {
            $('#bomImportForm').on('submit', function (event) {
                event.preventDefault();

                var formData = new FormData(this); // Get the form data
                var formObject = {};

                // Convert FormData to a plain object
                formData.forEach(function (value, key) {
                    formObject[key] = value;
                });

                formData.append('type', 'bom');

                $.ajax({
                    url: '/bom.import',
                    method: "POST",
                    data: formData,
                    contentType: false,
                    processData: false,
                    success: function (response) {
                        if (response.success) {
                            $('#response-message').html('<div class="alert alert-success">' + response.success + '</div>');
                        }
                        else if (response.error) {
                            $('#response-message').html('<div class="alert alert-danger">' + response.error + '</div>');
                        }

                        //TODO: Not super nice but works quite alright
                        const id = response.new_bom_id;
                        const bomTable = new TableManager({ type: 'bom' });
                        bomTable.rebuildTable().done(function () {
                            const bomListTable = new TableRowManager('#bom_list_table', 'bom');
                            bomListTable.selectNewRow(id);
                        });
                        updateInfoWindow('bom', id);

                    },
                    error: function (xhr, status, error) {

                        if (xhr.status === 403) {
                            const response = JSON.parse(xhr.responseText);
                            alert(response.message)
                        }
                        else {
                            // Handle error response
                            console.log(xhr.responseJSON);
                            var errorMessage = xhr.responseJSON?.error || 'An error occurred during the import.';
                            $('#response-message').html('<div class="alert alert-danger">' + errorMessage + '</div>');
                        }
                    }
                });
            });
        });
    });
}
