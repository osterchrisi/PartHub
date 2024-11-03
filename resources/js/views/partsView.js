import {
    initializeMultiSelect,
} from "../custom";

import { attachDeleteRowsHandler } from "../toolbar/toolbar";
import { MouserPartSearch } from "../MouserPartSearch";
import { SupplierRowManager } from "../Tables/SupplierRowManager";
import { TableRowManager } from "../Tables/TableRowManager";
import { TableManager } from "../Tables/TableManager";
import { CategoryTableManager } from "../Tables/CategoriesTableManager";
import { PartCreator } from "../Resources/ResourceCreators/PartCreator";
import { CategoryCreator } from "../Resources/ResourceCreators/CategoryCreator";

export function initializePartsView() {

    //* Table Manager for Parts Table
    const partsTableManager = new TableManager({
        type: 'part'
    });
    partsTableManager.bootstrapTable();
    partsTableManager.defineActions();

    //* Category Creator
    const categoryCreator = new CategoryCreator({
        type: 'category',
        endpoint: '/category.create',
        table: '#categories_list_table',
        newIdName: 'Category ID',
        inputForm: '#categoryEntryForm',
        inputFields: [
            { name: 'category_name', selector: '#addCategoryName' },
            { name: 'parent_category', selector: '#parentCategoryId' }
        ],
        inputModal: '#mCategoryEntry',
        addButton: '#addCategory',
    });

    //* Table Manager for Categories Table
    const categoriesTableManager = new CategoryTableManager({ type: 'category', resourceCreator: categoryCreator })
    categoriesTableManager.bootstrapTable();

    //* Table Row Manager for Parts Table
    const tableRowManager = new TableRowManager('#parts_table', 'part');
    tableRowManager.loadSelectedRow();

    //* Mouser API Search
    // Mouser API Search in part entry modal
    // const partSearch = new MouserPartSearch('mouserPartName', 'mouserSearchResults', 'mouserLoadingSpinner');

    //* Resource Creator for Parts
    const partCreator = new PartCreator({
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
        ],
        inputModal: '#mPartEntry',
        addButton: '#addPart',
    });


    $('#toolbarAddButton').click(function () {
        partCreator.showModal();
    });

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