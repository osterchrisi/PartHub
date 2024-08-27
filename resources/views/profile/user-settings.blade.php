@include('header')
@include('navbar')
<div class="container">
    <h2>User Settings</h2>

    <!-- Example switch for stock level notification -->
    <div class="form-check form-switch">
        <input class="form-check-input user-setting-switch" type="checkbox" id="stocklevel_notification"
            data-setting-name="stocklevel_notification">
        <label class="form-check-label" for="stocklevel_notification">Enable Stock Level Notifications via Email</label>
    </div>

    <!-- Future setting switches will have the same structure -->
    {{-- <div class="form-check form-switch">
        <input class="form-check-input user-setting-switch" type="checkbox" id="theme_preference"
            data-setting-name="theme_preference">
        <label class="form-check-label" for="theme_preference">Dark Theme</label>
    </div> --}}
</div>

<div>
    <form method="POST" action="{{ route('user.settings.update') }}">
        @csrf

        <div class="form-group">
            <label for="timezone">Timezone</label>
            <select name="timezone" id="timezone" class="form-control">
                @foreach (timezone_identifiers_list() as $timezone)
                    <option value="{{ $timezone }}" {{ $userTimezone == $timezone ? 'selected' : '' }}>
                        {{ $timezone }}
                    </option>
                @endforeach
            </select>
        </div>

        <button type="submit" class="btn btn-primary">Save Settings</button>
    </form>
    <div>
        @include('footer')
