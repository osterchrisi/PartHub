export { PartCreator };
import { ResourceCreator } from "./ResourceCreator";
import { SupplierRowManager } from "../../../Tables/SupplierRowManager";
import { DataFetchService } from "../DataFetchService";
import { DropdownManager } from "../../DropdownManager";

class PartCreator extends ResourceCreator {
    constructor(options, tableRebuildFunctions = []) {
        super(options, tableRebuildFunctions);
        this.initializeUppercaseToggle();
        this.initializePartEntryButtons();
        this.initializePartInputButtons();
        this.initializeAddStockToggler();
        this.attachCategoryModalCloseListeners();

        // Part specific managers
        this.supplierRowManager = new SupplierRowManager();
        this.dropdownManager = new DropdownManager({ inputModal: this.inputModal });
        this.skipDropdownPopulation = false;    // Flag to control dropdown population when partEntry modal is closed and re-opened
    }

    // Collect form data and add supplier info
    collectFormData() {
        super.collectFormData();
        this.data['suppliers'] = this.collectSupplierData();
    }

    handleSuccess(response) {
        super.handleSuccess(response);

        // Part entry modal specific thingies
        this.supplierRowManager.resetSupplierDataTable();
        this.resetPartEntryButtons();

        // Reset flags
        this.skipDropdownPopulation = false;
        this.dropdownManager.categoryCreated = false;
    }

    // Populate all dropdowns
    populateAllDropdowns(data) {
        const [locations, footprints, categories] = data;
        this.dropdownManager.addPartLocationDropdown(locations);
        this.dropdownManager.addPartFootprintDropdown(footprints);
        if (!this.dropdownManager.categoryCreated) {
            this.dropdownManager.addPartCategoryDropdown(categories);
            console.log("not sure if ever end up here");
        }
    }

    // Fetch data required for dropdowns (locations, footprints, categories)
    fetchAllDropdownData() {
        return [
            DataFetchService.getLocations(),
            DataFetchService.getFootprints(),
            DataFetchService.getCategories()
        ];
    }

    rebuildTables(id) {
        super.rebuildTables(id);
        this.tableManager.updateStockModal(id); // Adds the part name to the stock modal
    }

    // Attach listeners to category modal close buttons
    attachCategoryModalCloseListeners() {
        $('#closeCategoryModalButton1, #closeCategoryModalButton2').off('click').on('click', () => {
            this.skipDropdownPopulation = true;  // Upon returning to part entry modal, the dropdowns keep their values
            this.inputModal.modal('toggle');
        });
    }

    onModalHidden(event) {
        super.onModalHidden(event);

        // console.log("partModalHiddenByCategoryModal = ", this.partModalHiddenByCategoryModal(event));

        // // Check if the part entry modal was hidden because the category creation modal came into view
        // if (this.partModalHiddenByCategoryModal(event)) {
        //     console.log("Part modal hidden by category modal");
        //     $('#mouserSearchResults').empty();
        // }

        if (!this.skipDropdownPopulation) {
            $('#addPartAddStockSwitch').prop('checked', false).trigger('change');
        }
    }

    //TODO: This does NOT actually do what it says it will do - it's always false -> can go!
    // partModalHiddenByCategoryModal(event) {
    //     return event.target !== this.inputModal[0];
    // }

    onModalShow() {
        super.onModalShow();

        // Populate all dropdowns (modal gets shown anew)
        if (!this.skipDropdownPopulation) {
            let dataFetchPromises = this.fetchAllDropdownData();

            Promise.all(dataFetchPromises)
                .then(data => {
                    if (data.length > 0) {
                        // Populate dropdowns
                        this.populateAllDropdowns(data);
                        this.supplierRowManager.addSupplierDataRowButtonClickListener('#supplierDataTable', 'addSupplierRowBtn-partEntry');
                    }
                })
                .catch(error => console.error('Error fetching dropdown data:', error));
        }

        // Populate only categories (user came back from NOT creating a new category - otherwise dropdownManager does this)
        if (this.skipDropdownPopulation && !this.dropdownManager.categoryCreated) {
            DataFetchService.getCategories()
                .then(categories => {
                    this.dropdownManager.addPartCategoryDropdown(categories);
                })
                .catch(error => console.error('Error fetching categories:', error));

        }

        // Reset flags
        this.skipDropdownPopulation = false;
        this.dropdownManager.categoryCreated = false;
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

    // Initialize uppercase toggler for part name input field
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

    // Initialize the "Add Stock" toggler functionality for a new part
    initializeAddStockToggler() {
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

    // Initialize behaviour of 'Suppliers' and 'Additional Info' buttons
    initializePartEntryButtons() {
        // Highlight the "Suppliers" button when the suppliers section is toggled
        $('#addSuppliers').on('show.bs.collapse', function () {
            $('#showSuppliers').addClass('active');
        }).on('hide.bs.collapse', function () {
            $('#showSuppliers').removeClass('active');
        });

        // Highlight the "Additional Info" button when the advanced options section is toggled
        $('#advancedOptions').on('show.bs.collapse', function () {
            $('#showAdvanced').addClass('active');
        }).on('hide.bs.collapse', function () {
            $('#showAdvanced').removeClass('active');
        });
    }

    // Remove 'active' class from the 'Suppliers' and 'Additional Info' buttons
    resetPartEntryButtons() {
        $('#showSuppliers').removeClass('active');
        $('#showAdvanced').removeClass('active')
    }


    // Select between manual and API entry mode
    initializePartInputButtons() {
        // Initially show Manual Entry and hide Mouser Search
        $('#manualEntrySection').show();
        $('#mouserSearchSection').hide();

        // Manual Entry Button Click Event
        $('#manualEntryButton').on('click', function () {
            $('#manualEntrySection').show();
            $('#mouserSearchSection').hide();
            // Optionally mark the active button
            $(this).addClass('active');
            $('#mouserSearchButton').removeClass('active');
            $('#addPartName').focus();
        });

        // Mouser Search Button Click Event
        $('#mouserSearchButton').on('click', function () {
            $('#manualEntrySection').hide();
            $('#mouserSearchSection').show();
            // Optionally mark the active button
            $(this).addClass('active');
            $('#manualEntryButton').removeClass('active');
            $('#mouserPartName').focus();
        });
    }
}