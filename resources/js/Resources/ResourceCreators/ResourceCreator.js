export { ResourceCreator };
import { updateInfoWindow } from "../../custom";
import { MouserPartSearch } from "../../MouserPartSearch";
import { TableRowManager } from "../../Tables/TableRowManager";
import { TableManager } from "../../Tables/TableManager";
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

    this._shouldUpdateInfoWindow = true;
    this._shouldSelectandSaveNewRow = true;

    // Initialize modal behavior and listeners
    this.initializeModalBehavior();
    this.setupFormValidation();

    // Instantiate Manager Classes
    this.tableManager = this.createTableManager();
  }

  createTableManager() {
    return new TableManager({ type: this.type });
  }

  // Request creation of a new resource via AJAX
  requestCreation() {
    this.collectFormData();
    this.data['type'] = this.type;

    // Send AJAX request to create the resource
    this.sendAjaxRequest(this.data)
      .then((response) => this.handleSuccess(response))
      .catch((error) => this.handleError(error));
  }

  // Collect data from input fields to prepare for submission
  collectFormData() {
    this.data = {};
    this.inputFields.forEach(field => {
      this.data[field.name] = $(field.selector).val();
    });
  }

  // Send the AJAX request to the server to create a resource
  sendAjaxRequest(data) {
    const token = $('input[name="_token"]').attr('value'); // CSRF token
    return $.ajax({
      url: this.endpoint,
      type: 'POST',
      data: Object.assign({ _token: token }, data),
    });
  }

  // Handle successful resource creation
  handleSuccess(response) {
    const id = response[this.newIdName];
    if (this._shouldUpdateInfoWindow) {
      updateInfoWindow(this.type, id); // Update UI with new resource info
    }
    this.hideModal();
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
    }
  }

  // Rebuild tables after successful resource creation
  rebuildTables(id) {
    const promises = [this.tableManager.rebuildTable()];

    // Once all tables are rebuilt, update specific rows and modals
    $.when.apply($, promises)
      .done(() => {
        if (this._shouldSelectandSaveNewRow) {
          const tableRowManager = new TableRowManager(this.table);
          tableRowManager.selectNewRow(id);
          tableRowManager.saveSelectedRow(id);
        }
      })
      .fail(() => {
        console.error("Error in one or more table rebuild functions");
      });
  }

  // Remove click listener from the add button
  removeAddButtonClickListener() {
    this.addButton.off('click');
  }

  // Set up form validation for input fields
  setupFormValidation() {
    const formValidator = new FormValidator(this.inputForm, this.addButton);
    formValidator.attachValidation(this.requestCreation.bind(this));
  }

  // Initialize modal show and hide behaviors
  // There is also the global clearModalOnHiding function but it is aware of the category modal shenanigans
  initializeModalBehavior() {
    this.inputModal.on('hidden.bs.modal', (event) => this.onModalHidden(event));
    this.inputModal.on('show.bs.modal', () => this.onModalShow());
    $('#categoryCreationModal').on('show.bs.modal', () => { this.skipDropdownPopulation = true; });
  }

  // Show the modal
  showModal() {
    this.inputModal.modal('show');
  }

  // Hide the modal and reset add stock switch
  hideModal() {
    this.inputModal.modal('hide');
  }

  // Handle actions when the modal is hidden
  onModalHidden(event) {
    // Empty on purpose, overridable
    // But here is a bunny if you insist

    // (\(\                   \|/
    // ( -.-)                 -o-
    // o_(")(")               /|\

    // Look at him, just chilling in the sun
  }

  // Handle actions when the modal is shown
  onModalShow() {
    // Empty on purpose, overridable
    // But here is a bunny if you insist

    // (\(\                   \|/
    // ( -.-)                 -o-
    // o_(")(")               /|\

    // Look at him, just chilling in the sun
  }
}