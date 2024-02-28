import {
    loadActiveTab,
    addActiveTabEventListeners
} from './custom';

// Bootstrap Supplier Detail table here if wanted


export function initializeShowSupplier() {
    const defaultTab = document.getElementById('suppliersTabs').dataset.defaultTab; // data-default-tab attribute

    loadActiveTab('suppliers', defaultTab);
    addActiveTabEventListeners('suppliers');
};