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
            initializeShowLocation();
        },
        error: function (xhr) {
            if (xhr.status === 401) {
                $('#info-window').html('Your session expired. Please login again.')
            }
            else {
                // Display an error message if the PHP page failed to load
                $('#info-window').html('Failed to load additional location data.');
            }
        }
    });
};

/**
 * Updates the category info in the info window using an AJAX request.
 * @param {number} id - The ID of the category to update the info for.
 * @return void
 */
export function updateCategoryInfo(id) {
    $.ajax({
        url: '/category/' + id,
        type: 'GET',
        data: {},
        success: function (data) {
            // Replace the content of the info window with the loaded PHP page
            $('#info-window').html(data);
            initializeShowCategory();
        },
        error: function (xhr) {
            if (xhr.status === 401) {
                $('#info-window').html('Your session expired. Please login again.')
            }
            else {
                // Display an error message if the PHP page failed to load
                $('#info-window').html('Failed to load additional category data.');
            }
        }
    });
};

/**
 * Updates the supplier info in the info window using an AJAX request.
 * @param {number} id - The ID of the supplier to update the info for.
 * @return void
 */
export function updateSupplierInfo(id) {
    $.ajax({
        url: '/supplier/' + id,
        type: 'GET',
        data: {},
        success: function (data) {
            // Replace the content of the info window with the loaded PHP page
            $('#info-window').html(data);
            initializeShowSupplier();
        },
        error: function (xhr) {
            if (xhr.status === 401) {
                $('#info-window').html('Your session expired. Please login again.')
            }
            else {
                // Display an error message if the PHP page failed to load
                $('#info-window').html('Failed to load additional supplier data.');
            }
        }
    });
};