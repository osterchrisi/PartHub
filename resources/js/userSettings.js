class UserSettings {
    constructor() {
        this.init();
    }

    // Initialize the component
    init() {
        this.fetchAllSettings();
        this.setupEventListeners();
    }

    // Fetch all settings that are represented by switch elements on the page
    fetchAllSettings() {
        // Loop through all elements with the class 'user-setting-switch'
        $('.user-setting-switch').each((index, element) => {
            const settingName = $(element).data('setting-name');
            this.fetchSetting(settingName, element);
        });
    }

    // Fetch an individual setting from the server
    fetchSetting(settingName, element) {
        $.ajax({
            url: '/settings/${settingName}',
            method: 'GET',
            success: (response) => {
                // Set the switch state based on the fetched setting
                const isEnabled = response[settingName] === 'true';
                $(element).prop('checked', isEnabled);
            },
            error: (xhr, status, error) => {
                console.error(`Failed to fetch the ${settingName} setting:`, error);
                alert(`Failed to load the ${settingName} setting. Please try again.`);
            }
        });
    }

    // Set up the event listeners for toggling any switch element
    setupEventListeners() {
        // Add an event listener for all switches
        $('.user-setting-switch').on('change', (event) => {
            const element = event.target;
            const settingName = $(element).data('setting-name');
            const isEnabled = $(element).is(':checked');
            this.updateSetting(settingName, isEnabled);
        });
    }

    // Update a setting on the server
    updateSetting(settingName, isEnabled) {
        $.ajax({
            url: 'settings/${settingName}',
            method: 'POST',
            data: {
                value: isEnabled ? 'true' : 'false',
                _token: $('meta[name="csrf-token"]').attr('content') // CSRF token for Laravel
            },
            success: (response) => {
                console.log(`${settingName} setting updated successfully:`, response);
            },
            error: (xhr, status, error) => {
                console.error(`Failed to update the ${settingName} setting:`, error);
                alert(`Failed to update the ${settingName} setting. Please try again.`);
                // Revert the switch state in case of error
                $(element).prop('checked', !isEnabled);
            }
        });
    }
}

// Usage Example
// Initialize the class, passing the API URL and the authenticated user's ID
$(document).ready(() => {
    const userSettings = new UserSettings(apiUrl, userId);
});
