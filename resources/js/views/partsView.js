import {
    bootstrapPartsTable,
    bootstrapCategoriesListTable,
    definePartsTableActions,
    inlineProcessing,
    bootstrapTableSmallify,
    rebuildPartsTable
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
    var $table = $('#parts_table');
    var $menu = $('#parts_table_menu');
    definePartsTableActions($table, $menu);

    bootstrapCategoriesListTable();
    $('#categories_list_table th[data-field="category_edit"], #categories_list_table td[data-field="category_edit"]').hide();
    //TODO: Seems hacky but works. Otherwise the edit buttons always jump line:
    $('#category-window').width($('#category-window').width()+1);

    inlineProcessing();
    bootstrapTableSmallify();
    makeTableWindowResizable();

    focusStockChangeQuantity();
    focusNewPartName();

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
    fetchDataThenAttachClickListener();

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
        url: '/categories.list',
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

async function fetchDataThenAttachClickListener() {
    try {
        // Fetch locations, footprints and categories
        const locations = await getLocations();
        const footprints = await getFootprints();
        const categories = await getCategories();
        const suppliers = await getSuppliers();

        // Attach click listener to Add button
        $('#toolbarAddButton').click(function () {
            callPartEntryModal(locations, footprints, categories, suppliers);
        });

    } catch (error) {
        // Handle errors
        console.error('Error fetching data:', error);
    }
}