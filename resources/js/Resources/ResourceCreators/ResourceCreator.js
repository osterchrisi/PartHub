export { ResourceCreator };
import { updateInfoWindow } from "../../custom";
import { DropdownManager } from "../../DropdownManager";
import { MouserPartSearch } from "../../MouserPartSearch";
import { SupplierRowManager } from "../../../Tables/SupplierRowManager";
import { TableRowManager } from "../../../Tables/TableRowManager";
import { TableManager } from "../../../Tables/TableManager";
import { DataFetchService } from "../DataFetchService";
import { FormValidator } from "../../FormValidator";

class ResourceCreator {
  constructor(options, tableRebuildFunctions = []) {
    // Store configuration options for creating resources
    this.type = options.type;
    this.endpoint = options.endpoint;
    this.table = options.table_name;
    this.newIdName = options.newIdName;
    this.inputForm = $(options.inputForm);
    this.inputFields = options.inputFields;
    this.inputModal = $(options.inputModal);
    this.addButton = $(options.addButton);
    this.categoryId = options.categoryId || null;

    // Flag to control dropdown population when a modal is closed and re-opened
    this.skipDropdownPopulation = false;

    // Initialize modal behavior and listeners
    this.initializeModalBehavior();
    this.attachCategoryModalCloseListeners();

    // Instantiate Manager Classes to handle dropdowns and supplier rows
    this.dropdownManager = new DropdownManager({ inputModal: this.inputModal });
    this.supplierRowManager = new SupplierRowManager();
  }

  // Show the modal
  showModal() {
    this.inputModal.modal('show');
  }

  // Hide the modal and reset add stock switch
  hideModal() {
    this.inputModal.modal('hide');
    if (!this.skipDropdownPopulation) {
      $('#addPartAddStockSwitch').prop('checked', false);
    }
  }

  // Request creation of a new resource via AJAX
  requestCreation() {
    const data = this.collectFormData();
    data['type'] = this.type;
    if (this.categoryId) {
      data['parent_category'] = this.categoryId;
    }

    // Send AJAX request to create the resource
    this.sendAjaxRequest(data)
      .then((response) => this.handleSuccess(response))
      .catch((error) => this.handleError(error));
  }

  // Collect data from input fields to prepare for submission
  collectFormData() {
    const data = {};
    this.inputFields.forEach(field => {
      data[field.name] = $(field.selector).val();
    });
    return data;
  }

  // Send the AJAX request to the server to create a resource
  sendAjaxRequest(data) {
    const token = $('input[name="_token"]').attr('value'); // CSRF token for security
    return $.ajax({
      url: this.endpoint,
      type: 'POST',
      data: Object.assign({ _token: token }, data),
    });
  }

  // Handle successful resource creation
  handleSuccess(response) {
    const id = response[this.newIdName];
    if (this.type !== 'category') {
      updateInfoWindow(this.type, id); // Update UI with new resource info
    }
    this.hideModal();
    this.removeAddButtonClickListener();
    this.supplierRowManager.resetSupplierDataTable(); // Reset the supplier data table
    this.skipDropdownPopulation = false;
    this.dropdownManager.categoryCreated = false;
    this.rebuildTables(id); // Rebuild relevant tables
  }

  // Handle errors during resource creation
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

  // Rebuild tables after successful resource creation
  rebuildTables(id) {
    const tableManager = new TableManager({ type: this.type });
    const promises = [tableManager.rebuildTable()];

    // Once all tables are rebuilt, update specific rows and modals
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

  // Attach listeners to category modal close buttons
  attachCategoryModalCloseListeners() {
    $('#closeCategoryModalButton1, #closeCategoryModalButton2').off('click').on('click', () => {
      this.skipDropdownPopulation = true;
      this.inputModal.modal('toggle');
    });
  }

  // Attach listener to add button for form submission
  attachAddButtonClickListener() {
    if (!this.clickListenerAttached) {
      let dataFetchPromises = [];
      if (this.type === 'part' && !this.skipDropdownPopulation) {
        dataFetchPromises = this.fetchDropdownData(); // Fetch dropdown data if required
      }

      // Populate dropdowns and set up form validation once data is fetched
      Promise.all(dataFetchPromises)
        .then(data => this.populateDropdowns(data))
        .then(() => this.setupFormValidation())
        .catch(error => console.error('Error fetching data:', error));

      this.clickListenerAttached = true;
      this.skipDropdownPopulation = false;
    }
  }

  // Remove click listener from the add button
  removeAddButtonClickListener() {
    this.addButton.off('click');
  }

  // Fetch data required for dropdowns (locations, footprints, categories)
  fetchDropdownData() {
    return [DataFetchService.getLocations(), DataFetchService.getFootprints(), DataFetchService.getCategories()];
  }

  // Populate dropdown menus with fetched data
  populateDropdowns(data) {
    const [locations, footprints, categories] = data;
    if (this.type !== 'category') {
      if (this.type === 'part') {
        this.dropdownManager.addPartLocationDropdown(locations);
        this.dropdownManager.addPartFootprintDropdown(footprints);
        if (!this.dropdownManager.categoryCreated) {
          this.dropdownManager.addPartCategoryDropdown(categories);
        }
        this.dropdownManager.categoryCreated = false;
      }
    }
  }

  // Set up form validation for input fields
  setupFormValidation() {
    const formValidator = new FormValidator(this.inputForm, this.addButton);
    formValidator.attachValidation(this.requestCreation.bind(this));
  }

  // Initialize modal show and hide behaviors
  initializeModalBehavior() {
    this.inputModal.on('hidden.bs.modal', (event) => this.onModalHidden(event));
    this.inputModal.on('show.bs.modal', () => this.onModalShown());
    $('#categoryCreationModal').on('show.bs.modal', () => { this.skipDropdownPopulation = true; });
  }

  // Handle actions when the modal is hidden
  onModalHidden(event) {
    if (event.target === this.inputModal[0]) {
      console.log("ping");
      $('#mouserSearchResults').empty();
      this.removeAddButtonClickListener();
      this.clickListenerAttached = false;
    }
    if (!this.skipDropdownPopulation) {
      $('#addPartAddStockSwitch').prop('checked', false).trigger('change');
    }
  }

  // Handle actions when the modal is shown
  onModalShown() {
    if (!this.skipDropdownPopulation) {
      this.attachAddButtonClickListener();
    } else {
      console.log("now your cat select gets unresponsive, i promise it");
      this.setupFormValidation();
    }
    this.skipDropdownPopulation = false;
  }
}