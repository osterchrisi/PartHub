export { InfoWindow }

import { StockManager } from "./Resources/StockManager";
import { ImageManager } from "./Resources/ImageManager";
import { DocumentManager } from "./Resources/DocumentManager";
import { SupplierRowManager } from "../Tables/SupplierRowManager";
import { TableManager } from "../Tables/TableManager";

import {
    bootstrapBomDetailsTable,
} from './tables';

import {
    updateInfoWindow,
    deleteSelectedRows
} from "./custom";

class InfoWindow {
    constructor(type, id = null) {
        this.type = type;
        this.id = id;

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
                // Supplier Data Table
                $('#partSupplierDataTable').bootstrapTable({});
                $('#partSupplierDataTable').on('check.bs.table uncheck.bs.table ' +
                    'check-all.bs.table uncheck-all.bs.table',
                    function () {
                        $('#deleteSupplierRowBtn-info').prop('disabled', !$('#partSupplierDataTable').bootstrapTable('getSelections').length);
                    })


                const supplierRowManager = new SupplierRowManager();
                supplierRowManager.addSupplierDataRowButtonClickListener('#partSupplierDataTable', 'addSupplierRowBtn-info', this.id);


                $('#deleteSupplierRowBtn-info').click(() => {
                    let selection = $('#partSupplierDataTable').bootstrapTable('getSelections');
                    let deleteRowId = [];
                    deleteRowId.push(selection[0]._data['supplier-data-id']); // Correctly access the first row's _data field
                    console.log("delete id = ", deleteRowId);
                    deleteSelectedRows(deleteRowId, 'supplier_data', 'id', () => updateInfoWindow('part', this.id));
                });
                const tableManager = new TableManager({ type: 'supplierData' });
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
        $.ajax({
            url: '/locations.get',
            dataType: 'json',
            success: function (locations) {
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
            },
            error: function (error) {
                // console.log(error);
            }
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