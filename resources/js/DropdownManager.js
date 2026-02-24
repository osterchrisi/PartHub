export { DropdownManager }
import { ResourceCreator } from "./Resources/ResourceCreators/ResourceCreator";
import { DataFetchService } from "./Services/DataFetchService";

class DropdownManager {
    constructor(options = {}) {
        this.inputModal = options.inputModal || null;
        // Category creation flag to ensure proper category dropdown behaviour
        this.categoryCreated = false;
    }

    // XSS-safe select builder with support for attributes and options
    _buildSelect({ selectId = null, selectClass = '', attrs = {}, options = [] }) {
        const select = document.createElement('select');
        if (selectId) select.id = selectId;
        if (selectClass) select.className = selectClass;

        // set attributes (data-*, required, etc.)
        Object.entries(attrs || {}).forEach(([k, v]) => {
            if (k === 'required') {
                if (v) select.required = true;
                return;
            }
            if (k.startsWith('data-')) {
                select.setAttribute(k, String(v));
                return;
            }
            select.setAttribute(k, String(v));
        });

        (options || []).forEach(o => {
            const opt = document.createElement('option');
            opt.value = String(o.value ?? '');
            opt.textContent = String(o.label ?? '');
            if (o.selected) opt.selected = true;
            select.appendChild(opt);
        });

        return select;
    }

    _destroySelectizeIfAny(el) {
        const s = el?.selectize;
        if (s && typeof s.destroy === 'function') s.destroy();
    }

    /**
      * Creates and adds a dropdown list of locations to the part entry modal and 'selectizes' it.
      * @param {Array} locations - An array of objects representing locations to be displayed in the dropdown list.
      * Each location object must have a "location_id" and a "location_name" property.
      * @return {void}
      */
    addPartLocationDropdown(locations) {
        const div = document.getElementById("addPartLocDropdown");
        if (!div) return;

        div.replaceChildren();

        const label = document.createElement('label');
        label.className = 'input-group-text';
        label.htmlFor = 'addPartLocSelect';
        label.textContent = 'To';

        const select = this._buildSelect({
            selectId: 'addPartLocSelect',
            selectClass: 'form-select',
            attrs: { required: true },
            options: (locations || []).map(l => ({
                value: l.location_id,
                label: l.location_name
            }))
        });

        div.append(label, select);

        // ensure no double init
        this._destroySelectizeIfAny(select);

        const $select = $(select).selectize({
            create: (input) => this.createNewSelectizeDropdownEntry(input, 'location')
        });

        // Disable dropdown (optional add stock)
        $select[0].selectize.disable();
    }

    /**
    * Creates and adds a dropdown list of footprints to the part entry modal and 'selectizes' it.
    * @param {Array} footprints - An array of objects representing footprints to be displayed in the dropdown list.
    * Each footprint object must have a "footprint_id" and a "footprint_name" property.
    * @return {void}
    */
    addPartFootprintDropdown(footprints) {
        const div = document.getElementById("addPartFootprintDropdown");
        if (!div) return;

        div.replaceChildren();

        const select = this._buildSelect({
            selectId: 'addPartFootprintSelect',
            selectClass: 'form-select form-select-sm not-required',
            options: [
                { value: '', label: 'No Footprint', selected: true },
                ...(footprints || []).map(fp => ({
                    value: fp.footprint_id,
                    label: fp.footprint_name
                }))
            ]
        });

        const label = document.createElement('label');
        label.htmlFor = 'addPartFootprintSelect';
        label.textContent = 'Footprint';

        div.append(select, label);

        this._destroySelectizeIfAny(select);

        $(select).selectize({
            create: (input) => this.createNewSelectizeDropdownEntry(input, 'footprint'),
            placeholder: 'Select Footprint',
            onInitialize: function () { this.setValue(''); }
        });
    }

    /**
    * Creates and adds a dropdown list of manufacturers to the part entry modal and 'selectizes' it.
    * @param {Array} manufacturers - An array of objects representing manufacturers to be displayed in the dropdown list.
    * Each manufacturer object must have a "manufacturer_id" and a "manufacturer_name" property.
    * @return {void}
    */
    addPartManufacturerDropdown(manufacturers) {
        const div = document.getElementById("addPartManufacturerDropdown");
        if (!div) return;

        div.replaceChildren();

        const select = this._buildSelect({
            selectId: 'addPartManufacturerSelect',
            selectClass: 'form-select form-select-sm not-required',
            options: [
                { value: '', label: 'No Manufacturer', selected: true },
                ...(manufacturers || []).map(m => ({
                    value: m.manufacturer_id,
                    label: m.manufacturer_name
                }))
            ]
        });

        const label = document.createElement('label');
        label.htmlFor = 'addPartManufacturerSelect';
        label.textContent = 'Manufacturer';

        div.append(select, label);

        this._destroySelectizeIfAny(select);

        $(select).selectize({
            create: (input) => this.createNewSelectizeDropdownEntry(input, 'manufacturer'),
            placeholder: 'Select Manufacturer',
            onInitialize: function () { this.setValue(''); }
        });
    }

    /**
    * Creates and adds a dropdown list of suppliers to the part entry modal and 'selectizes' it.
    * @param {Array} suppliers - An array of objects representing suppliers to be displayed in the dropdown list.
    * Each supplier object must have a "supplier_id" and a "supplier_name" property.
    * @return {void}
    */
    addPartSupplierDropdown(suppliers, dropdownId, newRowIndex) {
        const div = document.getElementById(dropdownId);
        if (!div) {
            console.warn(`Dropdown container with ID ${dropdownId} not found.`);
            return;
        }

        div.replaceChildren();

        const select = this._buildSelect({
            selectClass: 'form-select form-select-sm not-required',
            attrs: { 'data-supplier-id': newRowIndex },
            options: [
                { value: '', label: 'No Supplier', selected: true },
                ...(suppliers || []).map(s => ({
                    value: s.supplier_id,
                    label: s.supplier_name
                }))
            ]
        });

        div.appendChild(select);

        this._destroySelectizeIfAny(select);

        $(select).selectize({
            create: (input) => this.createNewSelectizeDropdownEntry(input, 'supplier', dropdownId, newRowIndex),
            placeholder: 'Select Supplier',
            onInitialize: function () { this.setValue(''); },
            onDropdownOpen: function () {
                $('.bootstrap-table .fixed-table-container .fixed-table-body').css({ 'overflow-x': 'visible', 'overflow-y': 'visible' });
            },
            onDropdownClose: function () {
                $('.bootstrap-table .fixed-table-container .fixed-table-body').css({ 'overflow-x': 'auto', 'overflow-y': 'auto' });
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
        const div = document.getElementById("addPartCategoryDropdown");
        if (!div) return;

        div.replaceChildren();

        const select = this._buildSelect({
            selectId: 'addPartCategorySelect',
            selectClass: 'form-select form-select-sm not-required',
            attrs: { placeholder: 'Select Category' },
            options: [{ value: '', label: 'No Category', selected: true }]
        });

        // add nested options safely
        const nested = this.organizeCategories(categories || []);
        this._appendCategoryOptionNodes(select, nested, 0);

        const label = document.createElement('label');
        label.htmlFor = 'addPartCategorySelect';
        label.textContent = 'Category';

        div.append(select, label);

        this._destroySelectizeIfAny(select);

        $(select).selectize({
            create: (input) => this.createNewSelectizeDropdownEntry(input, 'category'),
            onInitialize: function () { this.setValue(''); }
        });
    }

    _appendCategoryOptionNodes(selectEl, categories, level) {
        const indent = '\u00A0'.repeat(level * 4); // matches old &nbsp;*4
        (categories || []).forEach(cat => {
            const opt = document.createElement('option');
            opt.value = String(cat.category_id ?? '');
            opt.textContent = `${indent}${String(cat.category_name ?? '')}`;
            selectEl.appendChild(opt);

            if (cat.children?.length) {
                this._appendCategoryOptionNodes(selectEl, cat.children, level + 1);
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

        // Create a map with all categories, each with an empty 'children' array
        categories.forEach(category => {
            categoryMap[category.category_id] = { ...category, children: [] };
        });

        let nestedCategories = [];

        categories.forEach(category => {
            if (category.parent_category === 0) {
                // If no parent category, add to the top-level categories
                nestedCategories.push(categoryMap[category.category_id]);
            } else if (categoryMap[category.parent_category]) {
                // Check if parent category exists in the map before adding children
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
    //TODO: Obsolete with new safe DOM approach, consider removing
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
    createNewSelectizeDropdownEntry(input, type, supplier_dropdownId = null, newRowIndex = null) {
        const token = $('input[name="_token"]').attr('value');
        let endpoint, newIdName, nameField, getFunction, dropdownFunction, dropdownId, $select;

        switch (type) {
            case 'location':
                endpoint = '/location.create';
                newIdName = 'Location ID';
                nameField = 'location_name';
                getFunction = DataFetchService.getLocations;
                dropdownFunction = this.addPartLocationDropdown.bind(this);
                dropdownId = 'addPartLocSelect';
                break;
            case 'footprint':
                endpoint = '/footprint.create';
                newIdName = 'Footprint ID';
                nameField = 'footprint_name';
                getFunction = DataFetchService.getFootprints;
                dropdownFunction = this.addPartFootprintDropdown.bind(this);
                dropdownId = 'addPartFootprintSelect';
                break;
            case 'manufacturer':
                endpoint = '/manufacturer.create';
                newIdName = 'Manufacturer ID';
                nameField = 'manufacturer_name';
                getFunction = DataFetchService.getManufacturers;
                dropdownFunction = this.addPartManufacturerDropdown.bind(this);
                dropdownId = 'addPartManufacturerSelect';
                break;
            case 'supplier':
                endpoint = '/supplier.create';
                newIdName = 'Supplier ID';
                nameField = 'supplier_name';
                getFunction = DataFetchService.getSuppliers;
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
        DataFetchService.getCategories().done((categories) => {
            const parentSel = document.getElementById('parentCategory');
            if (!parentSel) return;

            // Clear + rebuild options safely
            parentSel.replaceChildren();

            const nested = this.organizeCategories(categories || []);
            this._appendCategoryOptionNodes(parentSel, nested, 0);

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
                DataFetchService.getCategories().done((newList) => {
                    this.addPartCategoryDropdown(newList);
                    var selectize = $('#addPartCategorySelect')[0].selectize;
                    selectize.addItem(newEntry['category_id']);
                    this.categoryCreated = true;
                    $('#categoryCreationModal').modal('toggle');
                    this.inputModal.modal('toggle');

                    // Trigger category list table rebuild
                    $(document).trigger('category:created', [newEntry]);
                });
            },
            error: function () {
                console.error('Error creating new category');
            }
        });
    }

    addPartAlternativeDropdown(alternatives, dropdownId, newRowIndex) {
        const div = document.getElementById(dropdownId);
        if (!div) {
            console.warn(`Dropdown container with ID ${dropdownId} not found.`);
            return;
        }

        div.replaceChildren();

        const select = this._buildSelect({
            selectClass: 'form-select form-select-sm',
            attrs: { 'data-alternative-id': newRowIndex },
            options: [
                { value: '', label: 'Select Alternative', selected: true },
                ...(alternatives || []).map(p => ({
                    value: p.part_id,
                    label: p.part_name
                }))
            ]
        });

        div.appendChild(select);

        this._destroySelectizeIfAny(select);

        $(select).selectize({
            create: false,
            placeholder: 'Select Alternative',
            onInitialize: function () { this.setValue(''); },
            onDropdownOpen: function () {
                $('.bootstrap-table .fixed-table-container .fixed-table-body').css({ 'overflow-x': 'visible', 'overflow-y': 'visible' });
            },
            onDropdownClose: function () {
                $('.bootstrap-table .fixed-table-container .fixed-table-body').css({ 'overflow-x': 'auto', 'overflow-y': 'auto' });
            }
        });
    }

    /**
    * Creates a selectized dropdown for selecting a Part by ID.
    * Intended for BOM editor "add line" UX.
    *
    * @param {Array} parts - [{part_id, part_name}, ...]
    * @param {string} containerId - ID of the div where the select should be injected
    * @param {string} selectId - ID for the select element itself
    */
    addBomPartSelect(parts, containerId = 'bomEditorAddPartDropdown', selectId = 'bomEditorAddPartSelect') {
        const div = document.getElementById(containerId);
        if (!div) {
            console.warn(`Part dropdown container #${containerId} not found.`);
            return;
        }

        div.replaceChildren();

        const select = this._buildSelect({
            selectId,
            selectClass: 'form-select form-select-sm',
            attrs: { placeholder: 'Select part' },
            options: [
                { value: '', label: 'Select part...', selected: true },
                ...(parts || []).map(p => ({ value: p.part_id, label: p.part_name }))
            ]
        });

        div.appendChild(select);

        this._destroySelectizeIfAny(select);

        $(select).selectize({
            create: false,
            allowEmptyOption: true,
            placeholder: 'Select part…',
            onDropdownOpen: function () {
                $('.bootstrap-table .fixed-table-container .fixed-table-body').css({ 'overflow-x': 'visible', 'overflow-y': 'visible' });
            },
            onDropdownClose: function () {
                $('.bootstrap-table .fixed-table-container .fixed-table-body').css({ 'overflow-x': 'auto', 'overflow-y': 'auto' });
            }
        });
    }


}