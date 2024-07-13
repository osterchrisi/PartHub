import {
    loadActiveTab,
    addActiveTabEventListeners
} from './custom';

// Bootstrap Footprint Detail table here if wanted


export function initializeShowFootprint() {
    const defaultTab = document.getElementById('footprintTabs').dataset.defaultTab; // data-default-tab attribute

    loadActiveTab('footprint', defaultTab);
    addActiveTabEventListeners('footprint');
};