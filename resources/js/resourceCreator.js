export { ResourceCreator };
import { updateInfoWindow } from "./custom";
import { DropdownManager } from "./dropdownManager";
import { MouserPartSearch } from "./MouserPartSearch";

class ResourceCreator {
  constructor(options, tableRebuildFunctions = []) {
    // Options
    this.type = options.type;
    this.endpoint = options.endpoint;
    this.table = options.table_name;
    this.newIdName = options.newIdName;
    this.inputForm = $(options.inputForm);
    this.inputFields = options.inputFields;
    this.inputModal = $(options.inputModal);
    this.addButton = $(options.addButton);
    this.categoryId = options.categoryId || null;
    this.tableRebuildFunctions = tableRebuildFunctions;

    //TODO: Check for type, then do only necessary tasks

    // Initialize upper case toggle functionality for part input
    this.initializeUppercaseToggle();

    // Handle cancellation of the submit form modal, prevent multiple click listeners
    // Filtering for event.target, so the hiding of the category creation modal is not firing here
    this.clickListenerAttached = false;
    this.inputModal.on('hidden.bs.modal', (event) => {
      if (event.target === this.inputModal[0]) {
        $('#mouserSearchResults').empty();
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

    // Supplier Data
    this.addSupplierRow = this.addSupplierRow.bind(this);
    this.removeRowButtonClickListener = this.removeRowButtonClickListener.bind(this);
    this.removeRowButtonClickListener();
    this.newRowIndex = 0;

    // Instantiate DropdownManager
    this.dropdownManager = new DropdownManager({ inputModal: this.inputModal });
  }

  showModal() {
    this.inputModal.modal('show');
  }

  hideModal() {
    this.inputModal.modal('hide');
    $('#addPartAddStockSwitch').prop('checked', false);
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
    console.log("data after loop: ", data);
    data['type'] = this.type;

    if (this.categoryId) { data['parent_category'] = this.categoryId; }

    const token = $('input[name="_token"]').attr('value');
    console.log(Object.assign({ _token: token }, data));
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
        }
        else if (xhr.status === 403) {
          const response = JSON.parse(xhr.responseText);
          alert(response.message);
        }
        else {
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
            this.dropdownManager.addPartLocationDropdown(locations);
            this.dropdownManager.addPartFootprintDropdown(footprints);
            // this.dropdownManager.addPartSupplierDropdown(suppliers); // Only for single supplier layout
            if (this.dropdownManager.categoryCreated == false) {
              this.dropdownManager.addPartCategoryDropdown(categories);
            }
            this.dropdownManager.categoryCreated = false;
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
      this.dropdownManager.addPartSupplierDropdown(suppliers, newDropdownDiv, newRowIndex);
    });

    // Attach a save Supplier Row handler
    if (tableId === '#partSupplierDataTable') {
      this.saveSupplierDataRowButtonClickListener(`create-${newRowIndex}`);
    }
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
      let part_id = newSupplierData[0].part_id;

      // Prepare the data to be sent to the server
      let requestData = {
        part_id: part_id,
        type: 'supplier_data',
        suppliers: newSupplierData.map(supplierRow => {
          return {
            supplier_id: supplierRow.supplier_id,
            URL: supplierRow.URL,
            SPN: supplierRow.SPN,
            price: supplierRow.price
          };
        })
      };

      $.ajax({
        url: '/supplierData.create',
        type: 'POST',
        data: requestData,
        headers: {
          'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        success: function (response) {
          console.log('Supplier data saved successfully:', response);
          updateInfoWindow('part', part_id);
        },
        error: function (xhr) {
          if (xhr.status === 403) {
            const response = JSON.parse(xhr.responseText);
            alert(response.message);
          } else {
            console.error('Error saving supplier data:', xhr);
          }
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

  setCategoryId(categoryId) {
    this.categoryId = categoryId;
  }

}