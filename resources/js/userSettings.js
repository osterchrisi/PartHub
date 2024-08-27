export { UserSettings }


class UserSettings {
    constructor() {
        this.init();
    }

    init() {
        this.fetchAllSettings();
        this.setupEventListeners();
    }

    fetchAllSettings() {
        this.fetchSwitchSettings();
    }

    fetchSwitchSettings() {
        // Loop through all elements with the class 'user-setting-switch'
        $('.user-setting-switch').each((index, element) => {
            const settingName = $(element).data('setting-name');
            this.fetchBooleanSetting(settingName, element);
        });
    }

    // Fetch an individual setting from the server
    fetchBooleanSetting(settingName, element) {
        $.ajax({
            url: `settings/${settingName}`,
            method: 'GET',
            success: (response) => {
                // Set the switch state based on the fetched setting
                const isEnabled = response[settingName] === 'true';
                $(element).prop('checked', isEnabled);
            },
            error: (xhr, status, error) => {
                // console.error(`Failed to fetch the ${settingName} setting:`, error);
                // alert(`Failed to load the ${settingName} setting. Please try again.`);
            }
        });
    }

    // Update a setting on the server
    updateBooleanSetting(settingName, isEnabled) {
        const token = $('input[name="_token"]').attr('value');
        $.ajax({
            url: `settings/${settingName}`,
            method: 'POST',
            data: {
                value: isEnabled ? 'true' : 'false',
                _token: token
            },
            success: (response) => {
                // console.log(`${settingName} setting updated successfully:`, response);
            },
            error: (xhr, status, error) => {
                // console.error(`Failed to update the ${settingName} setting:`, error);
                // alert(`Failed to update the ${settingName} setting. Please try again.`);
                // Revert the switch state in case of error
                $(element).prop('checked', !isEnabled);
            }
        });
    }

    // Set up event listeners for toggling user setting switch elements
    setupEventListeners() {
        this.setupSwitchEventListeners();
    }

    setupSwitchEventListeners() {
        // Add an event listener for all switches
        $('.user-setting-switch').on('change', (event) => {
            const element = event.target;
            const settingName = $(element).data('setting-name');
            const isEnabled = $(element).is(':checked');
            this.updateBooleanSetting(settingName, isEnabled);
        });
    }
}
