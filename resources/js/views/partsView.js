import {
    bootstrapPartsTable,
    bootstrapCategoriesListTable,
    definePartsTableActions,
    defineCategoriesListInPartsViewTableActions,
    bootstrapTableSmallify,
    rebuildPartsTable,
    attachShowCategoriesButtonClickListener
} from "../tables";

import {
    initializePopovers,
    initializeMultiSelect,
    loadSelectedRow,
    updateInfoWindow
} from "../custom";

import { attachDeleteRowsHandler } from "../toolbar/toolbar";
import { ResourceCreator } from "../resourceCreator";

export function initializePartsView() {
    initializeMultiSelect('cat-select');

    bootstrapPartsTable();
    definePartsTableActions($('#parts_table'), $('#parts_table_menu'));

    bootstrapCategoriesListTable(); // Also attaches click listeners to the Edit buttons of the category table
    $('#categories_list_table th[data-field="category_edit"], #categories_list_table td[data-field="category_edit"]').hide();
    //TODO: Seems hacky but works. Otherwise the edit buttons always jump line:
    $('#category-window').width($('#category-window').width() + 1);

    attachShowCategoriesButtonClickListener();

    // Need to re-smallify after hiding / showing columns
    $('.bootstrap-table').on('column-switch.bs.table page-change.bs.table', function () {
        bootstrapTableSmallify();
    });

    loadSelectedRow('part', 'parts_table');
    // Experimental ajax search{
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

        rebuildPartsTable(modifiedQueryString);
    });

    initializePopovers();
    attachDeleteRowsHandler('parts_table', 'parts', 'part_id', rebuildPartsTable);

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
            { name: 'supplier', selector: '#addPartSupplierSelect' },
            { name: 'category', selector: '#addPartCategorySelect' },
        ],
        inputModal: '#mPartEntry',
        addButton: '#addPart'
    }, [rebuildPartsTable]);


    $('#toolbarAddButton').click(function () {
        newPartCreator.attachAddButtonClickListener();
        newPartCreator.showModal();
    });

    $.ajax({
        url: '/categories.get',
        dataType: 'json',
        error: function (error) {
            console.log(error);
        }
    }).done(categories => {
        defineCategoriesListInPartsViewTableActions($('#categories_list_table'), $('#bom_list_table_menu'), categories)
    });

    /**
     * Show location divs after potentially
     * having hidden them in the stock modal when hiding the modal
     * @return void
     */
    $('#mAddStock').on('hidden.bs.modal', function () {
        $('#FromStockLocationDiv-row').show();
        $('#ToStockLocationDiv-row').show();
    })
}