import {
    loadActiveTab,
    addActiveTabEventListeners
} from './custom';

// Bootstrap Location detail table here if wanted


export function initializeShowLocation() {
    const defaultTab = document.getElementById('locationsTabs').dataset.defaultTab; // data-default-tab attribute

    loadActiveTab('locations', defaultTab);
    addActiveTabEventListeners('locations');
};