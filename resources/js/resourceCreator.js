export { ResourceCreator };
import { updateInfoWindow } from "./custom";

class ResourceCreator {
  constructor(options) {
    // Options
    this.type = options.type;
    this.endpoint = options.endpoint;
    this.newIdName = options.newIdName;
    this.inputForm = $(options.inputForm);
    this.inputFields = options.inputFields;
    this.inputModal = $(options.inputModal);
    this.addButton = $(options.addButton);
    this.tableRebuildFunction = options.tableRebuildFunction;

    // Handle cancellation of the submit form modal, prevent multiple click listeners
    this.clickListenerAttached = false;
    this.inputModal.on('hidden.bs.modal', () => {
      this.removeAddButtonClickListener()
      this.clickListenerAttached = false;
    });
  }

  requestCreation() {
    const data = {};
    this.inputFields.forEach(field => {
      data[field.name] = $(field.selector).val();
    });

    const token = $('input[name="_token"]').attr('value');

    $.ajax({
      url: this.endpoint,
      type: 'POST',
      data: Object.assign({ _token: token }, data),
      success: (response) => {
        const id = JSON.parse(response)[this.newIdName];  // Get new ID
        updateInfoWindow(this.type, id);                  // Update InfoWindow
        this.hideModal();                                 // Hide Modal
        this.removeAddButtonClickListener();              // Remove Click Listener
        const queryString = window.location.search;
        $.when(this.tableRebuildFunction(queryString)).done(() => {
          $(`tr[data-id="${id}"]`).addClass('selected selected-last');
        });
      },
      error: (xhr) => {
        if (xhr.status === 419) {
          alert('CSRF token mismatch. Please refresh the page and try again.');
        } else {
          alert('An error occurred. Please try again.');
          $(this.inputModal).modal('hide');
          this.removeAddButtonClickListener();
        }
      }
    });
  }

  showModal() {
    this.inputModal.modal('show');
  }

  hideModal() {
    this.inputModal.modal('hide');
  }

  attachAddButtonClickListener() {
    // Check if the click listener has already been attached
    if (!this.clickListenerAttached) {
      // Fetch data asynchronously
      let dataFetchPromises = [];

      if (this.type === 'part') {
        dataFetchPromises.push(this.getLocations());
        dataFetchPromises.push(this.getFootprints());
        dataFetchPromises.push(this.getCategories());
        dataFetchPromises.push(this.getSuppliers());
      }

      // Wait for all data promises to resolve
      Promise.all(dataFetchPromises)
        .then(data => {
          const [locations, footprints, categories, suppliers] = data;

          if (this.type === 'part') {
            // Populate dropdowns
            this.addPartLocationDropdown(locations);
            this.addPartFootprintDropdown(footprints);
            this.addPartCategoryDropdown(categories);
            this.addPartSupplierDropdown(suppliers);
          }

          // Attach click listener and proceed
          this.validateAndSubmitForm(this.inputForm, this.addButton, this.requestCreation.bind(this));
          this.clickListenerAttached = true; // Set the flag to true after attaching the click listener
        })
        .catch(error => {
          console.error('Error fetching data:', error);
        });
    }
  }


  removeAddButtonClickListener() {
    this.addButton.off('click');
  }

  validateAndSubmitForm($form, $button, submitCallback, submitArgs = []) {
    // Attach event listeners for form validation and submission
    $button.click((event) => {
      event.preventDefault();
      submitFormIfValid();
    });

    $form.on('keydown', (event) => {
      // Check if the Enter key is pressed and the active element is not the selectized input
      if (event.key === 'Enter' && !$(document.activeElement).is('.selectized')) {
        event.preventDefault(); // Prevent default form submission
        submitFormIfValid();
      }
    });

    // Function to submit the form if it's valid
    const submitFormIfValid = () => {
      if ($form[0].checkValidity()) {
        const result = submitCallback.apply(null, submitArgs);
        return result;
      } else {
        displayFieldValidity();
      }
    };

    // Function to display validity status of required fields
    const displayFieldValidity = () => {
      $form.find('[required]').each(function () {
        const $field = $(this);
        if ($field[0].checkValidity()) {
          $field.removeClass('is-invalid').addClass('is-valid');
        } else {
          $field.removeClass('is-valid').addClass('is-invalid');
        }
      });
    };
  }

  getSuppliers() {
    return $.ajax({
      url: '/suppliers.get',
      dataType: 'json',
      error: function (error) {
        console.log(error);
      }
    })
  }

  getCategories() {
    return $.ajax({
      url: '/categories.get',
      dataType: 'json',
      error: function (error) {
        console.log(error);
      }
    })
  }

  getFootprints() {
    return $.ajax({
      url: '/footprints.get',
      dataType: 'json',
      error: function (error) {
        console.log(error);
      }
    })
  }

  getLocations() {
    return $.ajax({
      url: '/locations.get',
      dataType: 'json',
      error: function (error) {
        console.log(error);
      }
    })
  }

  /**
 * 
 * Creates and adds a dropdown list of locations to the part entry modal and 'selectizes' it.
 * @param {Array} locations - An array of objects representing locations to be displayed in the dropdown list.
 * Each location object must have a "location_id" and a "location_name" property.
 * @return {void}
 */
  addPartLocationDropdown(locations) {
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
    selectizeInstance.on('change', function (event) {
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
  addPartFootprintDropdown(footprints) {
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
  addPartSupplierDropdown(suppliers) {
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
  addPartCategoryDropdown(categories) {
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

}