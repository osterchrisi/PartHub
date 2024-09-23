import {
    bootstrapPartsTable,
    bootstrapCategoriesListTable,
    definePartsTableActions,
    defineCategoriesListInPartsViewTableActions,
    rebuildPartsTable,
    attachShowCategoriesButtonClickListener
} from "../tables";

import {
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
        addButton: '#addPart'
    }, [rebuildPartsTable]);


    $('#toolbarAddButton').click(function () {
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
    });

    // Bootstrap the supplierDataTable in the part entry modal only after it's been shown
    // Otherwise resizable columns don't work (because height = 0)
    $('#addSuppliers').on('shown.bs.collapse', event => {

        // Resize partEntry modal upon hiding supplier data table
        $('#mPartEntry').removeClass('modal-lg').addClass('modal-xl');

        // Bootstrap the table only if it isn't bootstrapped yet
        if ($('#supplierDataTable').data('bootstrap.table')) {
            //
        } else {
            // Timeout to wait for the size transformation to happen
            // Otherwise the resizable column handles are not where the columns are
            setTimeout(function () {
                $('#supplierDataTable').bootstrapTable({
                    formatNoMatches: function () {
                        return '';
                    },
                    resizable: true,
                });

                newPartCreator.addSupplierRow('#supplierDataTable');
            }, 300);
        }
    })

    // Reset all the bootstrap-table and collapse shenanigans in the Supplier Data section
    // of the part entry modal
    $('#mPartEntry').on('hidden.bs.modal', function () {
        $('#supplierDataTable').bootstrapTable('destroy');
        $('#addSuppliers').empty();  // Removes all content inside the div
        $('#addSuppliers').append(`
            <div id="supplierTableContainer">
                <table id="supplierDataTable" class="table table-sm table-bordered table-hover">
                    <thead>
                        <tr>
                            <th data-field="supplier">Supplier</th>
                            <th data-field="URL">URL</th>
                            <th data-field="SPN">SPN</th>
                            <th data-field="price">Price</th>
                            <th data-field="remove"></th>
                        </tr>
                    </thead>
                    <tbody>
                        <!-- Rows will be dynamically added here -->
                    </tbody>
                </table>
            </div>
            <button type="button" id="addSupplierRowBtn-partEntry" class="btn btn-sm btn-secondary mt-2">Add
            Supplier</button>
        `);
        // Collapse the supplier data div
        $('#addSuppliers').removeClass('show');
        // Resize modal size
        $('#mPartEntry').removeClass('modal-xl').addClass('modal-lg');
    });

    // Resize partEntry modal upon hiding supplier data table
    $('#addSuppliers').on('hidden.bs.collapse', event => {
        $('#mPartEntry').removeClass('modal-xl').addClass('modal-lg');
    });

}