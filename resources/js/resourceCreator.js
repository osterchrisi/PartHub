export { ResourceCreator };
import { updateInfoWindow } from "./custom";
import { bootstrapTableSmallify } from "./tables";

class ResourceCreator {
  constructor(options, tableRebuildFunctions = [], categoryId = null) {
    // Options
    this.type = options.type;
    this.endpoint = options.endpoint;
    this.table = options.table_name;
    this.newIdName = options.newIdName;
    this.inputForm = $(options.inputForm);
    this.inputFields = options.inputFields;
    this.inputModal = $(options.inputModal);
    this.addButton = $(options.addButton);
    this.tableRebuildFunctions = tableRebuildFunctions;

    // Initialize upper case toggle functionality for part input
    this.initializeUppercaseToggle();

    // Handle cancellation of the submit form modal, prevent multiple click listeners
    // Filtering for event.target, so the hiding of the category creation modal is not firing here
    this.clickListenerAttached = false;
    this.inputModal.on('hidden.bs.modal', (event) => {
      if (event.target === this.inputModal[0]) {
        this.removeAddButtonClickListener()
        this.clickListenerAttached = false;
      }
      $('#addPartAddStockSwitch').prop('checked', false).trigger('change');
    });

    this.inputModal.on('show.bs.modal', () => {
      this.attachAddButtonClickListener();
    });

    // Attach listeners to the category creation modal close buttons
    this.attachCategoryModalCloseListeners();

    if (categoryId) {
      this.categoryId = categoryId.categoryId;
    }

    // Category creation flag to ensure proper category dropdown behaviour
    this.categoryCreated = false;

    // Supplier Data
    //TODO: This seems strange
    // Bind functions to ensure correct `this` context
    this.addSupplierRow = this.addSupplierRow.bind(this);
    this.removeRowButtonClickListener = this.removeRowButtonClickListener.bind(this);

    // Call the removeRowButtonClickListener to make sure it listens from the start
    this.removeRowButtonClickListener();
    this.newRowIndex = 0;
  }


  /**
  * Handles the creation of a new resource via an AJAX request.
  *
  * This method collects data from input fields, sends an AJAX POST request to create the resource,
  * and handles the response. If successful, it updates the InfoWindow, hides the modal,
  * removes the click listener, rebuilds the table, and selects the newly created row.
  *
  * @method requestCreation
  */
  requestCreation() {
    const data = {};
    // Collect static and dynamic input fields
    this.inputFields.forEach(field => {
      if (typeof field.getValue === 'function') {
        // If it's a function (like the suppliers), execute it
        data[field.name] = field.getValue();
      } else {
        // Otherwise, collect the value from the selector
        data[field.name] = $(field.selector).val();
      }
    });

    if (this.categoryId) { data['parent_category'] = this.categoryId; }

    const token = $('input[name="_token"]').attr('value');

    $.ajax({
      url: this.endpoint,
      type: 'POST',
      data: Object.assign({ _token: token }, data),
      success: (response) => {
        const id = response[this.newIdName];                // Get new ID
        if (this.type != 'category') {
          updateInfoWindow(this.type, id);                  // Update InfoWindow unless a Category has been added
        }
        this.hideModal();                                   // Hide Modal
        this.removeAddButtonClickListener();                // Remove Click Listener
        const queryString = window.location.search;

        // Need to use map to create an array of promises, when.done() didn't work correctly
        const promises = this.tableRebuildFunctions.map(fn => {
          return fn(queryString, id); //fn(queryString, id) must return promise (usually AJAX call)
        });

        $.when.apply($, promises)
          .done(() => {
            if (this.type != 'category') {
              this.selectNewRow(id);
            }
          })
          .fail(() => {
            // console.error("Error in one or more table rebuild functions");
          });
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

  /**
  * Highlights and selects a newly added row in a Bootstrap table.
  *
  * This method finds the newly added row in the table data using the provided ID,
  * determines the appropriate page where the new row should be displayed, switches to that page,
  * and highlights the new row with visual effects.
  *
  * @param {number} id - The ID of the newly added row to be selected and highlighted.
  */
  selectNewRow(id) {
    // Get the table data after bootstrapping
    let tableData = $(this.table).bootstrapTable('getData');

    // Find the position of the new part in the data array
    let newRowPosition = tableData.findIndex(row => row['_ID_data'].id == id);

    if (newRowPosition !== -1) {
      // Get current page size
      let pageSize = $(this.table).bootstrapTable('getOptions').pageSize;

      // Calculate the page number where the new part will be displayed
      let pageNumber = Math.floor(newRowPosition / pageSize) + 1;

      // Switch to the appropriate page
      $(this.table).bootstrapTable('selectPage', pageNumber);

      // Highlight the new row after changing the page
      this.highlightAndSelectRow(id, 1000, 10);
    } else {
      console.warn('New row position not found for id:', id);
    }
  }

  /**
  * Highlights and selects a table row by ID.
  *
  * @param {string} id - The ID of the row to highlight and select.
  * @param {number} [highlightDuration=1000] - Duration (ms) to keep the row highlighted.
  * @param {number} [initialDelay=0] - Delay (ms) before starting the highlight.
  */
  highlightAndSelectRow(id, highlightDuration = 1000, initialDelay = 0) {
    setTimeout(() => {
      let $newRow = $(`tr[data-id="${id}"]`);
      if ($newRow.length > 0) {
        $newRow.addClass('highlight-new selected selected-last');
        setTimeout(() => {
          $newRow.removeClass('highlight-new');
        }, highlightDuration); // Keep the highlight for the specified duration
      }
    }, initialDelay); // Initial delay to wait until page change happens but seems it's never needed
  }

  showModal() {
    this.inputModal.modal('show');
  }

  hideModal() {
    this.inputModal.modal('hide');
    $('#addPartAddStockSwitch').prop('checked', false);
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
        // dataFetchPromises.push(this.getSuppliers()); // Only for single supplier layout
      }

      // Wait for all data promises to resolve
      Promise.all(dataFetchPromises)
        .then(data => {
          const [locations, footprints, categories, suppliers] = data;

          if (this.type === 'part') {
            // Populate dropdowns
            this.addPartLocationDropdown(locations);
            this.addPartFootprintDropdown(footprints);
            // this.addPartSupplierDropdown(suppliers); // Only for single supplier layout
            if (this.categoryCreated == false) {
              this.addPartCategoryDropdown(categories);
            }
            this.categoryCreated = false;
            this.toggleStockForm();
            this.addSupplierDataRowButtonClickListener('#supplierDataTable', 'addSupplierRowBtn-partEntry');
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

  /**
   * Toggle the "Add Stock" functionality for a new part
   */
  toggleStockForm() {
    $('#addPartAddStockSwitch').off('change').on('change', function () {
      $('#addPartQuantity').prop('disabled', !this.checked);

      var selectizeControl = $('#addPartLocSelect')[0].selectize;

      if (this.checked) {
        selectizeControl.enable();
      } else {
        selectizeControl.disable();
        $('#addPartQuantity').val('');
      }
    });
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

    //* Currently don't like it anymore, so uncommented the submission upon pressing Enter
    //* Still keeping the preventDefault in, so I don't get browser behaviour that submits forms upon pressing enter (leads to seemingly 'buggy' page reload)
    // Submit form on Enter keypress, unless you're on a selectized dropdown
    $form.on('keydown', (event) => {
      // Check if the Enter key is pressed and the active element is not the selectized input
      if (event.key === 'Enter' && !$(document.activeElement).is('.selectized')) {
        event.preventDefault(); // Prevent default form submission
        // submitFormIfValid();
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

    // Disable dropdown (it's optional to add stock)
    $select[0].selectize.disable();
  }

  /**
  * Creates and adds a dropdown list of footprints to the part entry modal and 'selectizes' it.
  * @param {Array} footprints - An array of objects representing footprints to be displayed in the dropdown list.
  * Each footprint object must have a "footprint_id" and a "footprint_name" property.
  * @return {void}
  */
  addPartFootprintDropdown(footprints) {
    var div = document.getElementById("addPartFootprintDropdown");
    var selectHTML = "<select class='form-select form-select-sm not-required' id='addPartFootprintSelect'>";
    selectHTML += "<option value=''>No Footprint</option>";

    for (var i = 0; i < footprints.length; i++) {
      selectHTML += "<option value='" + footprints[i]['footprint_id'] + "'>" + footprints[i]['footprint_name'] + "</option>";
    }
    selectHTML += "</select>";
    selectHTML += "<label for='addPartFootprintSelect'>Footprint</label>";
    div.innerHTML = selectHTML;

    $("#addPartFootprintSelect").selectize({
      create: (input) => {
        this.createNewSelectizeDropdownEntry(input, 'footprint');
      },
      placeholder: 'Select Footprint',
      onInitialize: function () {
        this.setValue(''); // "No Footprint" is selected by default
      }
    });
  }

  /**
  * Creates and adds a dropdown list of suppliers to the part entry modal and 'selectizes' it.
  * @param {Array} suppliers - An array of objects representing suppliers to be displayed in the dropdown list.
  * Each supplier object must have a "supplier_id" and a "supplier_name" property.
  * @return {void}
  */
  addPartSupplierDropdown(suppliers, dropdownId, newRowIndex) {
    const div = document.getElementById(dropdownId); // Use the passed ID to target the correct dropdown div
    let selectHTML = `<select class='form-select form-select-sm not-required' data-supplier-id='${newRowIndex}'>`;
    selectHTML += "<option value=''>No Supplier</option>";

    for (let i = 0; i < suppliers.length; i++) {
      selectHTML += `<option value='${suppliers[i]['supplier_id']}'>${suppliers[i]['supplier_name']}</option>`;
    }
    selectHTML += "</select>";
    div.innerHTML = selectHTML;

    // Initialize Selectize on the new dropdown
    $(`[data-supplier-id="${newRowIndex}"]`).selectize({  // Change to `data-supplier-id`
      create: (input) => {
        this.createNewSelectizeDropdownEntry(input, 'supplier', dropdownId, newRowIndex);
      },
      placeholder: 'Select Supplier',
      onInitialize: function () {
        this.setValue(''); // "No Supplier" is selected by default
      },
      onDropdownOpen: function () {
        // Adjust the overflow of the table when the dropdown opens
        $('.bootstrap-table .fixed-table-container .fixed-table-body').css({
          'overflow-x': 'visible',
          'overflow-y': 'visible'
        });
      },
      onDropdownClose: function () {
        // Reset the table overflow once the dropdown closes
        $('.bootstrap-table .fixed-table-container .fixed-table-body').css({
          'overflow-x': 'auto',
          'overflow-y': 'auto'
        });
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
    var nestedCategories = this.organizeCategories(categories);

    var selectHTML = "<select class='form-select form-select-sm not-required' placeholder='Category' id='addPartCategorySelect'>";
    selectHTML += "<option value=''>No Category</option>";
    selectHTML += this.addCategoryOptions(nestedCategories);
    selectHTML += "</select>";
    selectHTML += "<label for='addPartCategorySelect'>Category</label>";
    div.innerHTML = selectHTML;

    var $select = $("#addPartCategorySelect").selectize({
      create: (input) => {
        this.createNewSelectizeDropdownEntry(input, 'category');
      },
      onInitialize: function () {
        this.setValue(''); // "No Category" is selected by default
      }
    });
  }


  /**
  * Organizes categories into a nested structure.
  * @param {Array} categories - An array of category objects.
  * @return {Array} - Nested categories.
  */
  organizeCategories(categories) {
    let categoryMap = {};
    categories.forEach(category => {
      categoryMap[category.category_id] = { ...category, children: [] };
    });

    let nestedCategories = [];
    categories.forEach(category => {
      if (category.parent_category === 0) {
        nestedCategories.push(categoryMap[category.category_id]);
      } else {
        categoryMap[category.parent_category].children.push(categoryMap[category.category_id]);
      }
    });

    return nestedCategories;
  }

  /**
   * Generates HTML options for categories with nesting.
   * @param {Array} categories - Nested categories.
   * @param {number} level - Current nesting level.
   * @return {string} - HTML string of options.
   */
  addCategoryOptions(categories, level = 0) {
    let optionsHTML = '';
    categories.forEach(category => {
      let indent = '&nbsp;'.repeat(level * 4); // Indentation for nesting
      optionsHTML += "<option value='" + category.category_id + "'>" + indent + category.category_name + "</option>";
      if (category.children.length > 0) {
        optionsHTML += this.addCategoryOptions(category.children, level + 1);
      }
    });
    return optionsHTML;
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
  //TODO: Don't like how 'complicated' suppliers are...
  createNewSelectizeDropdownEntry(input, type, supplier_dropdownId = null, newRowIndex = null) {
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
      case 'category':
        this.showCategoryCreationModal(input);
        this.initializeSaveCategoryButton();
        return;
      default:
        console.error('Unknown type:', type);
        return;
    }

    if (type === 'supplier') {
      $select = $(`select[data-supplier-id="${newRowIndex}"]`).selectize();
    }
    else {
      $select = $(`#${dropdownId}`).selectize();
    }

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
        const newEntry = {
          [`${type}_id`]: response[newIdName],
          [`${type}_name`]: input
        };
        getFunction().done((newList) => {
          if (type === 'supplier') {
            dropdownFunction(newList, supplier_dropdownId, newRowIndex);
            var selectize = $(`select[data-supplier-id="${newRowIndex}"]`)[0].selectize;
          }
          else {
            dropdownFunction(newList);
            var selectize = $(`#${dropdownId}`)[0].selectize;
            selectize.enable(); // Needed for the normally disabled location selectize
          }
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

  /**
  * Initializes the uppercase toggle functionality for the part name input field.
  *
  * Sets up an event listener on the toggle button to transform the input text to
  * uppercase when enabled, and restore the original text when disabled.
  *
  * @method initializeUppercaseToggle
  */
  initializeUppercaseToggle() {
    const $toggleButton = $('#toggle-uppercase-button');
    const $addPartName = $('#addPartName'); //! Is hardcoded...
    let isUppercase = false;
    let originalValue = '';

    const toggleUppercase = () => {
      isUppercase = !isUppercase;
      if (isUppercase) {
        originalValue = $addPartName.val(); // Store the original value
        $addPartName.on('input.uppercase', function () {
          const uppercased = $(this).val().toUpperCase();
          $(this).val(uppercased);
        });
        $addPartName.val($addPartName.val().toUpperCase());
        $toggleButton.addClass('active'); // Add active state class
        $toggleButton.text('AA');
      } else {
        $addPartName.off('input.uppercase');
        $addPartName.val(originalValue); // Restore the original value
        $toggleButton.removeClass('active'); // Remove active state class
        $toggleButton.text('Aa');
      }
    };

    // Remove any existing click event listeners to prevent duplicate handling
    $toggleButton.off('click');

    // Event listener for the toggle button
    $toggleButton.click(() => {
      toggleUppercase();
    });

    // Initialize the button text
    $toggleButton.text('Aa');
  }

  /**
  * Shows the category creation modal and populates the parent category dropdown.
  *
  * @param {string} input - The initial input value for the category name.
  */
  showCategoryCreationModal(input) {
    // Populate parent category dropdown
    this.getCategories().done((categories) => {
      const nestedCategories = this.organizeCategories(categories);
      const optionsHTML = this.addCategoryOptions(nestedCategories);
      $('#parentCategory').html(optionsHTML);
      $('#categoryName').val(input);
      $('#categoryCreationModal').modal('toggle');
      this.inputModal.modal('toggle');
    });
  }

  /**
  * Saves a new category via AJAX and updates the category dropdown in the part entry modal
  */
  saveNewCategory() {
    const categoryName = $('#categoryName').val();
    const parentCategory = $('#parentCategory').val();
    const token = $('input[name="_token"]').attr('value');

    $.ajax({
      url: '/category.create',
      type: 'POST',
      data: {
        category_name: categoryName,
        parent_category: parentCategory,
        _token: token
      },
      success: (response) => {
        const newEntry = {
          category_id: response['Category ID'],
          category_name: categoryName
        };
        this.getCategories().done((newList) => {
          this.addPartCategoryDropdown(newList);
          var selectize = $('#addPartCategorySelect')[0].selectize;
          selectize.addItem(newEntry['category_id']);
          this.categoryCreated = true;
          $('#categoryCreationModal').modal('toggle');
          this.inputModal.modal('toggle');
        });
      },
      error: function () {
        console.error('Error creating new category');
      }
    });
  }

  /**
  * Initializes the save button for the category modal.
  */
  initializeSaveCategoryButton() {
    $('#saveCategoryButton').off('click').click(() => {
      this.saveNewCategory();
    });
  }

  /**
  * Attaches click listeners to the close buttons of the category modal.
  * Ensures that the category dropdown gets reinitialized if no new category has been created.
  * Otherwise the dropdown becomes unresponsive
  */
  attachCategoryModalCloseListeners() {
    $('#closeCategoryModalButton1, #closeCategoryModalButton2').off('click').on('click', () => {
      this.inputModal.modal('toggle');
      this.reinitializeCategoryDropdown();
    });
  }

  /**
  * Reinitializes the category dropdown in the part entry modal in case the modal was closed without creating a new category
  */
  reinitializeCategoryDropdown() {
    this.getCategories().done(newList => {
      this.addPartCategoryDropdown(newList);
    });
  }

  // Function to add a new supplier data row to a specific table
  addSupplierRow(tableId, partId) {
    let newRowIndex = this.newRowIndex;
    let newDropdownDiv = `addPartSupplier-${newRowIndex}`;

    // Check if the table requires extra fields
    let selectBox = '';
    let createBox = '';
    if (tableId === '#partSupplierDataTable') {
      selectBox = '<td></td>'; // Add an empty <td> for bootstrap-table selection functionality
      createBox = `<button type="button" class="btn btn-sm btn-success ms-1" id="create-${newRowIndex}"><i class="fas fa-check"></i></button>`
    }

    // Create the new row with a unique dropdown ID for each row
    let newRow = `<tr data-supplier-index="${newRowIndex}" data-part-id="${partId}">
                  ${selectBox}  <!-- Include the extra <td> if applicable -->
                  <td>
                      <div id='${newDropdownDiv}'></div>
                  </td>
                  <td><input type='text' class='form-control form-control-sm' placeholder='URL' data-url-id="${newRowIndex}"></td>
                  <td><input type='text' class='form-control form-control-sm' placeholder='SPN' data-spn-id="${newRowIndex}"></td>
                  <td><input type='text' class='form-control form-control-sm' placeholder='Price' data-price-id="${newRowIndex}"></td>
                  <td><div class='d-flex'><button type="button" class="btn btn-sm btn-danger remove-row-btn"><i class="fas fa-trash"></i></button>${createBox}</div></td>
                </tr>`;

    // Append the new row to the specified table body
    $(`${tableId} tbody`).append(newRow);

    // Fetch suppliers and populate the dropdown
    this.getSuppliers().done((suppliers) => {
      this.addPartSupplierDropdown(suppliers, newDropdownDiv, newRowIndex);
    });

    // Attach a save Supplier Row handler
    this.saveSupplierDataRowButtonClickListener(`create-${newRowIndex}`);
    this.newRowIndex++;
  }


  // Event listener for adding rows to a specific table
  addSupplierDataRowButtonClickListener(tableId, buttonId, partId = null) {
    $(`#${buttonId}`).off('click').on('click', () => {
      this.addSupplierRow(tableId, partId);
    });
  }


  // Event listener to remove row
  // Doing this "outside" of Bootstrap-Table since the table itself is also manipulated in the DOM directly
  removeRowButtonClickListener() {
    $(document).on('click', '.remove-row-btn', function () {
      $(this).closest('tr').remove();
    });
  }

  saveSupplierDataRowButtonClickListener(buttonId) {
    let $button = `#${buttonId}`;
    $($button).off('click').on('click', () => {
      this.saveSupplierDataRow($button);
    });
  }

  saveSupplierDataRow(button) {
    // Get the new supplier data for this row
    let newSupplierData = this.getNewSupplierDataRowData(button);

    // If there's supplier data
    if (newSupplierData.length > 0) {
      let part_id = newSupplierData[0].part_id; // Get the part_id from the first row (since all rows have the same part_id)

      // Prepare the data to be sent to the server
      let requestData = {
        part_id: part_id,
        suppliers: newSupplierData.map(supplierRow => {
          // Remove part_id from supplierRow and keep only supplier-specific data
          return {
            supplier_id: supplierRow.supplier_id,
            URL: supplierRow.URL,
            SPN: supplierRow.SPN,
            price: supplierRow.price
          };
        })
      };

      // Send the AJAX request
      $.ajax({
        url: '/supplierData.create',
        type: 'POST',
        data: requestData, // Send the correct structure
        headers: {
          'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        success: function (response) {
          console.log('Supplier data saved successfully:', response);
        },
        error: function (xhr) {
          console.error('Error saving supplier data:', xhr);
        }
      });
    } else {
      console.log('No supplier data to save');
    }
  }


  getNewSupplierDataRowData(button) {
    let newSupplierData = [];
    let $row = $(button).closest('tr'); // Find the closest row (tr) to the button clicked

    // Get the supplier index from the current row
    let rowIndex = $row.data('supplier-index');
    if (typeof rowIndex !== 'undefined') {
      // Collect the data for the current row
      let supplierRow = {
        part_id: $row.data('part-id'),
        supplier_id: $row.find(`[data-supplier-id="${rowIndex}"]`).val(),
        URL: $row.find(`[data-url-id="${rowIndex}"]`).val(),
        SPN: $row.find(`[data-spn-id="${rowIndex}"]`).val(),
        price: $row.find(`[data-price-id="${rowIndex}"]`).val()
      };
      newSupplierData.push(supplierRow);
    }
    console.log(newSupplierData);
    return newSupplierData;
  }

}