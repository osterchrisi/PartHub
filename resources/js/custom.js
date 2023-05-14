console.log("Fuck Vite");

/**
 * Focus the Part Name field in the part entry modal after showing
 * @return void
 */
function focusNewPartName() {
    $('#mPartEntry').on('shown.bs.modal', function () {
        $('#addPartName').focus();
    });
}

/**
 * Focus the Quantity field in the stock changes modal after showing
 * @return void
 */
function focusStockChangeQuantity() {
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
function preventTextSelectionOnShift($table) {
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
function removeClickListeners(id) {
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
 * Load the parts-info page and pass the id variable as a parameter
 * upon clicking a row in the parts table
 * @param {int} id The part ID for which to update the stock modal content
 * @return void
 */
function updatePartsInfo(id) {
    $.ajax({
        url: 'parts-info.php',
        type: 'POST',
        data: { part_id: id, hideNavbar: true },
        success: function (data) {
            // Replace the content of the info window with the loaded PHP page
            $('#info-window').html(data);
        },
        error: function () {
            // Display an error message if the PHP page failed to load
            $('#info-window').html('Failed to load additional part data.');
        }
    });
}

/**
 * Load the contents of stockModals page, pass the id and replace HTML in modal
 * upon clicking a row in the parts table
 * @param {int} id The part ID for which to update the stock modal content
 * @return void
 */
function updateStockModal(id) {
    $.ajax({
        url: '../includes/stockModals.php',
        type: 'GET',
        data: { part_id: id },
        success: function (data) {
            // Replace the content of the stock modal with the loaded PHP page
            $('#mAddStock').html(data);
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
function updateBomInfo(id) {
    $.ajax({
        url: 'show-bom.php',
        type: 'GET',
        data: { id: id, hideNavbar: true },
        success: function (data) {
            // Replace the content of the info window with the loaded PHP page
            $('#info-window').html(data);
        },
        error: function () {
            // Display an error message if the PHP page failed to load
            $('#info-window').html('Failed to load additional BOM data.');
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
});

/**
 * 'Selectize' the category multi select, prepare values and append to the hidden input field
 * @param {string} id The ID of the multi-select element to initialize.
 * @return void
 */
function initializeMultiSelect(id) {
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
function deleteSelectedRows(ids, table_name, column, successCallback) {
    console.log(ids, table_name, column, successCallback);
    // Like, delete 'em
    $.ajax({
        type: 'POST',
        url: '../includes/deleteRowInTable.php',
        data: {
            ids: ids,
            table: table_name,
            column: column
        },
        success: function (response) {
            console.log(response);
            console.log('success');
            // Updating table here because otherwise it rebuilds too fast
            var queryString = window.location.search;
            successCallback(queryString);
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
function validateForm(formId, button, callback, args = []) {
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
 * Saves the active tab in the local storage. 
 * @param {Event} event - The event that triggered this function.
 * @returns {void}
 */
function saveActiveTab(event) {
    const tabId = event.target.getAttribute('id');
    if (tabId) {
        localStorage.setItem('lastActiveTab', tabId);
    }
}

/**
* Loads the active tab from local storage and shows it.
* @returns {void}
*/
function loadActiveTab() {
    var lastActiveTab = localStorage.getItem('lastActiveTab') || 'partStockInfoTabToggler';

    if (lastActiveTab) {
        const tabElement = document.querySelector(`#${lastActiveTab}`);
        if (tabElement) {
            const tab = new bootstrap.Tab(tabElement);
            tab.show();
        }
    }
}

/**
 * Attaches an event listener to all togglable tabs which triggers the saveActiveTab function. 
 * @returns {void}
 */
function addActiveTabEventListeners() {
    const tabs = document.querySelectorAll('[data-bs-toggle="tab"]');
    tabs.forEach((tab) => {
        tab.addEventListener('shown.bs.tab', saveActiveTab);
    });
}

/**
 * Initializes Bootstrap popovers on all elements with the `data-bs-toggle="popover"` attribute.
 * @returns {void}
 */
function initializePopovers() {
    const popoverTriggerList = document.querySelectorAll('[data-bs-toggle="popover"]');
    const popoverList = [...popoverTriggerList].map(popoverTriggerEl => new bootstrap.Popover(popoverTriggerEl));
}

