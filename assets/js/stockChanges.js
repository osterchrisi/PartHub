// var from_location_exists = false;
// var to_location_exists = false;

// Create the "To Location" dropdown
function toStockLocationDropdown(locations) {
    var div = document.getElementById("ToStockLocationDiv");
    var selectHTML = "<label class='input-group-text' for='fromStockLocation'>To</label><select class='form-select' id='toStockLocation'>";
    for (var i = 0; i < locations.length; i++) {
        selectHTML += "<option value='" + locations[i]['location_id'] + "'>" + locations[i]['location_name'] + "</option>";
    }
    selectHTML += "</select>";
    div.innerHTML = selectHTML;
    to_location_exists = true;
}

// Create the "From Location" dropdown
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

// Show the Stock Modal, remove old click listener and attach new one
function callStockModal(change, locations) {
    if (change == 1) {
        document.getElementById('stockModalTitle').textContent = 'Add Stock';
        document.getElementById('stockChangeText').textContent = 'Add stock to ';
        toStockLocationDropdown(locations);
        $("#toStockLocation").selectize();

    }
    else if (change == -1) {
        document.getElementById('stockModalTitle').textContent = 'Reduce Stock';
        document.getElementById('stockChangeText').textContent = 'Reduce stock of ';
        fromStockLocationDropdown("FromStockLocationDiv", locations);
        $("#fromStockLocation").selectize();
    }
    else {
        document.getElementById('stockModalTitle').textContent = 'Move Stock';
        document.getElementById('stockChangeText').textContent = 'Move stock of ';
        toStockLocationDropdown(locations);
        fromStockLocationDropdown("FromStockLocationDiv", locations);
        $("#toStockLocation").selectize();
        $("#fromStockLocation").selectize();
    }

    $('#mAddStock').modal('show'); // Show modal
    removeClickListeners('#AddStock'); // Remove previously added click listener
    stockChangeSaveChangesClickListener(change); // Add click listener to the Save Changes button
}

// ClickListener for "Save Changes" button in Add Stock Modal
function stockChangeSaveChangesClickListener(change) {
    $('#AddStock').click(function () {
        q = $("#addStockQuantity").val(); // Quantity
        c = $("#addStockDescription").val(); // Comment

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

        //? Okay, this looks weird, maybe there is a cleaner way?
        //TODO: Make this better
        pid = <?php echo json_encode($part_id); ?>;

        var stockChanges = [{
            quantity: q,
            to_location: tl,
            from_location: fl,
            comment: c,
            part_id: pid,
            change: change
        }];

        console.log(stockChanges);

        // Call the stock changing script
        $.post('/PartHub/includes/prepareStockChanges.php', { stock_changes: stockChanges },
            function (response) {
                console.log(response);
                var r = JSON.parse(response);

                if (r.negative_stock.length === 0) {
                    //* Do the normal thing here, all requested stock available
                    console.log("This");
                    updatePartsInfo(pid);

                    // //TODO: This is bit of a hicky hacky but at least updates the cell for now
                    var new_stock_level = r.result[2];

                    var $cell = $('tr.selected-last td[data-column="total_stock"]');
                    $cell.text(new_stock_level);

                    // Reset modal and hide it
                    $('#mAddStock').on('hidden.bs.modal', function (e) {
                        $('#stockChangingForm')[0].reset();
                        $('#mStockModalInfo').empty();
                        $('#AddStock').attr('disabled', false);
                        $(this).modal('dispose');
                    }).modal('hide');
                }
                else {
                    //* User permission required
                    // Display warning and missing stock table
                    console.log("That");
                    $('#AddStock').attr('disabled', true);
                    var message = "<div class='alert alert-warning'>There is not enough stock available for " + r.negative_stock.length + " part(s). Do you want to continue anyway?<br>";
                    message += "<div style='text-align:right;'><button type='button' class='btn btn-secondary btn-sm' data-bs-dismiss='modal'>Cancel</button> <button type='submit' class='btn btn-primary btn-sm' id='btnChangeStockAnyway'>Do It Anyway</button></div></div>"
                    message += r.negative_stock_table;
                    $('#mStockModalInfo').html(message);

                    // Attach click listener to "Do It Anyway" button
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
                                $('#mAddStock').on('hidden.bs.modal', function (e) {
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
            });
    });
}

// Remove the locations dropdowns to keep them from stacking up
$('#mAddStock').on('hidden.bs.modal', function () {
    removeLocationDropdown("FromStockLocationDiv");
    removeLocationDropdown("ToStockLocationDiv");

});

// Remove a dropdown by ID
function removeLocationDropdown(location) {
    var div = document.getElementById(location);
    div.innerHTML = '';
}