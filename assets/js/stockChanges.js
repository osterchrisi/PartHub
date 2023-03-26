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

var modalEventListenerAdded = false;

// Modify the "Save Changes" click listener when the modal is toggled
function dup(change) {
    console.log("change = ", change);
    $('#mAddStock').modal('show');

    if (!modalEventListenerAdded) {
        var button = $(event.relatedTarget); // Button that triggered the modal
        // var change = button.data('change');
        console.log("change = ", change);
        removeClickListeners();
        saveChanges(change);
        modalEventListenerAdded = true;
    }
}

// Remove the previous click listener
function removeClickListeners() {
    $('#AddStock').off('click');
    modalEventListenerAdded = false;
}