import {
    loadActiveTab,
    addActiveTabEventListeners
} from './custom';

// Bootstrap Location detail table here if wanted


export function initializeShowCategory() {
    const defaultTab = document.getElementById('categoriesTabs').dataset.defaultTab; // data-default-tab attribute

    loadActiveTab('categories', defaultTab);
    addActiveTabEventListeners('categories');
};