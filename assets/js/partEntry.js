// Modify the "Save Changes" click listener when the modal is toggled
function callPartEntryModal() {
    $('#mPartEntry').modal('show'); // Show modal
    validateForm('partEntryForm', 'addPart');
    // saveChanges(change);
}

// Validate required fields in part adding modal
function validateForm(formId, button){
const form = document.getElementById(formId);
const submitBtn = document.getElementById(button);

// Form validation
submitBtn.addEventListener('click', function(event) {
  event.preventDefault();
  if (form.checkValidity()) {
    // Form is valid
    form.submit();
    pn = $("#addPartName").val(); // Part Name
    q = $("#addPartQuantity").val(); // Quantity
    l = $("#addPartLocId").val(); // Quantity

    // Inset new part into table
    $.post('/PartHub/includes/create-part.php',
    { part_name: pn, quantity: q, to_location: l},
    function (response) {
        console.log("Succesfully created new part with these beautiful IDs: ", response);
        console.log(response);
        var partId = JSON.parse(response)["Part ID"];
        console.log(partId);
        updatePartsInfo(partId);
        $('#mPartEntry').modal('hide'); // Hide modal
    });
    
  } else {
    // Form is invalid (required fields not filled)
    form.querySelectorAll('[required]').forEach(function(field) {
      if (field.checkValidity()) {
        field.classList.remove('is-invalid');
        field.classList.add('is-valid');
      } else {
        field.classList.remove('is-valid');
        field.classList.add('is-invalid');
      }
    });
  }
});
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