import {
  validateForm,
  removeClickListeners,
  updateInfoWindow
} from "./custom";

import { rebuildLocationsTable } from "./tables";

/**
 * Displays the category entry modal and attaches the validateForm function with the addLocationCallback function
 * 
 * @param {Array} categories An array of objects containing categories
 * @return void
 */
export function callLocationEntryModal() {
  $('#mLocationEntry').modal('show'); // Show modal
  validateForm('locationEntryForm', 'addLocation', addLocationCallback); // Attach validate form 
}

/**
 * Callback function for adding a new category to the database table.
 * This function retrieves the values of the category name and description from the input fields in the add category modal
 * It then sends a POST request to the server to insert the new category into the database.
 * If the insertion is successful, it updates the category information, hides the add category modal and removes the click listener from the add category button.
 * It then rebuilds the categories table and selects the newly added row.
 * @return void
 */
function addLocationCallback() {
  const ln = $("#addLocationName").val();           // Category Name
  const ld = $("#addLocationDescription").val();    // Category Description

  var token = $('input[name="_token"]').attr('value'); // X-CSRF Token

  $.ajax({
    url: '/category.create',
    type: 'POST',
    data: {
      location_name: ln,
      location_description: ld,
    },
    headers: {
      'X-CSRF-TOKEN': token
    },
    success: function (response) {
      // Response contains the new 'Category ID'
      var locationId = JSON.parse(response)["Category ID"];
      updateInfoWindow('category', locationId);       // Update info window
      $('#mLocationEntry').modal('hide');             // Hide modal
      removeClickListeners('#addLocation');           // Remove click listener from Add Category button

      // Rebuild categories table and select new row
      var queryString = window.category.search;
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
        removeClickListeners('#addLocation'); // Remove click listener from Add Category button
      }
    }
  });
}