// Modify the "Save Changes" click listener when the modal is toggled
function callPartEntryModal(locations) {
  addPartLocationDropdown(locations)
  $('#mPartEntry').modal('show'); // Show modal
  validateForm('partEntryForm', 'addPart', addPartCallback); // Attach validate form 
}

/**
* Callback function for adding a new part to the databaset table.
* This function retrieves the values of the part name, quantity and location from the relevant input fields in the add part modal, and then sends a POST request to the server to insert the new part into the database. If the insertion is successful, it updates the parts information, hides the add part modal and removes the click listener from the add part button. It then rebuilds the parts table and selects the newly added row.
*/
function addPartCallback() {
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