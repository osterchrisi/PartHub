import './bootstrap';
import { initializePartsView } from './views/partsView';
import { initializeBomsView } from './views/bomsView';
import { initializeCategoriesView } from './views/categoriesView';
import { initializeLocationsView } from './views/locationsView';
import { initializeFootprintsView } from './views/footprintsView';
import { initializeSuppliersView } from './views/suppliersView';
// import { initializeSignupView, processChallenge } from './views/signupView';
// import { initializeMultiView } from './multiView';
import {
    enableInlineProcessing,
    bootstrapTableSmallify
} from "./tables";
import {
    makeTableWindowResizable,
    clearModalOnHiding,
    focusFirstInputInModals
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
    // else if (currentView === 'signup') {
    //     initializeSignupView();
    // }
    // else if (currentView === 'multi') {
    //     initializeMultiView();
    // }

    // Common to all views
    enableInlineProcessing();
    bootstrapTableSmallify();
    makeTableWindowResizable();
    clearModalOnHiding();
    focusFirstInputInModals();

    // To track down weird selectize behaviour
    $(document).on('keydown', function (event) {
        if (event.key === 'Enter') {
            console.log('Enter key pressed:', event);
            console.log('Enter key pressed while focused on:', document.activeElement);
        }
    });

    // Initialize Tooltips
    const tooltipTriggerList = document.querySelectorAll('[data-bs-toggle="tooltip"]')
    const tooltipList = [...tooltipTriggerList].map(tooltipTriggerEl => new bootstrap.Tooltip(tooltipTriggerEl, {
        animation: true,
        delay: { "show": 500, "hide": 100 }
    }))

});