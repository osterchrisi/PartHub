import {
    bootstrapPartsTable,
    bootstrapCategoriesListTable,
    definePartsTableActions,
    defineCategoriesListInPartsViewTableActions,
    inlineProcessing,
    bootstrapTableSmallify,
    rebuildPartsTable,
    attachShowCategoriesButtonClickListener
} from "../tables";

import {
    focusStockChangeQuantity,
    focusNewPartName,
    initializePopovers,
    initializeMultiSelect,
    makeTableWindowResizable
} from "../custom";

import { callPartEntryModal } from '../partEntry';
import { attachDeleteRowsHandler } from "../toolbar/toolbar";

export function initializePartsView() {
    initializeMultiSelect('cat-select');

    bootstrapPartsTable();
    definePartsTableActions($('#parts_table'), $('#parts_table_menu'));

    bootstrapCategoriesListTable(); // Also attaches click listeners to the Edit buttons of the category table
    $('#categories_list_table th[data-field="category_edit"], #categories_list_table td[data-field="category_edit"]').hide();
    //TODO: Seems hacky but works. Otherwise the edit buttons always jump line:
    $('#category-window').width($('#category-window').width()+1);

    inlineProcessing();
    bootstrapTableSmallify();
    makeTableWindowResizable();

    focusStockChangeQuantity();
    focusNewPartName();
    attachShowCategoriesButtonClickListener();

    // Need to re-smallify after hiding / showing columns
    $('.bootstrap-table').on('column-switch.bs.table page-change.bs.table', function () {
        bootstrapTableSmallify();
    });

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
    fetchDataThenAttachClickListenerAndDefineCategoriesTableActions();

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

// Get locations
function getLocations() {
    return $.ajax({
        url: '/locations.get',
        dataType: 'json',
        error: function (error) {
            console.log(error);
        }
    })
}

// Get footprints
function getFootprints() {
    return $.ajax({
        url: '/footprints.get',
        dataType: 'json',
        error: function (error) {
            console.log(error);
        }
    })
}

// Get categories
function getCategories() {
    return $.ajax({
        url: '/categories.get',
        dataType: 'json',
        error: function (error) {
            console.log(error);
        }
    })
}

// Get suppliers
function getSuppliers() {
    return $.ajax({
        url: '/suppliers.get',
        dataType: 'json',
        error: function (error) {
            console.log(error);
        }
    })
}

async function fetchDataThenAttachClickListenerAndDefineCategoriesTableActions() {
    try {
        // Fetch locations, footprints, categories and suppliers for part entry modal
        const locations = await getLocations();
        const footprints = await getFootprints();
        const categories = await getCategories();
        const suppliers = await getSuppliers();

        // Attach click listener to Add (Parts) button
        $('#toolbarAddButton').click(function () {
            callPartEntryModal(locations, footprints, categories, suppliers);
        });

        // Define filtering table row actions for categories table on side pane
        defineCategoriesListInPartsViewTableActions($('#categories_list_table'), $('#bom_list_table_menu'), categories);

    } catch (error) {
        // Handle errors
        console.error('Error fetching data:', error);
    }
}