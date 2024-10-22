export { ResourceCreator };
import { updateInfoWindow } from "./custom";
import { DropdownManager } from "./DropdownManager";
import { MouserPartSearch } from "./MouserPartSearch";
import { SupplierRowManager } from "./SupplierRowManager";
import { TableRowManager } from "./TableRowManager";
import { TableManager } from "./TableManager";
import { DataFetchService } from "./DataFetchService";
import { FormValidator } from "./FormValidator";

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

    // Initialize modal behavior
    this.initializeModalBehavior();

    // Initialize uppercase toggle functionality for part input
    this.initializeUppercaseToggle();

    // Attach listeners to the category creation modal close buttons
    this.attachCategoryModalCloseListeners();

    // Instantiate Manager Classes
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
    const data = this.collectFormData();
    data['type'] = this.type;
    if (this.categoryId) {
      data['parent_category'] = this.categoryId;
    }

    this.sendAjaxRequest(data)
      .then((response) => this.handleSuccess(response))
      .catch((error) => this.handleError(error));
  }

  collectFormData() {
    const data = {};
    this.inputFields.forEach(field => {
      if (typeof field.getValue === 'function') {
        data[field.name] = field.getValue();
      } else {
        data[field.name] = $(field.selector).val();
      }
    });
    return data;
  }

  sendAjaxRequest(data) {
    const token = $('input[name="_token"]').attr('value');
    return $.ajax({
      url: this.endpoint,
      type: 'POST',
      data: Object.assign({ _token: token }, data),
    });
  }

  handleSuccess(response) {
    const id = response[this.newIdName];
    if (this.type !== 'category') {
      updateInfoWindow(this.type, id);
    }
    this.hideModal();
    this.removeAddButtonClickListener();
    this.rebuildTables(id);
  }

  handleError(xhr) {
    if (xhr.status === 419) {
      alert('CSRF token mismatch. Please refresh the page and try again.');
    } else if (xhr.status === 403) {
      const response = JSON.parse(xhr.responseText);
      alert(response.message);
    } else {
      alert('An error occurred. Please try again.');
      this.hideModal();
      this.removeAddButtonClickListener();
    }
  }

  rebuildTables(id) {
    const tableManager = new TableManager({ type: this.type });
    const promises = [tableManager.rebuildTable()];

    $.when.apply($, promises)
      .done(() => {
        if (this.type !== 'category') {
          const tableRowManager = new TableRowManager(this.table);
          tableRowManager.selectNewRow(id);
          tableRowManager.saveSelectedRow(id);
        }
        if (this.type === 'part') {
          tableManager.updateStockModal(id);
        }
      })
      .fail(() => {
        console.error("Error in one or more table rebuild functions");
      });
  }

  attachAddButtonClickListener() {
    if (!this.clickListenerAttached) {
      let dataFetchPromises = [];
      if (this.type === 'part') {
        dataFetchPromises = this.fetchDropdownData();
      }

      Promise.all(dataFetchPromises)
        .then(data => this.populateDropdowns(data))
        .then(() => this.setupFormValidation())
        .catch(error => console.error('Error fetching data:', error));

      this.clickListenerAttached = true;
    }
  }

  fetchDropdownData() {
    return [DataFetchService.getLocations(), DataFetchService.getFootprints(), DataFetchService.getCategories()];
  }

  populateDropdowns(data) {
    const [locations, footprints, categories] = data;

    if (this.type === 'part') {
      this.dropdownManager.addPartLocationDropdown(locations);
      this.dropdownManager.addPartFootprintDropdown(footprints);
      if (!this.dropdownManager.categoryCreated) {
        this.dropdownManager.addPartCategoryDropdown(categories);
      }
      this.dropdownManager.categoryCreated = false;
      this.toggleStockForm();
      this.supplierRowManager.addSupplierDataRowButtonClickListener('#supplierDataTable', 'addSupplierRowBtn-partEntry');
    }
  }

  setupFormValidation() {
    const formValidator = new FormValidator(this.inputForm, this.addButton);
    formValidator.attachValidation(this.requestCreation.bind(this));
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
    });
  }

  initializeModalBehavior() {
    this.inputModal.on('hidden.bs.modal', (event) => this.onModalHidden(event));
    this.inputModal.on('show.bs.modal', () => this.onModalShown());
  }

  onModalHidden(event) {
    if (event.target === this.inputModal[0]) {
      $('#mouserSearchResults').empty();
      this.removeAddButtonClickListener();
      this.clickListenerAttached = false;
    }
    $('#addPartAddStockSwitch').prop('checked', false).trigger('change');
  }

  onModalShown() {
    console.log("now fetch some stuff bro");
    this.attachAddButtonClickListener();
  }
}