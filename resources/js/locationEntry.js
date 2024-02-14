import {
  validateForm,
  updateLocationInfo,
  removeClickListeners
} from "./custom";

import { rebuildLocationsTable } from "./tables";

/**
 * Displays the part entry modal, initializes the location dropdown and attaches the validateForm function with the addPartCallback function
 * 
 * @param {Array} locations An array of objects containing locations
 * @return void
 */
export function callLocationEntryModal() {
  $('#mLocationEntry').modal('show'); // Show modal
  validateForm('locationEntryForm', 'addLocation', addLocationCallback); // Attach validate form 
}

/**
 * Callback function for adding a new location to the databaset table.
 * This function retrieves the values of the location name and description from the input fields in the add location modal
 * It then sends a POST request to the server to insert the new location into the database.
 * If the insertion is successful, it updates the location information, hides the add location modal and removes the click listener from the add location button.
 * It then rebuilds the locations table and selects the newly added row.
 * @return void
 */
function addLocationCallback() {
  const ln = $("#addLocationName").val();       // Part Name
  const ld = $("#addLocationDescription").val();    // Quantity

  var token = $('input[name="_token"]').attr('value');

  $.ajax({
    url: '/location.create',
    type: 'POST',
    data: {
      location_name: ln,
      location_description: ld,
    },
    headers: {
      'X-CSRF-TOKEN': token
    },
    success: function (response) {
      // Response contains 'Part ID', 'Stock Entry ID' and 'Stock Level History ID'
      var locationId = JSON.parse(response)["Location ID"];
      updateLocationInfo(locationId);
      $('#mLocationEntry').modal('hide'); // Hide modal
      removeClickListeners('#addLocation'); // Remove click listener from Add Location button

      // Rebuild locations table and select new row
      var queryString = window.location.search;
      $.when(rebuildLocationsTable(queryString)).done(function () {
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