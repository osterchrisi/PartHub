// ClickListener for "Save Changes" button in Add Stock Modal
function saveChanges(change) {
    $('#AddStock').click(function () {
        q = $("#addStockQuantity").val();
        c = $("#addStockDescription").val();
        l = $("#addStockLocation").val();

        //? Okay, this looks weird, maybe there is a cleaner way?
        uid = <?php echo json_encode($_SESSION['user_id']); ?>;
        pid = <?php echo json_encode($part_id); ?>;

        $.post('/PartHub/includes/stockChanges.php',
            { quantity: q, to_location: l, comment: c, user_id: uid, part_id: pid, change: change },
            function (response) {
                console.log("Succesfully created new stock history entry with number: ", response);
                updatePartsInfo(pid);
                $("#mAddStock").hide(); // Hide stockChange modal
            });
    });
}

//TODO: Give argument to removeClickListener for which element to remove the listener
// Modify the "Save Changes" click listener when the modal is toggled
function callStockModal(change) {

    if (change == 1) {
        document.getElementById('stockModalTitle').textContent = 'Add Stock';
        document.getElementById('stockChangeText').textContent = 'Add stock to ';
    }
    else if (change == -1) {
        document.getElementById('stockModalTitle').textContent = 'Reduce Stock';
        document.getElementById('stockChangeText').textContent = 'Reduce stock of';
    }
    else {
        document.getElementById('stockModalTitle').textContent = 'Move Stock';
        document.getElementById('stockChangeText').textContent = 'Move stock of';
        //TODO: Prepare stock moving form
    }

    $('#mAddStock').modal('show'); // Show modal
    console.log("change = ", change);
    removeClickListeners(); // Remove previously added click listener
    saveChanges(change);
}

// Remove the previous click listener
function removeClickListeners() {
    $('#AddStock').off('click');
}