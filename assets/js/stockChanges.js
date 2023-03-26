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

// Modify the "Save Changes" click listener when the modal is toggled
function callStockModal(change) {
    $('#mAddStock').modal('show');
    console.log("change = ", change);
    removeClickListeners(); //Remove previous click listener
    saveChanges(change);
}

// Remove the previous click listener
function removeClickListeners() {
    $('#AddStock').off('click');
}