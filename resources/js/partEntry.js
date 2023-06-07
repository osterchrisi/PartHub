import { validateForm } from "./custom";
/**
 * Displays the part entry modal, initializes the location dropdown and attaches the validateForm function with the addPartCallback function
 * 
 * @param {Array} locations An array of objects containing locations
 * @return void
 */
export function callPartEntryModal(locations) {
  addPartLocationDropdown(locations);
  $('#mPartEntry').modal('show'); // Show modal
  validateForm('partEntryForm', 'addPart', addPartCallback); // Attach validate form 
}

/**
 * Callback function for adding a new part to the databaset table.
 * This function retrieves the values of the part name, quantity and location from the relevant input fields in the add part modal, and then sends a POST request to the server to insert the new part into the database. If the insertion is successful, it updates the parts information, hides the add part modal and removes the click listener from the add part button. It then rebuilds the parts table and selects the newly added row.
 * @return void
 */
function addPartCallback() {
  const pn = $("#addPartName").val();       // Part Name
  const q = $("#addPartQuantity").val();    // Quantity
  const l = $("#addPartLocSelect").val();   // Location
  const c = $("#addPartComment").val();     // Comment
  const d = $("#addPartDescription").val(); // Description

  var token = $('input[name="_token"]').attr('value');

  $.ajax({
    url: '/parts.create',
    type: 'POST',
    data: { part_name: pn,
            quantity: q,
            to_location: l,
            comment: c,
            description: d },
    headers: {
      'X-CSRF-TOKEN': token
    },
    success: function (response) {
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
    },
    error: function (xhr) {
      // Handle the error
      if (xhr.status === 419) {
        // Token mismatch error
        alert('CSRF token mismatch. Please refresh the page and try again.');
      } else {
        // Other errors
        alert('An error occurred. Please try again.');
      }
    }
  });


  // // Inset new part into table
  // $.post('/parts.createPart',
  //   { part_name: pn, quantity: q, to_location: l },
  //   function (response) {
  //     // Response contains 'Part ID', 'Stock Entry ID' and 'Stock Level History ID'
  //     var partId = JSON.parse(response)["Part ID"];
  //     updatePartsInfo(partId);
  //     $('#mPartEntry').modal('hide'); // Hide modal
  //     removeClickListeners('#addPart'); // Remove click listener from Add Part button

  //     // Rebuild parts table and select new row
  //     var queryString = window.location.search;
  //     $.when(rebuildPartsTable(queryString)).done(function () {
  //       $('tr[data-id="' + partId + '"]').addClass('selected selected-last');
  //     });
  //   });
}

/**
 * 
 * Creates and adds a dropdown list of locations to the part entry modal and 'selectizes' it.
 * @param {Array} locations - An array of objects representing locations to be displayed in the dropdown list.
 * Each location object must have a "location_id" and a "location_name" property.
 * @return {void}
 */
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