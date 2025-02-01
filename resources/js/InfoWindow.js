export { InfoWindow }

import { StockManager } from "./Resources/StockManager";
import { ImageManager } from "./Resources/ImageManager";
import { DocumentManager } from "./Resources/DocumentManager";
import { SupplierRowManager } from "./Tables/SupplierRowManager";
import { AlternativesRowManager } from "./Tables/AlternativesRowManager";
import { TableManager } from "./Tables/TableManager";
import { DataFetchService } from "./Services/DataFetchService";

import {
    bootstrapBomDetailsTable,
} from './tables';

import {
    updateInfoWindow,
    deleteSelectedRows
} from "./custom";
import { Layout } from "./User Interface/Layout";

class InfoWindow {
    constructor(type, id = null) {
        this.type = type;
        this.id = id;

        // Set default tab in case nothing is set
        switch (type) {
            case 'part':
                this.defaultTab = 'partTabs'
                break;
            case 'bom':
                this.defaultTab = 'bomTabs'
                break;
            case 'location':
                this.defaultTab = 'locationTabs'
                break;
            case 'footprint':
                this.defaultTab = 'footprintTabs'
                break;
            case 'supplier':
                this.defaultTab = 'supplierTabs'
                break;
            case 'category':
                this.defaultTab = 'categoryTabs'
                break;
            default:
                break;
        }
    }

    initialize() {
        switch (this.type) {
            case 'part':
                this.bootstrapShowPartTables();
                const stockManager = new StockManager();
                stockManager.attachModalHideListener();
                this.setupStockChangeButtons(stockManager, this.id);
                this.setupImageManager();
                this.setupDocumentManager();

                //TODO: Clean these two up, lotta code here...
                //* Supplier Data Table in Info Window
                $('#partSupplierDataTable').bootstrapTable({});
                $('#partSupplierDataTable').on('check.bs.table uncheck.bs.table ' +
                    'check-all.bs.table uncheck-all.bs.table',
                    function () {
                        $('#deleteSupplierRowBtn-info').prop('disabled', !$('#partSupplierDataTable').bootstrapTable('getSelections').length);
                    })

                // Supplier Row Manager in Info Window
                const supplierRowManager = new SupplierRowManager({
                    inputForm: '#partSupplierDataTableForm',
                    table: '#partSupplierDataTable'
                });
                supplierRowManager.addSupplierDataRowButtonClickListener('addSupplierRowBtn-info', this.id);


                $('#deleteSupplierRowBtn-info').click(() => {
                    let selection = $('#partSupplierDataTable').bootstrapTable('getSelections');
                    let deleteRowId = [];
                    deleteRowId.push(selection[0]._data['supplier-data-id']); // Correctly access the first row's _data field
                    console.log("delete id = ", deleteRowId);
                    deleteSelectedRows(deleteRowId, 'supplier_data', 'id', () => updateInfoWindow('part', this.id));
                });
                const tableManager = new TableManager({ type: 'supplierData' });

                //* Part Alternative Data Table in Info Window
                $('#partAlternativeTable').bootstrapTable({});
                $('#partAlternativeTable').on('check.bs.table uncheck.bs.table ' +
                    'check-all.bs.table uncheck-all.bs.table',
                    function () {
                        $('#deleteAlternativeRowBtn-info').prop('disabled', !$('#partAlternativeTable').bootstrapTable('getSelections').length);
                    })

                // Alternative Row Manager in Info Window
                const alternativeRowManager = new AlternativesRowManager({
                    inputForm: '#partAlternativeTableForm',
                    table: '#partAlternativeTable'
                });
                alternativeRowManager.addAlternativeDataRowButtonClickListener('addAlternativeRowBtn-info', this.id);

                $('#deleteAlternativeRowBtn-info').click(() => {
                    let selection = $('#partAlternativeTable').bootstrapTable('getSelections');
                    let deleteRowId = [];
                    selection.forEach(row => {
                        deleteRowId.push(row['alternative-data-id']);
                    });
                    console.log("delete id = ", deleteRowId);
                    deleteSelectedRows(deleteRowId, 'alternative_data', 'id', () => updateInfoWindow('part', this.id));
                });
                break;
            case 'bom':
                bootstrapBomDetailsTable();
                this.allowHtmlTableElementsInPopover();
                this.initializeBomDetailPopover();
                break;
            case 'supplier':
                $('#supplierDetailsTable').bootstrapTable({});
            default:
                break;
        }
        this.setupTabs();
        Layout.initializeTooltips();
        Layout.initializePopovers();
    }

    setupTabs() {
        const defaultTab = document.getElementById(this.defaultTab).dataset.defaultTab; // data-default-tab attribute
        this.loadActiveTab(this.type, defaultTab);
        this.setupTabListeners(this.type);
    }

    /**
    * Saves the active tab for a specific infoWindow in the local storage.
    * @param {string} type - The identifier of the infoWindow.
    * @param {Event} event - The event that triggered this function.
    * @returns {void}
    */
    saveActiveTab(type, event) {
        const tabId = event.target.getAttribute('id');
        if (tabId) {
            localStorage.setItem('lastActiveTab_' + type, tabId);
        }
    }

    /**
     * Loads the active tab for a specific infoWindow from local storage and shows it.
     * @param {string} type - The identifier of the infoWindow.
     * @returns {void}
     */
    loadActiveTab(type, defaultTab) {
        var lastActiveTab = localStorage.getItem('lastActiveTab_' + type) || defaultTab;
        if (lastActiveTab) {
            // console.log("lastActiveTab = ", lastActiveTab);
            const tabElement = document.querySelector(`#${lastActiveTab}`);
            if (tabElement) {
                const tab = new bootstrap.Tab(tabElement);
                tab.show();
            }
        }
    }

    /**
     * Attaches an event listener to all togglable tabs in a specific infoWindow
     * which triggers the saveActiveTab function with the corresponding tye identifier.
     * @param {string} type - The identifier of the infoWindow.
     * @returns {void}
     */
    setupTabListeners(type) {
        const tabs = document.querySelectorAll('[data-bs-toggle="tab"]');
        tabs.forEach((tab) => {
            tab.addEventListener('shown.bs.tab', (event) => this.saveActiveTab(type, event));
        });
    }

    bootstrapShowPartTables() {
        const histTable = new TableManager({ type: 'partHistory' });
        const partInBomsTable = new TableManager({ type: 'partInBoms' });
        histTable.bootstrapTable();
        partInBomsTable.bootstrapTable();
    }

    setupStockChangeButtons(stockManager, id) {
        DataFetchService.getLocations()
            .done(locations => {
                // Add Stock Button
                $('#addStockButton').click(function () {
                    stockManager.showStockChangeModal(1, locations, id);
                });
                // Move Stock Button
                $('#moveStockButton').click(function () {
                    stockManager.showStockChangeModal(0, locations, id);
                });
                // Reduce Stock Button
                $('#reduceStockButton').click(function () {
                    stockManager.showStockChangeModal(-1, locations, id);
                });
            })
            .fail(error => {
                console.error(error);
            });
    }

    setupImageManager() {
        this.imageManager = new ImageManager(this.type, this.id);
        this.imageManager.setupImageContainer();
    }

    setupDocumentManager() {
        this.documentManager = new DocumentManager(this.type, this.id);
        this.documentManager.setupDocumentContainer();
    }

    allowHtmlTableElementsInPopover() {
        // Allow extra HTML elements for the BOM popover mini stock table
        var myDefaultAllowList = bootstrap.Tooltip.Default.allowList

        // Allow table elements
        myDefaultAllowList.table = []
        myDefaultAllowList.thead = []
        myDefaultAllowList.tr = []
        myDefaultAllowList.td = []
        myDefaultAllowList.tbody = []

        // Allow td elements and data-bs-option attributes on td elements
        myDefaultAllowList.td = ['data-bs-option']

    }

    initializeBomDetailPopover() {
        // Initialize all popovers
        const popoverTriggerList = document.querySelectorAll('[data-bs-toggle="popover"]');
        const popoverList = [...popoverTriggerList].map(popoverTriggerEl => new bootstrap.Popover(popoverTriggerEl));

        // Re-initialize the popovers after toggling a column
        //* This should be possible via the 'column-switch.bs.table' but it never fires...
        $(function () {
            $('#BomDetailsTable').on('post-body.bs.table', function () {
                const popoverTriggerList = document.querySelectorAll('[data-bs-toggle="popover"]');
                const popoverList = [...popoverTriggerList].map(popoverTriggerEl => new bootstrap
                    .Popover(popoverTriggerEl));
            });
        });
    }
}