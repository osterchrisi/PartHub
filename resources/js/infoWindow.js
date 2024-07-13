export { infoWindow }

class infoWindow {
    constructor(type) {
        this.type = type;

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
        // console.log(this.type)
    }


    initializeTabs() {
        console.log("initializing Tabs");
        const defaultTab = document.getElementById(this.defaultTab).dataset.defaultTab; // data-default-tab attribute
        console.log(defaultTab);

        this.loadActiveTab(this.type, defaultTab);
        this.addActiveTabEventListeners(this.type);
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
            console.log("lastActiveTab = ", lastActiveTab);
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
    addActiveTabEventListeners(type) {
        const tabs = document.querySelectorAll('[data-bs-toggle="tab"]');
        tabs.forEach((tab) => {
            tab.addEventListener('shown.bs.tab', (event) => this.saveActiveTab(type, event));
        });
    }
}