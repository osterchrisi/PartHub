import {
    bootstrapCategoriesListTable,
    defineCategoriesListInPartsViewTableActions,
    attachShowCategoriesButtonClickListener
} from "../tables";

import {
    initializeMultiSelect,
} from "../custom";

import { attachDeleteRowsHandler } from "../toolbar/toolbar";
import { ResourceCreator } from "../ResourceCreator";
import { MouserPartSearch } from "../MouserPartSearch";
import { SupplierRowManager } from "../SupplierRowManager";
import { TableRowManager } from "../TableRowManager";
import { TableManager } from "../TableManager";
import { CategoryTableManager } from "../CategoriesTableManager";

export function initializePartsView() {

    //* Table Manager for Parts Table
    const partsTableManager = new TableManager({
        type: 'part'
    });
    partsTableManager.bootstrapTable();
    partsTableManager.defineActions();

    // $('#parts_table').on('post-body.bs.table', function() {
    //     document.getElementById('parts_table_placeholder').classList.add('d-none');
    //     document.getElementById('parts_table').classList.remove('d-none');
    // });
    
    //* Table Manager for Categories Table
    const categoriesTableManager = new CategoryTableManager({ type: 'category'})
    categoriesTableManager.bootstrapTable();

    //* Table Row Manager for Parts Table
    const tableRowManager = new TableRowManager('#parts_table', 'part');
    tableRowManager.loadSelectedRow();

    //* Mouser API Search
    // Mouser API Search in part entry modal
    // const partSearch = new MouserPartSearch('mouserPartName', 'mouserSearchResults', 'mouserLoadingSpinner');
    togglePartInputs();
    togglePartEntryButtons();

    //* Resource Creator
    const newPartCreator = new ResourceCreator({
        type: 'part',
        endpoint: '/part.create',
        table_name: '#parts_table',
        newIdName: 'Part ID',
        inputForm: '#partEntryForm',
        inputFields: [
            { name: 'part_name', selector: '#addPartName' },
            { name: 'quantity', selector: '#addPartQuantity' },
            { name: 'to_location', selector: '#addPartLocSelect' },
            { name: 'comment', selector: '#addPartComment' },
            { name: 'description', selector: '#addPartDescription' },
            { name: 'footprint', selector: '#addPartFootprintSelect' },
            // { name: 'supplier', selector: '#addPartSupplierSelect' },
            { name: 'category', selector: '#addPartCategorySelect' },
            { name: 'min_quantity', selector: '#addPartMinQuantity' },
            {
                name: 'suppliers', getValue: function () {
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
            }
        ],
        inputModal: '#mPartEntry',
        addButton: '#addPart',
    }, []);


    $('#toolbarAddButton').click(function () {
        newPartCreator.showModal();
    });

    //* Supplier Row Manager
    const supplierRowManager = new SupplierRowManager();
    supplierRowManager.bootstrapSupplierDataTable();
    supplierRowManager.resetSupplierDataTableOnModalHide();
    supplierRowManager.resizeModalOnSupplierTableCollapse();

    attachDeleteRowsHandler('parts_table', 'parts', 'part_id', () => partsTableManager.rebuildTable());

    /**
    * Show location divs after potentially
    * having hidden them in the stock modal when hiding the modal
    * @return void
    */
    $('#mAddStock').on('hidden.bs.modal', function () {
        $('#FromStockLocationDiv-row').show();
        $('#ToStockLocationDiv-row').show();
    });
    initializeMultiSelect('cat-select');
    experimentalAjaxSearch(partsTableManager);

}

function experimentalAjaxSearch(partsTableManager) {
    $('#search').on("keyup", function () {
        // Get input value on change
        var inputVal = $(this).val();

        // Get query string from the URL and create a URLSearchParams object
        const queryString = window.location.search;
        const searchParams = new URLSearchParams(queryString);

        // Manipulate the "search" value and update it in the URL
        //! Doesn't actually update the URl and not sure if I want to
        let searchValue = searchParams.get('search');
        searchValue = inputVal;
        searchParams.set('search', searchValue);
        var modifiedQueryString = searchParams.toString();

        // Query database and rebuild partstable with result
        modifiedQueryString = '?' + modifiedQueryString;

        partsTableManager.rebuildTable(modifiedQueryString);
    });

}

function togglePartInputs() {
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

function togglePartEntryButtons() {
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