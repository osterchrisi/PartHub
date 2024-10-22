export { PartCreator };
import { ResourceCreator } from "./ResourceCreator";

class PartCreator extends ResourceCreator {
    constructor(options, tableRebuildFunctions = []) {
      super(options, tableRebuildFunctions);
      this.initializeUppercaseToggle();
    }
  
    // Collect form data and add supplier info
    collectFormData() {
      const data = super.collectFormData();
      data['suppliers'] = this.collectSupplierData();
      return data;
    }
  
    // Populate part-specific dropdowns and add additional event listeners
    populateDropdowns(data) {
      super.populateDropdowns(data);
      this.toggleStockForm();
      this.supplierRowManager.addSupplierDataRowButtonClickListener('#supplierDataTable', 'addSupplierRowBtn-partEntry');
    }
  
    // Collect data from supplier rows in the part form
    collectSupplierData() {
      let suppliers = [];
      $('#supplierDataTable tbody tr').each(function () {
        let rowIndex = $(this).data('supplier-index');
        let supplierRow = {
          supplier_id: $(`[data-supplier-id="${rowIndex}"]`).val(),
          URL: $(`[data-url-id="${rowIndex}"]`).val(),
          SPN: $(`[data-spn-id="${rowIndex}"]`).val(),
          price: $(`[data-price-id="${rowIndex}"]`).val()
        };
        suppliers.push(supplierRow);
      });
      return suppliers;
    }
  
    // Initialize uppercase toggle for part name input field
    initializeUppercaseToggle() {
      const $toggleButton = $('#toggle-uppercase-button');
      const $addPartName = $('#addPartName');
      let isUppercase = false;
      let originalValue = '';
  
      const toggleUppercase = () => {
        isUppercase = !isUppercase;
        if (isUppercase) {
          originalValue = $addPartName.val(); // Store original value before converting to uppercase
          $addPartName.on('input.uppercase', function () {
            const uppercased = $(this).val().toUpperCase();
            $(this).val(uppercased);
          });
          $addPartName.val($addPartName.val().toUpperCase());
          $toggleButton.addClass('active'); // Indicate active state
          $toggleButton.text('AA');
        } else {
          $addPartName.off('input.uppercase'); // Remove event listener for uppercase
          $addPartName.val(originalValue); // Restore original value
          $toggleButton.removeClass('active'); // Remove active state indication
          $toggleButton.text('Aa');
        }
      };
  
      // Ensure no duplicate event listeners are attached
      $toggleButton.off('click');
      $toggleButton.click(() => {
        toggleUppercase();
      });
  
      $toggleButton.text('Aa'); // Initialize button text
    }
  
    // Toggle the "Add Stock" functionality for a new part
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
  }