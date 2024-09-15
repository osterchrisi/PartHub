import './bootstrap';
import { initializePartsView } from './views/partsView';
import { initializeBomsView } from './views/bomsView';
import { initializeCategoriesView } from './views/categoriesView';
import { initializeLocationsView } from './views/locationsView';
import { initializeFootprintsView } from './views/footprintsView';
import { initializeSuppliersView } from './views/suppliersView';
import { initializeUserSettingsView } from './views/userSettingsView';
// import { initializeSignupView, processChallenge } from './views/signupView';
// import { initializeMultiView } from './multiView';
import {
    enableInlineProcessing,
    bootstrapTableSmallify
} from "./tables";
import {
    makeTableWindowResizable,
    clearModalOnHiding,
    focusFirstInputInModals,
    initializePopovers,
    initializeTooltips
} from './custom';

const currentView = document.body.getAttribute('data-view');

$(document).ready(function () {
    if (currentView === 'parts') {
        initializePartsView();
    }
    else if (currentView === 'boms') {
        initializeBomsView();
    }
    else if (currentView === 'categories') {
        initializeCategoriesView();
    }
    else if (currentView === 'locations') {
        initializeLocationsView();
    }
    else if (currentView === 'footprints') {
        initializeFootprintsView();
    }
    else if (currentView === 'suppliers') {
        initializeSuppliersView();
    }
    else if (currentView === 'user-settings') {
        initializeUserSettingsView();
    }

    // Common to all views
    enableInlineProcessing();
    // bootstrapTableSmallify();
    makeTableWindowResizable();
    clearModalOnHiding();
    focusFirstInputInModals();
    initializePopovers();
    initializeTooltips();
});