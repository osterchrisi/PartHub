export { ResourceCreator };
import { updateInfoWindow } from "./custom";
import { DropdownManager } from "./dropdownManager";
import { MouserPartSearch } from "./MouserPartSearch";
import { SupplierRowManager } from "./SupplierRowManager";
import { TableRowManager } from "./TableRowManager";


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

    // Instantiate Manager Classes
    console.log("this.table in rC = ", this.table);
    this.dropdownManager = new DropdownManager({ inputModal: this.inputModal });
    this.supplierRowManager = new SupplierRowManager();
    this.supplierRowManager.addSupplierDataRowButtonClickListener('#supplierDataTable', 'addSupplierRowBtn-partEntry');
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
              this.tableRowManager = new TableRowManager(this.table);
              this.tableRowManager.selectNewRow(id);
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
            this.supplierRowManager.addSupplierDataRowButtonClickListener('#supplierDataTable', 'addSupplierRowBtn-partEntry');
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

    // Event listener for adding rows to a specific table
  addSupplierDataRowButtonClickListener(tableId, buttonId, partId = null) {
    $(`#${buttonId}`).off('click').on('click', () => {
      this.addSupplierRow(tableId, partId);
    });
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
}