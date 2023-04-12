// Modify the "Save Changes" click listener when the modal is toggled
function callPartEntryModal() {
  $('#mPartEntry').modal('show'); // Show modal
  removeClickListeners('#mPartEntry'); // Remove previously added click listener
  validateForm('partEntryForm', 'addPart');
  // saveChanges(change);
}

// Validate required fields in part adding modal
function validateForm(formId, button) {
  const form = document.getElementById(formId);
  const submitBtn = document.getElementById(button);

  // Form validation
  submitBtn.addEventListener('click', function (event) {
    event.preventDefault();
    if (form.checkValidity()) {
      // Form is valid
      pn = $("#addPartName").val(); // Part Name
      q = $("#addPartQuantity").val(); // Quantity
      l = $("#addPartLocId").val(); // Quantity

      // Inset new part into table
      $.post('/PartHub/includes/create-part.php',
        { part_name: pn, quantity: q, to_location: l },
        function (response) {
          // Response contains 'Part ID', 'Stock Entry ID' and 'Stock Level History ID'
          var partId = JSON.parse(response)["Part ID"];
          updatePartsInfo(partId);
          $('#mPartEntry').modal('hide'); // Hide modal
          var queryString = window.location.search;
          $.ajax({
            url: '../includes/buildPartsTable.php' + queryString,
            success: function (data) {
              $('#table-window').html(data);
              bootstrapPartsTable();
              workThatTable();
            }
          });
        });

    } else {
      // Form is invalid (required fields not filled)
      form.querySelectorAll('[required]').forEach(function (field) {
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