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
                // Mouser API Key
                if (settingName === 'mouser_enabled') {
                    this.syncMouserKeyUi(isEnabled);
                }
            },
            error: (xhr, status, error) => {
                // console.error(`Failed to fetch the ${settingName} setting:`, error);
                // alert(`Failed to load the ${settingName} setting. Please try again.`);
            }
        });
    }

    // Update a setting on the server
    updateBooleanSetting(settingName, isEnabled, element) {
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
        this.setupMouserAPIKeyListeners();
    }

    setupSwitchEventListeners() {
        // Add an event listener for all switches
        $('.user-setting-switch').on('change', (event) => {
            const element = event.target;
            const settingName = $(element).data('setting-name');
            const isEnabled = $(element).is(':checked');

            // Mousser API Key
            if (settingName === 'mouser_enabled') {
                this.syncMouserKeyUi(isEnabled);
            }

            this.updateBooleanSetting(settingName, isEnabled, element);
        });
    }

    setupMouserAPIKeyListeners() {
        const $btn = $('#save_mouser_key');
        const $key = $('#mouser_api_key');

        if (!$btn.length || !$key.length) return;

        $btn.off('click.mouser').on('click.mouser', () => {
            const token = $('input[name="_token"]').attr('value');
            const apiKey = ($key.val() || '').trim();

            if (!apiKey) {
                alert('Please enter your Mouser API key.');
                return;
            }

            $.ajax({
                url: `settings/mouser_api_key`,
                method: 'POST',
                data: { value: apiKey, _token: token },
                success: () => {
                    $key.val('');
                    // optional minimal feedback
                    $key.attr('placeholder', '***** (Key stored, enter to replace)');
                },
                error: (xhr) => {
                    alert(xhr?.responseJSON?.message || 'Failed to save Mouser API key.');
                }
            });
        });
    }

    syncMouserKeyUi(isEnabled) {
        $('#mouser_api_key').prop('disabled', !isEnabled);
        $('#save_mouser_key').prop('disabled', !isEnabled);
    }


}
