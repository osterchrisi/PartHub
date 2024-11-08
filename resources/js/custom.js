import { ResourceCreator } from "./Resources/ResourceCreators/ResourceCreator";
import { InfoWindow } from "./InfoWindow";
import { SupplierRowManager } from "./Tables/SupplierRowManager";
import { Layout } from "./User Interface/Layout";

/**
 * Prevents text selection in the given table when the shift key is pressed (for selecting)
 * multiple rows at once).
 * @param {jQuery} $table - The table element to prevent text selection in.
 * @return void
 */
export function preventTextSelectionOnShift($table) {
    // Shift is pressed
    $(document).on('keydown', function (event) {
        if (event.shiftKey) {
            $table.addClass('table-no-select');
        }
    });

    // Shift is released
    $(document).on('keyup', function (event) {
        if (!event.shiftKey) {
            $table.removeClass('table-no-select');
        }
    });
}

/**
 * Removes the click event listeners from the HTML element with the specified ID.
 * @param {string} id - The ID of the HTML element from which to remove click event listeners.
 * @return void
 */
export function removeClickListeners(id) {
    $(id).off('click');
}

/**
 * 'Selectize' the category multi select, prepare values and append to the hidden input field
 * @param {string} id The ID of the multi-select element to initialize.
 * @return void
 */
export function initializeMultiSelect(id) {
    var $select = $('#' + id).selectize({
        plugins: ["remove_button", "clear_button"]
    });

    $('form').on('submit', function () {
        // Get the selected options from the selectize instance
        var selectedValues = $select[0].selectize.getValue();

        // Prepare values to look like an array
        for (var i = 0; i < selectedValues.length; i++) {
            selectedValues[i] = [selectedValues[i]];
        }
        selectedValues = JSON.stringify(selectedValues);

        // Update the value of the hidden input element
        $('#selected-categories').val(selectedValues);
    });
};

/**
 * Delete selected rows in the database table
 * @param {array} ids Array of IDs to delete
 * @param {string} table_name Name of the table in the database
 * @param {string} column Name of the column that holds the ID, e.g. part_id
 * @param {function} successCallback Function to call on successful deletion of rows. Used to rebuild the corresponding table
 * @return void
 */
export function deleteSelectedRows(ids, model, id_column, successCallback) {

    var token = $('input[name="_token"]').attr('value');

    $.ajax({
        url: '/deleteRow',
        type: 'POST',
        data: {
            ids: ids,
            table: model,
            column: id_column
        },
        headers: {
            'X-CSRF-TOKEN': token
        },
        success: function (response) {
            Layout.showDeletionConfirmationToast(ids.length, model);
            var queryString = window.location.search;
            successCallback(queryString);
        },
        error: function (xhr) {
            // Handle the error
            if (xhr.status === 419) {
                // Token mismatch error
                alert('CSRF token mismatch. Please refresh the page and try again.');
            } else {
                // Other errors
                alert('An error occurred. Please try again.');
            }
        }
    });
}

/**
 * Updates the info window with data fetched via AJAX request based on the provided type and ID.
 * @param {string} type - The type of data to update ('part', 'bom', 'location', 'footprint', 'supplier', 'category').
 * @param {number} id - The ID of the data to update the info for.
 * @return {void}
 */
export function updateInfoWindow(type, id) {
    $.ajax({
        url: `/${type}/${id}`,
        type: 'GET',
        data: {},
        success: function (data) {
            $('#info-window').html(data);
            const iw = new InfoWindow(type, id);
            iw.initialize();
        },
        error: function (xhr) {
            let errorMessage;
            if (xhr.status === 401) {
                errorMessage = 'Your session expired. Please login again.';
            } else if (xhr.status === 503) {
                errorMessage = 'Application in maintenance mode, please try again in a few seconds';
            } else {
                errorMessage = `Failed to load additional ${type} data.`;
            }

            const errorHTML = `
            <div class="alert alert-dark align-self-start mt-3 mx-3" role="alert">
            <h6 class="text-center">${errorMessage}</h6>
                </div>`;

            $('#info-window').html(errorHTML);
        }
    });
}

/**
 * Clears any input fields of any modal upon hiding (Cancel and/or submitting)
 */
export function clearModalOnHiding() {
    //TODO: Not sure about istantiating SRM here...
    const sr = new SupplierRowManager({});
    $(document).on('hidden.bs.modal', '.modal', function (event) {
        // Don't do it if the modal was hidden due to creating a new category
        const targetModalId = event.target.id;
        if (targetModalId !== 'categoryCreationModal') {
            // Check if part entry modal is being hidden due to showing category modal
            const isPartEntryHiddenDueToCategory = targetModalId === 'mPartEntry' && $('#categoryCreationModal').hasClass('show');

            // If not, clear inputs
            if (!isPartEntryHiddenDueToCategory) {
                // Clear input values
                $(this).find('input').val('');

                // Clear invalid field displays
                $('.text-danger').addClass('d-none').text('');
                $('input, select, textarea').removeClass('is-invalid');
                $('.selectize-control').removeClass('is-invalid');

                sr.resetSupplierDataTable({});
            }
            else {
                // Leave the values, the user came back from the category modal
            }
        }
    });

    // Stock changing modal with its "Continue Anyway" message
    $('#mAddStock').on('hidden.bs.modal', function (e) {
        $('#FromStockLocationDiv-row').show();
        $('#ToStockLocationDiv-row').show();
        $('#stockChangingForm')[0].reset();
        $('#mStockModalInfo').empty();
        $('#AddStock').attr('disabled', false);
        $(this).modal('dispose');
    });
}

/**
 * Focus the first input field in any modal after showing
 * @return void
 */
export function focusFirstInputInModals() {
    $(document).on('shown.bs.modal', '.modal', function () {
        $(this).find('input:visible:first').focus();
    });
}

/**
 * Displays a delete confirmation modal with a custom question.
 * Executes a callback function if the user confirms the deletion.
 *
 * @param {string} question - The question to display in the modal.
 * @param {function} confirmCallback - The callback function to execute upon confirmation.
 */
export function showDeleteConfirmationModal(question, confirmCallback) {
    // Set the delete question
    $('#deleteQuestion').text(question);

    // Show the modal
    $('#deleteConfirmationModal').modal('show');

    // Set up the click handler for the confirmation button
    $('#confirmDeleteButton').off('click').on('click', () => {
        confirmCallback();
        $('#deleteConfirmationModal').modal('hide');
    });
}
