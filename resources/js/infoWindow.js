export { infoWindow }

class infoWindow {
    constructor (type) {
        this.type = type

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
    }

    initializeTabs() {
        const defaultTab = document.getElementById('supplierTabs').dataset.defaultTab; // data-default-tab attribute

        loadActiveTab('suppliers', defaultTab);
        addActiveTabEventListeners('suppliers');
    }
}