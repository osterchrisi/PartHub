import {
  validateForm,
  updateFootprintInfo,
  removeClickListeners
} from "./custom";

import { rebuildFootprintsTable} from "./tables";

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
 * Callback function for adding a new footprint to the databaset table.
 * This function retrieves the values of the footprint name and description from the input fields in the add footprint modal
 * It then sends a POST request to the server to insert the new footprint into the database.
 * If the insertion is successful, it updates the footprint information, hides the add footprint modal and removes the click listener from the add footprint button.
 * It then rebuilds the footprints table and selects the newly added row.
 * @return void
 */
function addFootprintCallback() {
  const ln = $("#addFootprintName").val();       // Footprint Name
  const ld = $("#addFootprintAlias").val();    // Footprint Alias

  var token = $('input[name="_token"]').attr('value');

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
      // Response contains 'Part ID', 'Stock Entry ID' and 'Stock Level History ID'
      var footprintId = JSON.parse(response)["Footprint ID"];
      updateFootprintInfo(footprintId);
      $('#mFootprintEntry').modal('hide'); // Hide modal
      removeClickListeners('#addFootprint'); // Remove click listener from Add Footprint button

      // Rebuild footprints table and select new row
      var queryString = window.footprint.search;
      $.when(rebuildFootprintsTable(queryString)).done(function () {
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
 * Creates and adds a dropdown list of footprints to the part entry modal and 'selectizes' it.
 * @param {Array} footprints - An array of objects representing footprints to be displayed in the dropdown list.
 * Each footprint object must have a "footprint_id" and a "footprint_name" property.
 * @return {void}
 */
function addPartFootprintDropdown(footprints) {
  var div = document.getElementById("addPartLocDropdown");
  var selectHTML = "<label class='input-group-text' for='fromStockFootprint'>To</label><select class='form-select' id='addPartLocSelect' required>";
  for (var i = 0; i < footprints.length; i++) {
    selectHTML += "<option value='" + footprints[i]['footprint_id'] + "'>" + footprints[i]['footprint_name'] + "</option>";
  }
  selectHTML += "</select>";
  div.innerHTML = selectHTML;
  $("#addPartLocSelect").selectize();
}