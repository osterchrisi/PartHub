import { initializeShowBom } from "./showBom";
import { initializeShowPart } from "./showPart";
import { initializeShowFootprint } from "./showFootprint";
import { initializeShowLocation } from "./showLocation";
import { initializeShowCategory } from "./showCategory";
import { initializeShowSupplier } from "./showSupplier";

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
            // console.log(response);
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
        // console.log("lastActiveTab = ", lastActiveTab);
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
            switch (type) {
                case 'part':
                    initializeShowPart(id);
                    break;
                case 'bom':
                    initializeShowBom();
                    break;
                case 'location':
                    initializeShowLocation();
                    break;
                case 'footprint':
                    initializeShowFootprint();
                    break;
                case 'supplier':
                    initializeShowSupplier();
                    break;
                case 'category':
                    initializeShowCategory();
                    break;
                default:
                    break;
            }
        },
        error: function (xhr) {
            if (xhr.status === 401) {
                $('#info-window').html('Your session expired. Please login again.')
            } else {
                $('#info-window').html(`Failed to load additional ${type} data.`);
            }
        }
    });
}

export function fetchImages(type, id) {
    $.ajax({
        url: `/images/${type}/${id}`,
        type: 'GET',
        success: function (response) {
            // Check if images exist
            if (response.length > 0) {
                updateImages(response);
            }
            else {
                exit;
            }
        }
    });
}

export function updateImages(response) {
    $('#imageContainer').empty();
    response.forEach(function (image) {
        // Extract the file name from the full path
        var fileName = image.filename.substring(image.filename.lastIndexOf('/') + 1);

        // Construct the thumbnail path by replacing the file name and swapping extension to .webp
        var thumbnailPath = image.filename.replace(fileName, 'thumbnails/' + fileName.replace(/\.[^.]+$/, '') + '.webp');

        // Append a link to the real image
        $('#imageContainer').append('<a href="' + image.filename + '" data-toggle="lightbox" data-gallery="1"><img src="' + thumbnailPath + '" alt="Thumbnail"></a>&nbsp;');

        // Initialize Bootstrap 5 Lightbox on all thumbnails
        document.querySelectorAll('[data-toggle="lightbox"]').forEach(el => el.addEventListener('click', Lightbox.initialize));
    });

}

