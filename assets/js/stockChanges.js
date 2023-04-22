var from_location_exists = false;
var to_location_exists = false;

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
        uid = <?php echo json_encode($_SESSION['user_id']); ?>;
        pid = <?php echo json_encode($part_id); ?>;

        var stockChanges = [{
            quantity: q,
            to_location: tl,
            from_location: fl,
            comment: c,
            user_id: uid,
            part_id: pid,
            change: change
        }];

        // console.log(stockChanges);

        // Call the stock changing script
        $.post('/PartHub/includes/stockChanges.php', { stock_changes: stockChanges },
            function (response) {
                console.log("Sent:\n");
                console.log(stockChanges);
                console.log("Recieved:\n");
                console.log(response);

                //* Original code before iterating iterations down here:
                // console.log("Succesfully created new stock history entry with number: ", response);
                // updatePartsInfo(pid);

                // //TODO: This is bit of a hicky hacky but at least updates the cell for now
                // var new_stock_level = JSON.parse(response)[2];
                // var $cell = $('tr.selected-last td[data-column="total_stock"]');
                // $cell.text(new_stock_level);

                // $("#mAddStock").hide(); // Hide stockChange modal
            });
    });
}

// Remove the locations dropdowns to keep them from stacking up
$('#mAddStock').on('hidden.bs.modal', function () {
    if (from_location_exists) {
        removeLocationDropdown("FromStockLocationDiv");
        from_location_exists = false;
    }

    if (to_location_exists) {
        removeLocationDropdown("ToStockLocationDiv");
        to_location_exists = false;
    }
});

// Remove a dropdown by ID
function removeLocationDropdown(location) {
    var div = document.getElementById(location);
    div.innerHTML = '';
}