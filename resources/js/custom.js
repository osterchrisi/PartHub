import { ResourceCreator } from "./resourceCreator";
import { infoWindow } from "./infoWindow";

/**
 * Focus the Part Name field in the part entry modal after showing
 * @return void
 */
export function focusNewPartName() {
    $('#mPartEntry').on('shown.bs.modal', function () {
        $('#addPartName').focus();
    });
}

/**
 * Focus the Quantity field in the stock changes modal after showing
 * @return void
 */
export function focusStockChangeQuantity() {
    $('#mAddStock').on('shown.bs.modal', function () {
        $('#addStockQuantity').focus();
    });
}

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
 * Load the contents of stockModals page, pass the id and replace HTML in modal
 * upon clicking a row in the parts table
 * @param {int} id The part ID for which to update the stock modal content
 * @return void
 */
export function updateStockModal(id) {
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
 * Make the table-window and the info-window resizable
 * @return void 
 */
export function makeTableWindowResizable() {
    $('#table-window').resizable({
        handles: 'e'
    });
    $('#category-window').resizable({
        handles: 'e'
    });
};

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
            showDeletionConfirmationToast(ids.length, model);
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
 * Validates required fields in a form and handles form submission when the specified button is clicked or Enter is hit.
 * If all required fields are valid, the form is submitted, otherwise the user is notified.
 * @param {string} formId - The ID of the form to validate.
 * @param {string} button - The ID of the button element to attach the click listener to.
 * @param {function} submitCallback - The function to execute when the form is submitted and valid.
 * @param {Array} [submitArgs=[]] - Optional array of additional arguments to pass to the submitCallback function.
 * @returns {*} - The return value of the submitCallback function, if the form is valid.
 *                If the form is invalid, the function returns undefined.
 */
export function validateAndSubmitForm(formId, button, submitCallback, submitArgs = []) {
    const form = document.getElementById(formId);
    const submitBtn = document.getElementById(button);

    // Attach event listeners for form validation and submission
    $(submitBtn).click(function (event) {
        event.preventDefault();
        submitFormIfValid();
    });

    $(form).on('keydown', function (event) {
        // Check if the Enter key is pressed and the active element is not the selectized input
        if (event.key === 'Enter' && !document.activeElement.id.includes('selectized')) {
            event.preventDefault(); // Prevent default form submission
            //! If I enable this submitFormIfValid() function, the submissions keeps stacking up, so taking it out
            // submitFormIfValid();
        }
    });

    // Function to submit the form if it's valid
    function submitFormIfValid() {
        if (form.checkValidity()) {
            const result = submitCallback.apply(null, submitArgs);
            return result;
        } else {
            displayFieldValidity();
        }
    }

    // Function to display validity status of required fields
    function displayFieldValidity() {
        form.querySelectorAll('[required]').forEach(function (field) {
            if (field.checkValidity()) {
                field.classList.remove('is-invalid');
                field.classList.add('is-valid');
            } else {
                field.classList.remove('is-valid');
                field.classList.add('is-invalid');
            }
        });
    }
}

/**
 * Saves the selected row ID for a specific table in local storage.
 * @param {string} tableId - The identifier of the table.
 * @param {string} rowId - The ID of the selected row.
 * @returns {void}
 */
export function saveSelectedRow(tableId, rowId) {
    if (rowId) {
        localStorage.setItem('lastSelectedRow_' + tableId, rowId);
    }
}

/**
 * Loads the selected row for a specific table from local storage and marks it as selected.
 * @param {string} type - The type of data to update ('part', 'bom', 'location', 'footprint', 'supplier', 'category'). 
 * @param {string} tableId - The identifier of the table.
 * @param {function} onSelect - The function to call with the selected row ID.
 * @returns {void}
 */
export function loadSelectedRow(type, tableId) {
    var savedRowId = localStorage.getItem('lastSelectedRow_' + tableId);
    if (savedRowId) {
        const table = document.querySelector(`#${tableId}`);
        if (table) {
            const currentTable = new ResourceCreator({
                table_name: table
            });
            currentTable.selectNewRow(savedRowId);
            updateInfoWindow(type, savedRowId);
        }
    }
}


/**
 * Initializes Bootstrap popovers on all elements with the 'data-bs-toggle="popover"' attribute.
 * @returns {void}
 */
export function initializePopovers() {
    const popoverTriggerList = document.querySelectorAll('[data-bs-toggle="popover"]');
    const popoverList = [...popoverTriggerList].map(popoverTriggerEl => new bootstrap.Popover(popoverTriggerEl));
}

/**
 * Initializes Bootstrap toasts on all elements with the 'toast' class.
 * @returns {void}
 */
function initializeToasts() {
    const toastElList = document.querySelectorAll('.toast')
    const toastList = [...toastElList].map(toastEl => new bootstrap.Toast(toastEl, option))
}

export function showDeletionConfirmationToast(numElements, type) {
    const deleteToast = document.getElementById('tConfirmDelete');

    if (type == 'parts') {
        if (numElements > 1) {
            type = 'parts';
        } else {
            type = 'part';
        }
    }
    else if (type == 'boms') {
        if (numElements > 1) {
            type = 'BOMs';
        } else {
            type = 'BOM';
        }
    }
    else if (type == 'part_categories') {
        if (numElements > 1) {
            type = 'categories';
        } else {
            type = 'category';
        }
    }
    else if (type == 'image') {
        if (numElements > 1) {
            type = 'images';
        } else {
            type = 'image';
        }
    }

    const numDeletedItemsSpan = document.getElementById('numDeletedItems');
    numDeletedItemsSpan.textContent = numElements.toString();

    const typeSpan = document.getElementById('typeSpan');
    typeSpan.textContent = type.toString();

    const toast = bootstrap.Toast.getOrCreateInstance(deleteToast);
    toast.show();

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
            const iw = new infoWindow(type, id);
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
                <div class="alert alert-dark align-self-start" role="alert">
                    <p class="text-center">${errorMessage}</p>
                </div>`;

            $('#info-window').html(errorHTML);
        }
    });
}

/**
 * Clears any input fields of any modal upon hiding (Cancel and/or submitting)
 */
export function clearModalOnHiding() {
    $(document).on('hidden.bs.modal', '.modal', function () {
        $(this).find('input').val('');
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
export function showDeleteConfirmation(question, confirmCallback) {
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