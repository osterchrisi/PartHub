var from_location_exists = false;

//TODO: Give argument to removeClickListener for which element to remove the listener
// Modify the "Save Changes" click listener when the modal is toggled
function callStockModal(change, locations) {

    if (change == 1) {
        document.getElementById('stockModalTitle').textContent = 'Add Stock';
        document.getElementById('stockChangeText').textContent = 'Add stock to ';
    }
    else if (change == -1) {
        document.getElementById('stockModalTitle').textContent = 'Reduce Stock';
        document.getElementById('stockChangeText').textContent = 'Reduce stock of ';
    }
    else {
        document.getElementById('stockModalTitle').textContent = 'Move Stock';
        document.getElementById('stockChangeText').textContent = 'Move stock of ';
        // "From location" dropdown
        var div = document.getElementById("moveStockLocationDiv");
        var selectHTML = "<br><select class='form-select' id='moveStockLocation'>";
        for (var i = 0; i < locations.length; i++) {
            selectHTML += "<option value='" + locations[i]['location_id'] + "'>" + locations[i]['location_name'] + "</option>";
        }
        selectHTML += "</select>";
        div.innerHTML = selectHTML;
        from_location_exists = true;
    }

    $('#mAddStock').modal('show'); // Show modal
    console.log("change = ", change);
    removeClickListeners(); // Remove previously added click listener
    saveChanges(change);
}

// ClickListener for "Save Changes" button in Add Stock Modal
function saveChanges(change) {
    $('#AddStock').click(function () {
        q = $("#addStockQuantity").val(); // Quantity
        c = $("#addStockDescription").val(); // Comment
        tl = $("#addStockLocation").val(); // To Location
        fl = 5;
        if (change == '0') {
            fl = $("#moveStockLocation").val(); // From Location
        }

        //? Okay, this looks weird, maybe there is a cleaner way?
        uid = <?php echo json_encode($_SESSION['user_id']); ?>;
        pid = <?php echo json_encode($part_id); ?>;

        // Call the stock changing script
        $.post('/PartHub/includes/stockChanges.php',
            { quantity: q, to_location: tl, from_location: fl, comment: c, user_id: uid, part_id: pid, change: change },
            function (response) {
                console.log("Succesfully created new stock history entry with number: ", response);
                console.log("from_location_exists: ", from_location_exists);
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
    removeFromLocationDropdown();
    }
});

function removeFromLocationDropdown() {
    console.log("Removing dropdown");
    var div = document.getElementById("moveStockLocationDiv");
    div.innerHTML = '';
    from_location_exists = false;
}