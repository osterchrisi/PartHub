/**
 * Create the "To Location" dropdown
 * 
 * @param {string} divId The div element in which the element will be created
 * @param {Array} locations An array of associative arrays containing locations
 * 
 * @return void
 */
function toStockLocationDropdown(divId, locations) {
    var div = document.getElementById(divId);
    var selectHTML = "<label class='input-group-text' for='toStockLocation'>To</label><select class='form-select' id='toStockLocation'>";
    for (var i = 0; i < locations.length; i++) {
        selectHTML += "<option value='" + locations[i]['location_id'] + "'>" + locations[i]['location_name'] + "</option>";
    }
    selectHTML += "</select>";
    div.innerHTML = selectHTML;
    to_location_exists = true;
}

/**
 * Create the "From Location" dropdown
 * 
 * @param {string} divId The div element in which the element will be created
 * @param {Array} locations An array of associative arrays containing locations
 * 
 * @return void
 */
function fromStockLocationDropdown(divId, locations) {
    var div = document.getElementById(divId);
    var selectHTML = "<label class='input-group-text' for='fromStockLocation'>From</label><select class='form-select' id='fromStockLocation'>";
    for (var i = 0; i < locations.length; i++) {
        selectHTML += "<option value='" + locations[i]['location_id'] + "'>" + locations[i]['location_name'] + "</option>";
    }
    selectHTML += "</select>";
    div.innerHTML = selectHTML;
    from_location_exists = true;
}

/**
 * Show the stock modal and change text according to type of change.
 * Then generate location dropdown menus and selectize them.
 * Finally remove old click listener and attach new one to the 'Save Changes' button
 * 
 * @param {number} change - The type of change. '1' for adding, '-1' for reducing and '0' for moving stock
 * @param {Array} locations - An array of objects containing location information
 * @param {number} pid - The part ID for which to call the stock modal for
 */
function callStockModal(change, locations, pid) {
    if (change == 1) {
        document.getElementById('stockModalTitle').textContent = 'Add Stock';
        document.getElementById('stockChangeText').textContent = 'Add stock to ';
        $('#FromStockLocationDiv-row').hide();
        toStockLocationDropdown("ToStockLocationDiv", locations);
        $("#toStockLocation").selectize();

    }
    else if (change == -1) {
        document.getElementById('stockModalTitle').textContent = 'Reduce Stock';
        document.getElementById('stockChangeText').textContent = 'Reduce stock of ';
        $('#ToStockLocationDiv-row').hide();
        fromStockLocationDropdown("FromStockLocationDiv", locations);
        $("#fromStockLocation").selectize();
    }
    else {
        document.getElementById('stockModalTitle').textContent = 'Move Stock';
        document.getElementById('stockChangeText').textContent = 'Move stock of ';
        toStockLocationDropdown("ToStockLocationDiv", locations);
        fromStockLocationDropdown("FromStockLocationDiv", locations);
        $("#toStockLocation").selectize();
        $("#fromStockLocation").selectize();
    }

    $('#mAddStock').modal('show'); // Show modal
    removeClickListeners('#AddStock'); // Remove previously added click listener
    validateForm('stockChangingForm', 'AddStock', stockChangingFormExecution, [change, pid]); // Attach validate form 
}

/**
 * Gets the variables to prepare the stock changing array and sends it to the stock changing script via an AJAX call
 * @param change - The type of change. '1' for adding, '-1' for reducing and '0' for moving stock
 * @param {number} pid - The part ID for the stock change
 */
function stockChangingFormExecution(change, pid) {
    q = $("#addStockQuantity").val(); // Quantity
    c = $("#addStockDescription").val(); // Comment

    // Get required locations
    if (change == '1') {
        tl = $("#toStockLocation").val(); // To Location
        fl = 'NULL'; // From Location
    }
    if (change == '-1') {
        tl = 'NULL'; // To Location
        fl = $("#fromStockLocation").val(); // From Location
    }
    if (change == '0') {
        tl = $("#toStockLocation").val(); // To Location
        fl = $("#fromStockLocation").val(); // From Location
    }

    // Prepare stock changes array
    var stockChanges = [{
        quantity: q,
        to_location: tl,
        from_location: fl,
        comment: c,
        part_id: pid,
        change: change
    }];

    // Call the stock changing script
    callStockChangingScript(stockChanges, pid)
}

/**
 * Makes an AJAX call to the stock changing script. If stock change is supported by available stock, update part info window.
 * If there is stock shortage, display a message and request user permission.
 * 
 * @param {Array} stockChanges - Array containing all parameters necessary for the requested stock change
 * @param {number} pid - The part ID for which the stock is changes and later the info window updated
 * @return void
 */
function callStockChangingScript(stockChanges, pid) {

    var token = $('meta[name="csrf-token"]').attr('content');

    $.ajax({
        url: '/parts.prepareStockChanges',
        type: 'POST',
        data: { stock_changes: stockChanges },
        headers: {
            'X-CSRF-TOKEN': token
        },
        success: function (response) {
            console.log(response);
            var r = JSON.parse(response);

            if (r.negative_stock.length === 0) {
                //* Do the normal thing here, all requested stock available
                updatePartsInfo(pid);

                //TODO: This is bit of a hicky hacky but at least updates the cell for now
                var new_stock_level = r.result[2];
                var $cell = $('tr.selected-last td[data-column="total_stock"]');
                $cell.text(new_stock_level);

                // Reset modal and hide it
                $('#mAddStock').on('hidden.bs.modal', function (e) {
                    $('#FromStockLocationDiv-row').show();
                    $('#ToStockLocationDiv-row').show();
                    $('#stockChangingForm')[0].reset();
                    $('#mStockModalInfo').empty();
                    $('#AddStock').attr('disabled', false);
                    $(this).modal('dispose');
                }).modal('hide');
            }
            else {
                //* User permission required
                // Display warning and missing stock table
                $('#AddStock').attr('disabled', true);
                var message = "<div class='alert alert-warning'>There is not enough stock available for " + r.negative_stock.length + " part(s). Do you want to continue anyway?<br>";
                message += "<div style='text-align:right;'><button type='button' class='btn btn-secondary btn-sm' data-bs-dismiss='modal'>Cancel</button> <button type='submit' class='btn btn-primary btn-sm' id='btnChangeStockAnyway'>Do It Anyway</button></div></div>"
                message += r.negative_stock_table;
                $('#mStockModalInfo').html(message);

                // Attach click listener to "Do It Anyway" button
                changeStockAnywayClickListener(r, pid);
            }
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

    // $.post('/parts.prepareStockChanges', { stock_changes: stockChanges },
    //     function (response) {
    //         console.log(response);
    //         var r = JSON.parse(response);

    //         if (r.negative_stock.length === 0) {
    //             //* Do the normal thing here, all requested stock available
    //             updatePartsInfo(pid);

    //             //TODO: This is bit of a hicky hacky but at least updates the cell for now
    //             var new_stock_level = r.result[2];
    //             var $cell = $('tr.selected-last td[data-column="total_stock"]');
    //             $cell.text(new_stock_level);

    //             // Reset modal and hide it
    //             $('#mAddStock').on('hidden.bs.modal', function (e) {
    //                 $('#FromStockLocationDiv-row').show();
    //                 $('#ToStockLocationDiv-row').show();
    //                 $('#stockChangingForm')[0].reset();
    //                 $('#mStockModalInfo').empty();
    //                 $('#AddStock').attr('disabled', false);
    //                 $(this).modal('dispose');
    //             }).modal('hide');
    //         }
    //         else {
    //             //* User permission required
    //             // Display warning and missing stock table
    //             $('#AddStock').attr('disabled', true);
    //             var message = "<div class='alert alert-warning'>There is not enough stock available for " + r.negative_stock.length + " part(s). Do you want to continue anyway?<br>";
    //             message += "<div style='text-align:right;'><button type='button' class='btn btn-secondary btn-sm' data-bs-dismiss='modal'>Cancel</button> <button type='submit' class='btn btn-primary btn-sm' id='btnChangeStockAnyway'>Do It Anyway</button></div></div>"
    //             message += r.negative_stock_table;
    //             $('#mStockModalInfo').html(message);

    //             // Attach click listener to "Do It Anyway" button
    //             changeStockAnywayClickListener(r, pid);
    //         }
    //     });
}

/**
 * Changes the status of all requested changes to 'gtg' (good to go).
 * Attaches a click listener to the 'Do It Anyway' button and makes an AJAX call to the stock changing script (again) with the
 * requested stock changes now all set to 'gtg'.
 * 
 * @param {Array} r - The response of the stock changing script containing the initially requested changes with one of two statuses:
 * - 'gtg': Good to go
 * - 'permission_required': User permission required
 * @param {number} pid - The part ID for which the stock change was requested and the info window will be updated
 * @return void
 */
function changeStockAnywayClickListener(r, pid) {
    $('#btnChangeStockAnyway').on('click', function () {
        // //! In stock changing JS, this doesn't even have IDs array yet
        // //TODO: Passing ids for updating table after success but this won't work in the future for selectively updating
        // //TODO: Left it away for now and just hard-coding. Would be nice to unify in the future
        // continueAnyway(r, ids);

        for (const change of r.changes) {
            change.status = 'gtg';
        }
        // Call the stock changing script with the already prepared stock changes
        $.post('/PartHub/includes/prepareStockChanges.php', { stock_changes: r.changes },
            function (response) {
                console.log(response);
                // Reset modal and hide it
                $('#mAddStock').on('hidden.bs.modal', function (e) {
                    $('#FromStockLocationDiv-row').show();
                    $('#ToStockLocationDiv-row').show();
                    $('#stockChangingForm')[0].reset();
                    $('#mStockModalInfo').empty();
                    $('#AddStock').attr('disabled', false);
                    $(this).modal('dispose');
                }).modal('hide');
                //TODO: This can't work here (not giving any ids yet like in BOMs)
                // updatePartsInfo(ids[ids.length - 1]); // Update BOM info with last BOM ID in array
                updatePartsInfo(pid);
            }
        )
    });
}

/**
 * Event handler for removing all HTML elements inside "FromStockLocationDiv" and "ToStockLocationDiv" divs when the "mAddStock" modal is hidden.
 * This to keep them from stacking up.
 */
$('#mAddStock').on('hidden.bs.modal', function () {
    emptyDivFromHTML("FromStockLocationDiv");
    emptyDivFromHTML("ToStockLocationDiv");

});

/**
 * Empties a div from all its HTML elements by its element ID
 * @param {string} id - The div ID 
 * @return void
 */
function emptyDivFromHTML(id) {
    var div = document.getElementById(id);
    div.innerHTML = '';
}