var from_location_exists = false;
var to_location_exists = false;

// Creat the "To Location" dropdown
function toStockLocationDropdown(locations) {
    var div = document.getElementById("ToStockLocationDiv");
    var selectHTML = "<select class='form-select' id='addStockLocation'>";
    for (var i = 0; i < locations.length; i++) {
        selectHTML += "<option value='" + locations[i]['location_id'] + "'>" + locations[i]['location_name'] + "</option>";
    }
    selectHTML += "</select><label for='addStockLocation'>to</label>";
    div.innerHTML = selectHTML;
    to_location_exists = true;
}

// Creat the "From Location" dropdown
function fromStockLocationDropdown(locations){
    var div = document.getElementById("FromStockLocationDiv");
    var selectHTML = "<select class='form-select' id='fromStockLocation'>";
    for (var i = 0; i < locations.length; i++) {
        selectHTML += "<option value='" + locations[i]['location_id'] + "'>" + locations[i]['location_name'] + "</option>";
    }
    selectHTML += "</select><label for='fromStockLocation'>from</label>";
    div.innerHTML = selectHTML;
    from_location_exists = true;
}

//TODO: Give argument to removeClickListener for which element to remove the listener
// Modify the "Save Changes" click listener when the modal is toggled
function callStockModal(change, locations) {
    //TODO: Make the form look good
    if (change == 1) {
        document.getElementById('stockModalTitle').textContent = 'Add Stock';
        document.getElementById('stockChangeText').textContent = 'Add stock to ';
        toStockLocationDropdown(locations);

    }
    else if (change == -1) {
        document.getElementById('stockModalTitle').textContent = 'Reduce Stock';
        document.getElementById('stockChangeText').textContent = 'Reduce stock of ';
        fromStockLocationDropdown(locations);
    }
    else {
        document.getElementById('stockModalTitle').textContent = 'Move Stock';
        document.getElementById('stockChangeText').textContent = 'Move stock of ';
        toStockLocationDropdown(locations);
        fromStockLocationDropdown(locations);
    }

    $('#mAddStock').modal('show'); // Show modal
    removeClickListeners(); // Remove previously added click listener
    saveChanges(change);
}

// ClickListener for "Save Changes" button in Add Stock Modal
function saveChanges(change) {
    $('#AddStock').click(function () {
        q = $("#addStockQuantity").val(); // Quantity
        c = $("#addStockDescription").val(); // Comment
        

        if (change == '1') {
            tl = $("#addStockLocation").val(); // To Location
            fl = 'NULL'; // From Location
        }
        if (change == '-1') {
            tl = 'NULL'; // To Location
            fl = $("#fromStockLocation").val(); // From Location
        }
        if (change == '0') {
            tl = $("#addStockLocation").val(); // To Location
            fl = $("#fromStockLocation").val(); // From Location
        }

        //? Okay, this looks weird, maybe there is a cleaner way?
        uid = <?php echo json_encode($_SESSION['user_id']); ?>;
        pid = <?php echo json_encode($part_id); ?>;

        console.log(q, c, tl, fl, uid, pid);

        // Call the stock changing script
        $.post('/PartHub/includes/stockChanges.php',
            { quantity: q, to_location: tl, from_location: fl, comment: c, user_id: uid, part_id: pid, change: change },
            function (response) {
                console.log("Succesfully created new stock history entry with number: ", response);
                updatePartsInfo(pid);
                $("#mAddStock").hide(); // Hide stockChange modal
                removeFromLocationDropdown();
            });
    });
}

// Remove the previous click listener
function removeClickListeners() {
    $('#AddStock').off('click');
}

// Remove the "from locations" dropdown
$('#mAddStock').on('hidden.bs.modal', function () {
    if (from_location_exists) {
    removeFromLocationDropdown("FromStockLocationDiv");
    from_location_exists = false;
    }

    if (to_location_exists) {
    removeFromLocationDropdown("ToStockLocationDiv");
    to_location_exists = false;
    }
});

function removeFromLocationDropdown(location) {
    var div = document.getElementById(location);
    div.innerHTML = '';
}