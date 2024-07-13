import {
    loadActiveTab,
    addActiveTabEventListeners
} from './custom';

// Bootstrap Supplier Detail table here if wanted


export function initializeShowSupplier() {
    const defaultTab = document.getElementById('supplierTabs').dataset.defaultTab; // data-default-tab attribute

    loadActiveTab('supplier', defaultTab);
    addActiveTabEventListeners('supplier');
};