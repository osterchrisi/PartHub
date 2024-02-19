import { initializeShowBom } from "./showBom";
import { initializeShowPart} from "./showPart";

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
 * Send form "search_form" upon changing the results per page dropdown "resultspp"
 */
function sendFormOnDropdownChange() {
    var dropdown = document.getElementById("resultspp");
    dropdown.addEventListener("change", function () {
        var form = document.getElementById("search_form");
        form.submit();
    });
};

/** 
 * ClickListener for "Continue as demo user" button
 * Executes demo.php and then refers user back to index.php
 * @return void
 */
function continueAsDemoUser() {
    $('#continueDemo').click(function () {
        $.post('/PartHub/includes/demo.php', function (response) {
            window.location.href = "/PartHub/index.php?login";
        });
    });
}


/**
 * Load the parts info window for a given part ID
 * @param {int} id The part ID for which to update the info window
 * @return void
 */
export function updatePartsInfo(id) {
    $.ajax({
        url: "/part/" + id,
        type: 'GET',
        data: {},
        success: function (data) {
            // Replace the content of the info window with the loaded PHP page
            $('#info-window').html(data);
            initializeShowPart(id);
        },
        error: function (xhr, status, error) {
            if (xhr.status === 401) {
                alert("Your session expired. Please log in again.");
                // Alternatively, show Bootstrap modal when I have time to make a nice one:
                // $('#unauthorizedModal').modal('show');
            } else {
                // Handle other errors
                console.log("Error:", error);
            }
        }
    });
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
 * Updates the BOM info in the info window using an AJAX request.
 * @param {number} id - The ID of the BOM to update the info for.
 * @return void
 */
export function updateBomInfo(id) {
    $.ajax({
        url: '/bom/' + id,
        type: 'GET',
        data: {},
        success: function (data) {
            // Replace the content of the info window with the loaded PHP page
            $('#info-window').html(data);
            initializeShowBom();
        },
        error: function (xhr) {
            if (xhr.status === 401) {
                $('#info-window').html('Your session expired. Please login again.')
            }
            else {
                // Display an error message if the PHP page failed to load
                $('#info-window').html('Failed to load additional BOM data.');
            }
        }
    });
};

/**
 * Updates the location info in the info window using an AJAX request.
 * @param {number} id - The ID of the location to update the info for.
 * @return void
 */
export function updateLocationInfo(id) {
    $.ajax({
        url: '/location/' + id,
        type: 'GET',
        data: {},
        success: function (data) {
            // Replace the content of the info window with the loaded PHP page
            $('#info-window').html(data);
            //* Here would go a initializeShowLocation() function if there is ever any JS in that info window
        },
        error: function (xhr) {
            if (xhr.status === 401) {
                $('#info-window').html('Your session expired. Please login again.')
            }
            else {
                // Display an error message if the PHP page failed to load
                $('#info-window').html('Failed to load additional BOM data.');
            }
        }
    });
};

/**
 * Updates the footprint info in the info window using an AJAX request.
 * @param {number} id - The ID of the footprint to update the info for.
 * @return void
 */
export function updateFootprintInfo(id) {
    $.ajax({
        url: '/footprint/' + id,
        type: 'GET',
        data: {},
        success: function (data) {
            // Replace the content of the info window with the loaded PHP page
            $('#info-window').html(data);
            //* Here would go a initializeShowFootprint() function if there is ever any JS in that info window
        },
        error: function (xhr) {
            if (xhr.status === 401) {
                $('#info-window').html('Your session expired. Please login again.')
            }
            else {
                // Display an error message if the PHP page failed to load
                $('#info-window').html('Failed to load additional BOM data.');
            }
        }
    });
};

/**
 * Displays the BOM creation script in the info window using an AJAX request
 * @return void
 */
function displayBomCreate() {
    $.ajax({
        url: 'bom-create.php',
        type: 'GET',
        data: { hideNavbar: true },
        success: function (data) {
            // Replace the content of the info window with the loaded PHP page
            $('#info-window').html(data);
        },
        error: function () {
            // Display an error message if the PHP page failed to load
            $('#info-window').html('Failed to load BOM creation page.');
        }
    });
};

/**
 * Make the table-window and the info-window resizable
 * @return void 
 */
$(function () {
    $('#table-window').resizable({
        handles: 'e',
        resize: function () {
            var parentWidth = $('#table-window').parent().width();
            var tableWidth = $('#table-window').width();
            var infoWidth = parentWidth - tableWidth;
            $('#info-window').width(infoWidth);
        }
    });

    $('#table-window2').resizable({
        handles: 'e',
        resize: function () {
            var parentWidth = $('#table-window2').parent().width();
            var tableWidth = $('#table-window2').width();
            var infoWidth = parentWidth - tableWidth;
            $('#info-window2').width(infoWidth);
        }
    });

    $('#partsCollapse').resizable({
        handles: 'e',
        resize: function () {
            var parentWidth = $('#partsMegaContainer').parent().width();
            var tableWidth = $('#partsMegaContainer').width();
            var infoWidth = parentWidth - tableWidth;
            $('#bomsMegaContainer').width(infoWidth);
        }
    });
});

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

    console.log(ids.length);
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
            console.log(response);
            showDeletionConfirmationToast(ids.length);
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
 * Validates required fields in a form when the specified button is clicked.
 * @param {string} formId - The ID of the form to validate.
 * @param {string} button - The ID of the button element to attach the click listener to.
 * @param {function} callback - The function to execute when the form is submitted and valid.
 * @param {Array} [args=[]] - Optional array of additional arguments to pass to the callback function.
 * @returns {*} - The return value of the callback function, if the form is valid.
 *                If the form is invalid, the function returns undefined.
 */
export function validateForm(formId, button, callback, args = []) {
    const form = document.getElementById(formId);
    const submitBtn = document.getElementById(button);

    // Form validation
    $(submitBtn).click(function (event) {
        event.preventDefault();
        if (form.checkValidity()) {
            // Form is valid
            const result = callback.apply(null, args);
            return result;
        } else {
            console.log("invalid");
            // Form is invalid (required fields not filled)
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
    });
}

/**
 * Saves the active tab for a specific page in the local storage.
 * @param {string} page - The identifier of the page.
 * @param {Event} event - The event that triggered this function.
 * @returns {void}
 */
function saveActiveTab(page, event) {
    const tabId = event.target.getAttribute('id');
    if (tabId) {
        localStorage.setItem('lastActiveTab_' + page, tabId);
    }
}

/**
 * Loads the active tab for a specific page from local storage and shows it.
 * @param {string} page - The identifier of the page.
 * @returns {void}
 */
export function loadActiveTab(page, defaultTab) {
    var lastActiveTab = localStorage.getItem('lastActiveTab_' + page) || defaultTab;
    if (lastActiveTab) {
        console.log("lastActiveTab = ", lastActiveTab);
        const tabElement = document.querySelector(`#${lastActiveTab}`);
        if (tabElement) {
            const tab = new bootstrap.Tab(tabElement);
            tab.show();
        }
    }
}

/**
 * Attaches an event listener to all togglable tabs on a specific page
 * which triggers the saveActiveTab function with the corresponding page identifier.
 * @param {string} page - The identifier of the page.
 * @returns {void}
 */
export function addActiveTabEventListeners(page) {
    const tabs = document.querySelectorAll('[data-bs-toggle="tab"]');
    tabs.forEach((tab) => {
        tab.addEventListener('shown.bs.tab', (event) => saveActiveTab(page, event));
    });
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

function showDeletionConfirmationToast(numElements) {
    const deleteToast = document.getElementById('tConfirmDelete');
    const numDeletedItemsSpan = document.getElementById('numDeletedItems');
    numDeletedItemsSpan.textContent = numElements.toString();

    const toast = bootstrap.Toast.getOrCreateInstance(deleteToast);
    toast.show();

}