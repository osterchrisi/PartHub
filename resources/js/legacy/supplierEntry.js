import {
  validateAndSubmitForm,
  removeClickListeners,
  updateInfoWindow
} from "../custom";

import { rebuildSuppliersTable } from "../tables";

/**
 * Displays the supplier entry modal and attaches the validateAndSubmitForm function with the addSupplierCallback function
 * 
 * @param {Array} suppliers An array of objects containing suppliers
 * @return void
 */
export function callSupplierEntryModal() {
  $('#mSupplierEntry').modal('show'); // Show modal
  validateAndSubmitForm('supplierEntryForm', 'addSupplier', addSupplierCallback); // Attach validate form 
}

/**
 * Callback function for adding a new supplier to the database table.
 * This function retrieves the values of the supplier name and description from the input fields in the add supplier modal
 * It then sends a POST request to the server to insert the new supplier into the database.
 * If the insertion is successful, it updates the supplier information, hides the add supplier modal and removes the click listener from the add supplier button.
 * It then rebuilds the suppliers table and selects the newly added row.
 * @return void
 */
function addSupplierCallback() {
  const sn = $("#addSupplierName").val();               // Supplier Name
  var token = $('input[name="_token"]').attr('value');  // X-CSRF Token

  $.ajax({
    url: '/supplier.create',
    type: 'POST',
    data: {
      supplier_name: sn,
    },
    headers: {
      'X-CSRF-TOKEN': token
    },
    success: function (response) {
      // Response contains the new 'Supplier ID'
      var supplierId = JSON.parse(response)["Supplier ID"];
      updateInfoWindow('supplier', supplierId);         // Update info window
      $('#mSupplierEntry').modal('hide');               // Hide modal
      removeClickListeners('#addSupplier');             // Remove click listener from Add Supplier button

      // Rebuild suppliers table and select new row
      var queryString = window.location.search;
      $.when(rebuildSuppliersTable(queryString)).done(function () {
        $('tr[data-id="' + supplierId + '"]').addClass('selected selected-last');
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
        $('#mSupplierEntry').modal('hide');     // Hide modal
        removeClickListeners('#addSupplier');     // Remove click listener from Add Supplier button
      }
    }
  });
}