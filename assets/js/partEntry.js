// Modify the "Save Changes" click listener when the modal is toggled
function callPartEntryModal(locations) {
  addPartLocationDropdown(locations)
  $('#mPartEntry').modal('show'); // Show modal
  validateForm('partEntryForm', 'addPart');
}

// Validate required fields in part adding modal
function validateForm(formId, button) {
  const form = document.getElementById(formId);
  const submitBtn = document.getElementById(button);

  // Form validation
  $('#addPart').click(function (event) {
    // submitBtn.addEventListener('click', function (event) {
    event.preventDefault();
    if (form.checkValidity()) {
      // Form is valid
      pn = $("#addPartName").val(); // Part Name
      q = $("#addPartQuantity").val(); // Quantity
      l = $("#addPartLocSelect").val(); // Location
      console.log(pn, q, l);

      // Inset new part into table
      $.post('/PartHub/includes/create-part.php',
        { part_name: pn, quantity: q, to_location: l },
        function (response) {
          // Response contains 'Part ID', 'Stock Entry ID' and 'Stock Level History ID'
          var partId = JSON.parse(response)["Part ID"];
          updatePartsInfo(partId);
          $('#mPartEntry').modal('hide'); // Hide modal
          removeClickListeners('#addPart'); // Remove click listener from Add Part button

          // Rebuild parts table and select new row
          var queryString = window.location.search;
          $.when(rebuildPartsTable(queryString)).done(function () {
            $('tr[data-id="' + partId + '"]').addClass('selected selected-last');
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

// Create the part entry location dropdown
function addPartLocationDropdown(locations) {
  var div = document.getElementById("addPartLocDropdown");
  var selectHTML = "<label class='input-group-text' for='fromStockLocation'>To</label><select class='form-select' id='addPartLocSelect' required>";
  for (var i = 0; i < locations.length; i++) {
    selectHTML += "<option value='" + locations[i]['location_id'] + "'>" + locations[i]['location_name'] + "</option>";
  }
  selectHTML += "</select>";
  div.innerHTML = selectHTML;
  $("#addPartLocSelect").selectize();
}