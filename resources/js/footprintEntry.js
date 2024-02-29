import {
  validateForm,
  updateFootprintInfo,
  removeClickListeners
} from "./custom";

import { rebuildFootprintsTable } from "./tables";

/**
 * Displays the footprint entry modal and attaches the validateForm function with the addFootprintCallback function
 * 
 * @param {Array} footprints An array of objects containing footprints
 * @return void
 */
export function callFootprintEntryModal() {
  $('#mFootprintEntry').modal('show'); // Show modal
  validateForm('footprintEntryForm', 'addFootprint', addFootprintCallback); // Attach validate form 
}

/**
 * Callback function for adding a new footprint to the database table.
 * This function retrieves the values of the footprint name and alias from the input fields in the add footprint modal
 * It then sends a POST request to the server to insert the new footprint into the database.
 * If the insertion is successful, it updates the footprint information, hides the add footprint modal and removes the click listener from the add footprint button.
 * It then rebuilds the footprints table and selects the newly added row.
 * @return void
 */
function addFootprintCallback() {
  const fn = $("#addFootprintName").val();     // Footprint Name
  const fa = $("#addFootprintAlias").val();    // Footprint Alias

  var token = $('input[name="_token"]').attr('value'); // X-CSRF Token

  $.ajax({
    url: '/footprint.create',
    type: 'POST',
    data: {
      footprint_name: fn,
      footprint_alias: fa,
    },
    headers: {
      'X-CSRF-TOKEN': token
    },
    success: function (response) {
      // Response contains the new 'Footprint ID'
      var footprintId = JSON.parse(response)["Footprint ID"];
      updateFootprintInfo(footprintId);       // Update info window
      $('#mFootprintEntry').modal('hide');    // Hide modal
      removeClickListeners('#addFootprint');  // Remove click listener from Add Footprint button

      // Rebuild footprints table and select new row
      var queryString = window.location.search;
      $.when(rebuildFootprintsTable(queryString)).done(function () {
        $('tr[data-id="' + footprintId + '"]').addClass('selected selected-last');
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
        $('#mFootprintEntry').modal('hide');    // Hide modal
        removeClickListeners('#addFootprint');  // Remove click listener from Add Footprint button
      }
    }
  });
}