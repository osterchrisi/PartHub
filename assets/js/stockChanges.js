//TODO: Give argument to removeClickListener for which element to remove the listener
// Modify the "Save Changes" click listener when the modal is toggled
function callStockModal(change) {

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
        document.getElementById('moveStockLocation').textContent = 'Another select element coming to your neighborhood soon!';
        //TODO: This text ^ must be removed again
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
        console.log(typeof(change));
        fl = 0;
        if (change == '0') {
            fl = 1;
            //fl = $("#moveStockLocation").val(); // From Location
        }


        //? Okay, this looks weird, maybe there is a cleaner way?
        uid = <?php echo json_encode($_SESSION['user_id']); ?>;
        pid = <?php echo json_encode($part_id); ?>;

        $.post('/PartHub/includes/stockChanges.php',
            { quantity: q, to_location: tl, from_location: fl, comment: c, user_id: uid, part_id: pid, change: change },
            function (response) {
                console.log("Succesfully created new stock history entry with number: ", response);
                updatePartsInfo(pid);
                $("#mAddStock").hide(); // Hide stockChange modal
            });
    });
}

// Remove the previous click listener
function removeClickListeners() {
    $('#AddStock').off('click');
}