// Modify the "Save Changes" click listener when the modal is toggled
function callPartEntryModal() {
    $('#mPartEntry').modal('show'); // Show modal
    // saveChanges(change);
}

// ClickListener for "Save Changes" button in Part Entry Modal
//! Just copied from stock changing, not yet working
// function saveChanges(change) {
//     $('#AddStock').click(function () {
//         q = $("#addStockQuantity").val(); // Quantity
//         c = $("#addStockDescription").val(); // Comment
        

//         if (change == '1') {
//             tl = $("#toStockLocation").val(); // To Location
//             fl = 'NULL'; // From Location
//         }
//         if (change == '-1') {
//             tl = 'NULL'; // To Location
//             fl = $("#fromStockLocation").val(); // From Location
//         }
//         if (change == '0') {
//             tl = $("#toStockLocation").val(); // To Location
//             fl = $("#fromStockLocation").val(); // From Location
//         }

//         //? Okay, this looks weird, maybe there is a cleaner way?
//         uid = <?php echo json_encode($_SESSION['user_id']); ?>;
//         pid = <?php echo json_encode($part_id); ?>;

//         // Call the stock changing script
//         $.post('/PartHub/includes/stockChanges.php',
//             { quantity: q, to_location: tl, from_location: fl, comment: c, user_id: uid, part_id: pid, change: change },
//             function (response) {
//                 console.log("Succesfully created new stock history entry with number: ", response);
//                 updatePartsInfo(pid);
//                 $("#mAddStock").hide(); // Hide stockChange modal
//             });
//     });
// }