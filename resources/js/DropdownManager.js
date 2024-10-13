export { DropdownManager }
import { ResourceCreator } from "./ResourceCreator";

class DropdownManager {
    constructor(options = {}) {
        this.inputModal = options.inputModal || null;
        // Category creation flag to ensure proper category dropdown behaviour
        this.categoryCreated = false;
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
        $(`[data-supplier-id="${newRowIndex}"]`).selectize({
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
                _token: token,
                type: type,
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
            error: function (xhr) {
                if (xhr.status === 403) {
                    const response = JSON.parse(xhr.responseText);
                    alert(response.message);
                } else {
                    console.error('Error creating new entry');
                    $select.data('creating', false);
                }
            }
        });
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
* Initializes the save button for the category modal.
*/
    initializeSaveCategoryButton() {
        $('#saveCategoryButton').off('click').click(() => {
            this.saveNewCategory();
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
                type: 'category',
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