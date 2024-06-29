export { ResourceCreator };
import { updateInfoWindow } from "./custom";

class ResourceCreator {
  constructor(options, tableRebuildFunctions = [], categoryId = null) {
    // Options
    this.type = options.type;
    this.endpoint = options.endpoint;
    this.newIdName = options.newIdName;
    this.inputForm = $(options.inputForm);
    this.inputFields = options.inputFields;
    this.inputModal = $(options.inputModal);
    this.addButton = $(options.addButton);
    this.tableRebuildFunctions = tableRebuildFunctions;

    // Handle cancellation of the submit form modal, prevent multiple click listeners
    this.clickListenerAttached = false;
    this.inputModal.on('hidden.bs.modal', () => {
      this.removeAddButtonClickListener()
      this.clickListenerAttached = false;
    });
    if (categoryId) {
      this.categoryId = categoryId.categoryId;
    }
  }

  requestCreation() {
    const data = {};
    this.inputFields.forEach(field => {
      data[field.name] = $(field.selector).val();
    });

    if (this.categoryId) { data['parent_category'] = this.categoryId; }

    const token = $('input[name="_token"]').attr('value');

    $.ajax({
      url: this.endpoint,
      type: 'POST',
      data: Object.assign({ _token: token }, data),
      success: (response) => {
        const id = JSON.parse(response)[this.newIdName];  // Get new ID
        if (this.type != 'category') {
          updateInfoWindow(this.type, id);                  // Update InfoWindow unless a Category has been added
        }
        this.hideModal();                                   // Hide Modal
        this.removeAddButtonClickListener();                // Remove Click Listener
        const queryString = window.location.search;
        // Call each tableRebuildFunction in the array
        this.tableRebuildFunctions.forEach(fn => fn(queryString, id));
      },
      error: (xhr) => {
        if (xhr.status === 419) {
          alert('CSRF token mismatch. Please refresh the page and try again.');
        } else {
          alert('An error occurred. Please try again.');
          this.hideModal();                                 // Hide Modal
          this.removeAddButtonClickListener();              // Remove Click Listener
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

    // Currently don't like it anymore, so uncommented the submission upon pressing Enter...
    // Submit form on Enter keypress, unless you're on a selectized dropdown
    // $form.on('keydown', (event) => {
    //   // Check if the Enter key is pressed and the active element is not the selectized input
    //   if (event.key === 'Enter' && !$(document.activeElement).is('.selectized')) {
    //     event.preventDefault(); // Prevent default form submission
    //     submitFormIfValid();
    //   }
    // });

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
    });
  }

  getCategories() {
    return $.ajax({
      url: '/categories.get',
      dataType: 'json',
      error: function (error) {
        console.log(error);
      }
    });
  }

  getFootprints() {
    return $.ajax({
      url: '/footprints.get',
      dataType: 'json',
      error: function (error) {
        console.log(error);
      }
    });
  }

  getLocations() {
    return $.ajax({
      url: '/locations.get',
      dataType: 'json',
      error: function (error) {
        console.log(error);
      }
    });
  }

  /**
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

    var $select = $("#addPartLocSelect").selectize({
      create: (input) => {
        this.createNewSelectizeDropdownEntry(input, 'location');
      }
    });
  }

  /**
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
    $("#addPartFootprintSelect").selectize({
      create: (input) => {
        this.createNewSelectizeDropdownEntry(input, 'footprint');
      }
    });
  }

  /**
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

    var $select = $("#addPartSupplierSelect").selectize({
      create: (input) => {
        this.createNewSelectizeDropdownEntry(input, 'supplier');
      }
    });
  }


  /**
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


  /**
   * Creates a new entry of the specified type, updates the corresponding dropdown, selectizes and selects the new entry.
   * 
   * @param {string} input - The name of the new entry to be created.
   * @param {string} type - The type of entry to be created ('location', 'footprint', or 'supplier').
   * 
   * The type determines the endpoint, the field names in the response, and the functions used to fetch and update
   * the relevant dropdown.
   * 
   * The function performs the following steps:
   * 1. Sends an AJAX POST request to create the new entry.
   * 2. On success, fetches the updated list of entries of the specified type.
   * 3. Updates the relevant dropdown with the new list and selects the newly created entry.
   * 
   * @throws {Error} If the type is unknown.
   * @returns {void}
   */
  createNewSelectizeDropdownEntry(input, type) {
    const token = $('input[name="_token"]').attr('value');
    let endpoint, newIdName, nameField, getFunction, dropdownFunction, dropdownId, $select;

    switch (type) {
      case 'location':
        endpoint = '/location.create';
        newIdName = 'Location ID';
        nameField = 'location_name';
        getFunction = this.getLocations.bind(this);
        dropdownFunction = this.addPartLocationDropdown.bind(this);
        dropdownId = 'addPartLocSelect';
        break;
      case 'footprint':
        endpoint = '/footprint.create';
        newIdName = 'Footprint ID';
        nameField = 'footprint_name';
        getFunction = this.getFootprints.bind(this);
        dropdownFunction = this.addPartFootprintDropdown.bind(this);
        dropdownId = 'addPartFootprintSelect';
        break;
      case 'supplier':
        endpoint = '/supplier.create';
        newIdName = 'Supplier ID';
        nameField = 'supplier_name';
        getFunction = this.getSuppliers.bind(this);
        dropdownFunction = this.addPartSupplierDropdown.bind(this);
        dropdownId = 'addPartSupplierSelect';
        break;
      default:
        console.error('Unknown type:', type);
        return;
    }

    $select = $(`#${dropdownId}`).selectize();
    if ($select.data('creating')) {
      return;
    }
    $select.data('creating', true);

    $.ajax({
      url: endpoint,
      type: 'POST',
      data: {
        [nameField]: input,
        _token: token
      },
      success: (response) => {
        response = JSON.parse(response);
        const newEntry = {
          [`${type}_id`]: response[newIdName],
          [`${type}_name`]: input
        };
        getFunction().done((newList) => {
          dropdownFunction(newList);
          var selectize = $(`#${dropdownId}`)[0].selectize;
          selectize.addItem(newEntry[`${type}_id`]);
          $select.data('creating', false);
        });
      },
      error: function () {
        console.error('Error creating new entry');
        $select.data('creating', false);
      }
    });
  }

}