class ResourceCreator {
  constructor(options) {
    this.endpoint = options.endpoint;
    this.inputFields = options.fields;
    this.modalId = options.modalId;
    this.addButtonId = options.addButtonId;
    this.tableRebuildFunction = options.tableRebuildFunction;
  }

  createResouce() {
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
        const id = JSON.parse(response)[field.idName];
        this.updateInfo(id);
        $(this.modalId).modal('hide');
        this.removeClickListener(this.addButtonId);
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
          $(this.modalId).modal('hide');
          this.removeClickListener(this.addButtonId);
        }
      }
    });
  }

  updateInfo(id) {
    // Implement your updateInfo function based on the context
  }

  removeClickListener(buttonId) {
    // Implement your removeClickListener function based on the context
  }
}

// Create instances for each callback type
const newSupplier = new ResourceCreator({
  endpoint: '/supplier.create',
  fields: [{ name: 'supplier_name', selector: '#addSupplierName', idName: 'Supplier ID' }],
  modalId: '#mSupplierEntry',
  addButtonId: '#addSupplier',
  tableRebuildFunction: rebuildSuppliersTable
});

const newFootprint = new ResourceCreator({
  endpoint: '/footprint.create',
  fields: [
    { name: 'footprint_name', selector: '#addFootprintName', idName: 'Footprint ID' },
    { name: 'footprint_alias', selector: '#addFootprintAlias', idName: 'Footprint ID' }
  ],
  modalId: '#mFootprintEntry',
  addButtonId: '#addFootprint',
  tableRebuildFunction: rebuildFootprintsTable
});

// Attach event listeners
$(newSupplier.addButtonId).click(() => newSupplier.createResouce());
$(newFootprint.addButtonId).click(() => newFootprint.createResouce());
