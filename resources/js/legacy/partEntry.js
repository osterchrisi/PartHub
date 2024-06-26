import {
  validateAndSubmitForm,
  removeClickListeners,
  updateInfoWindow
} from "../custom";

import { rebuildPartsTable } from "../tables";

/**
 * Displays the part entry modal, initializes the location dropdown and attaches the validateAndSubmitForm function with the addPartCallback function
 * 
 * @param {Array} locations An array of objects containing locations
 * @return void
 */
export function callPartEntryModal(locations, footprints, categories, suppliers) {
  addPartLocationDropdown(locations);
  addPartFootprintDropdown(footprints);
  addPartCategoryDropdown(categories);
  addPartSupplierDropdown(suppliers);
  $('#mPartEntry').modal('show'); // Show modal
  validateAndSubmitForm('partEntryForm', 'addPart', addPartCallback); // Attach validate form 
}

/**
 * Callback function for adding a new part to the databaset table.
 * This function retrieves the values of the part name, quantity and location from the relevant input fields in the add part modal
 * It then sends a POST request to the server to insert the new part into the database.
 * If the insertion is successful, it updates the parts information, hides the add part modal and removes the click listener from the add part button.
 * It then rebuilds the parts table and selects the newly added row.
 * @return void
 */
function addPartCallback() {
  const pn = $("#addPartName").val();           // Name
  const q = $("#addPartQuantity").val();        // Quantity
  const l = $("#addPartLocSelect").val();       // Location
  const c = $("#addPartComment").val();         // Comment
  const d = $("#addPartDescription").val();     // Description
  const fp = $("#addPartFootprintSelect").val() // Footprint
  const s = $("#addPartSupplierSelect").val()   // Supplier
  const ct = $("#addPartCategorySelect").val()  // Category

  var token = $('input[name="_token"]').attr('value');

  $.ajax({
    url: '/part.create',
    type: 'POST',
    data: {
      part_name: pn,
      quantity: q,
      to_location: l,
      comment: c,
      description: d,
      footprint: fp,
      supplier: s,
      category: ct
    },
    headers: {
      'X-CSRF-TOKEN': token // X-CSRF Token
    },
    success: function (response) {
      // Response contains 'Part ID', 'Stock Entry ID' and 'Stock Level History ID'
      var partId = JSON.parse(response)["Part ID"];
      updateInfoWindow('part', partId);
      $('#mPartEntry').modal('hide');   // Hide modal
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
        $('#mPartEntry').modal('hide');   // Hide modal
        removeClickListeners('#addPart'); // Remove click listener from Add Part button
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

  var $select = $("#addPartLocSelect").selectize();

  // Get the Selectize instance
  var selectizeInstance = $select[0].selectize;

  // Prevent form submission when Enter is pressed while the dropdown is active
  selectizeInstance.on('change', function(event) {
    console.log("item selected");
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
  var div = document.getElementById("addPartFootprintDropdown");
  var selectHTML = "<select class='form-select form-select-sm not-required' placeholder='Footprint' id='addPartFootprintSelect'>";
  for (var i = 0; i < footprints.length; i++) {
    selectHTML += "<option value='" + footprints[i]['footprint_id'] + "'>" + footprints[i]['footprint_name'] + "</option>";
  }
  selectHTML += "</select>";
  selectHTML += "<label for='addPartFootprintSelect'>Footprint</label>";
  div.innerHTML = selectHTML;
  $("#addPartFootprintSelect").selectize();
}

/**
 * 
 * Creates and adds a dropdown list of suppliers to the part entry modal and 'selectizes' it.
 * @param {Array} suppliers - An array of objects representing suppliers to be displayed in the dropdown list.
 * Each supplier object must have a "supplier_id" and a "supplier_name" property.
 * @return {void}
 */
function addPartSupplierDropdown(suppliers) {
  var div = document.getElementById("addPartSupplierDropdown");
  var selectHTML = "<select class='form-select form-select-sm not-required' placeholder='Supplier' id='addPartSupplierSelect'>";
  for (var i = 0; i < suppliers.length; i++) {
    selectHTML += "<option value='" + suppliers[i]['supplier_id'] + "'>" + suppliers[i]['supplier_name'] + "</option>";
  }
  selectHTML += "</select>";
  selectHTML += "<label for='addPartSupplierSelect'>Supplier</label>";
  div.innerHTML = selectHTML;
  $("#addPartSupplierSelect").selectize();
}

/**
 * 
 * Creates and adds a dropdown list of categories to the part entry modal and 'selectizes' it.
 * @param {Array} categories - An array of objects representing categories to be displayed in the dropdown list.
 * Each category object must have a "category_id" and a "category_name" property.
 * @return {void}
 */
function addPartCategoryDropdown(categories) {
  var div = document.getElementById("addPartCategoryDropdown");
  var selectHTML = "<select class='form-select form-select-sm not-required' placeholder='Category' id='addPartCategorySelect'>";
  for (var i = 0; i < categories.length; i++) {
    selectHTML += "<option value='" + categories[i]['category_id'] + "'>" + categories[i]['category_name'] + "</option>";
  }
  selectHTML += "</select>";
  selectHTML += "<label for='addPartCategorySelect'>Category</label>";
  div.innerHTML = selectHTML;
  $("#addPartCategorySelect").selectize();
}