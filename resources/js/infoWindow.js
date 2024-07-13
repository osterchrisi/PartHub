export { infoWindow }

import { StockManager } from "./stockManager";

import {
    bootstrapPartInBomsTable,
    bootstrapHistTable,
    bootstrapTableSmallify
} from './tables';

import { fetchImages } from "./custom";

class infoWindow {
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
                this.bootstrapPartTables();
                const stockManager = new StockManager();
                stockManager.attachModalHideListener();
                this.setupStockChangeButtons(stockManager, this.id);
                this.imageStuff(this.id);
                break;
            case 'bom':
                this.defaultTab = 'bomTabs'
                break;
            default:
                break;
        }

    }

    setupTabs() {
        const defaultTab = document.getElementById(this.defaultTab).dataset.defaultTab; // data-default-tab attribute
        console.log(defaultTab);

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

    bootstrapPartTables() {
        bootstrapPartInBomsTable();
        bootstrapHistTable();
        bootstrapTableSmallify();
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

    imageStuff(part_id) {
        // Image stuff
        var currentPartType = "part"; // Change this to the appropriate type
        var currentPartId = part_id;
        fetchImages(currentPartType, currentPartId);

        // Handle form submission
        $('#imageUploadForm').submit(function (event) {
            // Prevent the default form submission
            event.preventDefault();

            // Serialize the form data
            var formData = new FormData(this);

            // Submit the form data via AJAX
            $.ajax({
                url: $(this).attr('action'),
                type: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                success: function (response) {
                    fetchImages(currentPartType, currentPartId);
                },
                error: function (xhr, status, error) {
                    // Handle any errors that occur during the upload process
                    console.error(error);
                }
            });
        });
    }
}