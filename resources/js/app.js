import './bootstrap';
import { initializePartsView } from './partsView';
import { initializeBomsView } from './bomsView';
import { initializeCategoriesView} from './categoriesView';
import { initializeLocationsView } from './locationsView';
import Alpine from 'alpinejs';

window.Alpine = Alpine;

Alpine.start();

const currentView = document.body.getAttribute('data-view');
console.log(currentView);

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
});