import {
  validateForm,
  removeClickListeners,
  updateInfoWindow
} from "./custom";

import { rebuildLocationsTable } from "./tables";

/**
 * Displays the location entry modal and attaches the validateForm function with the addLocationCallback function
 * 
 * @param {Array} locations An array of objects containing locations
 * @return void
 */
export function callLocationEntryModal() {
  $('#mLocationEntry').modal('show'); // Show modal
  validateForm('locationEntryForm', 'addLocation', addLocationCallback); // Attach validate form 
}

/**
 * Callback function for adding a new location to the database table.
 * This function retrieves the values of the location name and description from the input fields in the add location modal
 * It then sends a POST request to the server to insert the new location into the database.
 * If the insertion is successful, it updates the location information, hides the add location modal and removes the click listener from the add location button.
 * It then rebuilds the locations table and selects the newly added row.
 * @return void
 */
function addLocationCallback() {
  const ln = $("#addLocationName").val();           // Location Name
  const ld = $("#addLocationDescription").val();    // Location Description

  var token = $('input[name="_token"]').attr('value'); // X-CSRF Token

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
      // Response contains the new 'Location ID'
      var locationId = JSON.parse(response)["Location ID"];
      updateInfoWindow('location', locationId);       // Update info window
      $('#mLocationEntry').modal('hide');             // Hide modal
      removeClickListeners('#addLocation');           // Remove click listener from Add Location button

      // Rebuild locations table and select new row
      var queryString = window.location.search;
      $.when(rebuildLocationsTable(queryString)).done(function () {
        $('tr[data-id="' + locationId + '"]').addClass('selected selected-last');
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
        $('#mLocationEntry').modal('hide');   // Hide modal
        removeClickListeners('#addLocation'); // Remove click listener from Add Location button
      }
    }
  });
}